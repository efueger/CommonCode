<?php
namespace Auth;
if(!class_exists('Httpful\Request'))
{
    require('/var/www/common/libs/httpful/bootstrap.php');
}

class FlipsideAPIUser extends User
{
    private $userData;
    private $groupData = null;

    public function __construct($data)
    {
        $this->userData = $data['extended'];
    }

    function isInGroupNamed($name)
    {
        if($this->groupData === null)
        {
            $resp = \Httpful\Request::get('https://profiles.test.burningflipside.com/api/v1/users/me/groups')->authenticateWith($this->userData->uid, $this->userData->userPassword)->send();
            if($resp->hasErrors())
            {
                return false;
            }
            $this->groupData = $resp->body;
        }
        $count = count($this->groupData);
        for($i = 0; $i < $count; $i++)
        {
            if($this->groupData[$i]->cn === $name)
            {
                return true;
            }
        }
        return false;
    }

    function getDisplayName()
    {
        return $this->userData->displayname;
    }

    function getGivenName()
    {
        return $this->userData->givenname;
    }

    function getEmail()
    {
        return $this->userData->mail;
    }

    function getUid()
    {
        return $this->userData->uid;
    }

    function getPhoneNumber()
    {
        return $this->userData->mobile;
    }

    function getOrganization()
    {
        return $this->userData->o;
    }

    function getTitles()
    {
        return $this->userData->title;
    }

    function getState()
    {
        return $this->userData->st;
    }

    function getCity()
    {
        return $this->userData->l;
    }

    function getLastName()
    {
        return $this->userData->sn;
    }

    function getNickName()
    {
        return $this->userData->displayname;
    }

    function getAddress()
    {
        return $this->userData->postaladdress;
    }

    function getPostalCode()
    {
        return $this->userData->postalcode;
    }

    function getCountry()
    {
        return $this->userData->c;
    }

    function getOrganizationUnits()
    {
        return $this->userData->ou;
    }

    function getLoginProviders()
    {
        return $this->userData->host;
    }
}

?>
