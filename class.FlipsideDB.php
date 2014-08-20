<?php
require_once("/var/www/secure_settings/class.FlipsideSettings.php");
class FlipsideDB
{
    private static function get_connection_info_by_db_name($db_name)
    {
        $ret = array();
        $ret['host'] = FlipsideSettings::$db_info['db_'.$db_name]['host'];
        $ret['dsn']  = 'mysql:host='.$ret['host'].';dbname='.$db_name;
        $ret['user'] = FlipsideSettings::$db_info['db_'.$db_name]['user'];
        $ret['pass'] = FlipsideSettings::$db_info['db_'.$db_name]['pass'];
        return $ret;
    }
	
    public static function get_pdo($db_name)
    {
        $db_info = FlipsideDB::get_connection_info_by_db_name($db_name);
        $pdo = new PDO($db_info['dsn'], $db_info['user'], $db_info['pass']);
        if($pdo == FALSE)
        {
            return FALSE;
        }
        return $pdo;
    }

    private static function get_table_info($db_name, $db_table)
    {
        return FlipsideSettings::$db_info['db_'.$db_name]['table_'.$db_table];
    }

    private static function sql_escape(&$item1, $key, $pdo)
    {
        if(strcmp($item1, 'UTC_TIMESTAMP()') == 0)
        {
            /*Do nothing*/
        }
        else
        {
            $item1 = $pdo->quote($item1);
        }
    }

    public static function write_to_db($db_name, $db_table, $values)
    {
        $db_info = FlipsideDB::get_connection_info_by_db_name($db_name);
        $pdo = new PDO($db_info['dsn'], $db_info['user'], $db_info['pass']);
        if($pdo == FALSE)
        {
             return FALSE;
        }
        $key_array = array_keys($values);
        $keys = implode(',', $key_array);
        array_walk($values, 'FlipsideDB::sql_escape', $pdo);
        $values_str = implode(',', $values);
        $sql = 'INSERT INTO '.$db_table.' ('.$keys.') VALUES ('.$values_str.');';
        if($pdo->exec($sql) === FALSE)
        {
            $db_table_info = FlipsideDB::get_table_info($db_name, $db_table);
            if($db_table_info != FALSE)
            {
                if($pdo->exec($db_table_info) === FALSE)
                {
                    return FALSE;
                }
                else
                {
                    if($pdo->exec($sql) === FALSE)
                    {
                        return FALSE;
                    }
                    else
                    {
                        return TRUE;
                    }
                }
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            return TRUE;
        }
    }

    public static function select_field($db_name, $db_table, $db_field, $cond)
    {
        $db_info = FlipsideDB::get_connection_info_by_db_name($db_name);
        $pdo = new PDO($db_info['dsn'], $db_info['user'], $db_info['pass']);
        if($pdo == FALSE)
        {
             return FALSE;
        }
        $conditions = '';
        $keys = array_keys($cond);
        for($i = 0; $i < count($cond); $i++)
        {
            $conditions .= $keys[$i].$cond[$keys[$i]].' ';
            if($i != count($cond)-1)
            {
                $conditions .= ' AND ';
            }
        } 
        $sql = 'SELECT '.$db_field.' FROM '.$db_table.' WHERE '.$conditions.';';
        $stmt = $pdo->query($sql, PDO::FETCH_ASSOC);
        if($stmt == FALSE)
        {
            return FALSE;
        }
        $ret = $stmt->fetchAll();
        if($ret == FALSE)
        {
            return FALSE;
        }
        return $ret[0];
    }

    public static function delete_from_db($db_name, $db_table, $cond)
    {
        $db_info = FlipsideDB::get_connection_info_by_db_name($db_name);
        $pdo = new PDO($db_info['dsn'], $db_info['user'], $db_info['pass']);
        if($pdo == FALSE)
        {
             return FALSE;
        }
        $conditions = '';
        $keys = array_keys($cond);
        for($i = 0; $i < count($cond); $i++)
        {
            $conditions .= $keys[$i].$cond[$keys[$i]].' ';
            if($i != count($cond)-1)
            {
                $conditions .= ' AND ';
            }
        }
        $sql = 'DELETE FROM '.$db_table.' WHERE '.$conditions.';';
        return $pdo->exec($sql);
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
