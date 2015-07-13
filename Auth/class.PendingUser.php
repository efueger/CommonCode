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

    //I need to be able to get the unhashed password so that I can let the current backend hash it
    public function getPassword()
    {
        return false;
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
}

?>
