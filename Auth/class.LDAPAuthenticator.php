<?php
namespace Auth;

class LDAPAuthenticator
{
    public function login($username, $password)
    {
        $server = new \FlipsideLDAPServer();
        $user = $server->doLogin($username, $password);
        if($user !== false)
        {
            return array('res'=>true, 'extended'=>$user->uid[0]);
        }
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
        return new LDAPUser($data);
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
