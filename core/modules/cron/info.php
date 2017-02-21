<?php
class Info {
	
	function __construct() {
		$parser = new Parser();
		$parser->url("https://killserver.github.io/ForCardinal/pingSystem.txt");
		$parser->timeout(1);
		$parsers = $parser->get();
		$parser = new Parser();
		$parser->url($parsers);
		$parser->timeout(3);
		$parser->post(array("server" => str_replace("https", "http", config::Select('default_http_host')), "ip" => (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : "")));
		$parser->get();
		unset($parser);
	}
	
}
?>