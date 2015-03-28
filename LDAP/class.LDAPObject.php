<?php
namespace LDAP;

class LDAPObject extends \SerializableObject
{
     public $server;

     function __construct($array=false, $server=false)
     {
         parent::__construct($array);
         $this->server = $server;
     }
}

?>
