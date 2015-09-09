<?php
namespace Auth;
require_once('/var/www/common/libs/google/src/Google/autoload.php');

class GoogleAuthenticator extends Authenticator
{
    protected $client;
    protected $token = null;

    public function __construct($params)
    {
        parent::__construct($params);
        if(!isset($params['client_secrets_path']))
        {
            throw new \Exception('Missing required parameter client_secrets_path!');
        }
        if(!isset($params['redirect_url']))
        {
            $params['redirect_url'] = 'https://'.$_SERVER['HTTP_HOST'].'/oauth2callback.php?src=google';
        }
        $this->token = \FlipSession::getVar('GoogleToken', null);
        $this->client = new \Google_Client();
        $this->client->setAuthConfigFile($params['client_secrets_path']);
        $this->client->addScope(array(\Google_Service_Oauth2::USERINFO_PROFILE, \Google_Service_Oauth2::USERINFO_EMAIL));
        $this->client->setRedirectUri($params['redirect_url']);
    }

    public function get_supplement_link()
    {
        $auth_url = $this->client->createAuthUrl();
        return '<a href="'.filter_var($auth_url, FILTER_SANITIZE_URL).'"><img src="/img/common/google_sign_in.png" style="width: 2em;"/></a>';
    }

    public function authenticate($code, &$current_user = false)
    {
        $google_user = false;
        try{
            $this->client->authenticate($code);
            $this->token = $this->client->getAccessToken();
            \FlipSession::setVar('GoogleToken', $this->token);
            $oauth2_service = new \Google_Service_Oauth2($this->client);
            $google_user = $oauth2_service->userinfo->get();
        } catch(\Exception $ex) {
            return self::LOGIN_FAILED;
        }

        $auth = \AuthProvider::getInstance();
        $local_users = $auth->get_users_by_filter(false, new \Data\Filter('mail eq '.$google_user->email));
        if($local_users !== false && isset($local_users[0]))
        {
            if($local_users[0]->canLoginWith('google.com'))
            {
                $auth->impersonate_user($local_users[0]);
                return self::SUCCESS;
            }
            $current_user = $local_users[0];
            return self::ALREADY_PRESENT;
        }
        else
        {
            $user = new PendingUser();
            $user->setEmail($google_user->email);
            $user->setGivenName($google_user->givenName);
            $user->setLastName($google_user->familyName);
            $user->addLoginProvider('google.com');
            $ret = $auth->activate_pending_user(false, $user);
            if($ret === false)
            {
                 throw new \Exception('Unable to create user! '.$res);
            }
            return self::SUCCESS;
        }
    }

    public function get_user($data = false)
    {
        if($data === false)
        {
            $data = $this->token;
        }
        try {
            $this->client->setAccessToken($data);
            $oauth2_service = new \Google_Service_Oauth2($this->client);
            $google_user = $oauth2_service->userinfo->get();
            $profile_user = array();
            $profile_user['mail'] = $google_user->email;
            $profile_user['sn'] = $google_user->familyName;
            $profile_user['givenName'] = $google_user->givenName;
            $profile_user['displayName'] = $google_user->name;
            $profile_user['jpegPhoto'] = base64_encode(file_get_contents($google_user->picture));
            return $profile_user;
        } catch(\Exception $e)
        {
            return false;
        }
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
