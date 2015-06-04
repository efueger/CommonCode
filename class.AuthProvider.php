<?php
require_once('Autoload.php');
require_once("/var/www/secure_settings/class.FlipsideSettings.php");
class AuthProvider extends Singleton
{
    protected $methods;

    protected function __construct()
    {
        $this->methods = array();
        if(isset(FlipsideSettings::$auth_providers))
        {
            $count = count(FlipsideSettings::$auth_providers);
            for($i = 0; $i < $count; $i++)
            {
                $class = FlipsideSettings::$auth_providers[$i];
                array_push($this->methods, new $class());
            }
        }
    }

    public function login($username, $password)
    {
        $res = false;
        $count = count($this->methods);
        for($i = 0; $i < $count; $i++)
        {
            $res = $this->methods[$i]->login($username, $password);
            if($res !== false)
            {
                FlipSession::set_var('AuthMethod', get_class($this->methods[$i]));
                FlipSession::set_var('AuthData', $res);
                break;
            }
        }
        return $res;
    }

    public function is_logged_in($method_name, $data)
    {
        $auth = new $method_name();
        return $auth->is_logged_in($data);
    }

    public function get_user($method_name, $data)
    {
        $auth = new $method_name();
        return $auth->get_user($data);
    }

    public function get_group_by_name($method_name, $name)
    {
        if($method_name === false)
        {
            $ret = false;
            $res = false;
            $count = count($this->methods);
            for($i = 0; $i < $count; $i++)
            {
                $res = $this->methods[$i]->get_group_by_name($name);
                if($res !== false)
                {
                    if($ret === false)
                    {
                        $ret = $res;
                    }
                    else
                    {
                        $ret->merge($res);
                    }
                }
            }
            return $ret;
        }
        else
        {
            $auth = new $method_name();
            return $auth->get_group_by_name($name);
        }
    }

    public function get_users_by_filter($method_name, $filter, $select=false, $top=false, $skip=false, $orderby=false)
    {
        if($method_name === false)
        {
            $ret = false;
            $res = false;
            $count = count($this->methods);
            for($i = 0; $i < $count; $i++)
            {
                $res = $this->methods[$i]->get_users_by_filter($filter, $select, $top, $skip, $orderby);
                if($res !== false)
                {
                    if($ret === false)
                    {
                        $ret = $res;
                    }
                    else
                    {
                        $ret->merge($res);
                    }
                }
            }
            return $ret;
        }
        else
        {
            $auth = new $method_name();
            return $auth->get_users_by_filter($filter, $select, $top, $skip, $orderby);
        }
    }

    public function get_groups_by_filter($method_name, $filter, $select=false, $top=false, $skip=false, $orderby=false)
    {
        if($method_name === false)
        {
            $ret = false;
            $res = false;
            $count = count($this->methods);
            for($i = 0; $i < $count; $i++)
            {
                $res = $this->methods[$i]->get_groups_by_filter($filter, $select, $top, $skip, $orderby);
                if($res !== false)
                {
                    if($ret === false)
                    {
                        $ret = $res;
                    }
                    else
                    {
                        $ret->merge($res);
                    }
                }
            }
            return $ret;
        }
        else
        {
            $auth = new $method_name();
            return $auth->get_groups_by_filter($filter, $select, $top, $skip, $orderby);
        }
    }

    public function get_active_user_count($method_name = false)
    {
        if($method_name === false)
        {
            $user_count = 0;
            $count = count($this->methods);
            for($i = 0; $i < $count; $i++)
            {
                $user_count += $this->methods[$i]->get_active_user_count();
            }
            return $user_count;
        }
        else
        {
            $auth = new $method_name();
            return $auth->get_active_user_count();
        }
    }

    public function get_pending_user_count($method_name = false)
    {
        if($method_name === false)
        {
            $user_count = 0;
            $count = count($this->methods);
            for($i = 0; $i < $count; $i++)
            {
                $user_count += $this->methods[$i]->get_pending_user_count();
            }
            return $user_count;
        }
        else
        {
            $auth = new $method_name();
            return $auth->get_pending_user_count();
        }
    }

    public function get_group_count($method_name = false)
    {
        if($method_name === false)
        {
            $user_count = 0;
            $count = count($this->methods);
            for($i = 0; $i < $count; $i++)
            {
                $user_count += $this->methods[$i]->get_group_count();
            }
            return $user_count;
        }
        else
        {
            $auth = new $method_name();
            return $auth->get_group_count();
        }
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
