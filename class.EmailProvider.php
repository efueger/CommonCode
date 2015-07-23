<?php
require_once('Autoload.php');
require_once("/var/www/secure_settings/class.FlipsideSettings.php");
class EmailProvider extends Singleton
{
    protected $methods;

    protected function __construct()
    {
        $this->methods = array();
        if(isset(FlipsideSettings::$email_providers))
        {
            $keys = array_keys(FlipsideSettings::$email_providers);
            $count = count($keys);
            for($i = 0; $i < $count; $i++)
            {
                $class = $keys[$i];
                array_push($this->methods, new $class(FlipsideSettings::$email_providers[$keys[$i]]));
            }
        }
    }

    public function getEmailMethod($method_name)
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

    public function sendEmail($method_name, $email)
    {
        if($method_name === false)
        {
            $res = false;
            $count = count($this->methods);
            for($i = 0; $i < $count; $i++)
            {
                $res = $this->methods[$i]->sendEmail($email);
                if($res !== false)
                {
                    return $res;
                }
            }
            return $res;
        }
        else
        {
            $method = $this->getEmailMethod($method_name);
            return $method->sendEmail($email);
        }
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
