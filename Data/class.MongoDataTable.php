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
            if($filter instanceof \Data\Filter)
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
            if(is_array($filter))
            {
                $criteria = $filter;
            }
            else
            {
                $criteria = $filter->to_mongo_filter();
            }
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

    function create($data)
    {
        $res = $this->collection->insert($data);
        if($res === false || $res['err'] !== null)
        {
            return false;
        }
        return $data['_id'];
    }

    function update($filter, $data)
    {
        $criteria = array();
        if($filter !== false)
        {
            if(is_array($filter))
            {
                $criteria = $filter;
            }
            else
            {
                $criteria = $filter->to_mongo_filter();
            }
        }
        if(isset($data['_id']))
        {
            unset($data['_id']);
        }
        $res = $this->collection->update($criteria, array('$set' => $data));
        if($res === false || $res['err'] !== null)
        {
            return false;
        }
        return true;
    }

    function delete($filter)
    {
        $criteria = array();
        if($filter !== false)
        {
            if(is_array($filter))
            {
                $criteria = $filter;
            }
            else
            {
                $criteria = $filter->to_mongo_filter();
            }
        }
        $res = $this->collection->remove($criteria);
        if($res === false || $res['err'] !== null)
        {
            return false;
        }
        return true;
    }
}
?>
