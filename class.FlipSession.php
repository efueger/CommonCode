<?php
require_once('Autoload.php');
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

class FlipSession extends Singleton
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
        if(isset($_SESSION['flipside_user']))
        {
            return true;
        }
        else if(isset($_SESSION['AuthMethod']) && isset($_SESSION['AuthData']))
        {
            $auth = AuthProvider::getInstance();
            return $auth->is_logged_in($_SESSION['AuthMethod'], $_SESSION['AuthData']);
        }
        else
        {
            return false;
        }
    }

    static function get_user($fixServer = FALSE)
    {
        if(isset($_SESSION['flipside_user']))
        {
            return $_SESSION['flipside_user'];
        }
        else if(isset($_SESSION['AuthMethod']) && isset($_SESSION['AuthData']))
        {
            $auth = AuthProvider::getInstance();
            $user = $auth->get_user($_SESSION['AuthMethod'], $_SESSION['AuthData']);
            if($user !== null)
            {
                $_SESSION['flipside_user'] = $user;
            }
            return $user;
        }
        else
        {
            return null;
        }
    }

    static function refresh_user()
    {
        if(isset($_SESSION['flipside_user']))
        {
            $user = $_SESSION['flipside_user'];
            $user->refresh();
            return $user;
        }
        else
        {
            return false;
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
        return $user->isInGroupNamed('AAR') || $user->isInGroupNamed('AFs') || $user->isInGroupNamed('Leads');
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

    static function get_user_email()
    {
        if(isset($_SESSION['flipside_email']))
        {
            return $_SESSION['flipside_email'];
        }
        $user = FlipSession::get_user(TRUE);
        if($user == FALSE)
        {
            return FALSE;
        }
        if(isset($user->mail) && isset($user->mail[0]))
        {
            $_SESSION['flipside_email'] = $user->mail[0];
            return $_SESSION['flipside_email'];
        }
        return FALSE;
    }

    static function end()
    {
        if(isset($_SESSION) && !empty($_SESSION))
        {
            $_SESSION = array();
            session_destroy();
        }
    }

    static function unserialize_php_session($session_data)
    {
        $res = array();
        $offset = 0;
        while($offset < strlen($session_data))
        {
            if(!strstr(substr($session_data, $offset), "|"))
            {
                return false;
            }
            $pos = strpos($session_data, "|", $offset);
            $len = $pos - $offset;
            $name = substr($session_data, $offset, $len);
            $offset += $len+1;
            $data = @unserialize(substr($session_data, $offset));
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
            if($session_data === false)
            {
                array_push($res, array('sid' => $id));
            }
            else
            {
                $tmp = FlipSession::unserialize_php_session($session_data);
                $tmp['sid' ] = $id;
                array_push($res, $tmp);
            }
        }
        if(count($res) == 0)
        {
            return FALSE;
        }
        return $res;
    }

    static function get_session_by_id($sid)
    {
        $session_data = file_get_contents(ini_get('session.save_path').'/sess_'.$sid);
        return FlipSession::unserialize_php_session($session_data);
    }

    static function delete_session_by_id($sid)
    {
       return unlink(ini_get('session.save_path').'/sess_'.$sid); 
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
