<?php
namespace Auth\OAuth2;
require('/var/www/common/libs/httpful/bootstrap.php');

abstract class OAuth2Authenticator extends \Auth\Authenticator
{
    protected $redirect_uri;

    public function __construct($params)
    {
        parent::__construct($params);
        if(!isset($params['redirect_url']))
        {
            $this->redirect_uri = 'https://'.$_SERVER['HTTP_HOST'].'/oauth/callbacks/'.$this->getHostName();
        }
        else
        {
            $this->redirect_uri = $params['redirect_url'];
        }
    }

    public function get_supplement_link()
    {
        $auth_url = $this->getAuthorizationUrl();
        return '<a href="'.filter_var($auth_url, FILTER_SANITIZE_URL).'"><img src="'.$this->getSignInImg().'" style="width: 2em;"/></a>';
    }

    public function getSignInImg()
    {
        return '/img/common/'.$this->getHostName().'_sign_in.png';
    }

    abstract public function getAuthorizationUrl();
    abstract public function getAccessTokenUrl();
    abstract public function getUserFromToken($token);

    public function doAuthPost($params)
    {
        return \Httpful\Request::post($this->getAccessTokenUrl().'&code='.$params['code'])->send();
    }

    public function authenticate($params, &$current_user)
    {
        $resp = $this->doAuthPost($params);
        if($resp->hasErrors())
        {
            return self::LOGIN_FAILED; 
        }
        \FlipSession::set_var('OAuthToken', $resp->body);
        $user = $this->getUserFromToken($resp->body);
        if($user === false)
        {
            return self::LOGIN_FAILED;
        }
        $auth = \AuthProvider::getInstance();
        $local_users = $auth->get_users_by_filter(false, new \Data\Filter('mail eq '.$user->getEmail()));
        if($local_users !== false && isset($local_users[0]))
        {
            if($local_users[0]->canLoginWith($this->getHostName()))
            {
                $auth->impersonate_user($local_users[0]);
                return self::SUCCESS;
            }
            $current_user = $local_users[0];
            return self::ALREADY_PRESENT;
        }
        $ret = $auth->activate_pending_user(false, $user);
        if($ret === false)
        {
            throw new \Exception('Unable to create user! '.$res);
        }
        return self::SUCCESS;
    }
}
?>
