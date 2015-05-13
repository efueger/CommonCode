<?php
namespace Auth;

class User extends \SerializableObject
{
    function isInGroupNamed($name)
    {
        return false;
    }

    function getEmail()
    {
        return false;
    }

    function getUid()
    {
        return false;
    }

    protected function setPass($password)
    {
        return false;
    }

    function isProfileComplete()
    {
        return false;
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

    function edit_user($data)
    {
        if(isset($data->oldpass) && isset($data->password))
        {
            $this->change_pass($data->oldpass, $data->password);
        }
        print_r($data);
        die();
    }
}

?>
