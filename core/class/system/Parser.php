<?php
/*
 *
 * @version 2015-09-30 13:30:44 1.25.6-rc3
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc3
 * Version File: 1
 *
 * 1.1
 * create class parsing for more settings in proccess
 *
*/
class Parser {
	
	private $url = null;
	private $agent = null;
	private $proxy = null;
	private $referer = null;
	private $html = null;
	private $cookie_path = null;
	private $cookie = false;
	private $init = false;
	private $header = false;
	private $header_array = false;
	private $header_clear = false;
	private $gzip = false;
	private $error = false;
	private $display_errors = true;
	private $timeout = 3;
	private $post = array();
	private $headers = array();
	private $errors = array();
	
	function Parser($url=null) {
		if(!empty($url)) {
			$this->url = $url;
		}
	}
	
	function post($post = array()) {
		if(is_array($post) && sizeof($post)>0) {
			$this->post = array_merge($this->post, $post);
		}
	}
	
	function cookie($coo = true, $coopath = null) {
		if(empty($coopath)) {
			$coopath = rand(0, getrandmax());
		}
		$this->cookie = $coo;
		$this->cookie_path = $coopath;
	}
	
	function agent($agent) {
		$this->agent = $agent;
	}
	
	function referer($referer) {
		$this->referer = $referer;
	}
	
	function proxy($proxy) {
		$this->proxy = $proxy;
	}
	
	function url($url) {
		$this->url = $url;
	}
	
	function error($error = true, $display_errors = true) {
		$this->error = $error;
		$this->display_errors = $display_errors;
	}
	
	function header($header = true) {
		$this->header = $header;
	}
	
	function header_array($header_array = true) {
		$this->header_array = $header_array;
	}
	
	function headerClear($headerClear = true) {
		$this->header_clear = $headerClear;
	}
	
	function gzip($gzip = true) {
		$this->gzip = $gzip;
	}
	
	function init($init = true) {
		$this->init = $init;
	}
	
	function timeout($timeout) {
		$this->timeout = $timeout;
	}
	
	function getHeaders() {
		return $this->headers;
	}
	
	function getErrors() {
		return $this->errors;
	}
	
	function get($url = null) {
		if(empty($url)) {
			$url = $this->url;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		if(!empty($this->agent)) {
			curl_setopt($ch, CURLOPT_USERAGENT, $this->agent);
		} else {
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:14.0) Gecko/20100101 Firefox/14.0.1");
		}
//Установите эту опцию в ненулевое значение, если вы хотите, чтобы PHP завершал работу скрыто, если возвращаемый HTTP-код имеет значение выше 300. По умолчанию страница возвращается нормально с игнорированием кода.
	//curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	//Устанавливаем значение referer - адрес последней активной страницы
		if(is_bool($this->cookie) && $this->cookie && !empty($this->cookie_path)) {
			curl_setopt($ch, CURLOPT_COOKIEJAR, ROOT_PATH."core/cache/".$this->cookie_path.".txt");
			curl_setopt($ch, CURLOPT_COOKIEFILE, ROOT_PATH."core/cache/".$this->cookie_path.".txt");
		}
		if(!is_bool($this->cookie) && !empty($this->cookie)) {
			if(is_array($this->cookie)) {
				$nam = array_keys($this->cookie);
				$val = array_values($this->cookie);
				$this->cookie = "";
				for($i=0;$i<sizeof($nam);$i++) {
					$this->cookie .= $nam[$i]."=".$val[$i]."; ";
				}
				unset($nam, $val);
			}
			curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
		}
		if(!empty($this->referer)) {
			curl_setopt($ch, CURLOPT_REFERER, $this->referer);
		} else {
			curl_setopt($ch, CURLOPT_REFERER, $url);
		}
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		if(!$this->header) {
			curl_setopt($ch, CURLOPT_HEADER, 0);
		} else {
			curl_setopt($ch, CURLOPT_HEADER, 1);
		}
		if(is_array($this->post) && sizeof($this->post)>0) {
			$post = array();
			foreach($this->post as $k => $v) {
				if(!empty($k) && !empty($v)) {
					$post[] = $k."=".$v;
				} else if(!empty($k)) {
					$post[] = $k."=";
				}
			}
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		if (strtolower(substr($url,0,5))=='https'){
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}
		if(!config::Select("hosting")) {
			curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if($this->gzip) {
			curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
		}
		if(!empty($this->proxy)) {
			curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
		}
		if(!$this->error && ($this->html = curl_exec($ch)) === false) {
			$this->html = curl_error($ch);
		}
		if($this->header && $this->header_array && strpos($this->html, "\r\n\r\n")!==false) {
			$header = substr($this->html, 0, strpos($this->html, "\r\n\r\n"));
			$this->html = str_replace($header."\r\n\r\n", "", $this->html);
			$exp = explode("\n", $header);
			for($i=0;$i<sizeof($exp);$i++) {
				if($this->header_clear) {
					if(strpos($exp[$i], "HTTP/")!==false) {
						continue;
					}
					if(strpos($exp[$i], "Server")!==false) {
						continue;
					}
				}
				if(strpos($exp[$i], "HTTP/")!==false) {
					$ex = array("HTTP", str_replace("HTTP", "", $exp[$i]));
				} else {
					$ex = explode(":", $exp[$i]);
				}
				$this->headers[$ex[0]] = trim($ex[1]);
			}
		}
		$this->errors = curl_error($ch);
		curl_close($ch);
		if(!$this->init) {
			if($this->error && $this->display_errors) {
				return array("html" => $this->html, "error" => $this->errors);
			} else {
				return $this->html;
			}
		} else {
			return true;
		}
	}
	
	function getHTML() {
		return $this->html;
	}
	
}

?>