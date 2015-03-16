<?php
namespace http;

class Request {
    use \traits\Singleton;
    private $method;
    private $request_uri;
    public function getHeader($key) {
        $key = 'http_'. str_replace('-', '_', $key);
        return server($key);
    }
    public function getRequestURI() {
        if ($this->request_uri) {
            return $this->request_uri;
        }
        if ($uri = server('request_uri')) {
            return $this->request_uri = $uri;
        }
        throw new \RuntimeException('Unknown request URI');
    }
    public function getMethod() {
        if ($this->method) {
            return $this->method;
        }
        $method = strtoupper($this->header('x-http-method-override') ?: server('request_method'));
        if ($method != 'POST') return $this->method = $method;
        // 某些js库的ajax封装使用这种方式
        $method = post('_method') ?: $method;
        unset($_POST['_method']);
        return $this->method = strtoupper($method);
    }
    public function getExtension() {
        $path = parse_url($this->requestUri(), PHP_URL_PATH);
        return strtolower(pathinfo($path, PATHINFO_EXTENSION))
            ?: 'html';
    }
    public function isGET() {
        return ($this->method() === 'GET') ?: $this->isHEAD();
    }
    public function isPOST() {
        return $this->method() === 'POST';
    }
    public function isPUT() {
        return $this->method() === 'PUT';
    }
    public function isDELETE() {
        return $this->method() === 'DELETE';
    }
    public function isHEAD() {
        return $this->method() === 'HEAD';
    }
    public function isAJAX() {
        return strtolower($this->header('X_REQUESTED_WITH')) == 'xmlhttprequest';
    }
    public function getReferer() {
        return server('http_referer');
    }
    public function getIP($proxy = null) {
        $ip = $proxy
            ? server('http_x_forwarded_for') ?: server('remote_addr')
            : server('remote_addr');
        if (strpos($ip, ',') === false)
            return $ip;
        // private ip range, ip2long()
        $private = array(
            array(0, 50331647),             // 0.0.0.0, 2.255.255.255
            array(167772160, 184549375),    // 10.0.0.0, 10.255.255.255
            array(2130706432, 2147483647),  // 127.0.0.0, 127.255.255.255
            array(2851995648, 2852061183),  // 169.254.0.0, 169.254.255.255
            array(2886729728, 2887778303),  // 172.16.0.0, 172.31.255.255
            array(3221225984, 3221226239),  // 192.0.2.0, 192.0.2.255
            array(3232235520, 3232301055),  // 192.168.0.0, 192.168.255.255
            array(4294967040, 4294967295),  // 255.255.255.0 255.255.255.255
        );
        $ip_set = array_map('trim', explode(',', $ip));
        // 检查是否私有地址，如果不是就直接返回
        foreach ($ip_set as $key => $ip) {
            $long = ip2long($ip);
            if ($long === false) {
                unset($ip_set[$key]);
                continue;
            }
            $is_private = false;
            foreach ($private as $m) {
                list($min, $max) = $m;
                if ($long >= $min && $long <= $max) {
                    $is_private = true;
                    break;
                }
            }
            if (!$is_private) return $ip;
        }
        return array_shift($ip_set) ?: '0.0.0.0';
    }
    public function getAcceptTypes() {
        return $this->getAccept('http_accept');
    }
    public function getAcceptLanguage() {
        return $this->getAccept('http_accept_language');
    }
    public function getAcceptCharset() {
        return $this->getAccept('http_accept_charset');
    }
    public function getAcceptEncoding() {
        return $this->getAccept('http_accept_encoding');
    }
    public function isAcceptType($type) {
        return $this->isAccept($type, $this->getAcceptTypes());
    }
    public function isAcceptLanguage($lang) {
        return $this->isAccept($lang, $this->getAcceptLanguage());
    }
    public function isAcceptCharset($charset) {
        return $this->isAccept($charset, $this->getAcceptCharset());
    }
    public function isAcceptEncoding($encoding) {
        return $this->isAccept($encoding, $this->getAcceptEncoding());
    }
    //////////////////// protected method ////////////////////
    protected function getAccept($header_key) {
        if (!$accept = server($header_key))
            return array();
        $result = array();
        $accept = strtolower($accept);
        foreach (explode(',', $accept) as $accept) {
            if (($pos = strpos($accept, ';')) !== false)
                $accept = substr($accept, 0, $pos);
            $result[] = trim($accept);
        }
        return $result;
    }
    protected function isAccept($find, array $accept) {
        return in_array(strtolower($find), $accept, true);
    }
    /**
     * @deprecated
     */
    public function ip($proxy = null) {
        return $this->getIP($proxy);
    }
    /**
     * @deprecated
     */
    public function referer() {
        return $this->getReferer();
    }
    /**
     * @deprecated
     */
    public function method() {
        return $this->getMethod();
    }
    /**
     * @deprecated
     */
    public function extension() {
        return $this->getExtension();
    }
    /**
     * @deprecated
     */
    public function requestUri() {
        return $this->getRequestURI();
    }
    /**
     * @deprecated
     */
    public function header($key) {
        return $this->getHeader($key);
    }
}

// PHP END