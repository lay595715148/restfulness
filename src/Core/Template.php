<?php
namespace Lay\Core;

use Lay\Http\Request;
use Lay\Http\Response;
use Lay\Core\AbstractSingleton;
use Lay\Util\Utility;
use Lay\Core\App;

class Template extends AbstractSingleton {
    /**
     * HttpRequest对象
     *
     * @var HttpRequest $request
     */
    protected $request;
    /**
     * HttpReponse对象
     *
     * @var HttpReponse $response
     */
    protected $response;
    /**
     * the language
     *
     * @var string $lan
     */
    protected $lan = '';
    /**
     * 输出变量内容数组
     *
     * @var array $vars
     */
    protected $vars = array();
    /**
     * resources
     *
     * @var array $resources
     */
    protected $resources = array();
    /**
     * HTTP headers
     *
     * @var array $headers
     */
    protected $headers = array();
    /**
     * HTML metas
     *
     * @var array $metas
     */
    protected $metas = array();
    /**
     * HTML scripts
     *
     * @var array $jses
     */
    protected $jses = array();
    /**
     * HTML scripts in the end
     *
     * @var array $javascript
     */
    protected $javascript = array();
    /**
     * http attachments
     *
     * @var array $attachments
     */
    protected $attachments = array();
    /**
     * HTML css links
     *
     * @var array $csses
     */
    protected $csses = array();
    /**
     * template files directory
     *
     * @var string $dir
     */
    protected $dir = '';
    /**
     * the theme name
     *
     * @var string $theme
     */
    protected $theme = '';
    /**
     * file path
     *
     * @var string $file
     */
    protected $file = '';
    /**
     * redirect url
     *
     * @var string $redirect
     */
    protected $redirect = '';
    /**
     * rendering plain
     *
     * @var string $out
     */
    protected $plain = '';
    /**
     * if dirty rendering
     *
     * @var string $out
     */
    protected $dirty = '';
    /**
     * 构造方法
     */
    protected function __construct() {
        $this->request = Request::getInstance()->getHttpRequest();
        $this->response = Response::getInstance()->getHttpResponse();
        $this->language();//初始化语言
        $this->directory(App::$_docpath);//初始化模板文档目录
        $this->theme(App::get('theme', 'default'));//初始化主题皮肤

        App::$_event->listen($this, App::E_FINISH, array($this, 'spit'));
    }
    /**
     * get template file path
     * @return string
     */
    public function getFile() {
        return $this->file;
    }
    /**
     * get template dir path
     * @return string
     */
    public function getDir() {
        return $this->dir;
    }
    /**
     * push header for output
     *
     * @param string $header
     *            http header string
     */
    public function header($header) {
        $this->headers[] = $header;
    }
    /**
     * push variables with a name
     *
     * @param string $name
     *            name of variable
     * @param mixed $value
     *            value of variable
     */
    public function push($name, $value = null) {
        if(is_string($name) || is_numeric($name)) {
            if(array_key_exists($name, $this->vars)) {
                //Logger::warn($name . ' has been defined in template variables', 'TEMPLATE');
            }
            /*if(is_a($value, 'lay\core\Bean')) {
                $this->vars[$name] = $value->toArray();
            } else */
            if(is_object($value)) {
                $this->vars[$name] = get_object_vars($value);
            } else {
                $this->vars[$name] = is_null($value) ? '' : $value;
            }
        } else if(is_array($name)) {
            foreach($name as $n => $val) {
                $this->push($n, $val);
            }
        } else if(is_a($name, 'Iterator')) {
            $this->push(iterator_to_array($name));
        } else if(is_object($name)) {
            $this->push(get_object_vars($name));
        } else {
            //$this->vars[] = is_null($value) ? '' : $value;
        }
    }
    /**
     * set language
     *
     * @param string $lan
     *            language
     */
    public function language($lan = 'zh-cn') {
        $supports = App::get('languages', array('zh-cn'));
        $support = App::get('language', 'zh-cn');
        $this->lan = in_array($lan, (array)$supports) ? $lan : $support;
    }
    /**
     * set language
     *
     * @param string $lan
     *            language
     */
    public function resource() {
        $respath = $this->dir . DIRECTORY_SEPARATOR . 'resource' . DIRECTORY_SEPARATOR . $this->lan . '.php';
        if($path = realpath($respath)) {
            $this->resources = include $path;
        }
    }
    /**
     * set template dir
     * @param string $dir
     */
    public function directory($dir) {
        if($path = realpath($dir)) {
            $this->dir = $path;
        }
    }
    /**
     * set template filename
     *
     * @param string $filename
     */
    public function file($filename) {
        $filepath = $this->dir . DIRECTORY_SEPARATOR . 'theme' . DIRECTORY_SEPARATOR . $this->theme . DIRECTORY_SEPARATOR . $filename;
        if($path = realpath($filepath)) {
            $this->file = $path;
        }
    }
    /**
     * set template theme name
     *
     * @param string $theme
     */
    public function theme($theme) {
        $this->theme = $theme;
    }
    /**
     * clean template variables
     */
    public function distinct() {
        $this->vars = array();
    }
    /**
     * clean template file and variables
     */
    public function clean() {
        $this->file = '';
        $this->vars = array();
        $this->headers = array();
        $this->metas = array();
        $this->jses = array();
        $this->javascript = array();
        $this->attachments = array();
        $this->csses = array();
    }
    /**
     * set meta infomation
     *
     * @param array $meta
     *            array for html meta tag
     */
    public function meta($meta) {
        $metas = &$this->metas;
        if(is_array($meta)) {
            foreach($meta as $i => $m) {
                $metas[] = $m;
            }
        } else {
            $metas[] = $meta;
        }
    }
    /**
     * set include js path
     *
     * @param string $js
     *            javascript file src path in html tag script
     */
    public function js($js) {
        $jses = &$this->jses;
        if(is_array($js)) {
            foreach($js as $i => $j) {
                $jses[] = $j;
            }
        } else {
            $jses[] = $js;
        }
    }
    /**
     * set include js path,those will echo in end of document
     *
     * @param string $js
     *            javascript file src path in html tag script
     */
    public function javascript($js) {
        $javascript = &$this->javascript;
        if(is_array($js)) {
            foreach($js as $i => $j) {
                $javascript[] = $j;
            }
        } else {
            $javascript[] = $js;
        }
    }
    /**
     * set include css path
     *
     * @param string $css
     *            css file link path
     */
    public function css($css) {
        $csses = &$this->csses;
        if(is_array($css)) {
            foreach($css as $i => $c) {
                $csses[] = $c;
            }
        } else {
            $csses[] = $css;
        }
    }
    /**
     * get template headers,
     * return the point of template headers
     *
     * @return array
     */
    public function headers() {
        $h = &$this->headers;
        return $h;
    }
    /**
     * get template variables,
     * return the point of template variables
     *
     * @return array
     */
    public function vars() {
        $v = &$this->vars;
        return $v;
    }
    /**
     * 此方法只是标记了跳转，在输出时才真正地进行跳转
     * @param string $url
     * @param array $params
     */
    public function redirect($url, array $params = array()) {
        $this->redirect = $url . ($params ? '?' . http_build_query($params) : '');
    }
    /**
     * output as json string
     */
    public function json() {
        // if dirty data exists
        //ob_flush();
        $this->dirty = ob_get_contents();
        ob_end_clean();
        // if redirecting
        if($this->redirect) {
            $this->response->redirect($this->redirect);
        }
        // header json data
        $this->response->setContentType('application/json');
        // more headers
        foreach($this->headers as $header) {
            $this->response->setHeader($header);
        }
        // set varibales data
        if(version_compare(phpversion(), '5.4.0') > 0) {
            $this->response->setData(json_encode($this->vars, JSON_PRETTY_PRINT));
        } else {
            $this->response->setData(json_encode($this->vars));
        }
        
        if(headers_sent()) {
            echo $this->response->getData();
        } else {
            $this->response->send();
        }
    }
    /**
     * output as xml string
     */
    public function xml() {
        // if dirty data exists
        //ob_flush();
        $this->dirty = ob_get_contents();
        ob_end_clean();
        // if redirecting
        if($this->redirect) {
            $this->response->redirect($this->redirect);
        }
        // header xml data
        $this->response->setContentType('text/xml');
        // more headers
        foreach($this->headers as $header) {
            $this->response->setHeader($header);
        }
        // set varibales data
        $this->response->setData(Utility::array2XML($this->vars));
        
        //if(headers_sent()) {
            echo $this->response->getData();
        //} else {
        //    $this->response->send();
        //}
    }
    /**
     * get output data
     *
     * @return array
     */
    public function output() {
        // if plain data exists
        if($this->plain) {
            $results = $this->plain;
        } else {
            ob_start();
            $lan = &$this->lan;
            $vars = &$this->vars;
            $file = &$this->file;
            $metas = &$this->metas;
            $jses = &$this->jses;
            $javascript = &$this->javascript;
            $csses = &$this->csses;
            $headers = &$this->headers;
            $res = &$this->res;
            extract($vars);
            include ($file);
            //ob_flush();
            $results = $this->plain = ob_get_contents();
            ob_end_clean();
        }
        return $results;
    }
    /**
     * output as template
     *
     * @return void
     */
    public function display() {
        // if dirty data exists
        //ob_flush();
        $this->dirty = ob_get_contents();
        ob_end_clean();
        // if redirecting
        if($this->redirect) {
            $this->response->redirect($this->redirect);
        }
        // header plain data
        $this->response->setContentType('text/plain');
        // more headers
        foreach($this->headers as $header) {
            $this->response->setHeader($header);
        }
        // get output data
        $results = $this->output();
        // set output data
        $this->response->setData($results);
        // send
        //if(headers_sent()) {
            echo $this->response->getData();
        //} else {
        //    $this->response->send();
        //}
    }

    public function spit() {
        if($this->dirty) {
            print_r($this->dirty);
        }
        print_r($this->dirty);
        //ob_flush();
        //flush();
    }
}

// PHP END
