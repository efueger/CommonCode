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
    protected $attachments;

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
        $this->attachments = array();
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

    public function addAttachmentFromBuffer($name, $buffer, $mimeType = 'application/octet-stream')
    {
        array_push($this->attachments, array('name'=>$name, 'data'=>$buffer, 'mimeType'=>$mimeType));
    }

    public function addAttachmentFromFile($filename, $name = false)
    {
        if($name === false)
        {
            $name = basename($filename);
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filename);
        if($mimeType === false)
        {
            if(file_exists($filename) && is_file($filename) && is_readable($filename))
            {
                $this->addAttachmentFromBuffer($name, file_get_contents($filename));
            }
        }
        else
        {
            $this->addAttachmentFromBuffer($name, file_get_contents($filename), $mimeType);
        }
    }

    public function hasAttachments()
    {
        return empty($this->attachments) !== true;
    }

    public function getRawMessage()
    {
        $boundary = uniqid(rand(), true);
        $raw_message = 'To: '.$this->encodeRecipients($this->getToAddresses())."\n";
        $raw_message.= 'From: '.$this->encodeRecipients($this->getFromAddress())."\n";
        if(!empty($this->cc))
        {
            $raw_message.= 'CC: '. $this->encodeRecipients($this->getCCAddresses())."\n";
        }
        if(!empty($this->bcc))
        {
            $raw_message.= 'BCC: '. $this->encodeRecipients($this->getBCCAddresses())."\n";
        }
        $raw_message .= 'Subject: '.$this->getSubject()."\n";
        $raw_message .= 'MIME-Version: 1.0'."\n";
        $raw_message .= 'Content-type: Multipart/Mixed; boundary="'.$boundary.'"'."\n";
        $raw_message .= "\n--{$boundary}\n";
        $raw_message .= 'Content-type: Multipart/Alternative; boundary="alt-'.$boundary.'"'."\n";
        $text_body    = $this->getTextBody();
        if($text_body !== false && strlen($text_body) > 0)
        {
            $raw_message.= "\n--alt-{$boundary}\n";
            $raw_message.= "Content-Type: text/plain\n\n";
            $raw_message.= $text_body."\n";
        }
        $html_body    = $this->getHTMLBody();
        if($html_body !== false && strlen($html_body) > 0)
        {
            $charset = empty($this->messageHtmlCharset) ? '' : "; charset=\"{$this->messageHtmlCharset}\"";
            $raw_message .= "\n--alt-{$boundary}\n";
            $raw_message .= 'Content-Type: text/html; charset="UTF-8"'."\n\n";
            $raw_message .= $html_body."\n";
        }
        $raw_message.= "\n--alt-{$boundary}--\n";
        foreach($this->attachments as $attachment)
        {
            $raw_message.= "\n--{$boundary}\n";
            $raw_message.= 'Content-Type: '. $attachment['mimeType'].'; name="'.$attachment['name']."\"\n";
            $raw_message.= 'Content-Disposition: attachment'."\n";
            $raw_message.= 'Content-Transfer-Encoding: base64'."\n\n";
            $raw_message.= chunk_split(base64_encode($attachment['data']), 76, "\n")."\n";
        }
        $raw_message .= "\n--{$boundary}--\n";
        return $raw_message;
	}

    public function encodeRecipients($recipient)
    {
        if(is_array($recipient))
        {
            return join(', ', array_map(array($this, 'encodeRecipients'), $recipient));
        }
        if(preg_match("/(.*)<(.*)>/", $recipient, $regs))
        {
            $recipient = '=?UTF-8?B?'.base64_encode($regs[1]).'?= <'.$regs[2].'>';
        }
        return $recipient;
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
