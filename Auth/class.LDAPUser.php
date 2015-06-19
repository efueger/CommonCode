<?php
namespace Auth;
require_once("/var/www/secure_settings/class.FlipsideSettings.php");

class LDAPUser extends User
{
    private $ldap_obj;
    private $server;

    function __construct($data)
    {
        if(!isset($data['dn']) && !isset($data['extended']))
        {
            //Generic user object
            $this->server = \LDAP\LDAPServer::getInstance();
            $filter = new \Data\Filter('mail eq '.$data['mail']);
            $users = $this->server->read(\FlipsideSettings::$ldap['user_base'], $filter);
            if($users === false || !isset($users[0]))
            {
                throw new \Exception('No such LDAP User!');
            }
            $this->ldap_obj = $users[0];
        }
        else
        {
            if(isset($data['extended']))
            {
                $this->ldap_obj = $data['extended'];
            }
            else
            {
                $this->ldap_obj = $data;
            }
            $this->server   = $this->ldap_obj->server;
            if($this->server === null)
            {
                $this->server = \LDAP\LDAPServer::getInstance();
            }
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

    function getDisplayName()
    {
        if(!isset($this->ldap_obj->displayname) || !isset($this->ldap_obj->displayname[0]))
        {
            return $this->getGivenName();
        }
        return $this->ldap_obj->displayname[0];
    }

    function getGivenName()
    {
        if(!isset($this->ldap_obj->givenname) || !isset($this->ldap_obj->givenname[0]))
        {
            return false;
        }
        return $this->ldap_obj->givenname[0];
    }

    function getEmail()
    {
        if(!isset($this->ldap_obj->mail) || !isset($this->ldap_obj->mail[0]))
        {
            return false;
        }
        return $this->ldap_obj->mail[0];
    }

    function getUid()
    {
        if(!isset($this->ldap_obj->uid) || !isset($this->ldap_obj->uid[0]))
        {
            return false;
        }
        return $this->ldap_obj->uid[0];
    }

    function getPhoto()
    {
        if(!isset($this->ldap_obj->jpegphoto) || !isset($this->ldap_obj->jpegphoto[0]))
        {
            return false;
        }
        return $this->ldap_obj->jpegphoto[0];
    }

    function getPhoneNumber()
    {
        if(!isset($this->ldap_obj->mobile) || !isset($this->ldap_obj->mobile[0]))
        {
            return false;
        }
        return $this->ldap_obj->mobile[0];
    }

    function getOrganization()
    {
        if(!isset($this->ldap_obj->o) || !isset($this->ldap_obj->o[0]))
        {
            return 'Volunteer';
        }
        return $this->ldap_obj->o[0];
    }

    function getTitles()
    {
        if(!isset($this->ldap_obj->title) || !isset($this->ldap_obj->title[0]))
        {
            return false;
        }
        $titles = $this->ldap_obj->title;
        if(isset($titles['count']))
        {
            unset($titles['count']);
        }
        return $titles;
    }

    function getState()
    {
        if(!isset($this->ldap_obj->st) || !isset($this->ldap_obj->st[0]))
        {
            return false;
        }
        return $this->ldap_obj->st[0];;
    }

    function getCity()
    {
        if(!isset($this->ldap_obj->l) || !isset($this->ldap_obj->l[0]))
        {
            return false;
        }
        return $this->ldap_obj->l[0];;
    }

    function getLastName()
    {
        if(!isset($this->ldap_obj->sn) || !isset($this->ldap_obj->sn[0]))
        {
            return false;
        }
        return $this->ldap_obj->sn[0];;
    }

    function getNickName()
    {
        if(!isset($this->ldap_obj->cn) || !isset($this->ldap_obj->cn[0]))
        {
            return false;
        }
        return $this->ldap_obj->cn[0];;
    }

    function getAddress()
    {
        if(!isset($this->ldap_obj->postaladdress) || !isset($this->ldap_obj->postaladdress[0]))
        {
            return false;
        } 
        return $this->ldap_obj->postaladdress[0];
    }

    function getPostalCode()
    {
        if(!isset($this->ldap_obj->postalcode) || !isset($this->ldap_obj->postalcode[0]))
        {
            return false;
        }
        return $this->ldap_obj->postalcode[0];;
    }

    function getCountry()
    {
        if(!isset($this->ldap_obj->c) || !isset($this->ldap_obj->c[0]))
        {
            return false;
        }
        return $this->ldap_obj->c[0];
    }

    function getOrganizationUnits()
    {
        if(!isset($this->ldap_obj->ou))
        {
            return false;
        }
        $units = $this->ldap_obj->ou;
        if(isset($units['count']))
        {
            unset($units['count']);
        }
        return $units;
    }

    function getLoginProviders()
    {
        if(!isset($this->ldap_obj->host))
        {
            return false;
        }
        $hosts = $this->ldap_obj->host;
        if(isset($hosts['count']))
        {
            unset($hosts['count']);
        }
        return $hosts;
    }

    function addLoginProvider($provider)
    {
        $obj = array('dn'=>$this->ldap_obj->dn);
        if(isset($this->ldap_obj->host))
        {
            $obj['host'] = $this->ldap_obj->host;
            $obj['host'][$obj['host']['count']] = $provider;
            $obj['host']['count']++;
        }
        else
        {
            $obj['host'] = $provider;
        }
        return $this->server->update($obj);
    }

    function setPass($password)
    {
        $obj = array('dn'=>$this->ldap_obj->dn);
        $obj['userPassword'] = '{SHA}'.base64_encode(pack('H*',sha1($password)));
        return $this->server->update($obj);
    }

    function validate_password($password)
    {
        if($this->server->bind($this->ldap_obj->dn, $password))
        {
            return true;
        }
        return false;
    }

    static function from_name($name, $data=false)
    {
        if($data === false)
        {
            throw new \Exception('data must be set for LDAPUser');
        }
        $filter = new \Data\Filter("uid eq $name");
        $user = $data->read(\FlipsideSettings::$ldap['base'], $filter);
        if($user === false || !isset($user[0]))
        {
            return false;
        }
        return new static($user[0]);
    }

    static function from_dn($dn, $data=false)
    {
        if($data === false)
        {
            throw new \Exception('data must be set for LDAPUser');
        }
        $filter = new \Data\Filter("dn eq $dn");
        $user = $data->read(\FlipsideSettings::$ldap['base'], $filter);
        if($user === false || !isset($user[0]))
        {
            return false;
        }
        return new static($user[0]);
    }
}

?>
