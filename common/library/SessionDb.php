<?php

class SessionDb implements SessionHandlerInterface
{
  
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        session_set_save_handler($this, true);
    }

    public function open($save_path, $name) // 开启
    {
        return (bool) $this->db;
    }

    public function close()
    {
        return true;
    }

    public function read($id)   //读取
    {
        return (string) $this->db->fetchRow("SELECT `data` FROM __SESSION__ WHERE `id`='$id' AND `expires` > " . time())['data'];
    }

    public function write($id, $data)  //写入
    {
        $expires = time() + (int) ini_get('session.gc_maxlifetime');
        return (bool) $this->db->query("REPLACE INTO __SESSION__ SET `id`=?, `expires`=?, `data`=?", 'sis', [$id, $expires, $data]);
    }

    public function destroy($id)  //销毁
    {
        return (bool) $this->db->query("DELETE FROM __SESSION__ WHERE `id`='$id'");
    }

    public function gc($maxlifetime) //回收
    {
        return (bool) $this->db->query("DELETE FROM __SESSION__ WHERE (`expires` + $maxlifetime) < $maxlifetime");
    }
}
