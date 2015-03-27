<?php
require_once("/var/www/secure_settings/class.FlipsideSettings.php");
require_once('Autoload.php');
class DataSetFactory
{
    static function get_data_set($set_name)
    {
        static $instances = array();
        if(isset($instances[$set_name]))
        {
            return $instances[$set_name];
        }
        if(!isset(FlipsideSettings::$dataset[$set_name]))
        {
            throw new Exception('Unknown dataset name '.$set_name);
        }
        $set_data = FlipsideSettings::$dataset[$set_name];
        $class_name = '\\Data\\'.$set_data['type'];
        $obj = new $class_name($set_data['params']);
        $instances[$set_name] = $obj;
        return $obj;
    }
}
?>
