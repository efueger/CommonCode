<?php
namespace Auth;

class LDAPUser extends User
{
    private $ldap_obj;
    private $server;

    function __construct($data=false)
    {
        if($data !== false && !isset($data['dn']) && !isset($data['extended']))
        {
            //Generic user object
            $this->server = \LDAP\LDAPServer::getInstance();
            $filter = new \Data\Filter('mail eq '.$data['mail']);
            $users = $this->server->read($this->server->user_base, $filter);
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
            if(is_object($this->ldap_obj))
            {
                $this->server   = $this->ldap_obj->server;
            }
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
            if(strpos($array[$i], $this->server->group_base) !== false)
            {
                $dn = explode(',', $array[$i]);
                return $this->isInGroupNamed(substr($dn[0], 3));
            }
        }
    }

    function isInGroupNamed($name)
    {
        $filter = new \Data\Filter('cn eq '.$name);
        $group = $this->server->read($this->server->group_base, $filter);
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

    function getGroups()
    {
        $res = array();
        $groups = $this->server->read($this->server->group_base);
        if(!empty($groups))
        {
            $count = count($groups);
            for($i = 0; $i < $count; $i++)
            {
                if($this->isInGroupNamed($groups[$i]['cn'][0]))
                {
                    array_push($res, new LDAPGroup($groups[$i]));
                }
            }
            return $res;
        }
        else
        {
            return false;
        }
    }

    function addLoginProvider($provider)
    {
        if(!is_object($this->ldap_obj))
        {
            if($this->ldap_obj === false)
            {
                $this->ldap_obj = array();
            }
            $this->ldap_obj['host'] = $provider;
        }
        else
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
    }

    private function generateLDAPPass($pass)
    {
        mt_srand((double)microtime()*1000000);
        $salt = pack("CCCC", mt_rand(), mt_rand(), mt_rand(), mt_rand());
        $hash = base64_encode(pack('H*',sha1($pass.$salt)).$salt);
        return '{SSHA}'.$hash;
    }

    function setPass($password)
    {
        if(!is_object($this->ldap_obj))
        {
            if($this->ldap_obj === false)
            {
                $this->ldap_obj = array();
            }
            $this->ldap_obj['userPassword'] = $this->generateLDAPPass($password);
        }
        else
        {
            $obj = array('dn'=>$this->ldap_obj->dn);
            $obj['userPassword'] = $this->generateLDAPPass($password);
            if(isset($this->ldap_obj->uniqueidentifier))
            {
               $obj['uniqueIdentifier'] = null;
            }
            //Make sure we are bound in write mode
            $auth = \AuthProvider::getInstance();
            $ldap = $auth->getAuthenticator('Auth\LDAPAuthenticator');
            $ldap->get_and_bind_server(true);
            return $this->server->update($obj);
        }
    }

    function validate_password($password)
    {
        if($this->server->bind($this->ldap_obj->dn, $password))
        {
            return true;
        }
        return false;
    }

    function validate_reset_hash($hash)
    {
        if(isset($this->ldap_obj->uniqueidentifier) && strcmp($this->ldap_obj->uniqueidentifier[0], $hash) === 0)
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
        $user = $data->read($data->user_base, $filter);
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
        $user = $data->read($data->user_base, $filter);
        if($user === false || !isset($user[0]))
        {
            return false;
        }
        return new static($user[0]);
    }

    function setDisplayName($name)
    {
        if(!is_object($this->ldap_obj))
        {
            if($this->ldap_obj === false)
            {
                $this->ldap_obj = array();
            }
            $this->ldap_obj['displayName'] = $name;
        }
        else
        {
            $obj = array('dn'=>$this->ldap_obj->dn);
            $obj['displayName'] = $name;
            $this->ldap_obj->displayname = array($name);
            return $this->server->update($obj);
        }
    }

    function setGivenName($name)
    {
        if(!is_object($this->ldap_obj))
        {
            if($this->ldap_obj === false)
            {
                $this->ldap_obj = array();
            }
            $this->ldap_obj['givenName'] = $name;
        }
        else
        {
            $obj = array('dn'=>$this->ldap_obj->dn);
            $obj['givenName'] = $name;
            $this->ldap_obj->givenname = array($name);
            return $this->server->update($obj);
        }
    }

    function setLastName($sn)
    {
        if(!is_object($this->ldap_obj))
        {
            if($this->ldap_obj === false)
            {
                $this->ldap_obj = array();
            }
            $this->ldap_obj['sn'] = $sn;
        }
        else
        {
            $obj = array('dn'=>$this->ldap_obj->dn);
            $obj['sn'] = $sn;
            $this->ldap_obj->sn = array($sn);
            return $this->server->update($obj);
        }
    }

    function setEmail($email)
    {
        if(!is_object($this->ldap_obj))
        {
            if($this->ldap_obj === false)
            {
                $this->ldap_obj = array();
            }
            $this->ldap_obj['mail'] = $email;
        }
        else
        {
            $obj = array('dn'=>$this->ldap_obj->dn);
            $obj['mail'] = $email;
            $this->ldap_obj->mail = array($email);
            return $this->server->update($obj);
        }
    }

    function setUid($uid)
    {
        if(!is_object($this->ldap_obj))
        {
            if($this->ldap_obj === false)
            {
                $this->ldap_obj = array();
            }
            $this->ldap_obj['uid'] = $uid;
        }
        else
        {
            throw new \Exception('Unsupported!');
        }
    }

    function setPhoto($photo)
    {
        if(!is_object($this->ldap_obj))
        {
            if($this->ldap_obj === false)
            {
                $this->ldap_obj = array();
            }
            $this->ldap_obj['jpegPhoto'] = $photo;
        }
        else
        {
            $obj = array('dn'=>$this->ldap_obj->dn);
            $obj['jpegPhoto'] = $photo;
            $this->ldap_obj->jpegphoto = array($photo);
            return $this->server->update($obj);
        }
    }

    function setTitles($titles)
    {
        if(!is_object($this->ldap_obj))
        {
            if($this->ldap_obj === false)
            {
                $this->ldap_obj = array();
            }
            $this->ldap_obj['title'] = $titles;
        }
        else
        {
            $obj = array('dn'=>$this->ldap_obj->dn);
            if($titles === '') $titles = null;
            $obj['title'] = $titles;
            if(is_array($titles))
            {
                $this->ldap_obj->title = $titles;
            }
            else
            {
                $this->ldap_obj->title = array($titles);
            }
            return $this->server->update($obj);
        }
    }

    function setOrganizationUnits($ous)
    {
        if(!is_object($this->ldap_obj))
        {
            if($this->ldap_obj === false)
            {
                $this->ldap_obj = array();
            }
            $this->ldap_obj['ou'] = $ous;
        }
        else
        {
            $obj = array('dn'=>$this->ldap_obj->dn);
            if($ous === '') $ous = null;
            $obj['ou'] = $ous;
            if(is_array($ous))
            {
                $this->ldap_obj->ou = $ous;
            }
            else
            {
                $this->ldap_obj->ou = array($ous);
            }
            return $this->server->update($obj);
        }
    }

    function flushUser()
    {
        if(is_object($this->ldap_obj))
        {
            //In this mode we are always up to date
            return true;
        }
        $obj = $this->ldap_obj;
        $obj['objectClass'] = array('top', 'inetOrgPerson', 'extensibleObject');
        $obj['dn'] = 'uid='.$this->ldap_obj['uid'].','.$this->server->user_base;
        if(!isset($obj['sn']))
        {
            $obj['sn'] = $obj['uid'];
        }
        if(!isset($obj['cn']))
        {
            $obj['cn'] = $obj['uid'];
        }
        $ret = $this->server->create($obj);
        return $ret;
    }

    public function getPasswordResetHash()
    {
        //Make sure we are bound in write mode
        $auth = \AuthProvider::getInstance();
        $ldap = $auth->getAuthenticator('Auth\LDAPAuthenticator');
        $ldap->get_and_bind_server(true);
        $ldap_obj = $this->server->read($ldap->user_base, new \Data\Filter('uid eq '.$this->getUid()));
        $ldap_obj = $ldap_obj[0];
        $hash = hash('sha512', $ldap_obj->dn.';'.$ldap_obj->userpassword[0].';'.$ldap_obj->mail[0]);
        $obj = array('dn'=>$this->ldap_obj->dn);
        $obj['uniqueIdentifier'] = $hash;
        if($this->server->update($obj) === false)
        {
            throw new \Exception('Unable to create hash in LDAP object!');
        }
        return $hash;
    }

    public function delete()
    {
        //Make sure we are bound in write mode
        $auth = \AuthProvider::getInstance();
        $ldap = $auth->getAuthenticator('Auth\LDAPAuthenticator');
        $ldap->get_and_bind_server(true);
        return $this->server->delete($this->ldap_obj->dn);
    }
}

?>
