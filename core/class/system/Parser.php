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


class FileParser {
	private $name;
	private $mime;
	private $content;

	public function __construct($name = "", $mime = "", $content = "") {
		if(empty($content) && !empty($name) && is_readable($name)) {
			$info = pathinfo($name);
			if(!empty($info['basename']) && is_readable($name)) {
				$this->name = $info['basename'];
				$this->mime = mime_content_type($name);
				$content = file_get_contents($name);
				if($content !== false) {
					$this->content = $content;
				} else {
					throw new Exception('Don`t get content - "'.$name.'"');
				}
			} else {
				$this->name = $name;
				$this->mime = "application/octet-stream";
				$this->content = "";
			}
		} else {
			$this->name = $name;
			if(empty($mime) && !empty($name)) {
				$mime = mime_content_type($name);
			} else {
				$mime = "application/octet-stream";
			}
			$this->mime = $mime;
			$this->content = $content;
		}
	}

	public function Name() {
		return $this->name;
	}
	public function Mime() {
		return $this->mime;
	}
	public function Content() {
		return $this->content;
	}
}

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
	private $proxy_type = '';
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
	private $cookie_fullpath = "";
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

	private $customRequest = "";
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

	private $addCookie = array();

	private $parseCookie = false;
	private $ch = null;
	private $base_url = "";
	private $customRequestData = array();

	/**
	 * Parser constructor.
	 * @param string $url Url for parser
	 * @return Parser
     */
	final public function __construct($url = "") {
		if(!empty($url)) {
			$this->url = $url;
		}
		$this->ch = curl_init();
		return $this;
	}

	final public function getCurl() {
		return $this->ch;
	}

	final public function addCookie($k, $v) {
		$this->addCookie[] = $k."=".$v;
	}

	final public function base($url) {
		$this->base_url = $url;
		return $this;
	}

	final public function multipartFile($name = "", $mime = "", $content = "") {
		return new FileParser($name, $mime, $content);
	}

	/**
	 * Post data for sending
	 * @param array $post Send post data
	 * @return $this Parser
     */
	final public function post($post = array()) {
		if(is_array($post) && sizeof($post)>0) {
			$this->post = array_merge($this->post, $post);
		} else if(is_string($post) && !empty($post) && strlen($post)>0) {
			$this->post = $post;
		}
		return $this;
	}

	final public function postMultipart($post, $delimiter = 'default') {
		if(is_array($post) && sizeof($post)>0) {
			if($delimiter==='default') {
				$delimiter = "----WebKitFormBoundary".uniqid();
			}
			$ret = '';
			foreach($post as $name => $val) {
				$ret .= '--'.$delimiter."\r\n";
				$ret .= 'Content-Disposition: form-data; name="' . $name . '"';
				if($val instanceof FileParser) {
					$file = $val->Name();
					$mime = $val->Mime();
					$cont = $val->Content();
					$ret .= '; filename="'.$file.'"'."\r\n";
					$ret .= 'Content-Type: '.$mime."\r\n\r\n";
					$ret .= $cont."\r\n";
				} else {
					$ret .= "\r\n\r\n".($val)."\r\n";
				}
			}
			$ret .= "--".$delimiter."--\r\n";
			$this->headerList = array_merge($this->headerList, array("Content-Length: ".strlen($ret), "Content-Type: multipart/form-data; boundary=".$delimiter));
			$this->post = $ret;
		}
		return $this;
	}

	/**
	 * Set activate cookie and set name file for save cookies
	 * @param bool|true $coo Activate cookie
	 * @param string $coopath Cookie path
	 * @return $this Parser
     */
	final public function cookie($coo = true, $coopath = "", $cookie_fullpath = "") {
		if(empty($coopath)) {
			$coopath = rand(0, getrandmax());
		}
		$this->cookie = $coo;
		$this->cookie_path = $coopath;
		$this->cookie_fullpath = $cookie_fullpath;
		return $this;
	}

	/**
	 * Set user agent
	 * @param $agent UserAgent
	 * @return $this Parser
     */
	final public function agent($agent) {
		$this->agent = $agent;
		return $this;
	}

	/**
	 * Force switch referrer
	 * @param bool|true $forceReferrer Switch referrer
	 * @return $this Parser
     */
	final public function forceReferer($forceReferrer = true) {
		$this->forceReferrer = $forceReferrer;
		return $this;
	}

	/**
	 * Link referer where come
	 * @param $referer Needed link
	 * @return $this Parser
     */
	final public function referer($referer) {
		$this->referrer = (substr($referer, 0, 1)=="/" ? $this->base_url : "").ltrim($referer, "/");
		return $this;
	}

	/**
	 * Activation proxy
	 * @param $proxy IP proxy
	 * @return $this Parser
     */
	final public function proxy($proxy, $proxy_type = '') {
		$this->proxy = $proxy;
		if(!empty($proxy_type)) {
			$this->proxy_type = $proxy_type;
		}
		return $this;
	}

	/**
	 * Link for parser
	 * @param $url Link
	 * @return $this Parser
     */
	final public function url($url) {
		$this->url = (substr($url, 0, 1)=="/" ? $this->base_url : "").ltrim($url, "/");
		return $this;
	}

	/**
	 * Debug in parsing
	 * @param bool|true $error Activation debug
	 * @param bool|true $display_errors Activation view errors
	 * @return $this Parser
     */
	final public function error($error = true, $display_errors = true) {
		$this->error = $error;
		$this->display_errors = $display_errors;
		return $this;
	}

	/**
	 * Activation get header
	 * @param bool|true $header Activation parsing header
	 * @return $this Parser
     */
	final public function header($header = true) {
		$this->header = $header;
		return $this;
	}

	/**
	 * Send headers in proccess parsing
	 * @param array $h Array headers
	 * @return $this Parser
     */
	final public function headers($h = array()) {
		if(is_string($h) && strpos($h, "\n")!==false) {
			$h = trim($h);
			$h = explode("\n", $h);
			$h = array_filter($h);
		} else if(is_string($h)) {
			$h = array($h);
		}
		$this->headerList = array_merge($this->headerList, $h);
		return $this;
	}

	/**
	 * Activation parsing headers. Can get headers as array
	 * @param bool|true $header_array Switch headers
	 * @return $this Parser
     */
	final public function header_array($header_array = true) {
		$this->header_array = $header_array;
		return $this;
	}

	/**
	 * Delete HTTP and Server headers
	 * @param bool|true $headerClear Switch clear headers
	 * @return $this Parser
     */
	final public function headerClear($headerClear = true) {
		$this->header_clear = $headerClear;
		return $this;
	}

	public function postJSON($arr) {
		$this->post(json_encode($arr));
		return $this;
	}

	public function clear() {
		$this->customRequest = '';
		$this->headerList = array();
		return $this;
	}

	/**
	 * Activation gzip parsing
	 * @param bool|true $gzip Switch gzip
	 * @return $this Parser
     */
	final public function gzip($gzip = true) {
		$this->gzip = $gzip;
		return $this;
	}

	final public function customRequest($customRequest = "GET") {
		$this->customRequest = $customRequest;
		return $this;
	}

	/**
	 * Switch parsing at once or after calling
	 * @param bool|true $init Switch initialization
	 * @return $this Parser
     */
	final public function init($init = true) {
		$this->init = $init;
		return $this;
	}

	/**
	 * Timeout parsing
	 * @param $timeout Time in seconds
	 * @return $this Parser
     */
	final public function timeout($timeout) {
		$this->timeout = $timeout;
		return $this;
	}

	/**
	 * Get header list
	 * @return array Array headers
     */
	final public function getHeaders() {
		return $this->headers;
	}

	/**
	 * Get error list
	 * @return array Get errors
     */
	final public function getErrors() {
		return $this->errors;
	}

	/**
	 * Get response code
	 * @param $header Full request
	 * @return integer Code response
     */
	private function getResponseCode($header) {
		return (int) substr($header, 9, 3);
	}

	final public function parseCookie($parse = true) {
		$this->parseCookie = $parse;
	}

	final public function customRequestData($customRequestData) {
		$this->customRequestData = $customRequestData;
	}

	/**
	 * Initialization parsing
	 * @param string $url Need link and start, or initialization parsing
	 * @return array|bool|string|$this Result parsing
     */
	final public function get($url = "", $sss = false) {
		if(empty($url)) {
			$url = $this->url;
		} else {
			$url = (substr($url, 0, 1)=="/" ? $this->base_url : "").ltrim($url, "/");
		}
		if(empty($url)) {
			$url = $this->base_url;
		}
		$this->url = (!empty($this->base_url) ? str_replace($this->base_url, '', $url) : $url);
		curl_setopt($this->ch, CURLOPT_URL, $url);
		if(!empty($this->agent)) {
			curl_setopt($this->ch, CURLOPT_USERAGENT, $this->agent);
		} else {
			curl_setopt($this->ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:14.0) Gecko/20100101 Firefox/14.0.1");
		}
		//Установите эту опцию в ненулевое значение, если вы хотите, чтобы PHP завершал работу скрыто, если возвращаемый HTTP-код имеет значение выше 300. По умолчанию страница возвращается нормально с игнорированием кода.
		//curl_setopt($this->ch, CURLOPT_FAILONERROR, 1);
		//Устанавливаем значение referer - адрес последней активной страницы
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
			curl_setopt($this->ch, CURLOPT_COOKIE, $this->cookie);
		}
		if($this->cookie && (!empty($this->cookie_path) || !empty($this->cookie_fullpath))) {
			curl_setopt($this->ch, CURLOPT_COOKIEJAR, (!empty($this->cookie_fullpath) ? $this->cookie_fullpath : (defined("PATH_CACHE") ? PATH_CACHE.$this->cookie_path.".txt" : $this->cookie_path)));
			curl_setopt($this->ch, CURLOPT_COOKIEFILE, (!empty($this->cookie_fullpath) ? $this->cookie_fullpath : (defined("PATH_CACHE") ? PATH_CACHE.$this->cookie_path.".txt" : $this->cookie_path)));
		}
		if(sizeof($this->addCookie)>0) {
			curl_setopt($this->ch, CURLOPT_COOKIE, implode("&", $this->addCookie));
			$this->addCookie = array();
		}
		if(!empty($this->referrer)) {
			curl_setopt($this->ch, CURLOPT_REFERER, $this->referrer);
			$this->referrer = "";
		} else {
			curl_setopt($this->ch, CURLOPT_REFERER, $url);
		}
		curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
		if(!$this->header) {
			curl_setopt($this->ch, CURLOPT_HEADER, 0);
		} else {
			curl_setopt($this->ch, CURLOPT_HEADER, 1);
		}
		if(!empty($this->customRequestData)) {
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->customRequestData);
		}
		if(isset($this->headerList) && is_array($this->headerList) && sizeof($this->headerList)) {
			$headers = array();
			foreach($this->headerList as $k => $v) {
				if(!is_numeric($k)) {
					$headers[] = $k.": ".$v;
				} else {
					$headers[] = $v;
				}
			}
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
		}
		if(is_array($this->post) && sizeof($this->post)>0) {
			$post = array();
			foreach($this->post as $k => $v) {
				if($k != NULL && $v != NULL) {
					$post[] = $k."=".$v;
				} else if($k != NULL) {
					$post[] = $k."=";
				} else if($v != NULL) {
					$post[] = $v;
				}
			}
			curl_setopt($this->ch, CURLOPT_POST, 1);
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, implode("&", $post));
			$this->post = array();
		} else if(is_string($this->post) && !empty($this->post) && strlen($this->post)>0) {
			curl_setopt($this->ch, CURLOPT_POST, 1);
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->post);
			$this->post = array();
		}
		if(strtolower(substr($url,0,5))=='https') {
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
			if(defined("CURLOPT_SSL_VERIFYSTATUS")) {
				curl_setopt($this->ch, CURLOPT_SSL_VERIFYSTATUS, 0);
			}
		}
		if(defined("CURLOPT_NOSIGNAL")) {
			curl_setopt($this->ch, CURLOPT_NOSIGNAL, 1);
		}
		if(class_exists("config") && method_exists("config", "Select")) {
			if(!config::Select("hosting") || $this->forceReferrer) {
				curl_setopt($this->ch, CURLOPT_AUTOREFERER, 1);
				curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
			}
		} else if($this->forceReferrer) {
			curl_setopt($this->ch, CURLOPT_AUTOREFERER, 1);
			curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
		}
		curl_setopt($this->ch, CURLOPT_BINARYTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		if(!empty($this->customRequest)) {
			curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $this->customRequest);
		}
		if($this->gzip) {
			curl_setopt($this->ch, CURLOPT_ENCODING, 'gzip');
		}
		if(!empty($this->proxy)) {
			curl_setopt($this->ch, CURLOPT_PROXY, $this->proxy);
		}
		if(!empty($this->proxy_type)) {
			curl_setopt($this->ch, CURLOPT_PROXYTYPE, 7);
		}
		if(!$this->error && ($this->html = curl_exec($this->ch)) === false) {
			$this->html = curl_error($this->ch);
		}
		if($this->header && strpos($this->html, "\r\n\r\n")!==false) {
			$header = substr($this->html, 0, strpos($this->html, "\r\n\r\n"));
			$this->html = str_replace($header."\r\n\r\n", "", $this->html);
			if($this->header_array) {
				$this->headers = array();
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
						$ex = explode(":", $exp[$i], 2);
					}
					if(isset($this->headers[$ex[0]])) {
						if(!is_array($this->headers[$ex[0]])) {
							$t = $this->headers[$ex[0]];
							$this->headers[$ex[0]] = array();
							$this->headers[$ex[0]][] = $t;
						}
						$this->headers[$ex[0]][] = trim($ex[1]);
					} else {
						$this->headers[$ex[0]] = trim($ex[1]);
					}
				}
				if($this->parseCookie && isset($this->headers["Set-Cookie"])) {
					$this->headers["Set-Cookie"] = implode("; ", $this->headers["Set-Cookie"]);
					$this->headers["Set-Cookie"] = explode(";", $this->headers["Set-Cookie"], 2);
					$this->headers["Set-Cookie"] = array_map("trim", $this->headers["Set-Cookie"]);
					$c = array();
					for($i=0;$i<sizeof($this->headers["Set-Cookie"]);$i++) {
						$exp = explode("=", $this->headers["Set-Cookie"][$i]);
						if(isset($c[$exp[0]])) {
                            $t = $c[$exp[0]];
                            if(!is_array($c[$exp[0]])) {
								$c[$exp[0]] = array();
							}
							$c[$exp[0]][] = $t;
						} else {
							$c[$exp[0]] = $exp[1];
						}
					}
					$this->headers["Set-Cookie"] = $c;
				}
			} else {
				$this->headers = $header;
			}
		}
		$this->errors = curl_error($this->ch);
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
	
	final public function __toString() {
		return $this->html;
	}

	/**
	 * Get html code parsing
	 * @return string Parsed html
     */
	final public function getHTML() {
		return $this->html;
	}

	function __destruct() {
        if(!is_null($this->ch)) {
			@curl_close($this->ch);
        }
	}
	
}

?>