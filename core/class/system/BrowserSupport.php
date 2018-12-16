<?php

class BrowserSupport {

	final public static function webp() {
		return ($d = HTTP::getServer('HTTP_ACCEPT', false)) && (strstr($d, 'image/webp') !== false);
	}

	final public static function jp2() {
		return ($d = HTTP::getServer('HTTP_USER_AGENT', false)) && (strstr($d, 'Safari') !== false) && (preg_match('/Version\/(?P<version>[0-9]{1})/', $d, $parameters)) && ($parameters['version'] >= 6);
	}

	final public static function jxr() {
		return ($d = HTTP::getServer('HTTP_ACCEPT', false)) && (strstr($d, 'image/jxr') !== false);
	}

	final public static function gzip() {
		if(!function_exists('ob_gzhandler') || ini_get('zlib.output_compression')!=="" || HTTP::getServer('HTTP_ACCEPT_ENCODING', false)===false) {
			return false;
		}
		if(strpos(HTTP::getServer('HTTP_ACCEPT_ENCODING'), 'x-gzip') !== false) {
			return "x-gzip";
		}
		if(strpos(HTTP::getServer('HTTP_ACCEPT_ENCODING'), 'gzip') !== false) {
			return "gzip";
		}
		return false;
	}

}