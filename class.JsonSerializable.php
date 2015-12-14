<?php
/**
* JsonSerializable interface
*
* PHP 5.3 compatibility for PHP 5.4's \JsonSerializable
*
* @author Sam-Mauris Yong / mauris@hotmail.sg
* @copyright Copyright (c) 2010-2012, Sam-Mauris Yong
* @license http://www.opensource.org/licenses/bsd-license New BSD License
* @link http://www.php.net/manual/en/class.jsonserializable.php
*/

if(PHP_VERSION_ID < 50400)
{
/**
* JsonSerializable interface
*
* PHP 5.3 compatibility for PHP 5.4's \JsonSerializable
*
* @author Sam-Mauris Yong / mauris@hotmail.sg
* @copyright Copyright (c) 2010-2012, Sam-Mauris Yong
* @license http://www.opensource.org/licenses/bsd-license New BSD License
* @link http://www.php.net/manual/en/class.jsonserializable.php
*/
interface JsonSerializable {
    /** The jsonSerizlize function as defined by PHP 5.4 and greater */
    public function jsonSerialize();
}
}
?>
