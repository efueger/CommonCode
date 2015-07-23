<?php
namespace LDAP;

/**
 * function ldap_escape
 * @author Chris Wright
 * @version 2.0
 * @param string $subject The subject string
 * @param bool $dn Treat subject as a DN if TRUE
 * @param string|array $ignore Set of characters to leave untouched
 * @return string The escaped string
 */
function ldap_escape($subject, $dn = FALSE, $ignore = NULL)
{
    // The base array of characters to escape
    // Flip to keys for easy use of unset()
    $search = array_flip($dn ? array('\\', '+', '<', '>', ';', '"', '#') : array('\\', '*', '(', ')', "\x00"));

    // Process characters to ignore
    if(is_array($ignore))
    {
        $ignore = array_values($ignore);
    }
    for($char = 0; isset($ignore[$char]); $char++)
    {
        unset($search[$ignore[$char]]);
    }

    // Flip $search back to values and build $replace array
    $search = array_keys($search); 
    $replace = array();
    foreach($search as $char)
    {
        $replace[] = sprintf('\\%02x', ord($char));
    }

    // Do the main replacement
    $result = str_replace($search, $replace, $subject);

    // Encode leading/trailing spaces in DN values
    if($dn)
    {
        if($result[0] == ' ')
        {
            $result = '\\20'.substr($result, 1);
        }
        if($result[strlen($result) - 1] == ' ')
        {
            $result = substr($result, 0, -1).'\\20';
        }
    }

    return $result;
}

class LDAPServer extends \Singleton
{
    protected $ds;
    protected $connect;
    public $user_base;
    public $group_base;

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

    private function _get_connect_string($name, $proto=false)
    {
        if(strstr($name, ':') !== false)
        {
            return $name;
        }
        if($proto !== 'ldap')
        {
            return $proto.'://'.$name;
        }
        return $name;
    }

    function connect($name, $proto=false)
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
        ldap_set_option($this->ds, LDAP_OPT_PROTOCOL_VERSION, 3);
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
            $res = @ldap_bind($this->ds);
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

    function get_error()
    {
        return ldap_error($this->ds);
    }

    private function _fix_object($object, &$delete = false)
    {
        $entity = $object;
        if(!is_array($object))
        {
            $entity = $object->to_array();
        }
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
            else if($delete !== false && $entity[$keys[$i]] === null)
            {
                $delete[$keys[$i]] = array();
                unset($entity[$keys[$i]]);
            }
        }
        return $entity;
    }

    function create($object)
    {
        $dn = ldap_escape($object['dn'], true);
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
            $sr = @ldap_list($this->ds, $base_dn, $filter_str);
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

    function count($base_dn, $filter=false)
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
            $sr = ldap_list($this->ds, $base_dn, $filter_str, array('dn'));
        }
        catch(\Exception $e)
        {
            throw new \Exception($e->getMessage().' '.$filter_str, $e->getCode(), $e);
        }
        if($sr === false)
        {
            return false;
        }
        return ldap_count_entries($this->ds, $sr);
    }

    function update($object)
    {
        $dn = ldap_escape($object['dn'], true);
        $delete = array();
        $entity = $this->_fix_object($object, $delete);
        $ret = ldap_mod_replace($this->ds, $dn, $entity);
        if($ret === false)
        {
            throw new \Exception('Failed to update object with dn='.$dn);
        }
        if(!empty($delete))
        {
            $ret = ldap_mod_del($this->ds, $dn, $delete);
        }
        return $ret;
    }

    function delete($dn)
    {
        return ldap_delete($this->ds, $dn);
    }
}

?>
