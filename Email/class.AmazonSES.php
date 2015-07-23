<?php
namespace Email;

require('/var/www/common/libs/aws/aws-autoloader.php');
class AmazonSES
{
    protected $ses;

    public function __construct($params)
    {
        $credentials = \Aws\Common\Credentials\Credentials::fromIni('default', $params['ini']);

        $this->ses = \Aws\Ses\SesClient::factory([
            'version' => 'latest',
            'region'  => 'us-west-2',
            'credentials' => $credentials
         ]);
    }

    public function canSend()
    {
        $result = $this->ses->getSendQuota();
        $result = $result->getAll();
        $res = $result['Max24HourSend'] - $result['SentLast24Hours'];
        return $res;
    }

    public function sendEmail($email)
    {
        $args = array();
	$args['Source'] = $email->getFromAddress();
	$args['Destination'] = array();
        $args['Destination']['ToAddresses'] = $email->getToAddresses();
        $args['Destination']['CcAddresses'] = $email->getCCAddresses();
        $args['Destination']['BccAddresses'] = $email->getBCCAddresses();
        $args['Message'] = array();
        $args['Message']['Subject'] = array();
        $args['Message']['Subject']['Data'] = $email->getSubject();
        $args['Message']['Body'] = array();
        $args['Message']['Body']['Text'] = array();
        $args['Message']['Body']['Html'] = array();
        $args['Message']['Body']['Text']['Data'] = $email->getTextBody();
        $args['Message']['Body']['Html']['Data'] = $email->getHtmlBody();
        $args['ReplyToAddresses'] = array($email->getReplyTo());
        return $this->ses->sendEmail($args);
    }
}
?>
