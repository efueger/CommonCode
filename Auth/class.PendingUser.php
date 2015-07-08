<?php
namespace Auth;

class PendingUser extends User
{
    public function getHash()
    {
        return false;
    }

    public function getRegistrationTime()
    {
        return false;
    }

    public function isInGroupNamed($name)
    {
        return false;
    }

    public function jsonSerialize()
    {
        $user = array();
        $user['hash'] = $this->getHash();
        $user['mail'] = $this->getEmail();
        $user['uid'] = $this->getUid();
        $user['time'] = $this->getRegistrationTime()->format(\DateTime::RFC822);
        $user['class'] = get_class($this);
        return $user; 
    }
}

?>
