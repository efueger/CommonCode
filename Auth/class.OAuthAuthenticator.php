<?php
namespace Auth;

class OAuthAuthenticator extends Authenticator
{
    public function login($username, $password)
    {
        $client = new \http\Client();
        $req = new \http\Client\Request('POST', 
                                        'https://profiles.burningflipside.com/OAUTH2/token.php',
                                        ['Content-Type' => 'application/x-www-form-urlencoded']);
        $req->getBody()->append(new \http\QueryString(array(
            'grant_type'=>'password',
            'username'=>$username,
            'password'=>$password,
            'client_id'=>'flipside_oauth2')));
        $client->enqueue($req);
        try
        {
            $client->send();
            $resp = $client->getResponse();
            if($resp->getResponseCode() !== 200)
            {
                return false;
            }
            $obj = json_decode($resp->getBody()->toString());
            return array('res'=>true, 'extended'=>$obj); 
        }
        catch(Exception $ex)
        {
            return false;
        }
    }

    public function isLoggedIn($data)
    {
        if(isset($data['extended']->access_token))
        {
            return true;
        }
        return false;
    }

    public function getUser($data)
    {
        $access_token = $data['extended']->access_token;
        return new ApiUser($access_token, 'https://profiles.burningflipside.com/api/v1');
    }

    public function getGroupByName($name)
    {
        $access_token = $data['extended']->access_token;
        return new APIGroup($name, $access_token, 'https://profiles.burningflipside.com/api/v1');
    }

    public function getUserByName($name)
    {
        $access_token = $data['extended']->access_token;
        return new APIUser($access_token, 'https://profiles.burningflipside.com/api/v1', $name);
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
