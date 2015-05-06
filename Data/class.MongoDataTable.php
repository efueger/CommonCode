<?php
namespace Data;

class MongoDataTable extends DataTable
{
    protected $collection;

    function __construct($collection)
    {
        $this->collection = $collection;
    }

    function count($filter=false)
    {
        $criteria = array();
        if($filter !== false)
        {
            $criteria = $filter->to_mongo_filter();
        }
        return $this->collection->count($criteria);
    }

    function search($filter=false, $select=false, $count=false, $skip=false, $sort=false, $params)
    {
        $fields   = array();
        $criteria = array();
        if($filter !== false)
        {
            $criteria = $filter->to_mongo_filter();
        }
        if($select !== false)
        {
            $fields = array_fill_keys($select, 1);
        }
        $cursor   = $this->collection->find($criteria, $fields);
        if($params !== false && isset($params['fields']))
        {
            $cursor->fields($params['fields']);
        }
        if($sort  !== false)
        {
            $cursor->sort($sort);
        }
        if($skip  !== false)
        {
            $cursor->skip($skip);
        }
        if($count !== false)
        {
            $cursor->limit($count);
        }
        $ret      = array();
        foreach($cursor as $doc)
        {
            array_push($ret, $doc);
        }
        return $ret;
    }
}
?>
