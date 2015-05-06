<?php
namespace Data;

class MongoDataSet extends DataSet
{
    protected $client;
    protected $db;

    function __construct($params)
    {
        if(isset($params['user']))
        {
            $this->client = new \MongoClient('mongodb://'.$params['host'].'/'.$params['db'], array('username'=>$params['user'], 'password'=>$params['pass']));
        }
        else
        {
            $this->client = new \MongoClient('mongodb://'.$params['host'].'/'.$params['db']);
        }
        $this->db = $this->client->selectDB($params['db']);
    }

    function tableExists($name)
    {
        $collections = $this->db->getCollectionNames();
        if(in_array($name, $collections))
        {
             return true;
        }
        return false;
    }

    function getTable($name)
    {
        return new MongoDataTable($this->db->selectCollection($name));
    }
}
?>
