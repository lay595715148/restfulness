<?php
namespace Lay\Core;

abstract class AbstractAction {
    /**
     * 创建事件触发方法
     */
    protected abstract function onCreate();
    /**
     * GET事件触发方法
     */
    protected abstract function onGet();
    /**
     * POST事件触发方法
     */
    protected abstract function onPost();
    /**
     * PUT事件触发方法
     */
    protected abstract function onPut();
    /**
     * DELETE事件触发方法
     */
    protected abstract function onDelete();
    /**
     * HEAD事件触发方法
     */
    protected abstract function onHead();
    /**
     * PATCH事件触发方法
     */
    protected abstract function onPatch();
    /**
     * OPTIONS事件触发方法
     */
    protected abstract function onOptions();
}

// PHP END
