<?php
namespace Auth;

class LDAPUser extends User
{
    private $uid;
    private $server;

    function __construct($data)
    {
        $this->uid    = $data['extended'];
        $this->server = new \FlipsideLDAPServer();
    }

    function isInGroupNamed($name)
    {
        $users = $this->server->getUsers('(uid='.$this->uid.')');
        if($users === false || !isset($users[0]))
        {
            \FlipSession::end();
            return false;
        }
        return $users[0]->isInGroupNamed($name);
    }

    function getEmail()
    {
        $users = $this->server->getUsers('(uid='.$this->uid.')');
        if($users === false || !isset($users[0]))
        {
            \FlipSession::end();
            return false;
        }
        return $users[0]->mail[0];
    }
}

?>
