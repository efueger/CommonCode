<?php
require_once('PHPMailer/PHPMailerAutoload.php');
require_once('/var/www/secure_settings/class.FlipsideSettings.php');
class FlipsideMail extends PHPMailer
{
    public function __construct()
    {
        parent::__construct();
        $this->isSMTP();
        $this->SMTPAuth = true;
        $this->Host = FlipsideSettings::$smtp['smtp_host']; 
        $this->Username = FlipsideSettings::$smtp['smtp_user'];
        $this->Password = FlipsideSettings::$smtp['smtp_pass'];
        $this->SMTPSecure = FlipsideSettings::$smtp['smtp_proto'];
        $this->Port = FlipsideSettings::$smtp['smtp_port'];
    }

    private static function add_to($item1, $key, $mail)
    {
        $mail->addAddress($item1);
    }

    public function send_HTML($mail)
    {
        if(isset($mail['from']))
        {
            $this->From = $mail['from'];
        }
        else
        {
            $this->From = $this->Username;
        }
        if(isset($mail['from_name']))
        {
            $this->FromName = $mail['from_name'];
        }
        else
        {
            $this->FromName = 'Burning Flipside';
        }
        $this->clearAllRecipients();
        if(is_array($mail['to']))
        {
            array_walk($mail['to'], 'FlipsideMail::addTo', $this);
        }
        else
        {
            $this->addAddress($mail['to']);
        }
        $this->isHTML(true);

        $this->Subject = $mail['subject'];
        $this->Body    = $mail['body'];
        $this->AltBody = $mail['alt_body'];

        return $this->send();
    }
}
?>
