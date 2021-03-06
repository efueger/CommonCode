<?php
/**
 * An easily serializable class
 *
 * This file describes a serializable object
 *
 * PHP version 5 and 7
 *
 * @author Patrick Boyd / problem@burningflipside.com
 * @copyright Copyright (c) 2015, Austin Artistic Reconstruction
 * @license http://www.apache.org/licenses/ Apache 2.0 License
 */

/**
 * An object that can be serialized and accessed as an array.
 *
 * This class can be serialized to various formats
 */
class SerializableObject implements ArrayAccess,JsonSerializable
{
    /**
     * Create the object from an array
     *
     * @param array $array The array of object properties
     */
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

    /**
     * Serialize the object into a format consumable by json_encode
     *
     * @return mixed The object in a more serialized format
     */
    public function jsonSerialize()
    {
        return $this;
    }

    /**
     * Convert the object into an XML string
     *
     * @return string The XML format of the object
     */
    public function xmlSerialize()
    {
        $xml = new XmlWriter();
        $xml->openMemory();
        $xml->startDocument('1.0');
        if(isset($this[0]))
        {
            $xml->startElement('Array');
            $this->array2XML($xml, 'Entity', (array)$this);
            $xml->endElement();
        }
        else
        {
            $this->object2XML($xml, $this);
        }
        $xml->endElement();
        return $xml->outputMemory(true);
    }

    /**
     * Convert an object to XML without document tags
     *
     * @param XmlWriter $xml The XMLWriter to write the object to
     * @param mixed $data The data to serialze to XML
     */
    private function object2XML(XMLWriter $xml, $data)
    {
        foreach($data as $key => $value)
        {
            if(is_array($value) || is_numeric($key))
            {
                $this->array2XML($xml, $key, (array)$value);
            }
            else if(is_object($value))
            {
                $xml->startElement($key);
                $this->object2XML($xml, $value);
		$xml->endElement();
            }
            else
            {
                if($key[0] === '$')
                {
                    $xml->writeElement(substr($key, 1), $value);
                }
                else
                {
                    $xml->writeElement($key, $value);
                }
            }
        }
    }

    /**
     * Convert an array to XML without document tags
     *
     * @param XmlWriter $xml The XMLWriter to write the object to
     * @param string $keyParent The key of the parent object
     * @param mixed $data The data to serialze to XML
     */
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

    /**
     * Convert json back to an object
     *
     * @param string $json The JSON string to deserialize back into an object
     *
     * @return SerializableObject The object the json deserializes into 
     */
    public static function jsonDeserialize($json)
    {
        $array = json_decode($json, true);
        return new self($array);
    }

    /**
     * Convert the object to a serizlized string
     *
     * @param string $fmt The format to serialize into
     * @param array|false $select Which fields to include
     *
     * @return string The object in string format
     */
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

    /**
     * Function to allow the caller to set a value in the object via object[offset] = value
     *
     * @param string $offset The key to set
     * @param mixed $value The value for the key
     */
    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }

    /**
     * Function to allow the caller to determin if a value in the object is set
     *
     * @param string $offset The key to determine if it has a value
     *
     * @return boolean Does the key have a value?
     */
    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }

    /**
     * Function to allow the caller to delete the value in the object for a key
     *
     * @param string $offset The key to unset
     */
    public function offsetUnset($offset)
    {
        unset($this->{$offset});
    }

    /**
     * Function to allow the caller to obtain the value for a key
     *
     * @param string $offset The key to return the value for
     *
     * @return mixed the value in the key
     */
    public function offsetGet($offset)
    {
        return $this->{$offset};
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
