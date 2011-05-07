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
}
