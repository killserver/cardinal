<?php
class Info {
	
	function __construct() {
		$parser = new Parser();
		$parser->url("http://killer.pp.ua/index.php/reg_site");
		$parser->post(array("server" => str_replace("https", "http", config::Select('default_http_host')), "ip" => (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : "")));
		$parser->get();
		unset($parser);
	}
	
}
?>