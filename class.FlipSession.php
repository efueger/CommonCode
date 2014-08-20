<?php
require_once('class.FlipsideLDAPServer.php');
require_once('class.FlipsideDB.php');
if (!isset($_SESSION)) { session_start(); }

class FlipSession
{
    static function does_var_exist($name)
    {
        return isset($_SESSION[$name]);
    }

    static function get_var($name, $default = FALSE)
    {
        if(FlipSession::does_var_exist($name))
        {
            return $_SESSION[$name];
        }
        else
        {
            return $default;
        }
    }

    static function set_var($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    static function is_logged_in()
    {
        return isset($_SESSION['flipside_user']);
    }

    static function get_user($fixServer = FALSE)
    {
        if(isset($_SESSION['flipside_user']))
        {
            if($fixServer)
            {
                $user = $_SESSION['flipside_user'];
                $server = new FlipsideLDAPServer();
                $user->resetServer($server);
                return $user;
            }
            else
            {
                return $_SESSION['flipside_user'];
            }
        }
        else
        {
            return FALSE;
        }
    }

    static function get_user_copy()
    {
        return clone FlipSession::get_user();
    }

    static function set_user($user)
    {
        $_SESSION['flipside_user'] = $user;
    }

    static function user_is_lead()
    {
        $user = FlipSession::get_user();
        if($user == FALSE)
        {
            return FALSE;
        }
        $server = new FlipsideLDAPServer();
        $user->resetServer($server);
        return $user->isAARMember() || $user->isAreaFacilitator() || $user->isLead();
    }

    static function get_uid_int()
    {
        if(isset($_SESSION['flipside_uid']))
        {
            return $_SESSION['flipside_uid'];
        }
        $user = FlipSession::get_user();
        if($user == FALSE)
        {
            return FALSE;
        }
        $uid = FlipsideDB::select_field('rdn_uid', 'uid', 'uid', array('rdn'=>'=\''.$user->dn.'\''));
        if($uid === FALSE)
        {
            FlipsideDB::write_to_db('rdn_uid', 'uid', array('rdn'=>$user->dn));
            $uid = FlipsideDB::select_field('rdn_uid', 'uid', 'uid', array('rdn'=>'=\''.$user->dn.'\''));
            if($uid === FALSE)
            {
                return FALSE;
            }
            $_SESSION['flipside_uid'] = $uid['uid'];
            return $uid['uid'];
        }
        else
        {
            $_SESSION['flipside_uid'] = $uid['uid'];
            return $uid['uid'];
        }
    }

    static function end()
    {
        $_SESSION = array();
        session_destroy();
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
