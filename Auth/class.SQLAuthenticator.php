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

class SQLAuthenticator extends Authenticator
{
    private $data_set = null;
    private $data_tables = array();

    private function get_data_set()
    {
        if($this->data_set !== null)
        {
            return $this->data_set;
        }
        $this->data_set = \DataSetFactory::get_data_set('authentication');
        return $this->data_set;
    }

    private function get_data_table($name)
    {
         if(isset($this->data_tables[$name]))
         {
             return $this->data_tables[$name];
         }
         $data_set = $this->get_data_set();
         if($data_set === null)
         {
             throw new \Exception('Unable to obtain dataset for SQL Authentication!');
         }
         $data_table = $data_set[$name];
         $this->data_tables[$name] = $data_table;
         return $this->data_tables[$name];
    }

    public function login($username, $password)
    {
        $user_data_table = $this->get_data_table('user');
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

    public function get_group_by_name($name)
    {
        $group_data_table = $this->get_data_table('group');
        $filter = new \Data\Filter("gid eq '$name'");
        $groups = $group_data_table->read($filter);
        if($groups === false || !isset($groups[0]))
        {
            return false;
        }
        return new SQLGroup($groups[0]);
    }

    public function get_user_by_name($name)
    {
        $user_data_table = $this->get_data_table('user');
        $filter = new \Data\Filter("uid eq '$username'");
        $users = $user_data_table->read($filter);
        if($users === false || !isset($users[0]))
        {
            return false;
        }
        return new SQLUser($users[0]);
    }

    public function get_groups_by_filter($filter, $select=false, $top=false, $skip=false, $orderby=false)
    {
        $group_data_table = $this->get_data_table('group');
        $groups = $group_data_table->read($filter, $select, $top, $skip, $orderby);
        if($groups === false)
        {
            return false;
        }
        $count = count($groups);
        for($i = 0; $i < $count; $i++)
        {
            $groups[$i] = new SQLGroup($groups[$i]);
        }
        return $groups;
    }

    public function get_users_by_filter($filter, $select=false, $top=false, $skip=false, $orderby=false)
    {
        $user_data_table = $this->get_data_table('user');
        $users = $user_data_table->read($filter, $select, $top, $skip, $orderby);
        if($users === false)
        {
            return false;
        }
        $count = count($users);
        for($i = 0; $i < $count; $i++)
        {
            $users[$i] = new SQLUsers($users[$i]);
        }
        return $users;
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
