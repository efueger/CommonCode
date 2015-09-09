<?php
require_once('Autoload.php');
require_once("/var/www/secure_settings/class.FlipsideSettings.php");
class AuthProvider extends Singleton
{
    protected $methods;

    protected function __construct()
    {
        $this->methods = array();
        if(isset(FlipsideSettings::$authProviders))
        {
            $keys = array_keys(FlipsideSettings::$authProviders);
            $count = count($keys);
            for($i = 0; $i < $count; $i++)
            {
                $class = $keys[$i];
                array_push($this->methods, new $class(FlipsideSettings::$authProviders[$keys[$i]]));
            }
        }
    }

    public function getAuthenticator($methodName)
    {
        $count = count($this->methods);
        for($i = 0; $i < $count; $i++)
        {
            if(strcasecmp(get_class($this->methods[$i]), $methodName) === 0)
            {
                return $this->methods[$i];
            }
        }
        return false;
    }

    public function getUserByLogin($username, $password)
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
                FlipSession::setVar('AuthMethod', get_class($this->methods[$i]));
                FlipSession::setVar('AuthData', $res);
                break;
            }
        }
        return $res;
    }

    public function isLoggedIn($data, $methodName)
    {
        $auth = $this->getAuthenticator($methodName);
        return $auth->is_logged_in($data);
    }

    public function getUser($data, $methodName)
    {
        $auth = $this->getAuthenticator($methodName);
        return $auth->get_user($data);
    }

    public function getGroupByName($name, $methodName = false)
    {
        if($methodName === false)
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
            $auth = $this->getAuthenticator($methodName);
            return $auth->get_group_by_name($name);
        }
    }

    public function getUsersByFilter($filter, $select=false, $top=false, $skip=false, $orderby=false, $methodName = false)
    {
        if($methodName === false)
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
            $auth = $this->getAuthenticator($methodName);
            return $auth->get_users_by_filter($filter, $select, $top, $skip, $orderby);
        }
    }

    public function getPendingUsersByFilter($filter, $select=false, $top=false, $skip=false, $orderby=false, $methodName = false)
    {
        if($methodName === false)
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
            $auth = $this->getAuthenticator($methodName);
            return $auth->get_pending_users_by_filter($filter, $select, $top, $skip, $orderby);
        }
    }

    public function getGroupsByFilter($filter, $select=false, $top=false, $skip=false, $orderby=false, $methodName = false)
    {
        if($methodName === false)
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
            $auth = $this->getAuthenticator($methodName);
            return $auth->get_groups_by_filter($filter, $select, $top, $skip, $orderby);
        }
    }

    public function getActiveUserCount($methodName = false)
    {
        if($methodName === false)
        {
            $userCount = 0;
            $count = count($this->methods);
            for($i = 0; $i < $count; $i++)
            {
                if($this->methods[$i]->current === false) continue;

                $userCount += $this->methods[$i]->get_active_user_count();
            }
            return $userCount;
        }
        else
        {
            $auth = $this->getAuthenticator($methodName);
            return $auth->get_active_user_count();
        }
    }

    public function getPendingUserCount($methodName = false)
    {
        if($methodName === false)
        {
            $userCount = 0;
            $count = count($this->methods);
            for($i = 0; $i < $count; $i++)
            {
                if($this->methods[$i]->pending === false) continue;

                $userCount += $this->methods[$i]->get_pending_user_count();
            }
            return $userCount;
        }
        else
        {
            $auth = $this->getAuthenticator($methodName);
            return $auth->get_pending_user_count();
        }
    }

    public function getGroupCount($methodName = false)
    {
        if($methodName === false)
        {
            $groupCount = 0;
            $count = count($this->methods);
            for($i = 0; $i < $count; $i++)
            {
                if($this->methods[$i]->current === false) continue;

                $groupCount += $this->methods[$i]->get_group_count();
            }
            return $groupCount;
        }
        else
        {
            $auth = $this->getAuthenticator($methodName);
            return $auth->get_group_count();
        }
    }

    public function getSupplementaryLinks()
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

    public function impersonateUser($userArray)
    {
        if(is_object($userArray))
        {
            \FlipSession::setUser($userArray);
        }
        else
        {
            $user = new $userArray['class']($userArray);
            \FlipSession::setUser($user);
        }
    }

    public function getTempUserByHash($hash, $methodName = false)
    {
        if($methodName === false)
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
            $auth = $this->getAuthenticator($methodName);
            return $auth->get_temp_user_by_hash($hash);
        }
    }

    public function createPendingUser($user, $methodName = false)
    {
        if($methodName === false)
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
            $auth = $this->getAuthenticator($methodName);
            return $auth->create_pending_user($user);
        }
    }

    public function activatePendingUser($user, $methodName = false)
    {
        if($methodName === false)
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
            $auth = $this->getAuthenticator($methodName);
            return $auth->activate_pending_user($user);
        }
    }

    public function getUserByResetHash($hash, $methodName = false)
    {
        if($methodName === false)
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
            $auth = $this->getAuthenticator($methodName);
            return $auth->get_user_by_reset_hash($hash);
        }
    }

    public function getSuplementalProviderByHost($host)
    {
        $count = count($this->methods);
        for($i = 0; $i < $count; $i++)
        {
            if($this->methods[$i]->supplement === false) continue;

            if($this->methods[$i]->getHostName() === $host)
            {
                return $this->methods[$i];
            }
        }
        return false;
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
