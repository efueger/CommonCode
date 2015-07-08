<?php
namespace Auth;
require_once("/var/www/secure_settings/class.FlipsideSettings.php");

function sort_array(&$array, $orderby)
{
    $count = count($array);
    $keys  = array_keys($orderby);
    for($i = 0; $i < $count; $i++)
    {
        for($j = $i; $j < $count; $j++)
        {
            $d = strcasecmp($array[$i][$keys[0]][0], $array[$j][$keys[0]][0]);
            switch($orderby[$keys[0]])
            {
                case 1:
                    if($d > 0) swap($array, $i, $j);
                    break;
                case 0:
                    if($d < 0) swap($array, $i, $j);
                    break;
            }
        }
    }
}

function swap(&$array, $i, $j)
{
    $tmp = $array[$i];
    $array[$i] = $array[$j];
    $array[$j] = $tmp;
}

class LDAPAuthenticator extends Authenticator
{
    private $host;
    private $user_base;
    private $group_base;

    public function __construct($params)
    {
        parent::__construct($params);
        if(isset($params['host']))
        {
            $this->host = $params['host'];
        }
        else
        {
            if(isset(\FlipsideSettings::$ldap['proto']))
            {
                $this->host = \FlipsideSettings::$ldap['proto'].'://'.\FlipsideSettings::$ldap['host'];
            }
            else
            {
                $this->host = \FlipsideSettings::$ldap['host'];
            }
        }
        if(isset($params['user_base']))
        {
           $this->user_base = $params['user_base'];
        }
        else
        {
            $this->user_base = \FlipsideSettings::$ldap['user_base'];
        }
        if(isset($params['group_base']))
        {
            $this->group_base = $params['group_base'];
        }
        else
        {
            $this->group_base = \FlipsideSettings::$ldap['group_base'];
        }
    }

    private function get_and_bind_server()
    {
        $server = \LDAP\LDAPServer::getInstance();
        $server->user_base = $this->user_base;
        $server->group_base = $this->group_base;
        $server->connect($this->host);
        $ret = $server->bind();
        if($ret === false)
        {
            return false;
        }
        return $server;
    }

    public function login($username, $password)
    {
        $server = $this->get_and_bind_server();
        if($server === false)
        {
            return false;
        }
        $filter = new \Data\Filter("uid eq $username or mail eq $username");
        $user = $server->read($this->user_base, $filter);
        if($user === false || count($user) === 0)
        {
            return false;
        }
        $user = $user[0];
        $server->unbind();
        $ret = $server->bind($user->dn, $password);
        if($ret !== false)
        {
            return array('res'=>true, 'extended'=>$user); 
        }
        return false;
    }

    public function is_logged_in($data)
    {
        if(isset($data['res']))
        {
            return $data['res'];
        }
        return false;
    }

    public function get_user($data)
    {
        return new LDAPUser($data);
    }

    public function get_group_by_name($name)
    {
        $server = $this->get_and_bind_server();
        if($server === false)
        {
            return false;
        }
        return LDAPGroup::from_name($name, $server);
    }

    public function get_groups_by_filter($filter, $select=false, $top=false, $skip=false, $orderby=false)
    {
        $server = $this->get_and_bind_server();
        if($server === false)
        {
            return false;
        }
        if($filter === false)
        {
            $filter = new \Data\Filter('cn eq *');
        }
        $groups = $server->read($this->group_base, $filter);
        if($groups === false)
        {
            return false;
        }
        $count = count($groups);
        for($i = 0; $i < $count; $i++)
        {
            $groups[$i] = new LDAPGroup($groups[$i]);
        }
        return $groups;
    }

    public function get_active_user_count()
    {
        $server = $this->get_and_bind_server();
        if($server === false)
        {
            return false;
        }
        return $server->count($this->user_base);
    }

    public function get_users_by_filter($filter, $select=false, $top=false, $skip=false, $orderby=false)
    {
        $server = $this->get_and_bind_server();
        if($server === false)
        {
            return false;
        }
        if($filter === false)
        {
            $filter = new \Data\Filter('cn eq *');
        }
        $users = $server->read($this->user_base, $filter);
        if($users === false)
        {
            return false;
        }
        $count = count($users);
        if($orderby !== false)
        {
            sort_array($users, $orderby);
        }
        if($select !== false)
        {
            $select = array_flip($select);
        }
        if($skip !== false && $top !== false)
        {
            $users = array_slice($users, $skip, $top);
        }
        else if($top !== false)
        {
            $users = array_slice($users, 0, $top);
        }
        else if($skip !== false)
        {
            $users = array_slice($users, $skip);
        }
        $count = count($users);
        for($i = 0; $i < $count; $i++)
        {
            $tmp = new LDAPUser($users[$i]);
            if($select !== false)
            {
                $tmp = $tmp->jsonSerialize();
                $tmp = array_intersect_key($tmp, $select);
            }
            $users[$i] = $tmp;
        }
        return $users;
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
