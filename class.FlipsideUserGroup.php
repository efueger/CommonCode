<?php

require_once("ldap/core_schema.php");

class FlipsideUserGroup extends groupOfNames
{
    function getGroupName()
    {
        return $this->cn;
    }

    function getMembers($nested=TRUE)
    {
        $members = array();
        for($i = 0; $i < $this->member["count"]; $i++)
        {
            $class = $this->server->getObjectClassForDN($this->member[$i]);
            if($class != FALSE && in_array("groupOfNames", $class) != FALSE)
            {
                if($nested)
                {
                    $subGroup = $this->server->getGroupByDN($this->member[$i]);
                    if($subGroup != FALSE)
                    {
                        $subGroupMembers = $subGroup->getMembers();
                        if($subGroupMembers != FALSE)
                        {
                            $members = array_merge($members, $subGroupMembers);
                        }
                    }
                }
            }
            else
            {
                array_push($members, $this->member[$i]);
            }
        }
        return $members;
    }

    function getGroups($nested=TRUE)
    {
        $res = $this->server->getGroups("(member=".$this->dn.")");
        if($res == FALSE || !isset($res[0]))
        {
            $res = $this->server->getGroups("(uniqueMember=".$this->dn.")");
            if($res == FALSE || !isset($res[0]))
            {
                return FALSE;
            }
        }
        else
        {
            $res2 = $this->server->getGroups("(uniqueMember=".$this->dn.")");
            if($res2 != FALSE)
            {
                $res = array_merge($res, $res2);
            } 
        }
        if($nested)
        {
            /*See if this group is a member of other groups*/
            $parentGroups = $res[0]->getGroups();
            if($parentGroups != FALSE)
            {
                $res = array_merge($res, $parentGroups);
            }
        }
        return $res; 
    }
}
// vim: set tabstop=4 shiftwidth=4 expandtab:
?>
