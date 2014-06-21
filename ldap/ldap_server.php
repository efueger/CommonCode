<?php

class ldap_server
{
    protected $ds = null;
    private $name;
    private $proto;
    private $connect_cn;
    private $connect_pass;

    function __construct($server_name, $proto = 'ldap')
    {
        //print("ldap_server::__construct entered server_name=".$server_name."\n");
        if($proto != 'ldap')
        {
            $this->ds = ldap_connect($proto.'://'.$server_name);
        }
        else
        {
            $this->ds = ldap_connect($server_name);
        }
        $this->name = $server_name;
        $this->proto = $proto;
        //print("$ds = ".$this->ds."\n");
    }

    function __destruct()
    {
        //print("ldap_server::__destruct entered\n");
        if($this->ds != null) ldap_close($this->ds);
        //print("ldap_server::__destruct finished\n");
    }

    function reconnect()
    {
        $this->ds = ldap_connect($server_name);
        if($this->proto != 'ldap')
        {
            $this->ds = ldap_connect($this->proto.'://'.$this->name);
        }
        else
        {
            $this->ds = ldap_connect($this->name);
        }
    }

    function bind($cn=null,$password=null)
    {
        //print("ldap_server::bind entered\n");
        $res = FALSE;
        if($cn == null)
        {
            /*Anonymous bind*/
            //print("ldap_server::bind performing anonymous bind\n");
            $res = ldap_bind($this->ds);
            $this->connect_cn = null;
            $this->connect_pass = null;
        }
        else
        {
            //print("ldap_server::bind performing bind\n");
            $res = ldap_bind($this->ds, $cn, $password);
            $this->connect_cn = $cn;
            $this->connect_pass = $password;
        }
        //print("ldap_server::bind return ".$res."\n");
        //if($res == FALSE)
        //{
            //print("ldap_server::bind error ".ldap_error($this->ds)."\n");
        //}
        return $res;
    }

    function search($base_dn, $filter)
    {
        //print("ldap_server::seach entered\n");
        if($this->ds == null)
        {
            $this->reconnect();
            $this->bind($this->connect_cn, $this->connect_pass);
        }
        $sr = ldap_search($this->ds, $base_dn, $filter);
        if($sr == FALSE)
        {
            //print("ldap_server::seach ldap_search failed\n");
            return FALSE;
        }
        return ldap_get_entries($this->ds, $sr);
    }

    function getObjectByDN($dn)
    {
        $sr = ldap_read($this->ds, $dn, "(objectclass=*)");
        if($sr == FALSE)
        {
            return FALSE;
        }
        return ldap_get_entries($this->ds, $sr);
    }

    function getObjectClassForDN($dn)
    {
        $data = $this->getObjectByDN($dn);
        if($data && isset($data[0]) && isset($data[0]["objectclass"]))
        {
            $res = array();
            for($i = 0; $i < $data[0]["objectclass"]["count"]; $i++)
            {
                array_push($res, $data[0]["objectclass"][$i]);
            }
            return $res;
        }
        else
        {
            return FALSE;
        }
    }

    function testLogin($dn,$pass)
    {
        $temp_ds = FALSE;
        if($this->proto != 'ldap')
        {
            $temp_ds = ldap_connect($this->proto.'://'.$this->name);
        }
        else
        {
            $temp_ds = ldap_connect($this->name);
        }
        if($temp_ds == FALSE)
        {
            return FALSE;
        }
        $res = ldap_bind($temp_ds, $dn, $pass);
        return $res;
    }

    function replaceAttribute($dn, $attribs)
    {
        return ldap_mod_replace($this->ds, $dn, $attribs);
    }

    function writeObject($object)
    {
        $dn = $object->dn;
        $entity = get_object_vars($object);
        $entity = array_filter($entity);
        $keys = array_keys($entity);
        for($i = 0; $i < count($keys); $i++)
        {
            if(is_array($entity[$keys[$i]]))
            {
                $array = $entity[$keys[$i]];
                unset($entity[$keys[$i]]);
                for($j = 0; $j < count($array); $j++)
                {
                    $entity[$keys[$i]][$j] = $array[$j];
                }
            }
        }
        unset($entity['dn']);

        $ret = ldap_add($this->ds, $dn, $entity);
        return $ret;
    }
}
// vim: set tabstop=4 shiftwidth=4 expandtab:
?>
