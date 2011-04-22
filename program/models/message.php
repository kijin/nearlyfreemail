<?php

namespace Models;

class Message extends \Beaver\Base
{
    // ORM properties.
    
    protected static $_table = 'messages';
    public $id;
    public $account_id;
    public $folder_id;
    public $msgid;
    public $sender;
    public $recipient;
    public $refs;
    public $cc;
    public $bcc;
    public $reply_to;
    public $subject;
    public $content;
    public $charset;
    public $sent_time;
    public $received_time;
    public $attachments;
    public $spam_score;
    public $is_draft;
    public $is_read;
    public $is_replied;
    public $is_starred;
    public $notes;
    
    // Get a list of messages in a folder.
    
    public static function get_list($account_id, $folder_id, $count, $page)
    {
        return self::select('WHERE account_id = ? AND folder_id = ? ORDER BY received_time DESC LIMIT ? OFFSET ?', array($account_id, $folder_id, $count, $count * ($page - 1)));
    }
    
    // Search messages for a series of keywords.
    
    public static function search($account_id, $folder_id, $keywords, $count, $page)
    {
        if ($folder_id)
        {
            $querystring = 'WHERE account_id = ? AND folder_id = ? ';
            $params = array($account_id, $folder_id);
        }
        else
        {
            $querystring = 'WHERE account_id = ? ';
            $params = array($account_id);
        }
        
        foreach ($keywords as $keyword)
        {
            if (trim($keyword) === '') continue;
            $keyword = '%' . str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $keyword) . '%';
            $querystring .= 'AND (subject || \' \' || content LIKE ? ESCAPE ?) ';
            $params[] = $keyword;
            $params[] = '\\';
        }
        
        $querystring .= 'AND is_draft = 0 ORDER BY received_time DESC LIMIT ? OFFSET ?';
        $params[] = $count;
        $params[] = $count * ($page - 1);
        return self::select($querystring, $params);
    }
    
    // Mark as read.
    
    public function mark_as_read()
    {
        if ($this->is_read) return;
        $transid = \Common\DB::try_begin_transaction();
        $this->save(array('is_read' => 1));
        Folder::get($this->folder_id)->adjust_new_messages_count(-1);
        \Common\DB::try_commit($transid);
    }
    
    // Mark as unread.
    
    public function mark_as_unread()
    {
        if (!$this->is_read) return;
        $transid = \Common\DB::try_begin_transaction();
        $this->save(array('is_read' => 0));
        Folder::get($this->folder_id)->adjust_new_messages_count(1);
        \Common\DB::try_commit($transid);
    }
    
    // Mark as replied.
    
    public function mark_as_replied()
    {
        \Common\DB::query('UPDATE messages SET is_replied = is_replied | 1 WHERE id = ?', $this->id);
    }
    
    // Mark as forwarded.
    
    public function mark_as_forwarded()
    {
        \Common\DB::query('UPDATE messages SET is_replied = is_replied | 2 WHERE id = ?', $this->id);
    }
    
    // Mark as draft.
    
    public function mark_as_draft()
    {
        $this->save(array('is_draft' => 1));
    }
    
    // Mark as sent.
    
    public function mark_as_sent()
    {
        $this->save(array('is_draft' => 2));
    }
    
    // Mark as starred.
    
    public function mark_as_starred()
    {
        $this->save(array('is_starred' => 1));
    }
    
    // Move to a different folder.
    
    public function move_to_folder($folder_id)
    {
        $old_folder_id = $this->folder_id;
        if ($old_folder_id == $folder_id) return;
        
        $transid = \Common\DB::try_begin_transaction();
        $this->save(array('folder_id' => $folder_id));
        Folder::get($old_folder_id)->adjust_all_messages_count(-1);
        Folder::get($folder_id)->adjust_all_messages_count(1);
        if (!$this->is_read)
        {
            Folder::get($old_folder_id)->adjust_new_messages_count(-1);
            Folder::get($folder_id)->adjust_new_messages_count(1);
        }
        \Common\DB::try_commit($transid);
    }
    
    // Save override.
    
    public function save($data = array())
    {
        $must_update_folder_too = $this->_is_unsaved_object;
        $transid = \Common\DB::try_begin_transaction();
        parent::save($data);
        if ($must_update_folder_too)
        {
            Folder::get($this->folder_id)->adjust_all_messages_count(1);
            if (!$this->is_read) Folder::get($this->folder_id)->adjust_new_messages_count(1);
        }
        \Common\DB::try_commit($transid);
    }
    
    // Delete override.
    
    public function delete()
    {
        $transid = \Common\DB::try_begin_transaction();
        \Common\DB::query('DELETE FROM attachments WHERE message_id = ?', $this->id);
        \Common\DB::query('DELETE FROM originals WHERE message_id = ?', $this->id);
        parent::delete();
        Folder::get($this->folder_id)->adjust_all_messages_count(-1);
        if (!$this->is_read) Folder::get($this->folder_id)->adjust_new_messages_count(-1);
        \Common\DB::try_commit($transid);
    }
    
    // Set the original (non-transcoded) subject/content and the message source.
    
    public function set_original_and_source($subject, $content, $source)
    {
        $query = \Common\DB::prepare('INSERT INTO originals (message_id, subject, content, source) VALUES (?, ?, ?, ?)');
        $subject = gzdeflate($subject);
        $content = gzdeflate($content);
        $fp = fopen($source, 'rb');
        stream_filter_append($fp, 'convert.base64-decode');
        stream_filter_append($fp, 'zlib.deflate');
        $query->bindParam(1, $this->id);
        $query->bindParam(2, $subject);
        $query->bindParam(3, $content);
        $query->bindParam(4, $fp, \PDO::PARAM_LOB);
        $query->execute();
    }
    
    // Add an attachment.
    
    public function add_attachment($name, $filename)
    {
        $query = \Common\DB::prepare('INSERT INTO attachments (message_id, filename, filesize, deleted, content) VALUES (?, ?, ?, ?, ?)');
        $deleted = 0;
        $fp = fopen($filename, 'rb');
        $fs = filesize($filename);
        $query->bindParam(1, $this->id);
        $query->bindParam(2, $name);
        $query->bindParam(3, $fs);
        $query->bindParam(4, $deleted);
        $query->bindParam(5, $fp, \PDO::PARAM_LOB);
        $query->execute();
    }
    
    // Get a list of attachments.
    
    public function get_attachments()
    {
        $query = \Common\DB::query('SELECT id, filename, filesize, deleted FROM attachments WHERE message_id = ? ORDER BY id', $this->id);
        $return = array();
        while ($obj = $query->fetchObject())
        {
            $return[] = $obj;
        }
        return $return;
    }
    
    // Get the contents of an attachment.
    
    public function get_attachment($file_id)
    {
        $query = \Common\DB::query('SELECT filename, filesize, content FROM attachments WHERE id = ? AND message_id = ? LIMIT 1', $file_id, $this->id);
        $query->bindColumn(1, $filename, \PDO::PARAM_STR);
        $query->bindColumn(2, $filesize, \PDO::PARAM_INT);
        $query->bindColumn(3, $content, \PDO::PARAM_LOB);
        $success = $query->fetch(\PDO::FETCH_BOUND);
        if (!$success) return array(null, null, null);
        return array($filename, $filesize, $content);
    }
    
    // Delete an attachment.
    
    public function delete_attachment($file_id)
    {
        return \Common\DB::query('DELETE FROM attachments WHERE id = ? AND message_id = ?', $file_id, $this->id);
    }
    
    // Get the original (non-transcoded) subject/content.
    
    public function get_original_subject_and_content()
    {
        $query = \Common\DB::query('SELECT subject, content FROM originals WHERE message_id = ? LIMIT 1', $this->id);
        $query->bindColumn(1, $subject, \PDO::PARAM_STR);
        $query->bindColumn(2, $content, \PDO::PARAM_STR);
        $success = $query->fetch(\PDO::FETCH_BOUND);
        if (!$success) return array(null, null);
        return array(gzinflate($subject), gzinflate($content));
    }
    
    // Get the message source.
    
    public function get_source()
    {
        $query = \Common\DB::query('SELECT source FROM originals WHERE message_id = ? LIMIT 1', $this->id);
        $query->bindColumn(1, $source, \PDO::PARAM_LOB);
        $success = $query->fetch(\PDO::FETCH_BOUND);
        if (!$success) return false;
        if (is_resource($source))  // The PDO manual says that large objects will be returned as a stream.
        {
            stream_filter_append($source, 'zlib.inflate');
            return $source;
        }
        else  // But actually it could be a string, depending on the PHP version. See PHP bug #40913.
        {
            return gzinflate($source);
        }
    }
    
    // Delete the message source.
    
    public function delete_source()
    {
        return \Common\DB::query('DELETE FROM originals WHERE message_id = ?', '', $this->id);
    }
}
