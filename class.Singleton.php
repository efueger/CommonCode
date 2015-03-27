<?php
class Singleton
{
    public static function getInstance()
    {
        static $instance = null;
        if(null === $instance)
        {
            $instance = new static();
        }
        return $instance;
    }

    protected function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
