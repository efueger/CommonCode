<?php
namespace Auth;

if(!function_exists('password_hash') || !function_exists('password_verify')) 
{
    define('PASSWORD_BCRYPT', 1);
    define('PASSWORD_DEFAULT', PASSWORD_BCRYPT);
    define('PASSWORD_BCRYPT_DEFAULT_COST', 10);

    function password_hash($password, $algo = PASSWORD_DEFAULT)
    {
        if(is_null($password) || is_int($password))
        {
            $password = (string)$password;
        }
        if(!is_string($password))
        {
            trigger_error("password_hash(): Password must be a string", E_USER_WARNING);
            return false;
        }
        if(!is_int($algo))
        {
            trigger_error("password_hash() expects parameter 2 to be long, " . gettype($algo) . " given", E_USER_WARNING);
            return false;
        }
        $resultLength = 0;
        switch($algo)
        {
            case PASSWORD_BCRYPT:
                $cost = PASSWORD_BCRYPT_DEFAULT_COST;
                $raw_salt_len = 16;
                $required_salt_len = 22;
                $hash_format = sprintf("$2y$%02d$", $cost);
                $resultLength = 60;
                break;
            default:
                trigger_error(sprintf("password_hash(): Unknown password hashing algorithm: %s", $algo), E_USER_WARNING);
                return false;
        }
        $salt = openssl_random_pseudo_bytes($raw_salt_len);
        $base64_digits = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
        $bcrypt64_digits = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $base64_string = base64_encode($salt);
        $salt = strtr(rtrim($base64_string, '='), $base64_digits, $bcrypt64_digits);
        $salt = substr($salt, 0, $required_salt_len);
        $hash = $hash_format . $salt;
        $ret = crypt($password, $hash);
        if(!is_string($ret) || strlen($ret) != $resultLength)
        {
            return false;
        }
        return $ret;
    }

    function password_verify($password, $hash)
    {
        $ret = crypt($password, $hash);
        if(!is_string($ret) || strlen($ret) != strlen($hash) || strlen($ret) <= 13)
        {
            return false;
        }
        $status = 0;
        $count  = strlen($ret);
        for($i = 0; $i < $count; $i++)
        {
            $status |= (ord($ret[$i]) ^ ord($hash[$i]));
        }
        return $status === 0;
    }
}

class SQLAuthenticator
{
    public function login($username, $password)
    {
        $auth_data_set = \DataSetFactory::get_data_set('authentication');
        $user_data_table = $auth_data_set['user'];
        $filter = new \Data\Filter("uid eq '$username'");
        $users = $user_data_table->read($filter, 'uid,pass');
        if($users === false || !isset($users[0]))
        {
            return false;
        }
        if(password_verify($password, $users[0]['pass']))
        {
            return array('res'=>true, 'extended'=>$users[0]['uid']);
        }
        return false;
    }

    public function is_logged_in($data)
    {
        if(isset($data['res']))
        {
            return $data['res'];
        }
        return false;
    }

    public function get_user($data)
    {
        return new SQLUser($data);
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
