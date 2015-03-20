<?php
namespace traits;

use core\Service;

trait Action {
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
}

// PHP END