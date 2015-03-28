<?php
namespace LDAP;

class LDAPServer extends \Singleton
{
    protected $ds;
    protected $connect;

    protected function __construct()
    {
        $this->ds = null;
    }

    public function __destruct()
    {
    }

    public function __wakeup()
    {
        $this->ds = ldap_connect($this->connect);
    }

    private function _get_connect_string($name, $proto='ldap')
    {
        if($proto !== 'ldap')
        {
            return $proto.'://'.$name;
        }
        return $name;
    }

    function connect($name, $proto='ldap')
    {
        $connect_str = $this->_get_connect_string($name, $proto);
        if($this->ds === null)
        {
            $this->connect = $connect_str;
            $this->ds      = ldap_connect($this->connect);
        }
        else if($connect_str !== $this->connect)
        {
            ldap_close($this->ds);
            $this->connect = $connect_str;
            $this->ds      = ldap_connect($this->connect);
        }
        if($this->ds === false)
        {
            $this->ds = null;
            return false;
        }
        return true;
    }

    function disconnect()
    {
        if($this->ds !== null)
        {
            ldap_close($this->ds);
            $this->ds = null;
        }
        $this->connect = false;
    }

    function bind($cn=null,$password=null)
    {
        $res = false;
        if($this->ds === null)
        {
            throw new \Exception('Not connected');
        }
        if($cn === null || $password === null)
        {
            $res = ldap_bind($this->ds);
        }
        else
        {
            try
            {
                $res = ldap_bind($this->ds, $cn, $password);
            }
            catch(\Exception $ex)
            {
                $this->ds = ldap_connect($this->connect);
                $res = @ldap_bind($this->ds, $cn, $password);
            }
        }
        return $res;
    }

    function unbind()
    {
        if($this->ds === null)
        {
            return true;
        }
        return @ldap_unbind($this->ds);
    }

    private function _fix_object($object)
    {
        $entity = $object->to_array();
        $entity = array_filter($entity);
        unset($entity['dn']);
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
        return $entity;
    }

    function create($object)
    {
        $dn = ldap_escape($object['dn'], true, LDAP_ESCAPE_DN);
        $entity = $this->_fix_object($object);
        $ret = ldap_add($this->ds, $dn, $entity);
        if($ret === false)
        {
            throw new \Exception('Failed to create object with dn='.$dn);
        }
        return $ret;
    }

    function read($base_dn, $filter=false)
    {
        $filter_str = '(objectclass=*)';
        if($filter !== false)
        {
            $filter_str = $filter->to_ldap_string();
        }
        if($this->ds === null)
        {
            throw new \Exception('Not connected');
        }
        try
        {
            $sr = ldap_search($this->ds, $base_dn, $filter_str);
        }
        catch(\Exception $e)
        {
            throw new \Exception($e->getMessage().' '.$filter_str, $e->getCode(), $e);
        }
        if($sr === false)
        {
            return false;
        }
        $res = ldap_get_entries($this->ds, $sr);
        if(is_array($res))
        {
            $ldap = $res;
            $res = array();
            for($i = 0; $i < $ldap['count']; $i++)
            {
                array_push($res, new LDAPObject($ldap[$i], $this));
            }
        }
        return $res;
    }

    function update($object)
    {
        $dn = ldap_escape($object['dn'], true, LDAP_ESCAPE_DN);
        $entity = $this->_fix_object($object);
        $ret = ldap_mod_replace($this->ds, $dn, $entity);
        if($ret === false)
        {
            throw new \Exception('Failed to update object with dn='.$dn);
        }
        return $ret;
    }

    function delete($dn)
    {
        return ldap_delete($this->ds, $dn);
    }
}

?>
