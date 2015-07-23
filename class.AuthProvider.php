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
            $keys = array_keys(FlipsideSettings::$auth_providers);
            $count = count($keys);
            for($i = 0; $i < $count; $i++)
            {
                $class = $keys[$i];
                array_push($this->methods, new $class(FlipsideSettings::$auth_providers[$keys[$i]]));
            }
        }
    }

    public function getAuthenticator($method_name)
    {
        $count = count($this->methods);
        for($i = 0; $i < $count; $i++)
        {
            if(strcasecmp(get_class($this->methods[$i]), $method_name) === 0)
            {
                return $this->methods[$i];
            }
        }
        return false;
    }

    public function get_user_by_login($username, $password)
    {
        $res = false;
        $count = count($this->methods);
        for($i = 0; $i < $count; $i++)
        {
            $res = $this->methods[$i]->login($username, $password);
            if($res !== false)
            {
                return $this->methods[$i]->get_user($res);
            }
        }
        return $res;
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
        $auth = $this->getAuthenticator($method_name);
        return $auth->is_logged_in($data);
    }

    public function get_user($method_name, $data)
    {
        $auth = $this->getAuthenticator($method_name);
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
                if($this->methods[$i]->current === false) continue;

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
            $auth = $this->getAuthenticator($method_name);
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
                if($this->methods[$i]->current === false) continue;

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
            $auth = $this->getAuthenticator($method_name);
            return $auth->get_users_by_filter($filter, $select, $top, $skip, $orderby);
        }
    }

    public function get_pending_users_by_filter($method_name, $filter, $select=false, $top=false, $skip=false, $orderby=false)
    {
        if($method_name === false)
        {
            $ret = false;
            $res = false;
            $count = count($this->methods);
            for($i = 0; $i < $count; $i++)
            {
                if($this->methods[$i]->pending === false) continue;

                $res = $this->methods[$i]->get_pending_users_by_filter($filter, $select, $top, $skip, $orderby);
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
            $auth = $this->getAuthenticator($method_name);
            return $auth->get_pending_users_by_filter($filter, $select, $top, $skip, $orderby);
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
                if($this->methods[$i]->current === false) continue;

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
            $auth = $this->getAuthenticator($method_name);
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
                if($this->methods[$i]->current === false) continue;

                $user_count += $this->methods[$i]->get_active_user_count();
            }
            return $user_count;
        }
        else
        {
            $auth = $this->getAuthenticator($method_name);
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
                if($this->methods[$i]->pending === false) continue;

                $user_count += $this->methods[$i]->get_pending_user_count();
            }
            return $user_count;
        }
        else
        {
            $auth = $this->getAuthenticator($method_name);
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
                if($this->methods[$i]->current === false) continue;

                $user_count += $this->methods[$i]->get_group_count();
            }
            return $user_count;
        }
        else
        {
            $auth = $this->getAuthenticator($method_name);
            return $auth->get_group_count();
        }
    }

    public function get_supplementary_links()
    {
        $ret = array();
        $count = count($this->methods);
        for($i = 0; $i < $count; $i++)
        {
            if($this->methods[$i]->supplement === false) continue;

            array_push($ret, $this->methods[$i]->get_supplement_link());
        }
        return $ret;
    }

    public function impersonate_user($user_array)
    {
        if(is_object($user_array))
        {
            \FlipSession::set_user($user_array);
        }
        else
        {
            $user = new $user_array['class']($user_array);
            \FlipSession::set_user($user);
        }
    }

    public function get_temp_user_by_hash($method_name, $hash)
    {
        if($method_name === false)
        {
            $count = count($this->methods);
            for($i = 0; $i < $count; $i++)
            {
                if($this->methods[$i]->pending === false) continue;

                $ret = $this->methods[$i]->get_temp_user_by_hash($hash);
                if($ret !== false)
                {
                    return true;
                }
            }
            return false;
        }
        else
        {
            $auth = $this->getAuthenticator($method_name);
            return $auth->get_temp_user_by_hash($hash);
        }
    }

    public function create_pending_user($method_name, $user)
    {
        if($method_name === false)
        {
            $count = count($this->methods);
            for($i = 0; $i < $count; $i++)
            {
                if($this->methods[$i]->pending === false) continue;

                $ret = $this->methods[$i]->create_pending_user($user);
                if($ret !== false)
                {
                    return true;
                }
            }
            return false;
        }
        else
        {
            $auth = $this->getAuthenticator($method_name);
            return $auth->create_pending_user($user);
        }
    }

    public function activate_pending_user($method_name, $user)
    {
        if($method_name === false)
        {
            $count = count($this->methods);
            for($i = 0; $i < $count; $i++)
            {
                if($this->methods[$i]->current === false) continue;

                $ret = $this->methods[$i]->activate_pending_user($user);
                if($ret !== false)
                {
                    $this->impersonate_user($ret);
                    return true;
                }
            }
            return false;
        }
        else
        {
            $auth = $this->getAuthenticator($method_name);
            return $auth->activate_pending_user($user);
        }
    }

    public function get_user_by_reset_hash($method_name, $hash)
    {
        if($method_name === false)
        {
            $count = count($this->methods);
            for($i = 0; $i < $count; $i++)
            {
                if($this->methods[$i]->current === false) continue;

                $ret = $this->methods[$i]->get_user_by_reset_hash($hash);
                if($ret !== false)
                {
                    return $ret;
                }
            }
            return false;
        }
        else
        {
            $auth = $this->getAuthenticator($method_name);
            return $auth->get_user_by_reset_hash($hash);
        }
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
