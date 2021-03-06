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
	 * Set activate cookie and set name file for save cookies
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
	 * Set user agent
	 * @param $agent UserAgent
	 * @return $this Parser
     */
	final function agent($agent) {
		$this->agent = $agent;
		return $this;
	}

	/**
	 * Force switch referrer
	 * @param bool|true $forceReferrer Switch referrer
	 * @return $this Parser
     */
	final function forceReferer($forceReferrer = true) {
		$this->forceReferrer = $forceReferrer;
		return $this;
	}

	/**
	 * Link referer where come
	 * @param $referer Needed link
	 * @return $this Parser
     */
	final function referer($referer) {
		$this->referrer = $referer;
		return $this;
	}

	/**
	 * Activation proxy
	 * @param $proxy IP proxy
	 * @return $this Parser
     */
	final function proxy($proxy) {
		$this->proxy = $proxy;
		return $this;
	}

	/**
	 * Link for parser
	 * @param $url Link
	 * @return $this Parser
     */
	final function url($url) {
		$this->url = $url;
		return $this;
	}

	/**
	 * Debug in parsing
	 * @param bool|true $error Activation debug
	 * @param bool|true $display_errors Activation view errors
	 * @return $this Parser
     */
	final function error($error = true, $display_errors = true) {
		$this->error = $error;
		$this->display_errors = $display_errors;
		return $this;
	}

	/**
	 * Activation get header
	 * @param bool|true $header Activation parsing header
	 * @return $this Parser
     */
	final function header($header = true) {
		$this->header = $header;
		return $this;
	}

	/**
	 * Send headers in proccess parsing
	 * @param array $h Array headers
	 * @return $this Parser
     */
	final function headers($h = array()) {
		$this->headerList = array_merge($this->headerList, $h);
		return $this;
	}

	/**
	 * Activation parsing headers. Can get headers as array
	 * @param bool|true $header_array Switch headers
	 * @return $this Parser
     */
	final function header_array($header_array = true) {
		$this->header_array = $header_array;
		return $this;
	}

	/**
	 * Delete HTTP and Server headers
	 * @param bool|true $headerClear Switch clear headers
	 * @return $this Parser
     */
	final function headerClear($headerClear = true) {
		$this->header_clear = $headerClear;
		return $this;
	}

	/**
	 * Activation gzip parsing
	 * @param bool|true $gzip Switch gzip
	 * @return $this Parser
     */
	final function gzip($gzip = true) {
		$this->gzip = $gzip;
		return $this;
	}

	/**
	 * Switch parsing at once or after calling
	 * @param bool|true $init Switch initialization
	 * @return $this Parser
     */
	final function init($init = true) {
		$this->init = $init;
		return $this;
	}

	/**
	 * Timeout parsing
	 * @param $timeout Time in seconds
	 * @return $this Parser
     */
	final function timeout($timeout) {
		$this->timeout = $timeout;
		return $this;
	}

	/**
	 * Get header list
	 * @return array Array headers
     */
	final function getHeaders() {
		return $this->headers;
	}

	/**
	 * Get error list
	 * @return array Get errors
     */
	final function getErrors() {
		return $this->errors;
	}

	/**
	 * Get response code
	 * @param $header Full request
	 * @return $this Code response
     */
	final private function getResponseCode($header) {
		return (int) substr($header, 9, 3);
	}

	/**
	 * Initialization parsing
	 * @param string $url Need link and start, or initialization parsing
	 * @return array|bool|string|$this Result parsing
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
//Установите эту опцию в ненулевое значение, если вы хотите, чтобы PHP завершал работу скрыто, если возвращаемый HTTP-код имеет значение выше 300. По умолчанию страница возвращается нормально с игнорированием кода.
	//curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	//Устанавливаем значение referer - адрес последней активной страницы
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
					if(strpos($exp[$i], "1.0")!==false) {
						$this->headers['HTTPVersion'] = "1.0";
					}
					if(strpos($exp[$i], "1.1")!==false) {
						$this->headers['HTTPVersion'] = "1.1";
					}
					if(strpos($exp[$i], "2.0")!==false) {
						$this->headers['HTTPVersion'] = "2.0";
					}
					$this->headers['code'] = $this->getResponseCode($exp[$i]);
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
	 * Get html code parsing
	 * @return string Parsed html
     */
	final function getHTML() {
		return $this->html;
	}
	
}

?>