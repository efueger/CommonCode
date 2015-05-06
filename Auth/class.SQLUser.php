<?php
namespace Auth;

class SQLUser extends User
{
    private $uid;

    function __construct($data)
    {
        $this->uid = $data['extended'];
    }

    function isInGroupNamed($name)
    {
        $auth_data_set = \DataSetFactory::get_data_set('authentication');
        $group_data_table = $auth_data_set['group'];
        $filter = new \Data\Filter("uid eq '$this->uid' and gid eq '$name'");
        $groups = $group_data_table->read($filter);
        if($groups === false || !isset($groups[0]))
        {
            return false;
        }
        return true;

    }

    function getEmail()
    {
        return $this->uid;
    }

    function getUid()
    {
        return $this->uid;
    }
}

?>
