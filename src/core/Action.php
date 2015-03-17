<?php
namespace core;

use core\EventEmitter;

abstract class Action extends AbstractAction {
	use traits\Singleton;
	use traits\Action;
    /**
     * 事件常量，创建时
     *
     * @var string
     */
    const E_CREATE = 'action:create';
    /**
     * 事件常量，GET时
     *
     * @var string
     */
    const E_GET = 'action:get';
    /**
     * 事件常量，POST时
     *
     * @var string
     */
    const E_POST = 'action:post';
    /**
     * 事件常量，PUT
     *
     * @var string
     */
    const E_PUT = 'action:put';
    /**
     * 事件常量，DELETE
     *
     * @var string
     */
    const E_DELETE = 'action:delete';
    /**
     * 事件常量，HEAD
     *
     * @var string
     */
    const E_HEAD = 'action:head';
    /**
     * 事件常量，PATCH
     *
     * @var string
     */
    const E_PATCH = 'action:patch';
    /**
     * 事件常量，OPTIONS
     *
     * @var string
     */
    const E_OPTIONS = 'action:options';
    /**
     * 事件常量，结束时
     *
     * @var string
     */
    const E_STOP = 'action_stop';
    /**
     * 钩子常量，创建时
     *
     * @var string
     */
    const H_CREATE = 'hook_action_create';
    /**
     * 事件常量，摧毁时
     *
     * @var string
     */
    const E_DESTROY = 'action_destroy';
    /**
     * 钩子常量，结束时
     *
     * @var string
     */
    const H_STOP = 'hook_action_stop';
    

    /**
     * 构造方法
     *
     * @param string $name 名称
     * @param Template $template 模板引擎对象
     */
    protected function __construct($name) {
        $this->name = $name;
        $this->request = new HttpRequest();
        $this->response = new HttpResponse();
        $this->template = new Template($this->request, $this->response);
        //EventEmitter::on(self::E_CREATE, array($this, 'onCreate'), 1);
        //PluginManager::exec(self::H_CREATE, array($this));
        //EventEmitter::emit(self::E_CREATE, array($this));
    }
    /**
     * 创建事件触发方法
     * @see \lay\core\AbstractAction::onCreate()
     */
    public function onCreate() {
        
    }
    /**
     * REQUEST事件触发方法
     * @see \lay\core\AbstractAction::onRequest()
     */
    public function onRequest() {
        switch($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                // 触发action的get事件
                EventEmitter::emit(Action::E_GET, array(
                        $this
                ));
                break;
            case 'POST':
                // 触发action的post事件
                EventEmitter::emit(Action::E_POST, array(
                        $this
                ));
                break;
            default:
                break;
        }
    }
    /**
     * GET事件触发方法
     * @see \lay\core\AbstractAction::onGet()
     */
    public function onGet() {
        
    }
    /**
     * POST事件触发方法
     * @see \lay\core\AbstractAction::onPost()
     */
    public function onPost() {
        
    }
    /**
     * 结束事件触发方法
     * @see \lay\core\AbstractAction::onStop()
     */
    public function onStop() {
        
    }
    /**
     * 摧毁事件触发方法
     * @see \lay\core\AbstractAction::onDestroy()
     */
    public function onDestroy() {
        
    }
}