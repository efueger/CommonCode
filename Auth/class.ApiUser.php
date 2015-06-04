<?php
namespace Auth;

class ApiUser extends User
{
    private $uid_for_url;
    private $json;
    private $access_token;
    private $root;

    function __construct($access_token, $uri_root, $uid='me')
    {
        $this->root = $uri_root;
        $this->access_token = $access_token;
        $this->uid_for_url = $uid;

        try
        {
            $resp = $this->send_request("/users/$uid");
            if($resp->getResponseCode() !== 200)
            {
                return null;
            }
            $this->json = json_decode($resp->getBody()->toString());
        }
        catch(Exception $ex)
        {
            return null;
        }
    }

    private function get_request($uri, $method='GET')
    {
        $req = new \http\Client\Request($method,
                                        $this->root.$uri);
        $req->addHeader('Authorization', 'Bearer '.$this->access_token);
        return $req;
    }

    private function send_request($uri, $method='GET')
    {
        $client = new \http\Client();
        $req = $this->get_request($uri, $method);
        $client->enqueue($req);
        try
        {
            $client->send();
        }
        catch(\http\Exception\RuntimeException $ex) {}
        return $client->getResponse();
    }

    function isInGroupNamed($name)
    {
        $resp = $this->send_request('/users/'.$this->uid_for_url.'/groups');
        if(@$resp->getResponseCode() === false)
        {
            \FlipSession::end();
            return false;
        }
        if($resp->getResponseCode() !== 200)
        {
            if($resp->getResponseCode() === 303)
            {
                \FlipSession::end();
            }
            return false;
        }
        $obj = json_decode($resp->getBody()->toString());
        $count = count($obj);
        for($i = 0; $i < $count; $i++)
        {
            if($obj[$i]->cn === $name)
            {
                return true;
            }
        }
        return false;
    }

    function getEmail()
    {
        if($this->json === false)
        {
            return false;
        }
        if(isset($this->json->mail))
        {
            return $this->json->mail;
        }
        return false;
    }

    function getUid()
    {
        if($this->uid_for_url !== 'me')
        {
            return $this->uid_for_url;
        }
        if($this->json === false)
        {
            return false;
        }
        if(isset($this->json->uid))
        {
            return $this->json->uid;
        }
        return false;
    }
}

?>
