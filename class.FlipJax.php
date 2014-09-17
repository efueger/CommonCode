<?php
require_once("class.FlipSession.php");
class FlipJax
{
    const SUCCESS = 0;
    const UNRECOGNIZED_METHOD = 1;
    const INVALID_PARAM = 2;
    const ALREADY_LOGGED_IN = 3;
    const INVALID_LOGIN = 4;

    const UNKNOWN_ERROR = 255;

    protected $post_params = array();
    protected $get_params = array();

    function validate_params($params, $required_params)
    {
        foreach($required_params as $param => $type)
        {
            if(!isset($params[$param]))
            {
                return array('err_code' => self::INVALID_PARAM, 'param_name' => $param);
            }
        }
        return self::SUCCESS;
    }

    function validate_get_params($params)
    {
        return $this->validate_params($params, $this->get_params);
    }

    function validate_post_params($params)
    {
        return $this->validate_params($params, $this->post_params);
    }

    function get($params)
    {
        return self::UNRECOGNIZED_METHOD;
    }

    function post($params)
    {
        return self::UNRECOGNIZED_METHOD;
    }

    function is_logged_in()
    {
        return FlipSession::is_logged_in(); 
    }

    function run()
    {
        $ret = FALSE;
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'GET')
        {
            $ret = $this->validate_get_params($_GET);
            if($ret == self::SUCCESS)
            {
                $ret = $this->get($_GET);
            }
        }
        else if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $ret = $this->validate_post_params($_POST);
            if($ret == self::SUCCESS)
            {
                $ret = $this->post($_POST);
            }
        }
        else
        {
            $ret = self::UNRECOGNIZED_METHOD;
        }
        echo $this->encode_response($ret);
    }

    function error_message($err_code, $data)
    {
        switch($err_code)
        {
            case self::SUCCESS:
                return FALSE;
            case self::UNRECOGNIZED_METHOD:
                return "Unrecognized Operation ".$_SERVER['REQUEST_METHOD'];
            case self::INVALID_PARAM:
                if(isset($data['param_name']))
                {
                    return "Invalid Parameter! Expected parameter ".$data['param_name']." to be set";
                }
                else
                {
                    return "Invalid Parameter! Expected parameter to be set";
                }
            case self::ALREADY_LOGGED_IN:
                return "Already Logged In!";
            case self::INVALID_LOGIN:
                return "Invalid Username or Password!";
            case self::UNKNOWN_ERROR:
                return "Unknown error code ".$err_code;
        }
    }

    function encode_response($resp)
    {
        $data  = array();
        $err_code = FALSE;
        if(is_array($resp))
        {
            if(isset($resp['err_code']))
            {
                $err_code = $resp['err_code'];
                unset($resp['err_code']);
            }
            else
            {
                $err_code = self::SUCCESS;
            }
            $data = $resp;
        }
        else
        {
            $err_code = $resp;
        }
        $error = $this->error_message($err_code, $data);
        if($err_code != self::SUCCESS)
        {
            $data['error'] = $error;
        }
        else
        {
            $data['success'] = self::SUCCESS;
        }
        return json_encode($data);
    }
}

class FlipJaxSecure extends FlipJax
{
    function run()
    {
        if($_SERVER["HTTPS"] != "on")
        {
            header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
            exit();
        }
        return parent::run();
    }
}
?>
