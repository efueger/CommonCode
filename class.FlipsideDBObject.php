<?php
class FlipsideDBObject
{
    protected $_tbl_name;

    static function get_table_name()
    {
        $type = new static();
        if(isset($type->_tbl_name))
        {
            return $type->_tbl_name;
        }
        else
        {
            return get_class($type);
        }
    }

    function insert_to_db($db)
    {
        $values = get_object_vars($this);
        if(isset($values['_tbl_name']))
        {
            unset($values['_tbl_name']);
        }
        $table = self::get_table_name();
        return $db->insert_array($table, $values);
    }

    function set_object_vars($vars)
    {
        $values = get_object_vars($this);
        foreach($values as $name => $old)
        {
            $this->$name = isset($vars[$name]) ? $vars[$name] : $old;
        }
    }

    static function get_all_of_type($db)
    {
        $array = $db->select(self::get_table_name());
        $res = array();
        for($i = 0; $i < count($array); $i++)
        {
            $type = new static();
            $type->set_object_vars($array[$i]);
            array_push($res, $type);
        }
        return $res;
    }

    static function select_from_db($db, $col, $value)
    {
        $table = self::get_table_name();
        $array = $db->select($table, '*', array($col=>'=\''.$value.'\''));
        if($array == FALSE || !isset($array[0]))
        {
            return FALSE;
        }
        $type = new static();
        $type->set_object_vars($array[0]);
        return $type;
    }
}
?>
