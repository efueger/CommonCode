<?php
class SerializableObject implements ArrayAccess,JsonSerializable
{
    public function __construct($array=false)
    {
        if($array !== false && is_array($array))
        {
            foreach($array as $key => $value)
            {
                $this->{$key} = $value;
            }
        }
    }

    public function jsonSerialize()
    {
        return $this;
    }

    public static function jsonDeserialize($json)
    {
        $array = json_decode($json, true);
        return new self($array);
    }

    public function serializeObject($fmt = 'json', $select = false)
    {
        $copy = $this;
        if($select !== false)
        {
            $copy = new self();
            $count = count($select);
            for($i = 0; $i < $count; $i++)
            {
                $copy->{$select[$i]} = $this->offsetGet($select[$i]);
            }
        }
        switch($fmt)
        {
            case 'json':
                return json_encode($copy);
            default:
                throw new Exception('Unsupported fmt '.$fmt);
        }
    }

    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }

    public function offsetUnset($offset)
    {
        unset($this->{$offset});
    }

    public function offsetGet($offset)
    {
        return $this->{$offset};
    }
}
?>
