<?php
require_once('class.FlipsideLDAPServer.php');
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

    static function get_user()
    {
        if(isset($_SESSION['flipside_user']))
        {
            return $_SESSION['flipside_user'];
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

    static function end()
    {
        $_SESSION = array();
        session_destroy();
    }
}
?>
