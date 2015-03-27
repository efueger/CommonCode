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
}

?>
