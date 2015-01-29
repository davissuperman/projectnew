<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://4wei.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 尘缘 <130775@qq.com>
// +----------------------------------------------------------------------
// $Id$
defined('THINK_PATH') or exit();
/**
 +------------------------------------------------------------------------------
 * Memcache缓存类
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Util
 * @author    vus520 <130775@qq.com>
 * @version   0.0.1
 +------------------------------------------------------------------------------
 */
class CacheRedis extends Cache
{//类定义开始

    /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public $redis;
    function __construct($options='')
    {
        if ( !extension_loaded('redis') ) {
            throw_exception(L('_NOT_SUPPERT_').':redis');
        }
        if(empty($options)) {
            $options = array
            (
                'host'  => C('REDIS_HOST') ? C('REDIS_HOST') : '127.0.0.1',
                'port'  => C('REDIS_PORT') ? C('REDIS_PORT') : 6379,
                'timeout' => C('DATA_CACHE_TIME') ? C('DATA_CACHE_TIME') : false,
                'expire' => 1296000,
                'persistent' => false
            );
        }
        $func = $options['persistent'] ? 'pconnect' : 'connect';

//        $this->expire = isset($options['expire'])?$options['expire']:C('DATA_CACHE_TIME');
        $this->handler  = new Redis;
        $this->connected = $options['timeout'] === false ?
			$this->handler->$func($options['host'], $options['port']) :
			$this->handler->$func($options['host'], $options['port'], $options['timeout']);
        $this->type = strtoupper(substr(__CLASS__, 5));
        $this->expire = C('DATA_CACHE_TIME');
        $this->redis = $this->handler;
    }

    /**
     +----------------------------------------------------------
     * 是否连接
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    private function isConnected()
    {
        return $this->connected;
    }

    /**
     +----------------------------------------------------------
     * 读取缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function get($name)
    {
        N('cache_read',1);
        $value = $this->handler->get($name);
        $jsonData = json_decode( $value, true );
        return $jsonData;
    }

    /**
     +----------------------------------------------------------
     * 写入缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function set($name, $value, $ttl = null)
    {
        N('cache_write',1);
        $value = (is_object($value) || is_array($value)) ? json_encode($value) : $value;
        if(isset($ttl) && is_int($ttl))
            $expire = $ttl;
        else
            $expire = $this->expire;

        if(isset($expire) && is_int($expire))
        {
        	return $this->handler->setex($name, $expire, $value);
        }else{
        	return $this->handler->set($name, $value);
        }
    }

    public function lpush($key,$value){
        $value = (is_object($value) || is_array($value)) ? json_encode($value) : $value;
        return $this->handler->lPush($key,$value);
    }

    public function lrange($key, $start, $end){
        $arr = $this->handler->lrange($key, $start, $end);
        $tmp = array();
        foreach($arr as $key => $each){
            $tmp[] = json_decode($each,true);
        }
        return $tmp;
    }

    /**
     +----------------------------------------------------------
     * 删除缓存
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function rm($name)
    {
        return $this->handler->delete($name);
    }

    /**
     +----------------------------------------------------------
     * 清除缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function clear()
    {
        return $this->handler->flushDB();
    }
}//类定义结束
?>