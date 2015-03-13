<?php
namespace util;

class RESTful {
    protected static $allowed_http_methods = array('get', 'delete', 'post', 'put', 'options', 'patch', 'head');
    protected static $_supported_formats = array(
        'xml'           => 'application/xml',
        'json'          => 'application/json',
        'jsonp'         => 'application/javascript',
        'serialized'    => 'application/vnd.php.serialized',
        'php'           => 'text/plain',
        'html'          => 'text/html',
        'csv'           => 'application/csv'
    );
    /*
     * Detect SSL use
     *
     * Detect whether SSL is being used or not
     */
    protected static function _detect_ssl()
    {
            // $_SERVER['HTTPS'] (http://php.net/manual/en/reserved.variables.server.php)
            // Set to a non-empty value if the script was queried through the HTTPS protocol
            return (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']));
    }
    /**
     * 
     */
    public static function get($resource, $data = array(), $sid = '', $headers = array()) {

    }
    public static function post($resource, $data = array(), $sid = '', $headers = array()) {

    }
    public static function put($resource, $data = array(), $sid = '', $headers = array()) {

    }
    public static function delete($resource, $data = array(), $sid = '', $headers = array()) {

    }
    public static function jsonCall($resource, $state, $params = array()) {

    }
    public static function request($protocal, $host, $resource, $representation = '.json', $state = 'GET', $params = array(), $upload = array()) {
        $state = strtoupper($state);
        $ch = curl_init();        
        //curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-HTTP-Method-Override: $state"));//
        switch ($state) {
            case 'GET':
                curl_setopt($ch, CURLOPT_URL, $protocal.'://'.$host.$resource.$representation.'?'.http_build_query($params));
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_URL, $protocal.'://'.$host.$resource.$representation);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                if(!empty($upload) && is_array($upload)) {
                    foreach ($upload as $key => $file) {
                        $upload[$key] = strpos($file, '@') === 0 ? '@'. realpath(substr($file, 1)) : '@'. realpath($file);
                    }
                    $params = array_merge($params, $upload);
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_URL, $protocal.'://'.$host.$resource.$representation);
                //curl_setopt($ch, CURLOPT_PUT, true);
                if(!empty($upload) && is_array($upload)) {
                    foreach ($upload as $key => $file) {
                        $upload[$key] = strpos($file, '@') === 0 ? '@'. realpath(substr($file, 1)) : '@'. realpath($file);
                    }
                    $params = array_merge($params, $upload);
                    //上传文件时自动转为POST
                    //curl_setopt($ch, CURLOPT_POST, TRUE);
                } else {
                    //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $state);
                }
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $state);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_URL, $protocal.'://'.$host.$resource.$representation);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $state);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
            default:
                curl_setopt($ch, CURLOPT_URL, $protocal.'://'.$host.$resource.$representation);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $state);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
        }

        $rsp = curl_exec($ch);
        curl_close($ch);
        #print_r($rsp);
        $ret = json_decode($rsp, true);
        //print_r($ret);
        //exit();
        if ($ret == null) {
            $ret = array('rsp' => 0, 'rsp_text'=> $rsp);
        }
        return array($ret, $params, $state);
    }
}