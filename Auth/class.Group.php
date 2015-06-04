<?php
namespace Auth;

class Group extends \SerializableObject
{
    public function getGroupName()
    {
        return false;
    }

    public function getDescription()
    {
        return false;
    }

    public function getMemberUids()
    {
        return array();
    }

    public function members($details=false)
    {
        return array();
    }

    public function member_count()
    {
        return count($this->members());
    }

    public function jsonSerialize()
    {
        $group = array();
        try{
        $group['cn'] = $this->getGroupName();
        $group['description'] = $this->getDescription();
        $group['member'] = $this->getMemberUids();
        } catch(\Exception $e) {echo $e->getMessage(); die();}
        return $group;
    }

    static function from_name($name, $data=false)
    {
        return false;
    }
}
?>
