<?php
namespace Email;
class Email extends \SerializableObject
{
    protected $sender;
    protected $to;
    protected $cc;
    protected $bcc;
    protected $replyTo;
    protected $subject;
    protected $htmlBody;
    protected $textBody;

    public function __construct()
    {
        $this->sender = false;
        $this->to = array();
        $this->cc = array();
        $this->bcc = array();
        $this->replyTo = false;
        $this->subject = false;
        $this->htmlBody = '';
        $this->textBody = '';
    }

    public function getFromAddress()
    {
        if($this->sender === false)
        {
            return 'Burning Flipside <webmaster@burningflipside.com>';
        }
        return $this->sender;
    }

    public function getToAddresses()
    {
        return $this->to;
    }

    public function getCCAddresses()
    {
        return $this->cc;
    }

    public function getBCCAddresses()
    {
        return $this->bcc;
    }

    public function getReplyTo()
    {
        if($this->replyTo === false)
        {
            return $this->getFromAddress();
        }
        return $this->replyTo;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getHTMLBody()
    {
        return $this->htmlBody;
    }

    public function getTextBody()
    {
        return $this->textBody;
    }

    public function setFromAddress($email, $name=false)
    {
        if($name !== false)
        {
            $this->sender = $name.' <'.$email.'>';
        }
        else
        {
            $this->sender = $email;
        }
    }

    public function addToAddress($email, $name=false)
    {
        $this->addAddress($this->to, $email, $name);
    }

    public function addCCAddress($email, $name=false)
    {
        $this->addAddress($this->cc, $email, $name);
    }

    public function addBCCAddress($email, $name=false)
    {
        $this->addAddress($this->bcc, $email, $name);
    }

    protected function addAddress(&$list, $email, $name=false)
    {
        $address = $email;
        if($name !== false)
        {
            $address = $name.' <'.$email.'>';
        }
        array_push($list, $address);
    }

    public function setReplyTo($email, $name=false)
    {
        if($name !== false)
        {
            $this->replyTo = $name.' <'.$email.'>';
        }
        else
        {
            $this->replyTo = $email;
        }
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function setHTMLBody($body)
    {
        $this->htmlBody = $body;
    }

    public function setTextBody($body)
    {
        $this->textBody = $body;
    }

    public function appendToHTMLBody($body)
    {
        $this->htmlBody.= $body;
    }

    public function appendToTextBody($body)
    {
        $this->textBody.= $body;
    }
}
?>
