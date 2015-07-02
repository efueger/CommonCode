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

    public function xmlSerialize()
    {
        $xml = new XmlWriter();
        $xml->openMemory();
        $xml->startDocument('1.0');
        $this->object2XML($xml, $this);
        $xml->endElement();
        return $xml->outputMemory(true);
    }

    private function object2XML(XMLWriter $xml, $data)
    {
        foreach($data as $key => $value)
        {
            if(is_object($value))
            {
                $xml->startElement($key);
                $this->getObject2XML($xml, $value);
		$xml->endElement();
            }
            else if(is_array($value))
            {
                $this->array2XML($xml, $key, $value);
            }
            else
            {
                $xml->writeElement($key, $value);
            }
        }
    }

    private function array2XML(XMLWriter $xml, $keyParent, $data)
    {
        foreach($data as $key => $value)
        {
	    if(is_string($value))
            {
                $xml->writeElement($keyParent, $value);
                continue;
            }
            if(is_numeric($key))
            {
                $xml->startElement($keyParent);
            }
 
            if(is_object($value))
            {
                $this->object2XML($xml, $value);
            }
            else if(is_array($value))
            {
                $this->array2XML($xml, $key, $value);
                continue;
            }
 
            if(is_numeric($key))
            {
                $xml->endElement();
            }
        }
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
