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
        return false;
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

    function addLoginProvider($provider)
    {
        throw new \Exception('Cannot add provider for this login type!');
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

    function change_pass($oldpass, $newpass)
    {
        if($this->validate_password($oldpass) === false)
        {
            throw new \Exception('Invalid Password!', 3);
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

    function edit_user($data)
    {
        if(isset($data->oldpass) && isset($data->password))
        {
            $this->change_pass($data->oldpass, $data->password);
        }
        print_r($data);
        die();
    }

    public function jsonSerialize()
    {
        $user = array();
        try{
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
        } catch(\Exception $e) { echo $e->getMessage(); die(); }
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
