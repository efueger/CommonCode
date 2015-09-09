<?php
namespace Auth;

class Authenticator
{
    const SUCCESS         = 0;
    const ALREADY_PRESENT = 1;
    const LOGIN_FAILED    = 2;

    public $current = false;
    public $pending = false;
    public $supplement = false;

    public function __construct($params)
    {
        $this->current = $params['current'];
        $this->pending = $params['pending'];
        $this->supplement = $params['supplement'];
    }

    public function login($username, $password)
    {
        return false;
    }

    public function isLoggedIn($data)
    {
        return false;
    }

    public function getUser($data)
    {
        return null;
    }

    public function getGroupByName($name)
    {
        return null;
    }

    public function getUserByName($name)
    {
        return null;
    }

    public function getGroupsByFilter($filter, $select=false, $top=false, $skip=false, $orderby=false)
    {
        return false;
    }

    public function getUsersByFilter($filter, $select=false, $top=false, $skip=false, $orderby=false)
    {
        return false;
    }

    public function getPendingUsersByFilter($filter, $select=false, $top=false, $skip=false, $orderby=false)
    {
        return false;
    }

    public function getActiveUserCount()
    {
        $users = $this->getUsersByFilter(false);
        if($users === false)
        {
            return 0;
        }
        return count($users);
    }

    public function getPendingUserCount()
    {
        $users = $this->getPendingUsersByFilter(false);
        if($users === false)
        {
            return 0;
        }
        return count($users);
    }

    public function getGroupCount()
    {
        $groups = $this->getGroupsByFilter(false);
        if($groups === false)
        {
            return 0;
        }
        return count($groups);
    }

    public function getSupplementLink()
    {
        return false;
    }

    public function createPendingUser($user)
    {
        return false;
    }

    public function activatePendingUser($user)
    {
        return false;
    }

    public function getUserByResetHash($hash)
    {
        return false;
    }

    public function getTempUserByHash($hash)
    {
        return false;
    }

    public function getHostName()
    {
        return false;
    }
}
?>
