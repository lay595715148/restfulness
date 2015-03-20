<?php
namespace traits;

// 事件方法
trait Event {
    public function onEvent($event, $callback) {
        return \core\Event::getInstance()->listen($this, $event, $callback);
    }
    public function fireEvent($event, array $args = null) {
        return \core\Event::getInstance()->fire($this, $event, $args);
    }
    public function clearEvent($event = null) {
        return \core\Event::getInstance()->clear($this, $event);
    }
    public static public function subscribeEvent($event, $callback) {
        return \core\Event::getInstance()->subscribe(get_called_class(), $event, $callback);
    }
}

// PHP END