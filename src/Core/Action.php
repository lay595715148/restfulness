<?php
namespace Lay\Core;

use Lay\Core\EventEmitter;
use Lay\Http\Request;
use Lay\Http\Response;

use Lay\Traits\Singleton;

abstract class Action extends AbstractAction {
	use Singleton;
	//use Lay\Traits\Action;
    /**
     * 事件常量，创建时
     *
     * @var string
     */
    const E_CREATE = 'action:event:create';
    /**
     * 事件常量，GET时
     *
     * @var string
     */
    const E_GET = 'action:event:get';
    /**
     * 事件常量，POST时
     *
     * @var string
     */
    const E_POST = 'action:event:post';
    /**
     * 事件常量，PUT
     *
     * @var string
     */
    const E_PUT = 'action:event:put';
    /**
     * 事件常量，DELETE
     *
     * @var string
     */
    const E_DELETE = 'action:event:delete';
    /**
     * 事件常量，HEAD
     *
     * @var string
     */
    const E_HEAD = 'action:event:head';
    /**
     * 事件常量，PATCH
     *
     * @var string
     */
    const E_PATCH = 'action:event:patch';
    /**
     * 事件常量，OPTIONS
     *
     * @var string
     */
    const E_OPTIONS = 'action:event:options';
    /**
     * 事件常量，结束时
     *
     * @var string
     */
    const E_STOP = 'action:event:stop';
    

    /**
     * HttpRequest
     * @var HttpRequest
     */
    protected $request;
    /**
     * HttpResponse
     * @var HttpResponse
     */
    protected $response;
    /**
     * 存放业务逻辑对象的数组
     * @var array
    */
    protected $services = array();
    /**
     * 模板引擎对象
     * @var Template
    */
    protected $template;
    
    /**
     * 构造方法
     *
     * @param string $name 名称
     * @param Template $template 模板引擎对象
     */
    protected function __construct() {
        //$this->name = get_called_class();
        $this->request = Request::getInstance();
        $this->response = Response::getInstance();
        //$this->template = new Template($this->request, $this->response);
        //EventEmitter::on(self::E_CREATE, array($this, 'onCreate'), 1);
        //PluginManager::exec(self::H_CREATE, array($this));
        //EventEmitter::emit(self::E_CREATE, array($this));
    }

    /**
     * 返回HttpRequest
     * @return HttpRequest
     */
    public function getRequest() {
        return $this->request;
    }
    /**
     * 返回HttpReponse
     * @return HttpReponse
     */
     public function getResponse() {
        return $this->response;
    }
    /**
     * 返回模板引擎对象
     * @return Template
     */
    public function getTemplate() {
        return $this->template;
    }
    /**
     * 获取Service对象
     * @param string $classname
     * @return Service
     */
    public function service($classname) {
        if(!array_key_exists($classname, $this->services) && class_exists($classname)) {
            $this->services[$classname] = $classname::getInstance();
        }
        return $this->services[$classname];
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
    public function onPut() {
        
    }
    public function onDelete() {
        
    }
    public function onPatch() {
        
    }
    public function onHead() {
        
    }
    public function onOptions() {
        
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