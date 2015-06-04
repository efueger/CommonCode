<?php
namespace Auth;

class Authenticator
{
    public function login($username, $password)
    {
        return false;
    }

    public function is_logged_in($data)
    {
        return false;
    }

    public function get_user($data)
    {
        return null;
    }

    public function get_group_by_name($name)
    {
        return null;
    }

    public function get_user_by_name($name)
    {
        return null;
    }

    public function get_groups_by_filter($filter, $select=false, $top=false, $skip=false, $orderby=false)
    {
        return false;
    }

    public function get_users_by_filter($filter, $select=false, $top=false, $skip=false, $orderby=false)
    {
        return false;
    }

    public function get_active_user_count()
    {
        $users = $this->get_users_by_filter(false);
        if($users === false)
        {
            return 0;
        }
        return count($users);
    }

    public function get_pending_user_count()
    {
        return 0;
    }

    public function get_group_count()
    {
        $groups = $this->get_groups_by_filter(false);
        if($groups === false)
        {
            return 0;
        }
        return count($groups);
    }
}
?>
