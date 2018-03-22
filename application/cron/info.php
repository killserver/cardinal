<?php
class Info {
	
	function __construct() {
		if(defined("CLOSE_FUNCTION") && strpos(CLOSE_FUNCTION, "curl")!==false) {
			return;
		}
		$online = 0;
		if(is_writeable(PATH_CACHE_USERDATA) && file_exists(PATH_CACHE_USERDATA."userOnline.txt")) {
			$online = file_get_contents(PATH_CACHE_USERDATA."userOnline.txt");
		}
		$parser = new Parser();
		$parser->url("https://killserver.github.io/ForCardinal/pingSystem.txt");
		$parser->timeout(1);
		$parsers = $parser->get();
		$parser = new Parser();
		$parser->url($parsers);
		$parser->timeout(3);
		$parser->post(array("server" => str_replace("https", "http", config::Select('default_http_host')), "ip" => (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : ""), "version" => (defined("VERSION") ? VERSION : ""), "online" => $online));
		$parser->get();
		unset($parser);
	}
	
}
?>