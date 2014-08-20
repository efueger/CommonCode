<?php
require_once("ldap/core_schema.php");
require_once("class.FlipsideDB.php");
require_once("class.FlipsideLDAPServer.php");
class FlipsideUser extends inetOrgPerson
{
    function __construct($server, $data = FALSE, $uid = FALSE, $password = FALSE, $email= FALSE)
    {
        if($data != FALSE)
        {
            parent::__construct($server, $data);
        }
        else
        {
            $this->uid = array($uid);
            $this->userPassword = $password;
            $this->mail = array($email);
            $this->resetServer($server);
        }
    }

    private static function convert_from_stdClass($obj)
    {
        return unserialize(sprintf('O:12:"FlipsideUser"%s', strstr(strstr(serialize($obj), '"'), ':')));
    }

    public static function get_temp_user_by_hash($hash)
    {
        $data = FlipsideDB::select_field('registration', 'registration', 'data', array('hash'=>'="'.$hash.'"'));
        if($data == FALSE)
        {
            return FALSE;
        }
        $data = json_decode($data['data']);
        $user = FlipsideUser::convert_from_stdClass($data);
        $user->dn = 'uid='.$user->uid[0].',ou=Users,dc=burningflipside,dc=com';
        $user->objectClass = array('top', 'inetOrgPerson');
        $user->sn = $user->uid[0];
        $user->cn = $user->uid[0];
        return $user;
    }

    public static function getUserByResetHash($hash)
    {
        $uid = FlipsideDB::select_field('registration', 'reset', 'uid', array('hash'=>'="'.$hash.'"'));
        if($uid == FALSE || !isset($uid['uid']))
        {
            return FALSE;
        }
        $server = new FlipsideLDAPServer();
        $users = $server->getUsers("(uid=".$uid['uid'].")");
        if($users == FALSE || !isset($users[0]))
        {
            return FALSE;
        }
        return $users[0];
    }

    function exists()
    {
       if($this->server->userWithUIDExists($this->uid[0]))
       {
           return TRUE;
       }
       else if($this->server->userWithEmailExists($this->mail[0]))
       {
           return TRUE;
       }
       else
       {
           return FALSE;
       }
    }

    function getHash($salt = '')
    {
        return hash('sha512', json_encode($this).$salt);
    }

    function flushToTempDB()
    {
         $hash = $this->GetHash();
         FlipsideDB::write_to_db('registration', 'registration', array('hash'=>$hash,'data'=>json_encode($this),'time'=>'UTC_TIMESTAMP()'));
         return $hash;
    }

    function putInResetDB()
    {
        FlipsideDB::delete_from_db('registration', 'reset', array('uid'=>'="'.$this->uid[0].'"')); 
        $hash = $this->GetHash(microtime());
        FlipsideDB::write_to_db('registration', 'reset', array('hash'=>$hash,'uid'=>$this->uid[0],'time'=>'UTC_TIMESTAMP()'));
        return $hash;
    }

    function eraseFromTempDB($hash)
    {
        return FlipsideDB::delete_from_db('registration', 'registration', array('hash'=>'="'.$hash.'"'));
    }

    function getGroups($nested=TRUE)
    {
        $res = $this->server->getGroups("(member=".$this->dn.")");
        if($res == FALSE || !isset($res[0]))
        {
            return FALSE;
        }
        else if($nested)
        {
            /*See if this group is a member of other groups*/
            $parentGroups = $res[0]->getGroups();
            if($parentGroups != FALSE)
            {
                $res = array_merge($res, $parentGroups);
            }
        }
        return $res;
    }

    function isInGroupNamed($name)
    {
        $groups = $this->getGroups();
        if($groups)
        {
            for($i = 0; $i < count($groups); $i++)
            {
                if($groups[$i]->cn[0] == $name)
                {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }

    function isAARMember()
    {
        if($this->isInGroupNamed("AAR"))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    function isAreaFacilitator()
    {
        if($this->isInGroupNamed("AFs"))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }

    }
    
    function isLead()
    {
        if($this->isInGroupNamed("Leads"))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
}

// vim: set tabstop=4 shiftwidth=4 expandtab:
?>
