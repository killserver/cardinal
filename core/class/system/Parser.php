<?php
/*
 *
 * @version 1.25.7-a3
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.7-a3
 * Version File: 1
 *
 * 1.1
 * create class parsing for more settings in proccess
 * 1.2
 * fix error in post data
 *
*/

/**
 * Class Parser
 */
class Parser {

	/**
	 * @var string
     */
	private $url = "";
	/**
	 * @var string
     */
	private $agent = "";
	/**
	 * @var string
     */
	private $proxy = "";
	/**
	 * @var string
     */
	private $referrer = "";
	/**
	 * @var string
     */
	private $html = "";
	/**
	 * @var string
     */
	private $cookie_path = "";
	/**
	 * @var bool
     */
	private $cookie = false;
	/**
	 * @var bool
     */
	private $init = false;
	/**
	 * @var bool
     */
	private $header = false;
	/**
	 * @var bool
     */
	private $header_array = false;
	/**
	 * @var bool
     */
	private $header_clear = false;
	/**
	 * @var bool
     */
	private $gzip = false;
	/**
	 * @var bool
     */
	private $error = false;
	/**
	 * @var bool
     */
	private $forceReferrer = false;
	/**
	 * @var bool
     */
	private $display_errors = true;
	/**
	 * @var int
     */
	private $timeout = 3;
	/**
	 * @var array
     */
	private $post = array();
	/**
	 * @var array
     */
	private $headers = array();
	/**
	 * @var array
     */
	private $errors = array();
	/**
	 * @var array
     */
	private $headerList = array();

	/**
	 * Parser constructor.
	 * @param string $url Url for parser
	 * @return Parser
     */
	function Parser($url = "") {
		if(!empty($url)) {
			$this->url = $url;
		}
		return $this;
	}

	/**
	 * Post data for sending
	 * @param array $post Send post data
	 * @return $this Parser
     */
	final function post($post = array()) {
		if(is_array($post) && sizeof($post)>0) {
			$this->post = array_merge($this->post, $post);
		}
		return $this;
	}

	/**
	 * @param bool|true $coo Activate cookie
	 * @param string $coopath Cookie path
	 * @return $this Parser
     */
	final function cookie($coo = true, $coopath = "") {
		if(empty($coopath)) {
			$coopath = rand(0, getrandmax());
		}
		$this->cookie = $coo;
		$this->cookie_path = $coopath;
		return $this;
	}

	/**
	 * Use agent
	 * @param $agent UserAgent
	 * @return $this
     */
	final function agent($agent) {
		$this->agent = $agent;
		return $this;
	}

	/**
	 * @param bool|true $forceReferrer
	 * @return $this
     */
	final function forceReferer($forceReferrer = true) {
		$this->forceReferrer = $forceReferrer;
		return $this;
	}

	/**
	 * @param $referer
	 * @return $this
     */
	final function referer($referer) {
		$this->referrer = $referer;
		return $this;
	}

	/**
	 * @param $proxy
	 * @return $this
     */
	final function proxy($proxy) {
		$this->proxy = $proxy;
		return $this;
	}

	/**
	 * @param $url
	 * @return $this
     */
	final function url($url) {
		$this->url = $url;
		return $this;
	}

	/**
	 * @param bool|true $error
	 * @param bool|true $display_errors
	 * @return $this
     */
	final function error($error = true, $display_errors = true) {
		$this->error = $error;
		$this->display_errors = $display_errors;
		return $this;
	}

	/**
	 * @param bool|true $header
	 * @return $this
     */
	final function header($header = true) {
		$this->header = $header;
		return $this;
	}

	/**
	 * @param array $h
	 * @return $this
     */
	final function headers($h = array()) {
		$this->headerList = array_merge($this->headerList, $h);
		return $this;
	}

	/**
	 * @param bool|true $header_array
	 * @return $this
     */
	final function header_array($header_array = true) {
		$this->header_array = $header_array;
		return $this;
	}

	/**
	 * @param bool|true $headerClear
	 * @return $this
     */
	final function headerClear($headerClear = true) {
		$this->header_clear = $headerClear;
		return $this;
	}

	/**
	 * @param bool|true $gzip
	 * @return $this
     */
	final function gzip($gzip = true) {
		$this->gzip = $gzip;
		return $this;
	}

	/**
	 * @param bool|true $init
	 * @return $this
     */
	final function init($init = true) {
		$this->init = $init;
		return $this;
	}

	/**
	 * @param $timeout
	 * @return $this
     */
	final function timeout($timeout) {
		$this->timeout = $timeout;
		return $this;
	}

	/**
	 * @return array
     */
	final function getHeaders() {
		return $this->headers;
	}

	/**
	 * @return array
     */
	final function getErrors() {
		return $this->errors;
	}

	/**
	 * @param string $url
	 * @return array|bool|mixed|string|$this
     */
	final function get($url = "") {
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
//���������� ��� ����� � ��������� ��������, ���� �� ������, ����� PHP �������� ������ ������, ���� ������������ HTTP-��� ����� �������� ���� 300. �� ��������� �������� ������������ ��������� � �������������� ����.
	//curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	//������������� �������� referer - ����� ��������� �������� ��������
		if(is_bool($this->cookie) && $this->cookie && !empty($this->cookie_path)) {
			curl_setopt($ch, CURLOPT_COOKIEJAR, ROOT_PATH."core".DS."cache".DS.$this->cookie_path.".txt");
			curl_setopt($ch, CURLOPT_COOKIEFILE, ROOT_PATH."core".DS."cache".DS.$this->cookie_path.".txt");
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
		if(!empty($this->referrer)) {
			curl_setopt($ch, CURLOPT_REFERER, $this->referrer);
		} else {
			curl_setopt($ch, CURLOPT_REFERER, $url);
		}
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		if(!$this->header) {
			curl_setopt($ch, CURLOPT_HEADER, 0);
		} else {
			curl_setopt($ch, CURLOPT_HEADER, 1);
		}
		if(isset($this->headerList) && is_array($this->headerList) && sizeof($this->headerList)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headerList);
		}
		if(is_array($this->post) && sizeof($this->post)>0) {
			$post = array();
			foreach($this->post as $k => $v) {
				if(!empty($k) && !empty($v)) {
					$post[] = $k."=".$v;
				} else if(!empty($k)) {
					$post[] = $k."=";
				} else if(!empty($v)) {
					$post[] = $v;
				}
			}
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, implode("&", $post));
		}
		if(strtolower(substr($url,0,5))=='https') {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}
		if(!config::Select("hosting") || $this->forceReferrer) {
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
					$ex = array("HTTP", str_replace(array("HTTP", "/", "1.0", "1.1", "2.0"), "", $exp[$i]));
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
			return $this;
		}
	}

	/**
	 * @return string
     */
	final function getHTML() {
		return $this->html;
	}
	
}

?>