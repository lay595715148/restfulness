<?php
namespace Lay\Util;

use Lay\Http\Request;
use Lay\Http\Response;
use Lay\Traits\Singleton;

use RuntimeException;
use Exception;

class RESTful{
    use Singleton;
    protected static $_supported_http_methods = array('get', 'delete', 'post', 'put', 'patch', 'head');
    protected static $_supported_formats = array(
        'xml'           => 'application/xml',
        'json'          => 'application/json',
        'jsonp'         => 'application/javascript',
        'serialized'    => 'application/vnd.php.serialized',
        'php'           => 'text/plain',
        'css'           => 'text/css',
        'html'          => 'text/html',
        'csv'           => 'application/csv'
    );
    public static $method_emulate = true;

    protected $handler;
    protected $options = array();
    protected function __construct() {
        if (!extension_loaded('curl'))
            throw new RuntimeException('Require curl extension');
    }
    public function __destruct() {
        $this->close();
    }
    public function close() {
        if ($this->handler) {
            curl_close($this->handler);
            $this->handler = null;
        }
        return $this;
    }
    public function setOptions(array $options) {
        foreach ($options as $key => $val)
            $this->options[$key] = $val;
        return $this;
    }
    public function execute($url, array $options = array()) {
        $this->close();
        $curl_options = $this->options;
        foreach ($options as $key => $val)
            $curl_options[$key] = $val;
        $curl_options[CURLOPT_URL] = $url;
        $handler = curl_init();
        curl_setopt_array($handler, $curl_options);
        $result = curl_exec($handler);
        if ($result === false)
            throw new RuntimeException('Curl Error: '. curl_error($handler), curl_errno($handler));
        $this->handler = $handler;
        return $result;
    }
    public function send($scheme, $hostport, $resource, $rep = 'json', $state = 'GET', array $params = array(), array $upload = array(), $username = '', $password = '') {
        $method = strtoupper($state);
        // 数组必须用http_build_query转换为字符串
        // 否则会使用multipart/form-data而不是application/x-www-form-urlencoded
        //$params = http_build_query($params) ?: null;
        $rep = ltrim($rep, '.');
        $rep = empty($rep) ? '' : '.' . $rep;
        $url = $scheme . '://' . $hostport . '/' . ltrim($resource, '/') . $rep;
        $options = array();
        $options[CURLOPT_HTTPHEADER][] = 'X-HTTP-METHOD-OVERRIDE: '. $method;
        if ($method == 'GET' || $method == 'HEAD') {
            if ($params)
                $url = $url .'?'. http_build_query($params);
            if ($method == 'GET') {
                $options[CURLOPT_HTTPGET] = true;
            } else {
                $options[CURLOPT_CUSTOMREQUEST] = 'HEAD';
                $options[CURLOPT_NOBODY] = true;
            }
        } else {
            if ($method == 'POST') {
                $options[CURLOPT_POST] = true;
            } elseif (static::$method_emulate) {
                $options[CURLOPT_POST] = true;
                //$options[CURLOPT_POSTFIELDS] = $params;
            } else {
                $options[CURLOPT_CUSTOMREQUEST] = $method;
            }
            if(!empty($upload) && $method == 'POST') {
                foreach ($upload as $key => $file) {
                    $upload[$key] = '@' . realpath(ltrim($file, '@'));
                }
                $params = array_merge($params, $upload);
                $options[CURLOPT_POSTFIELDS] = $params;
            } else if($params) {
                $options[CURLOPT_POSTFIELDS] = http_build_query($params);
            }
        }
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_HEADER] = true;

        $result = $this->execute($url, $options);

        $message = array();
        $message['info'] = $this->getInfo();
        $header_size = $message['info']['header_size'];
        $message['header'] = preg_split('/\r\n/', substr($result, 0, $header_size), 0, PREG_SPLIT_NO_EMPTY);
        $message['body'] = $this->represent(substr($result, $header_size), $rep);
        return $message;
    }
    public function get($url, $path, $rep = 'json', array $params = array()) {
        list($scheme, $host, $port, $user, $pass, $param, $fragment) = $this->parse($url);
        $params = array_merge($param, $params);
        $this->send($scheme, "$host:$port", $path, $rep, 'GET', $params, array(), $user, $pass);
    }
    public function post($url, $path, $rep = 'json', array $params = array(), array $upload = array()) {
        list($scheme, $host, $port, $user, $pass, $param, $fragment) = $this->parse($url);
        $params = array_merge($param, $params);
        $this->send($scheme, "$host:$port", $path, $rep, 'POST', $params, $upload, $user, $pass);
    }
    public function put($url, $path, $rep = 'json', array $params = array()) {
        list($scheme, $host, $port, $user, $pass, $param, $fragment) = $this->parse($url);
        $params = array_merge($param, $params);
        $this->send($scheme, "$host:$port", $path, $rep, 'PUT', $params, $upload, $user, $pass);
    }
    public function delete($url, $path, $rep = 'json', array $params = array()) {
        list($scheme, $host, $port, $user, $pass, $param, $fragment) = $this->parse($url);
        $params = array_merge($param, $params);
        $this->send($scheme, "$host:$port", $path, $rep, 'DELETE', $params, $upload, $user, $pass);
    }
    public function head($url, $path, $rep = 'json', array $params = array()) {
        list($scheme, $host, $port, $user, $pass, $param, $fragment) = $this->parse($url);
        $params = array_merge($param, $params);
        $this->send($scheme, "$host:$port", $path, $rep, 'HEAD', $params, $upload, $user, $pass);
    }
    public function patch($url, $path, $rep = 'json', array $params = array()) {
        list($scheme, $host, $port, $user, $pass, $param, $fragment) = $this->parse($url);
        $params = array_merge($param, $params);
        $this->send($scheme, "$host:$port", $path, $rep, 'PATCH', $params, $upload, $user, $pass);
    }
    private function parse($url) {
        $params = array();
        $info = parse_url($url);
        $scheme = empty($info['scheme']) ? 'http' : $info['scheme'];
        $host = empty($info['host']) ? '127.0.0.1' : $info['host'];
        $port = empty($info['port']) ? 80 : $info['port'];
        $user = empty($info['user']) ? '' : $info['user'];
        $pass = empty($info['pass']) ? '' : $info['pass'];
        $path = empty($info['path']) ? '/' : $info['path'];
        $query = empty($info['query']) ? '' : $info['query'];
        $fragment = empty($info['fragment']) ? '' : $info['fragment'];
        !empty($query) && parse_str($query, $params);
        //$pathinfo = pathinfo($path);
        //$ext = empty($pathinfo['extension']) ? '' : $pathinfo['extension'];
        //$path = empty($ext) ? $path : substr($path, -1, strlen($ext) + 1);
        return array($scheme, $host, $port, $user, $pass, $param, $fragment);
    }
    public function getInfo($info = null) {
        if (!$this->handler)
            return false;
        return ($info === null)
             ? curl_getinfo($this->handler)
             : curl_getinfo($this->handler, $info);
    }
    protected function represent($body, $rep = 'json') {
        $rep = strtolower(ltrim($rep, '.'));
        switch ($rep) {
            case 'json':
                $ret = json_decode($body, true);
                $ret = $ret !== null ? $ret : array('code' => 0, 'data'=> $body);
                break;
            default:
                $ret = $body;
                break;
        }
        return $ret;
    }

    /*public static function get($key = null) {
        if ($key === null) return $_GET;
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }
    public static function post($key = null) {
        if ($key === null) return $_POST;
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }
    public static function cookie($key = null) {
        if ($key === null) return $_COOKIE;
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
    }
    public static function put($key = null) {
        global $_PUT;
        if ($_PUT === null) {
            if (self::req()->isPUT()) {
                if (strtoupper(self::server('request_method')) == 'PUT') {
                    parse_str(file_get_contents('php://input'), $_PUT);
                } else {
                    $_PUT = &$_POST;
                }
            } else {
                $_PUT = array();
            }
        }
        if ($key === null) return $_PUT;
        return isset($_PUT[$key]) ? $_PUT[$key] : null;
    }
    public static function request($key = null) {
        if ($key === null) return array_merge(self::put(), $_REQUEST);
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : put($key);
    }*/
    public static function has_get($key) {
        return array_key_exists($key, $_GET);
    }
    public static function has_post($key) {
        return array_key_exists($key, $_POST);
    }
    public static function has_put($key) {
        return array_key_exists($key, self::put());
    }
    public static function has_request($key) {
        return array_key_exists($key, $_REQUEST);
    }
    public static function env($key = null) {
        if ($key === null) return $_ENV;
        $key = strtoupper($key);
        return isset($_ENV[$key]) ? $_ENV[$key] : false;
    }
    public static function server($key = null) {
        if ($key === null) return $_SERVER;
        $key = strtoupper($key);
        return isset($_SERVER[$key]) ? $_SERVER[$key] : false;
    }
    public static function service($name, $args = null) {
        if ($args !== null)
            $args = array_slice(func_get_args(), 1);
        return \Lysine\Service\Manager::getInstance()->get($name, $args);
    }
    public static function req() {
        return Request::getInstance();
    }
    public static function res() {
        return Response::getInstance();
    }
    public static function cfg($keys = null) {
        $keys = $keys === null
              ? null
              : is_array($keys) ? $keys : func_get_args();
        return \Lysine\Config::get($keys);
    }
    /*
     * Detect SSL use
     *
     * Detect whether SSL is being used or not
     */
    protected static function _detect_ssl() {
        // $_SERVER['HTTPS'] (http://php.net/manual/en/reserved.variables.server.php)
        // Set to a non-empty value if the script was queried through the HTTPS protocol
        return isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']);
    }
}