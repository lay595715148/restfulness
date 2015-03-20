<?php
namespace Lay\Core;

abstract class AbstractAction {
    /**
     * 创建事件触发方法
     */
    public abstract function onCreate();
    /**
     * GET事件触发方法
     */
    public abstract function onGet();
    /**
     * POST事件触发方法
     */
    public abstract function onPost();
    /**
     * PUT事件触发方法
     */
    public abstract function onPut();
    /**
     * DELETE事件触发方法
     */
    public abstract function onDelete();
    /**
     * HEAD事件触发方法
     */
    public abstract function onHead();
    /**
     * PATCH事件触发方法
     */
    public abstract function onPatch();
    /**
     * OPTIONS事件触发方法
     */
    public abstract function onOptions();
    /**
     * 结束事件触发方法
     */
    public abstract function onStop();
    /**
     * 摧毁事件触发方法
     */
    public abstract function onDestroy();
}

// PHP END
