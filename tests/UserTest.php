<?php
require_once('Autoload.php');
class UserTest extends PHPUnit_Framework_TestCase
{
    public function testUser()
    {
        $user = new \Auth\User();
        $this->assertFalse($user->isInGroupNamed('AAR'));
        $this->assertFalse($user->getDisplayName());
        $this->assertFalse($user->getGivenName());
        $this->assertFalse($user->getEmail());
        $this->assertFalse($user->getUid());
        $this->assertFalse($user->getPhoto());
        $this->assertFalse($user->getPhoneNumber());
        $this->assertFalse($user->getOrganization());
        $this->assertFalse($user->getTitles());
        $this->assertFalse($user->getState());
        $this->assertFalse($user->getCity());
        $this->assertFalse($user->getLastName());
        $this->assertFalse($user->getNickName());
        $this->assertFalse($user->getAddress());
        $this->assertFalse($user->getPostalCode());
        $this->assertFalse($user->getCountry());
        $this->assertFalse($user->getOrganizationUnits());
        $this->assertFalse($user->getLoginProviders());
        $this->assertFalse($user->getGroups());
        try{
            $user->addLoginProvider('google.com');
            $this->assertFalse(true);
        }
        catch(\Exception $e)
        {
            $this->assertFalse(false);
        }
        $this->assertFalse($user->canLoginWith('google.com'));
        $this->assertFalse($user->isProfileComplete());
        $this->assertFalse($user->validate_password('test'));
        $this->assertFalse($user->validate_reset_hash('test'));
        $this->assertFalse($user->setDisplayName('test'));
        $this->assertFalse($user->setGivenName('test'));
        $this->assertFalse($user->setEmail('test@test.com'));
        $this->assertFalse($user->setUid('test'));
        $this->assertFalse($user->setPhoto('test'));
        $this->assertFalse($user->setPhoneNumber('test'));
        $this->assertFalse($user->setOrganization('test'));
        $this->assertFalse($user->setTitles('test'));
        $this->assertFalse($user->setTitles(array('test', 'test2')));
        $this->assertFalse($user->setState('TX'));
        $this->assertFalse($user->setCity('test'));
        $this->assertFalse($user->setLastName('test'));
        $this->assertFalse($user->setNickName('test'));
        $this->assertFalse($user->setAddress('test'));
        $this->assertFalse($user->setPostalCode('test'));
        $this->assertFalse($user->setCountry('test'));
        $this->assertFalse($user->setOrganizationUnits('test'));
        $this->assertFalse($user->getPasswordResetHash());
    }

    public function testLDAPUser()
    {
        $user = new \Auth\LDAPUser();
        try{
            $this->assertFalse($user->isInGroupNamed('AAR'));
        } catch(\Exception $e)
        {
            $this->assertFalse(false);
        }
        $this->assertFalse($user->getDisplayName());
        $this->assertFalse($user->getGivenName());
        $this->assertFalse($user->getEmail());
        $this->assertFalse($user->getUid());
        $this->assertFalse($user->getPhoto());
        $this->assertFalse($user->getPhoneNumber());
        $this->assertEquals('Volunteer', $user->getOrganization());
        $this->assertFalse($user->getTitles());
        $this->assertFalse($user->getState());
        $this->assertFalse($user->getCity());
        $this->assertFalse($user->getLastName());
        $this->assertFalse($user->getNickName());
        $this->assertFalse($user->getAddress());
        $this->assertFalse($user->getPostalCode());
        $this->assertFalse($user->getCountry());
        $this->assertFalse($user->getOrganizationUnits());
        $this->assertFalse($user->getLoginProviders());
        try{
            $this->assertFalse($user->getGroups());
        } catch(\Exception $e)
        {
            $this->assertFalse(false);
        }
    }

    public function testSQLUser()
    {
        $user = new \Auth\SQLUser();
        try{
            $this->assertFalse($user->isInGroupNamed('AAR'));
        } catch(\Exception $e)
        {
            $this->assertFalse(false);
        }
        $this->assertFalse($user->getDisplayName());
        $this->assertFalse($user->getGivenName());
        $this->assertFalse($user->getEmail());
        $this->assertFalse($user->getUid());
        $this->assertFalse($user->getPhoto());
        $this->assertFalse($user->getPhoneNumber());
        $this->assertFalse($user->getOrganization());
        $this->assertFalse($user->getTitles());
        $this->assertFalse($user->getState());
        $this->assertFalse($user->getCity());
        $this->assertFalse($user->getLastName());
        $this->assertFalse($user->getNickName());
        $this->assertFalse($user->getAddress());
        $this->assertFalse($user->getPostalCode());
        $this->assertFalse($user->getCountry());
        $this->assertFalse($user->getOrganizationUnits());
        $this->assertFalse($user->getLoginProviders());
        $this->assertFalse($user->getGroups());
    }

    public function testFlipsideAPIUser()
    {
        $user = new \Auth\FlipsideAPIUser();
        try{
            $this->assertFalse($user->isInGroupNamed('AAR'));
        } catch(\Exception $e)
        {
            $this->assertFalse(false);
        }
        $this->assertFalse($user->getDisplayName());
        $this->assertFalse($user->getGivenName());
        $this->assertFalse($user->getEmail());
        $this->assertFalse($user->getUid());
        $this->assertFalse($user->getPhoto());
        $this->assertFalse($user->getPhoneNumber());
        $this->assertFalse($user->getOrganization());
        $this->assertFalse($user->getTitles());
        $this->assertFalse($user->getState());
        $this->assertFalse($user->getCity());
        $this->assertFalse($user->getLastName());
        $this->assertFalse($user->getNickName());
        $this->assertFalse($user->getAddress());
        $this->assertFalse($user->getPostalCode());
        $this->assertFalse($user->getCountry());
        $this->assertFalse($user->getOrganizationUnits());
        $this->assertFalse($user->getLoginProviders());
        $this->assertFalse($user->getGroups());
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
