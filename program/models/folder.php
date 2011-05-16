<?php

namespace Models;

class Folder extends \Beaver\Base
{
    // ORM properties.
    
    protected static $_table = 'folders';
    public $id;
    public $account_id;
    public $name;
    public $messages_all = 0;
    public $messages_new = 0;
    
    // Find a folder by account ID and folder name.
    
    public static function get_folder($account_id, $name)
    {
        $folder = self::select('WHERE account_id = ? AND name = ? LIMIT 1', array($account_id, $name));
        return $folder ? $folder[0] : null;
    }
    
    // Find all folders belonging to an account.
    
    public static function get_folders($account_id)
    {
        $folders = self::get_if_account_id($account_id, 'name+');
        foreach ($folders as $index => $folder)
        {
            if ($folder->name === 'Inbox')
            {
                unset($folders[$index]);
                array_unshift($folders, $folder);
            }
        }
        return $folders;
    }
    
    // Adjust the all-messages count.
    
    public function adjust_all_messages_count($diff)
    {
        \Common\DB::query('UPDATE folders SET messages_all = messages_all + ? WHERE id = ?', $diff, $this->id);
        $this->messages_all += $diff;
    }

    // Adjust the new-messages count.
    
    public function adjust_new_messages_count($diff)
    {
        \Common\DB::query('UPDATE folders SET messages_new = messages_new + ? WHERE id = ?', $diff, $this->id);
        $this->messages_new += $diff;
    }
    
    // Empty.
    
    public function empty_folder()
    {
        \Common\DB::begin_transaction();
        \Common\DB::query('DELETE FROM attachments WHERE message_id IN (SELECT id FROM messages WHERE folder_id = ?)', $this->id);
        \Common\DB::query('DELETE FROM originals WHERE message_id IN (SELECT id FROM messages WHERE folder_id = ?)', $this->id);
        \Common\DB::query('DELETE FROM messages WHERE folder_id = ?', $this->id);
        $this->save(array('messages_all' => 0, 'messages_new' => 0));
        \Common\DB::commit();
    }
    
    // Export.
    
    public function export()
    {
        $query = \Common\DB::query('SELECT messages.sender, messages.received_time, originals.source FROM messages, originals ' . 
            'WHERE messages.id = originals.message_id AND messages.id IN (SELECT id FROM messages WHERE folder_id = ?) ORDER BY message_id', $this->id);
        $query->bindColumn(1, $sender, \PDO::PARAM_STR);
        $query->bindColumn(2, $received_time, \PDO::PARAM_INT);
        $query->bindColumn(3, $source, \PDO::PARAM_LOB);
        while ($query->fetch(\PDO::FETCH_BOUND))
        {
            if (is_resource($source))
            {
                stream_filter_append($source, 'zlib.inflate');
                if (feof($source)) continue;
                $firstline = fgets($source);
                if (strncasecmp($firstline, 'From ', 5))
                {
                    $sender_objects = \Models\Contact::extract($sender);
                    $sender_email = count($sender_objects) ? ($sender_objects[0]->email ?: '-') : '-';
                    $asctime = gmdate('D M j H:i:s Y', $received_time);
                    if (strlen($asctime) < 24) $asctime = substr($asctime, 0, 8) . ' ' . substr($asctime, 8);
                    echo "From $sender_email  $asctime\n";
                }
                echo $firstline;
                while ($line = fgets($source))
                {
                    if (!strncasecmp($line, 'From ', 5) || !strncasecmp($line, '>', 1)) echo ' ';
                    echo $line;
                }
            }
            else
            {
                $lines = explode("\n", gzinflate($source));
                if (!count($lines)) continue;
                if (strncasecmp($lines[0], 'From ', 5))
                {
                    $sender_objects = \Models\Contact::extract($sender);
                    $sender_email = count($sender_objects) ? ($sender_objects[0]->email ?: '-') : '-';
                    $asctime = gmdate('D M j H:i:s Y', $received_time);
                    if (strlen($asctime) < 24) $asctime = substr($asctime, 0, 8) . ' ' . substr($asctime, 8);
                    echo "From $sender_email  $asctime\n";
                }
                foreach ($lines as $index => $line)
                {
                    if ($index > 0 && !strncasecmp($line, 'From ', 5) || !strncasecmp($line, '>', 1)) echo ' ';
                    echo $line . "\n";
                }
            }
            echo "\n\n";
            flush();
        }
    }
}
