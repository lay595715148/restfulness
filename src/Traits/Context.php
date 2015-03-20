<?php
namespace trait;

use core\AbstractContextHandler;

// 上下文消息
trait Context {
    protected $context_handler;
    public function setContext($key, $val) {
        return $this->getContextHandler(true)->set($key, $val);
    }
    public function getContext($key = null) {
        return $this->getContextHandler(true)->get($key);
    }
    public function hasContext($key) {
        return $this->getContextHandler(true)->has($key);
    }
    public function removeContext($key) {
        return $this->getContextHandler(true)->remove($key);
    }
    public function clearContext() {
        return $this->getContextHandler(true)->clear();
    }
    public function saveContext() {
        return $this->getContextHandler(true)->save();
    }
    public function setContextHandler(AbstractContextHandler $handler) {
        $this->context_handler = $handler;
    }
    public function getContextHandler($throw_exception = false) {
        if (!$this->context_handler && $throw_exception) {
            throw new \RuntimeException('Please set context handler before use');
        }
        return $this->context_handler ?: false;
    }
}

// PHP END