<?php
namespace util;

class RESTful {
	/**
	 * 
	 */
	public function get($resource, $data = array(), $sid = '', $headers = array()) {

	}
	public function post($resource, $data = array(), $sid = '', $headers = array()) {

	}
	public function put($resource, $data = array(), $sid = '', $headers = array()) {

	}
	public function delete($resource, $data = array(), $sid = '', $headers = array()) {

	}
	public function jsonCall($resource, $state, $params = array()) {

	}
	public function curl($resource, $representation, $state = 'GET', $params = array()) {
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $this->server_url);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $state);
		$rsp = curl_exec($ch);
                
		curl_close($ch);
		#print_r($rsp);
        $ret = json_decode($rsp, $this->isObject);
		//print_r($ret);
		//exit();
        if(isset($ret->msg) && $ret->data ==""){
            if(is_array($ret->msg) || is_object($ret->msg)) {
            	foreach ($ret->msg as $k => $v){
            		$ret->msg = $v;
            	}
            }
        }
		if ($ret == null) {
			$ret = array(
				'rsp' => 0,
				'rsp_text'=> $rsp);
		}
		return $ret;
	}
}