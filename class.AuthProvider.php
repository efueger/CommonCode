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
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
