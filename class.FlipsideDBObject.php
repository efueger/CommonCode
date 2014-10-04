<?php
class FlipsideDBObject
{
    protected $_tbl_name;
    protected $_sql_special;
    protected $_sql_ai_key;
    protected $_sql_ignore;

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

    protected function to_value_array($drop_sql_ignore = TRUE, &$arrays, &$ai_key_name = NULL)
    {
        $values = get_object_vars($this);
        foreach($values as $key => $value)
        {
            if(strncmp($key, '_', 1) == 0)
            {
                unset($values[$key]);
            }
            if(is_array($this->_sql_special))
            {
                if(isset($this->_sql_special[$key]))
                {
                    $values[$key] = call_user_func($this->_sql_special[$key], $value);
                }
            }
            if(is_array($this->_sql_ignore) && $drop_sql_ignore == TRUE) 
            {
                if(in_array($key, $this->_sql_ignore))
                {
                    unset($values[$key]);
                }
            }
            if(is_array($this->_sql_ai_key))
            {
                if(in_array($key, $this->_sql_ai_key))
                {
                    if($value == null)
                    {
                        //If the key isn't set I can only do an INSERT...
                        $op = 'insert';
                        if($ai_key_name != NULL) $ai_key_name = $key;
                        unset($values[$key]);
                    }
                }
            }
            if(strncmp($key, '_', 1) != 0 && is_array($value))
            {
                $arrays[$key] = $values[$key];
                unset($values[$key]);
            }
        }
        return $values;
    }

    protected function set_in_db($db, $op)
    {
        $values = get_object_vars($this);
        $arrays = array();
        $ai_key_name = null;
        foreach($values as $key => $value)
        {
            if(strncmp($key, '_', 1) == 0)
            {
                unset($values[$key]);
            }
            if(is_array($this->_sql_special))
            {
                if(isset($this->_sql_special[$key]))
                {
                    $values[$key] = call_user_func($this->_sql_special[$key], $value);
                }
            }
            if(is_array($this->_sql_ignore))
            {
                if(in_array($key, $this->_sql_ignore))
                {
                    unset($values[$key]);
                }
            }
            if(is_array($this->_sql_ai_key))
            {
                if(in_array($key, $this->_sql_ai_key))
                {
                    if($value == null)
                    {
                        //If the key isn't set I can only do an INSERT...
                        $op = 'insert';
                        $ai_key_name = $key;
                        unset($values[$key]);
                    }
                }
            }
            if(is_array($value))
            {
                $arrays[$key] = $values[$key];
                unset($values[$key]);
            }
        }
        $table = self::get_table_name();
        if($op == 'insert')
        {
            $ret = $db->insert_array($table, $values);
        }
        else
        {
            $ret = $db->replace_array($table, $values);
        }
        if($ret === FALSE)
        {
            return $ret;
        }
        if($ai_key_name != null)
        {
            $this->$ai_key_name = $ret;
        }
        foreach($arrays as $key => $value)
        {
            for($i = 0; $i < count($value); $i++)
            {
                if(is_subclass_of($value[$i], 'FlipsideDBObject'))
                {
                    if($op == 'insert')
                    {
                        $value[$i]->insert_to_db($db);
                    }
                    else
                    {
                        $value[$i]->replace_in_db($db);
                    }
                }
                else
                {
                    echo "Couldn't add object: ".print_r($value[$i], TRUE);
                }
            }
        }
        return $ret;
    }

    function insert_to_db($db)
    {
        return $this->set_in_db($db, 'insert');
    }


    function replace_in_db($db)
    {
        return $this->set_in_db($db, 'replace');
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
        if($array == FALSE)
        {
            return FALSE;
        }
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
        if(count($array) > 1)
        {
            $all = array();
            for($i = 0; $i < count($array); $i++)
            {
                $type = new static();
                $type->set_object_vars($array[$i]);
                $all[$i] = $type;
            }
            return $all;
        }
        else
        {
            $type = new static();
            $type->set_object_vars($array[0]);
            return $type;
        }
    }

    static function select_from_db_multi_conditions($db, $conds, $conj='AND')
    {
        $table = self::get_table_name();
        $array = $db->select($table, '*', $conds, $conj);
        if($array == FALSE || !isset($array[0]))
        {
            return FALSE;
        }
        if(count($array) > 1)
        {
            $all = array();
            for($i = 0; $i < count($array); $i++)
            {
                $type = new static();
                $type->set_object_vars($array[$i]);
                $all[$i] = $type;
            }
            return $all;
        }
        else
        {
            $type = new static();
            $type->set_object_vars($array[0]);
            return $type;
        }
    }
}
?>
