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
    private function get_and_bind_server()
    {
        $server = \LDAP\LDAPServer::getInstance();
        if(isset(\FlipsideSettings::$ldap['proto']))
        {
            $server->connect(\FlipsideSettings::$ldap['host'],
                             \FlipsideSettings::$ldap['proto']);
        }
        else
        {
            $server->connect(\FlipsideSettings::$ldap['host']);
        }
        $ret = $server->bind(\FlipsideSettings::$ldap_auth['read_write_user'],
                             \FlipsideSettings::$ldap_auth['read_write_pass']);
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
        $user = $server->read(\FlipsideSettings::$ldap['user_base'], $filter);
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
        $groups = $server->read(\FlipsideSettings::$ldap['group_base'], $filter);
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
        return $server->count(\FlipsideSettings::$ldap['user_base']);
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
        $users = $server->read(\FlipsideSettings::$ldap['user_base'], $filter);
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
