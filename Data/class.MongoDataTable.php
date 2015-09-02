<?php
namespace Data;

class MongoDataTable extends DataTable
{
    protected $collection;
    protected $namespace;

    function __construct($collection, $collection_name=false)
    {
        if($collection_name !== false)
        {
            $this->namespace = $collection.'.'.$collection_name;
        }
        else
        {
            $this->collection = $collection;
        }
    }

    function count($filter=false)
    {
        $criteria = array();
        if($filter !== false)
        {
            if($filter instanceof Data\Filter)
            {
                $criteria = $filter->to_mongo_filter();
            }
            else
            {
                $criteria = $filter;
            }
        }
        return $this->collection->count($criteria);
    }

    function search($filter=false, $select=false, $count=false, $skip=false, $sort=false, $params=false)
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
