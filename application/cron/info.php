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
		$pingList = array();
		$parser = new Parser();
		$parser->url("https://killserver.github.io/ForCardinal/pingSystem.txt");
		$parser->timeout(1);
		$parsers = $parser->get();
		$parsers = trim($parsers);
		$parsers = str_replace("\r\n", "\n", $parsers);
		$parsers = explode("\n", $parsers);
		$pingList = array_merge($pingList, $parsers);
		execEventRef("ping_list", $pingList);
		for($i=0;$i<sizeof($pingList);$i++) {
			$parser = new Parser();
			$parser->url($pingList[$i]);
			$parser->timeout(3);
			$parser->post(array("server" => str_replace("https", "http", config::Select('default_http_host')), "ip" => (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : ""), "version" => (defined("VERSION") ? VERSION : ""), "online" => $online));
			$parser->get();
			unset($parser);
		}
		if(file_exists(PATH_MEDIA."engine-mail.lock") && !file_exists(PATH_MEDIA."engine-mail-done.lock")) {
			$rev = false;
			if(config::Select("speed_update")===true) {
				$parser = new Parser("https://raw.githubusercontent.com/killserver/cardinal/trunk/version/intversion.txt");
				$parser->timeout(3);
				$parser = $parser->get();
				$rev = true;
			} else {
				$parser = new Parser("https://raw.githubusercontent.com/killserver/cardinal/trunk/version/version.txt");
				$parser->timeout(3);
				$parser = $parser->get();
			}
			if(cardinal_version($parser)) {
				if($rev) {
					$version = new Parser("https://raw.githubusercontent.com/killserver/cardinal/trunk/version/version.txt");
					$version->timeout(3);
					$version = $version->get();
				}
				$res = false;
				try {
					$mail = file_get_contents(PATH_MEDIA."engine-mail.lock");
					$res = nmail($mail, "Появилось обновление движка <b>Cardinal Engine</b>.<br>\nУ Вас стоит версия <b>".VERSION."</b>".($rev ? " rev. <b>".INTVERSION."</b>" : "")."<br>\nНовая версия: <b>".($rev ? $version : $parser)."</b>".($rev ? " rev. <b>".$parser."</b>" : ""), "Cardinal Engine [quick-install]");
				} catch(Exception $ex) {}
				if($res) {
					file_put_contents(PATH_MEDIA."engine-mail-done.lock", "");
				}
			}

		}
	}
	
}
?>