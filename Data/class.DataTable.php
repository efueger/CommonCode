<?php
namespace Data;

class DataTable implements \ArrayAccess
{
    protected $data = null;

    function search($filter=false, $select=false, $count=false)
    {
        if($this->data === null)
        {
            throw new \Exception('Unimplemented');
        }
        if($filter !== false)
        {
            $array = $filter->filter_array($this->data);
        }
        return $array;
    }

    function create($data)
    {
        throw new \Exception('Unimplemented');
    }

    function read($filter=false, $select=false, $count=false)
    {
        return $this->search($filter, $select, $count);
    }

    function update($filter, $data)
    {
        throw new \Exception('Unimplemented');
    }

    function delete($filter)
    {
        throw new \Exception('Unimplemented');
    }

    function prefetch_all()
    {
        $this->data = $this->read(false, false);
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }
}
?>
