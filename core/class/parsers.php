<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
exit();
}

interface parsers {
	public function uid($server);
	public function duration($server);
	public function ajax($url, $ajax="html");
	public function video($server);
	public function image($server);
	public function get($data);
}

?>