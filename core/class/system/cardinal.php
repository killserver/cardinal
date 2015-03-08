<?php
/*
*
* Version Engine: 1.25.3
* Version File: 2
*
* 2.4
* add support XXX category
*
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

final class cardinal {

	private $config;
	public function cardinal() {
		if(!$this->robots($_SERVER["HTTP_USER_AGENT"])) {
			define("IS_BOT", false);
		} else {
			define("IS_BOT", true);
		}
		if(isset($_COOKIE['plus18'])) {
			define("IS_XXX", "true");
		}
		$otime = config::select("cardinal_time");
		if($otime <= time()-12*60*60) {
			self::add();
			self::stats();
		}
	}

	public static function nstrlen($text) {
		if(function_exists("mb_strlen")) {
			return mb_strlen($text);
		} elseif(function_exists("iconv_strlen")) {
			return iconv_strlen($text);
		} else {
			return strlen($text);
		}
	}
	
	private function robots($useragent) {
	global $config;
		$arr = array();
		$pcre = array_keys(config::Select('robots'));
		$dats = array_values(config::Select('robots'));
		for($i=0;$i<sizeof($pcre);$i++) {
			$arr["#.*".$pcre[$i].".*#si"] = $dats[$i];
		}
		$result = preg_replace(array_keys($arr), $arr, $useragent);
		return $result == $useragent ? false : $result;
	}
	
	public static function set_eighteen() {
		if(!isset($_COOKIE['plus18'])) {
			setcookie("plus18", "1", (time()+(60*24*60*60)), "/", ".".config::Select("default_http_hostname"), false, true);
		} else {
			setcookie("plus18", "", (time()-(120*24*60*60)), "/", ".".config::Select("default_http_hostname"), false, true);
		}
	}
	
	public static function get_eighteen() {
		if(isset($_COOKIE['plus18'])) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function view_eighteen() {
		if(!cardinal::get_eighteen()) {
			templates::assign_vars(array(
				"title" => "{L_alert}",
				"error" => "{L_alert_up_eighteen}",
			));
			$view = templates::complited_assing_vars("info");
			templates::complited($view);
			templates::display();
		}
	}

	private static function add() {
		$sql = db::doquery("SELECT id, name, data, video_movie, descr, images, subtitles, `time`, added, moder, cat, album FROM cardinal_movie WHERE activ = \"yes\" ORDER BY id ASC LIMIT ".mrand(1, 3), true);
		$last_add = 1;
		$conv = array();
		while($row = db::fetch_array($sql)) {
			$rand = rand(0, 2);
			$name = str_replace("\"", "\\\"", $row['name']);
			$descr = str_replace("\"", "\\\"", $row['descr']);
			$name_id = cut(md5($row['name']), 10);

			db::doquery("INSERT INTO shablons SET albums = \"".cut(md5(others_video($name)), 15)."\", name = \"".$name."\", descr = \"".$descr."\", ids = \"".$name_id."\" ON DUPLICATE KEY UPDATE ids=concat(`ids`,',".$name_id."')");

			db::doquery("INSERT IGNORE INTO movie SET name = \"".$name."\", name_id = \"".$name_id."\", data = \"".$row['data']."\", video_movie = \"".$row['video_movie']."\", descr = \"".$descr."\", subtitles = \"".$row['subtitles']."\", `time`=".(time()+($last_add*60)).", added = \"".$row['added']."\", moder = \"".$row['moder']."\", cat = \"".$row['cat']."\", album = \"".$row['album']."\"");
			$tag_list = explode(" ", preg_replace("/[^\w\s]/u", "", htmlspecialchars_decode($row['name'])));
			$tags = array();
			for($i=0;$i<sizeof($tag_list); $i++) {
				if(self::nstrlen($tag_list[$i])>2) {
					$tags[] = $tag_list[$i];
				}
			}
			for($i=0; $i<sizeof($tags); $i++) {
				db::doquery("INSERT IGNORE INTO tags SET video_name = \"".$name."\", tag = \"".$tags[$i]."\"");
			}
			db::doquery("UPDATE cardinal_movie SET activ = \"no\" WHERE id = ".$row['id']);
			$last_add = ($last_add+$rand);
		}
		db::free();
		config::Update("cardinal_time", time());
	}

	protected static function amper($data) {
		if(is_array($data)) {
			$returns = array();
			foreach($data as $name => $val) {
				if(!empty($val)) {
					$returns[] = $name."=".$val;
				} else {
					$returns[] = $name;
				}
			}
			return implode("&", $returns);
		} else {
			return $data;
		}
	}

	public static function stats() {
		$res = db::doquery("SELECT SUM(views) AS views, name_id FROM stat GROUP BY name_id", true);
		while($row = db::fetch_assoc($res)) {
			db::doquery("UPDATE movie SET view = ".$row['views']." WHERE name_id = \"".$row['name_id']."\"");
		}
		db::free();
	}

	public static function hackers($page, $referer=null) {
		if(!empty($referer)) {
			$ref = ", referer = \"".urlencode($referer)."\"";
		} else {
			$ref = "";
		}
		db::doquery("INSERT INTO hackers SET ip = \"".HTTP::getip()."\", page = \"".urlencode($page)."\", post = \"".urlencode(self::amper($_POST))."\", get = \"".urlencode(self::amper($_GET))."\"".$ref.", activ = \"yes\"");
		location("{C_default_http_host}?hacker");
	}

	function __destruct() {
		unset($this);
	}

}

?>