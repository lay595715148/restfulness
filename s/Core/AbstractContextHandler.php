<?php
namespace Lay\Core;

// 会话之间的上下文数据封装
abstract class ContextHandler {
    protected $config;
    public abstract function set($key, $val);
    public abstract function get($key = null);
    public abstract function has($key);
    public abstract function remove($key);
    public abstract function clear();
    public function __construct(array $config) {
        $this->config = $config;
    }
    public function setConfig($key, $val) {
        $this->config[$key] = $val;
    }
    public function getConfig($key = null) {
        return ($key === null)
             ? $this->config
             : isset($this->config[$key]) ? $this->config[$key] : null;
    }
    public function getToken() {
        if (!$token = $this->getConfig('token'))
            throw new \UnexpectedValueException('Undefined context save token');
        return $token;
    }
    // 保存上下文数据，根据需要重载
    public function save() {
    }
    public static function factory($type, array $config) {
        switch (strtolower($type)) {
            case 'session': return new SessionContextHandler($config);
            case 'cookie': return new CookieContextHandler($config);
            case 'redis': return new RedisContextHandler($config);
            default:
                throw new \UnexpectedValueException('Unknown context handler type: '. $type);
        }
    }
}

// PHP END