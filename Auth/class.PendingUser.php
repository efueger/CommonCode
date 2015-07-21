<?php
namespace Auth;

class PendingUser extends User
{
    public function getHash()
    {
        return false;
    }

    public function getRegistrationTime()
    {
        return false;
    }

    public function isInGroupNamed($name)
    {
        return false;
    }

    public function getEmail()
    {
        if(isset($this->email))
        {
            return $this->email;
        }
        return parent::getEmail();
    }

    public function getGivenName()
    {
        if(isset($this->givenName))
        {
            return $this->givenName;
        }
        return parent::getGivenName();
    }

    public function getLastName()
    {
        if(isset($this->sn))
        {
            return $this->sn;
        }
        return parent::getLastName();
    }

    //I need to be able to get the unhashed password so that I can let the current backend hash it
    public function getPassword()
    {
        return false;
    }

    function getLoginProviders()
    {
        if(isset($this->host))
        {
            return $this->host;
        }
        return parent::getLoginProviders();
    }

    function addLoginProvider($provider)
    {
        if(isset($this->host))
        {
            array_push($this->host, $provider);
        }
        else
        {
            $this->host = array($provider);
        }
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;
    }

    public function setLastName($sn)
    {
        $this->sn = $sn;
    }

    public function jsonSerialize()
    {
        $user = array();
        $user['hash'] = $this->getHash();
        $user['mail'] = $this->getEmail();
        $user['uid'] = $this->getUid();
        $user['time'] = $this->getRegistrationTime()->format(\DateTime::RFC822);
        $user['class'] = get_class($this);
        return $user; 
    }

    public function sendEmail()
    {
        $mail = new FlipsideMail();
        //TODO read the mail text from a database like the ticket system
        $mail_data = array(
                'to'       => $_POST['email'],
                'subject'  => 'Burning Flipside Registration',
                'body'     => 'Thank you for signing up with Burning Flipside. Your registration is not complete until you follow the link below.<br/>
                <a href="https://profiles.burningflipside.com/finish.php?hash='.$hash.'">Complete Registration</a><br/>
                Thank you,<br/>
                Burning Flipside Technology Team',
                'alt_body' => 'Thank you for signing up with Burning Flipside. Your registration is not complete until you goto the address below.
                https://profiles.burningflipside.com/finish.php?hash='.$hash.'
                Thank you,
                Burning Flipside Technology Team'
                );
        return $mail->send_HTML($mail_data);
    }

    public function delete()
    {
    }
}

?>
