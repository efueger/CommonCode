<?php
namespace Auth;
require_once("/var/www/secure_settings/class.FlipsideSettings.php");

class LDAPUser extends User
{
    private $ldap_obj;
    private $server;

    function __construct($data)
    {
        $this->ldap_obj = $data['extended'];
        $this->server   = $this->ldap_obj->server;
        if($this->server === null)
        {
            $this->server = \LDAP\LDAPServer::getInstance();
        }
    }

    private function check_child_group($array)
    {
        for($i = 0; $i < $array['count']; $i++)
        {
            if(strpos($array[$i], \FlipsideSettings::$ldap['group_base']) !== false)
            {
                $dn = explode(',', $array[$i]);
                return $this->isInGroupNamed(substr($dn[0], 3));
            }
        }
    }

    function isInGroupNamed($name)
    {
        $filter = new \Data\Filter('cn eq '.$name);
        $group = $this->server->read(\FlipsideSettings::$ldap['group_base'], $filter);
        if(!empty($group))
        {
            $group = $group[0];
            $dn  = $this->ldap_obj->dn;
            $uid = $this->ldap_obj->uid[0];
            if(isset($group['member']))
            {
                if(in_array($dn, $group['member']))
                {
                    return true;
                }
                else
                {
                    return $this->check_child_group($group['member']);
                }
            }
            else if(isset($group['uniquemember']))
            {
                if(in_array($dn, $group['uniquemember']))
                {
                    return true;
                }
                else
                {
                    return $this->check_child_group($group['uniquemember']);
                }
            }
            else if(isset($group['memberUid']) && in_array($uid, $group['memberUid']))
            {
                return true;
            }
        }
        return false;
    }

    function getEmail()
    {
        return $this->ldap_obj->mail[0];
    }

    function getUid()
    {
        return $this->ldap_obj->uid[0];
    }
}

?>
