<?php
namespace Auth;

class SQLPendingUser extends PendingUser
{
    private $hash;
    private $time;
    private $blob;

    function __construct($data)
    {
        $this->hash = $data['hash'];
        $this->time = new \DateTime($data['time']);
        $this->blob = json_decode($data['data']);
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function getRegistrationTime()
    {
        return $this->time;
    }

    function getEmail()
    {
        if(is_array($this->blob->mail))
        {
            return $this->blob->mail[0];
        }
        return $this->blob->mail;
    }

    function getUid()
    {
        if(is_array($this->blob->uid))
        {
            return $this->blob->uid[0];
        }
        return $this->blob->uid;
    }
}

?>
