<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "./ldap");
require_once("ldap/ldap_server.php");
require_once("class.FlipsideUserGroup.php");
require_once("class.FlipsideUser.php");

require_once("/var/www/secure_settings/class.FlipsideSettings.php");

class FlipsideLDAPServer extends ldap_server
{
    public $flipside_user_base            = "ou=Users,dc=burningflipside,dc=com";

    function __construct($server_name=null)
    {
        if($server_name == null)
        {
            $server_name = FlipsideSettings::$ldap['host'];
        }
        $proto = FlipsideSettings::$ldap['proto'];
        parent::__construct($server_name, $proto);
        parent::bind(FlipsideSettings::$ldap_auth['read_write_user'], FlipsideSettings::$ldap_auth['read_write_pass']);
    }

    function search($filter)
    {
        return parent::search(FlipsideSettings::$ldap['base'], $filter);
    }

    function search_extended($base_dn, $filter)
    {
        return parent::search($base_dn, $filter);
    }

    function getGroups($filter="(cn=*)")
    {
        $raw = $this->search_extended(FlipsideSettings::$ldap['group_base'], $filter);
        $res = array();
        for($i = 0; $i < $raw["count"]; $i++)
        {
            array_push($res, new FlipsideUserGroup($this, $raw[$i]));
        }
        return $res;
    }

    function getUsers($filter="(cn=*)")
    {
        $raw = $this->search_extended(FlipsideSettings::$ldap['user_base'], $filter);
        $res = array();
        for($i = 0; $i < $raw["count"]; $i++)
        {
            array_push($res, new FlipsideUser($this, $raw[$i]));
        }
        return $res;
    }

    function getGroupByDN($dn)
    {
        $raw = $this->getObjectByDN($dn);
        if($raw == FALSE)
        {
            return FALSE;
        }
        return new FlipsideUserGroup($this, $raw[0]);
    }

    function testLogin($uid,$pass)
    {
        $dn = "uid=".$uid.",".FlipsideSettings::$ldap['user_base'];
        return parent::testLogin($dn,$pass);
    }
    
    function testLoginByEmail($email,$pass)
    {
        $users = $this->getUsers("(mail=".$email.")");
        if($users == FALSE && !isset($users[0]) && !isset($users[0]->uid) && !isset($users[0]->uid[0]))
        {
            return FALSE;
        }
        $uid = $users[0]->uid[0];
        return $this->testLogin($uid,$pass);
    }

    function userWithEmailExists($email)
    {
        $users = $this->getUsers("(mail=".$email.")");
        if($users == FALSE && !isset($users[0]) && !isset($users[0]->uid) && !isset($users[0]->uid[0]))
        {
            return FALSE;
        }
        return TRUE;
    }

    function userWithUIDExists($uid)
    {
        $users = $this->getUsers("(uid=".$uid.")");
        if($users == FALSE && !isset($users[0]) && !isset($users[0]->uid) && !isset($users[0]->uid[0]))
        {
            return FALSE;
        }
        return TRUE;
    }

    function doLogin($uid, $pass)
    {
        $res = $this->testLogin($uid, $pass);
        if($res == FALSE)
        {
            $res = $this->testLoginByEmail($uid, $pass);
            if($res == FALSE)
            {
                return FALSE;
            }
        }
        $users = $this->getUsers("(uid=".$uid.")");
        if($users == FALSE)
        {
            $users = $this->getUsers("(mail=".$uid.")");
            if($users == FALSE)
            {
                return FALSE;
            }
        }
        return $users[0];
    }
}

// vim: set tabstop=4 shiftwidth=4 expandtab:
?>
