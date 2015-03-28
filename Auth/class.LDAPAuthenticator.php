<?php
namespace Auth;
require_once("/var/www/secure_settings/class.FlipsideSettings.php");

class LDAPAuthenticator
{
    public function login($username, $password)
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
        $filter = new \Data\Filter("uid eq $username or mail eq $username");
        $ret = $server->bind(\FlipsideSettings::$ldap_auth['read_write_user'],
                             \FlipsideSettings::$ldap_auth['read_write_pass']);
        if($ret === false)
        {
            return false;
        }
        $user = $server->read(\FlipsideSettings::$ldap['base'], $filter);
        if($user === false)
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
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
