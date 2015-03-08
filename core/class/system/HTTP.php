<?PHP
/*
*
* Version Engine: 1.25.3
* Version File: 0.4
*
* 0.4
* add return header last modified in page for client
*
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

final class HTTP {
	
	public function HTTP() {
		
	}
	
	public static function getip() {
		if(getenv('HTTP_X_FORWARDED_FOR')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('HTTP_CLIENT_IP')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} else {
			$ip = getenv('REMOTE_ADDR');
		}
		if(strpos($ip, ",")!==false) {
			$ips = explode(",", $ip);
			$ip = current($ips);
			unset($ips);
		}
	return $ip;
	}
	
	public static function lastmod($LastModified_unix) {
		$LastModified = gmdate("D, d M Y H:i:s \G\M\T", $LastModified_unix);
		$IfModifiedSince = false;
		if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
			$IfModifiedSince = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
		if(!is_bool($IfModifiedSince) && $IfModifiedSince >= $LastModified_unix) {
			header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
			return;
		}
		header('Last-Modified: '.$LastModified);
		header('Expires: '.$LastModified);
	}
	
	public static function echos($echo = null) {
		if(!empty($echo)) {
			echo $echo;
			unset($echo);
		}
		if(!defined("ALL_GOOD")) {
			define("ALL_GOOD", true);
		}
	}
	
}

?>