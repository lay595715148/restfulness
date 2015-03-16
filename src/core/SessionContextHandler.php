<?php
namespace core;

// $config = array(
//     'token' => (string),     // 必须，上下文存储唯一标识
// );
//
// $handler = ContextHandler::factory('session', $config);
// $handler = new SessionContextHandler($config);
class SessionContextHandler extends AbstractContextHandler {
    public function set($key, $val) {
        $token = $this->getToken();
        $_SESSION[$token][$key] = $val;
    }
    public function get($key = null) {
        $token = $this->getToken();
        $context = isset($_SESSION[$token]) ? $_SESSION[$token] : array();
        return ($key === null)
             ? $context
             : (isset($context[$key]) ? $context[$key] : null);
    }
    public function has($key) {
        $token = $this->getToken();
        return isset($_SESSION[$token][$key]);
    }
    public function remove($key) {
        $token = $this->getToken();
        unset($_SESSION[$token][$key]);
    }
    public function clear() {
        $token = $this->getToken();
        unset($_SESSION[$token]);
    }
}

// PHP END