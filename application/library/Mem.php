<?php
/**
* memcache操作类
* @author    huangtao
* @version   1.0
* @copyright 2013-08-20
*/
class Mem{
    protected static $servers   = array( );
    protected static $mem;
    protected static function init()
    {
        try {
            $arr[] = Yaf_Application::app()->getConfig()->application->memcache->servers;
            self::$servers =  $arr;
            if (empty(self::$mem))
            {
                self::$mem = new Memcache;
                foreach (self::$servers as $val)
                {
                    $exp = explode(':', $val);
                    self::$mem->addServer($exp[0], $exp[1]);
                }
            }
        }catch (Exception $e){
            die('Memcache Connection Fail');
        }
    }
    public static function flush()
    {
        self::init();
        return self::$mem->flush();
    }
    public static function get($key)
    {
        self::init();
        return self::$mem->get($key);
    }
    public static function delete($key)
    {
        self::init();
        return self::$mem->delete($key);
    }
    public static function set($key = '', $var = '', $expire = 3600)
    {
        self::init();
        return self::$mem->set($key, $var, 0, $expire);
    }
    public static function add($key = '', $var = '', $expire = 3600)
    {
        self::init();
        return self::$mem->add($key, $var, 0, $expire);
    }
}