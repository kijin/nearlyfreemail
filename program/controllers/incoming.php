<?php

namespace Controllers;

class Incoming extends Base
{
    // This is the super-duper method that handles all incoming messages.
    
    public function receive($key)
    {
        // Does the client speak HTTP POST?
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !count($_POST))
        {
            error_log('Received a GET request. Request denied. [code 405]');
            $this->return_status(405, 'Not POST');
        }
        
        // Does the client look like NFSN's e-mail forwarding gateway?
        
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'NearlyFreeSpeech.NET') === false ||
            !isset($_POST['From']) || !isset($_POST['To']) || !isset($_POST['Date']) || 
            !isset($_POST['Subject']) || !isset($_POST['Body']) ||
            !isset($_FILES['headers']) || !isset($_FILES['raw0']) ||
            !is_uploaded_file($_FILES['raw0']['tmp_name']))
        {
            error_log('This client doesn\'t look like NFSN. Request denied. [code 403]');
            $this->return_status(403, 'Wrong Client');
        }
        
        // Does the URL key belong to an account/alias here?
        
        error_log('NEW MESSAGE RECEIVED FOR KEY: ' . $key);
        $alias = \Models\Alias::find('WHERE incoming_key LIKE ?', array($key . '%'));
        if (!$alias)
        {
            error_log('Recipient not found. Message rejected. [code 404]');
            $this->return_status(404, 'Recipient Not Found');
        }
        
        $alias = $alias[0];
        $account = $alias->get_account();
        error_log('Recipient found: ' . $alias->name . ' <' . $alias->email . '>');
        
        // Extract additional information from headers.
        
        error_log('Extracting additional information from headers...');
        $headers = iconv_mime_decode_headers(file_get_contents($_FILES['headers']['tmp_name']), 2);
        $in_reply_to = '';
        $references = '';
        $cc = '';
        $reply_to = '';
        $spam_score = 0;
        $multipart = false;
        $charset = false;
        
        foreach ($headers as $key => $value)
        {
            $key = strtolower($key);
            switch ($key)
            {
                case 'in-reply-to': $in_reply_to = $value; break;
                case 'references': $references = $value; break;
                case 'cc': $cc = $value; break;
                case 'reply-to': $reply_to = $value; break;
                case 'x-spam-score': $spam_score = $value; break;
                case 'content-type':
                    $content_type = $value;
                    if (preg_match('/^multipart\\//i', $content_type))
                    {
                        error_log('This is a multipart message in MIME format.');
                        $multipart = true;
                    }
                    elseif (preg_match('/charset="?([^";]+)"?/i', $content_type, $matches))
                    {
                        error_log('Character encoding detected: ' . trim($matches[1]));
                        $charset = trim($matches[1]);
                    }
                    break;
            }
        }
        
        // Append 'In-Reply-To' to 'References' for future reference. (In most cases, this is already included.)
        
        if ($in_reply_to && preg_match('/^<[^<>]+>$/', $in_reply_to))
        {
            if (strpos($references, $in_reply_to) === false) $references .= ' ' . $in_reply_to;
        }
        
        // Attempt to detect the character encoding of the body part of a multipart message.
        
        if ($multipart && !$charset)
        {
            error_log('Attempting to detect the character encoding of the body part...');
            $fp = fopen($_FILES['raw0']['tmp_name'], 'r');  // Note: The raw message is base64 encoded.
            fseek($fp, floor($_FILES['headers']['size'] / 3) * 4);  // Skip the approximate length of headers.
            $lines = base64_decode(fread($fp, 4096));  // Read the first 4KB. It will be 3KB after decoding.
            $lines = explode("\n", $lines);
            foreach ($lines as $line)
            {
                if (preg_match('/^Content-Type: text\\/.+;.*charset="?([^";]+)"?/i', $line, $matches))
                {
                    error_log('Character encoding detected: ' . trim($matches[1]));
                    $charset = trim($matches[1]);
                    break;
                }
            }
            fclose($fp);
        }
        
        // Transcode important parts of the message to UTF-8.
        
        $sender = $_POST['From'];
        $recipient = $_POST['To'];
        $subject = $original_subject = $_POST['Subject'];
        $content = $original_content = $_POST['Body'];
        if ($charset && $charset !== 'UTF-8')
        {
            error_log('Transcoding the message to UTF-8...');
            if (@mb_check_encoding($sender, $charset)) $sender = mb_convert_encoding($sender, 'UTF-8', $charset);
            if (@mb_check_encoding($recipient, $charset)) $recipient = mb_convert_encoding($recipient, 'UTF-8', $charset);
            if (@mb_check_encoding($cc, $charset)) $cc = mb_convert_encoding($cc, 'UTF-8', $charset);
            if (@mb_check_encoding($reply_to, $charset)) $reply_to = mb_convert_encoding($reply_to, 'UTF-8', $charset);
            if (@mb_check_encoding($subject, $charset)) $subject = mb_convert_encoding($subject, 'UTF-8', $charset);
            if (@mb_check_encoding($content, $charset)) $content = mb_convert_encoding($content, 'UTF-8', $charset);
        }
        
        // Count attachments. (Skip the plain text and HTML versions of the message body.)
        
        $attachments = array();
        foreach ($_FILES as $key => $file)
        {
            if (strncmp($key, 'part', 4)) continue;
            if (substr($file['name'], 0, 4) === 'body' && substr($file['name'], 4) === substr($key, 4)) continue;
            if (!is_uploaded_file($file['tmp_name'])) continue;
            $attachments[] = $key;
        }
        
        // Decide which folder to file this into.
        
        if ($spam_score && $account->get_setting('spam_threshold') > 0 && $spam_score >= $account->get_setting('spam_threshold'))
        {
            error_log('This might be spam. (spam score: ' . $spam_score . ')');
            $folder = \Models\Folder::get_folder($account->id, 'Spam');
            if (!$folder)
            {
                error_log('Database error: Spam folder doesn\'t exist. [code 500]');
                $this->return_status(500, 'Database Error');
            }
        }
        else
        {
            $folder = \Models\Folder::get_folder($account->id, 'Inbox');
            if (!$folder)
            {
                error_log('Database error: Inbox folder doesn\'t exist. [code 500]');
                $this->return_status(500, 'Database Error');
            }
        }
        
        // Time to hit the database.
        
        try
        {
            // Everything should happen inside a single transaction.
            
            \Common\DB::begin_transaction();
            
            // Create a new message object.
            
            error_log('Creating a new message object...');
            $message = new \Models\Message();
            $message->account_id = $account->id;
            $message->alias_id = $alias->id;
            $message->folder_id = $folder->id;
            $message->msgid = isset($_POST['Message-ID']) ? $_POST['Message-ID'] : '';
            $message->sender = $sender;
            $message->recipient = $recipient;
            $message->refs = $references;
            $message->cc = $cc;
            $message->bcc = '';
            $message->reply_to = $reply_to;
            $message->subject = $subject;
            $message->content = $content;
            $message->charset = $charset;
            $message->sent_time = strtotime($_POST['Date']);
            $message->received_time = time();
            $message->attachments = count($attachments);
            $message->spam_score = $spam_score;
            $message->is_draft = 0;
            $message->is_read = 0;
            $message->is_replied = 0;
            $message->is_starred = 0;
            $message->notes = '';
            $message->save();
            
            // Save all attachments.
            
            foreach ($attachments as $attachment)
            {
                $name = $_FILES[$attachment]['name'];
                if (preg_match('/^body\\d+$/', $name))  // NFS.N destroys international filenames.
                {
                    $type = $_FILES[$attachment]['type'];  // So let's at least restore the extension.
                    $ext = \Common\MIME::get_extension($type);
                    if ($ext) $name .= '.' . $ext;
                }
                $name = \Common\Security::filter($name, 'filename');  // Security check.
                error_log('Saving attachment: ' . $name);
                $message->add_attachment($name, $_FILES[$attachment]['tmp_name']);
            }
            
            // Compress and save the original subject/content and message source.
            
            error_log('Saving the message source in compressed form...');
            $message->set_original_and_source($original_subject, $original_content, $_FILES['raw0']['tmp_name']);
            
            // Fingers crossed...
            
            error_log('Committing the transaction...');
            \Common\DB::commit();
        }
        
        // Did anything go wrong?
        
        catch (\PDOException $e)
        {
            error_log('Database error: ' . $e->getMessage() . ' [code 500]');
            $this->return_status(500, 'Database Error');
        }
        
        // Congratulations, your message got through.
        
        error_log('Message successfully delivered. [code 200]');
        $this->return_status(200, 'OK');
    }
    
    // Shortcut for displaying status messages.
    
    protected function return_status($code, $message)
    {
        \Common\Response::send_http_status_code($code);
        header('Content-Type: text/plain; charset=UTF-8');
        echo $message;
        exit;
    }
}
