<?php
namespace Auth;

class User extends \SerializableObject
{
    public static $titlenames = null;

    function isInGroupNamed($name)
    {
        return false;
    }

    function getDisplayName()
    {
        return $this->getNickName();
    }

    function getGivenName()
    {
        return $this->getUid();
    }

    function getEmail()
    {
        return false;
    }

    function getUid()
    {
        return $this->getEmail();
    }

    function getPhoto()
    {
        return false;
    }

    function getPhoneNumber()
    {
        return false;
    }

    function getOrganization()
    {
        return false;
    }

    function getTitles()
    {
        return false;
    }

    function getTitleNames()
    {
        $titles = $this->getTitles();
        if($titles === false)
        {
            return false;
        }
        if(self::$titlenames === null)
        {
            $data_set = \DataSetFactory::get_data_set('profiles');
            $data_table = $data_set['position'];
            $titlenames = $data_table->read();
            self::$titlenames = array();
            $count = count($titlenames);
            for($i = 0; $i < $count; $i++)
            {
                self::$titlenames[$titlenames[$i]['short_name']] = $titlenames[$i];
            }
        }
        $count = count($titles);
        for($i = 0; $i < $count; $i++)
        {
            if(isset(self::$titlenames[$titles[$i]]))
            {
                $title = self::$titlenames[$titles[$i]];
                $titles[$i] = $title['name'];
            }
        }
        return $titles;
    }

    function getState()
    {
        return false;
    }

    function getCity()
    {
        return false;
    }

    function getLastName()
    {
        return false;
    }

    function getNickName()
    {
        return $this->getUid();
    }

    function getAddress()
    {
        return false;
    }

    function getPostalCode()
    {
        return false;
    }

    function getCountry()
    {
        return false;
    }

    function getOrganizationUnits()
    {
        return false;
    }

    function getLoginProviders()
    {
        return false;
    }

    function getGroups()
    {
        return false;
    }

    function addLoginProvider($provider)
    {
        throw new \Exception('Cannot add provider for this login type!');
    }

    function canLoginWith($provider)
    {
        $hosts = $this->getLoginProviders();
        if($hosts === false) return false;
        $count = count($hosts);
        for($i = 0; $i < $count; $i++)
        {
            if(strcasecmp($hosts[$i], $provider) === 0) return true;
        }
        return false;
    }

    protected function setPass($password)
    {
        return false;
    }

    function isProfileComplete()
    {
        if($this->getCountry() === false    || $this->getAddress() === false ||
           $this->getPostalCode() === false || $this->getCity() === false ||
           $this->getState() === false      || $this->getPhoneNumber() === false)
        {
            return false;
        }
        return true;
    }

    function validate_password($password)
    {
        return false;
    }

    function validate_reset_hash($hash)
    {
        return false;
    }

    function change_pass($oldpass, $newpass, $is_hash=false)
    {
        if($is_hash === false && $this->validate_password($oldpass) === false)
        {
            throw new \Exception('Invalid Password!', 3);
        }
        if($is_hash === true && $this->validate_reset_hash($oldpass) === false)
        {
            throw new \Exception('Invalid Reset Hash!', 3);
        }
        if($this->setPass($newpass) === false)
        {
            throw new \Exception('Unable to set password!', 6);
        }
        return true;
    }

    function setDisplayName($name)
    {
        return $this->setNickName($name);
    }

    function setGivenName($name)
    {
        return $this->getUid($name);
    }

    function setEmail($email)
    {
        return false;
    }

    function setUid($uid)
    {
        return false;
    }

    function setPhoto($photo)
    {
        return false;
    }

    function setPhoneNumber($phone)
    {
        return false;
    }

    function setOrganization($org)
    {
        return false;
    }

    function setTitles($titles)
    {
        return false;
    }

    function setState($state)
    {
        return false;
    }

    function setCity($city)
    {
        return false;
    }

    function setLastName($sn)
    {
        return false;
    }

    function setNickName($displayName)
    {
        return $this->setUid($displayName);
    }

    function setAddress($address)
    {
        return false;
    }

    function setPostalCode($postalcode)
    {
        return false;
    }

    function setCountry($country)
    {
        return false;
    }

    function setOrganizationUnits($ous)
    {
        return false;
    }

    function editUser($data)
    {
        //Make sure we are bound in write mode
        $auth = \AuthProvider::getInstance();
        $ldap = $auth->getAuthenticator('Auth\LDAPAuthenticator');
        $ldap->get_and_bind_server(true);
        if(isset($data->oldpass) && isset($data->password))
        {
            $this->change_pass($data->oldpass, $data->password);
            unset($data->oldpass);
            unset($data->password);
        }
        else if(isset($data->hash) && isset($data->password))
        {
            $this->change_pass($data->hash, $data->password, true);
            return;
        }
        if(isset($data->displayName))
        {
            $this->setDisplayName($data->displayName);
            unset($data->displayName);
        }
        if(isset($data->givenName))
        {
            $this->setGivenName($data->givenName);
            unset($data->givenName);
        }
        if(isset($data->jpegPhoto))
        {
            $this->setPhoto(base64_decode($data->jpegPhoto));
            unset($data->jpegPhoto);
        }
        if(isset($data->mail))
        {
            if($data->mail !== $this->getEmail())
            {
                throw new \Exception('Unable to change email!');
            }
            unset($data->mail);
        }
        if(isset($data->uid))
        {
            if($data->uid !== $this->getUid())
            {
                throw new \Exception('Unable to change uid!');
            }
            unset($data->uid);
        }
        if(isset($data->mobile))
        {
            $this->setPhoneNumber($data->mobile);
            unset($data->mobile);
        }
        if(isset($data->o))
        {
            $this->setOrganization($data->o);
            unset($data->o);
        }
        if(isset($data->title))
        {
            $this->setTitles($data->title);
            unset($data->title);
        }
        if(isset($data->st))
        {
            $this->setState($data->st);
            unset($data->st);
        }
        if(isset($data->l))
        {
            $this->setCity($data->l);
            unset($data->l);
        }
        if(isset($data->sn))
        {
            $this->setLastName($data->sn);
            unset($data->sn);
        }
        if(isset($data->cn))
        {
            $this->setNickName($data->cn);
            unset($data->cn);
        }
        if(isset($data->postalAddress))
        {
            $this->setAddress($data->postalAddress);
            unset($data->postalAddress);
        }
        if(isset($data->postalCode))
        {
            $this->setPostalCode($data->postalCode);
            unset($data->postalCode);
        }
        if(isset($data->c))
        {
            $this->setCountry($data->c);
            unset($data->c);
        }
        if(isset($data->ou))
        {
            $this->setOrganizationUnits($data->ou);
            unset($data->ou);
        }
    }

    public function getPasswordResetHash()
    {
        return false;
    }

    public function jsonSerialize()
    {
        $user = array();
        $user['displayName'] = $this->getDisplayName();
        $user['givenName'] = $this->getGivenName();
        $user['jpegPhoto'] = base64_encode($this->getPhoto());
        $user['mail'] = $this->getEmail();
        $user['mobile'] = $this->getPhoneNumber();
        $user['uid'] = $this->getUid();
        $user['o'] = $this->getOrganization();
        $user['title'] = $this->getTitles();
        $user['titlenames'] = $this->getTitleNames();
        $user['st'] = $this->getState();
        $user['l'] = $this->getCity();
        $user['sn'] = $this->getLastName();
        $user['cn'] = $this->getNickName();
        $user['postalAddress'] = $this->getAddress();
        $user['postalCode'] = $this->getPostalCode();
        $user['c'] = $this->getCountry();
        $user['ou'] = $this->getOrganizationUnits();
        $user['host'] = $this->getLoginProviders();
        $user['class'] = get_class($this);
        return $user;
    }

    public function getVcard()
    {
        $ret = "BEGIN:VCARD\nVERSION:2.1\n";
        $ret.= 'N:'.$this->getLastName().';'.$this->getGivenName()."\n";
        $ret.= 'FN:'.$this->getGivenName()."\n";
        $ret.= 'TITLE:'.implode(',', $this->getTitles())."\n";
        $ret.= "ORG: Austin Artistic Reconstruction\n";
        $ret.= 'TEL;TYPE=MOBILE,VOICE:'.$this->getPhoneNumber()."\n";
        $ret.= 'EMAIL;TYPE=PREF,INTERNET:'.$this->getEmail()."\n";
        $ret.= "END:VCARD\n";
        return $ret;
    }
}

?>
