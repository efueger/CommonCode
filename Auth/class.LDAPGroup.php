<?php
namespace Auth;
require_once("/var/www/secure_settings/class.FlipsideSettings.php");

class LDAPGroup extends Group
{
    private $ldap_obj;
    private $server;

    function __construct($data)
    {
        $this->ldap_obj = $data;
        $this->server   = $this->ldap_obj->server;
        if($this->server === null)
        {
            $this->server = \LDAP\LDAPServer::getInstance();
        }
    }

    public function getGroupName()
    {
        return $this->ldap_obj->cn[0];
    }

    public function getDescription()
    {
        if(!isset($this->ldap_obj->description) || !isset($this->ldap_obj->description[0]))
        {
            return false;
        } 
        return $this->ldap_obj->description[0];
    }

    public function getMemberUids()
    {
        $members = array();
        $raw_members = false;
        if(isset($this->ldap_obj['member']))
        {
            $raw_members = $this->ldap_obj['member'];
        }
        else if(isset($this->ldap_obj['uniquemember']))
        {
            $raw_members = $this->ldap_obj['uniquemember'];
        }
        else if(isset($this->ldap_obj['memberuid']))
        {
            $raw_members = $this->ldap_obj['memberuid'];
        }
        for($i = 0; $i < $raw_members['count']; $i++)
        {
            if(strncmp($raw_members[$i], 'cn=', 3) === 0)
            {
                $child = self::from_dn($raw_members[$i], $this->server);
                if($child !== false)
                {
                    $members = array_merge($members, $child->members());
                }
            }
            else
            {
                array_push($members, $raw_members[$i]);
            }
        }
        $count = count($members);
        for($i = 0; $i < $count; $i++)
        {
            $split = explode(',', $members[$i]);
            if(count($split) === 1)
            {
            }
            else
            {
                $members[$i] = substr($split[0], 4);
            }
        }
        return $members;
    }

    public function members($details=false)
    {
        $members = array();
        $raw_members = false;
        if(isset($this->ldap_obj['member']))
        {
            $raw_members = $this->ldap_obj['member'];
        }
        else if(isset($this->ldap_obj['uniquemember']))
        {
            $raw_members = $this->ldap_obj['uniquemember'];
        }
        else if(isset($this->ldap_obj['memberuid']))
        {
            $raw_members = $this->ldap_obj['memberuid'];
        }
        for($i = 0; $i < $raw_members['count']; $i++)
        {
            if(strncmp($raw_members[$i], 'cn=', 3) === 0)
            {
                $child = self::from_dn($raw_members[$i], $this->server);
                if($child !== false)
                {
                    $members = array_merge($members, $child->members());
                }
            }
            else
            {
                array_push($members, $raw_members[$i]);
            }
        }
        if($details === true)
        {
            $details = array();
            $count = count($members);
            for($i = 0; $i < $count; $i++)
            {
                $split = explode(',', $members[$i]);
                if(count($split) === 1)
                {
                    $details[$i] = LDAPUser::from_name($members[$i], $this->server);
                }
                else
                {
                    $details[$i] = LDAPUser::from_name(substr($split[0], 4), $this->server);
                }
            }
            unset($members);
            $members = $details;
        }
        return $members;
    }

    static function from_dn($dn, $data=false)
    {
        if($data === false)
        {
            throw new \Exception('data must be set for LDAPGroup');
        }
        $group = $data->read($dn, false, true);
        if($group === false || !isset($group[0]))
        {
            return false;
        }
        return new static($group[0]);
    }

    static function from_name($name, $data=false)
    {
        if($data === false)
        {
            throw new \Exception('data must be set for LDAPGroup');
        }
        $filter = new \Data\Filter("cn eq $name");
	$group = $data->read($data->group_base, $filter);
        if($group === false || !isset($group[0]))
        {
            return false;
        }
        return new static($group[0]);
    }
}
?>
