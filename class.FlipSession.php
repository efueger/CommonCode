<?php
require_once('class.FlipsideLDAPServer.php');
require_once('class.FlipsideDB.php');
if (!isset($_SESSION)) { session_start(); }
if(!isset($_SESSION['ip_address']))
{
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
}
if(!isset($_SESSION['init_time']))
{
    $_SESSION['init_time'] = date('c');
}

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

    static function unserialize_php_session($session_data)
    {
        $res = array();
        $offset = 0;
        while($offset < strlen($session_data))
        {
            if(!strstr(substr($session_data, $offset), "|"))
            {
                return FALSE;
            }
            $pos = strpos($session_data, "|", $offset);
            $len = $pos - $offset;
            $name = substr($session_data, $offset, $len);
            $offset += $len+1;
            $data = unserialize(substr($session_data, $offset));
            $res[$name] = $data;
            $offset += strlen(serialize($data));
        }
        return $res;
    }

    static function get_all_sessions()
    {
        $res = array();
        $sess_files = scandir(ini_get('session.save_path'));
        for($i = 0; $i < count($sess_files); $i++)
        {
            if($sess_files[$i][0] == '.')
            {
                continue;
            }
            $id = substr($sess_files[$i], 5);
            $session_data = file_get_contents(ini_get('session.save_path').'/'.$sess_files[$i]);
            $res[$id] = FlipSession::unserialize_php_session($session_data);
        }
        if(count($res) == 0)
        {
            return FALSE;
        }
        return $res;
    }

    static function delete_session_by_id($sid)
    {
       return unlink(ini_get('session.save_path').'/sess_'.$sid); 
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
