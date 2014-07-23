<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die;
}

final class cardinal {

	private $config;
	function __construct() {
		$this->config = new config();
		$otime = $this->config->select("cardinal_time");

		if($otime <= time()-12*60*60) {
			$this->add();
		}
	}

	function nstrlen($text) {
		if(function_exists("mb_strlen")) {
			return mb_strlen($text);
		} elseif(function_exists("iconv_strlen")) {
			return iconv_strlen($text);
		} else {
			return strlen($text);
		}
	}

	private function add() {
		$sql = db::doquery("SELECT id, name, data, video_movie, descr, images, subtitles, `time`, added, moder, cat, album FROM cardinal_movie WHERE activ = \"yes\" ORDER BY id ASC LIMIT ".mrand(1, 3), true);
		$last_add = 1;
		$conv = array();
		while($row = db::fetch_array($sql)) {
			$rand = rand(0, 2);
			$name = str_replace("\"", "\\\"", $row['name']);
			$descr = str_replace("\"", "\\\"", $row['descr']);
			db::doquery("INSERT IGNORE INTO movie SET name = \"".$name."\", name_id = \"".cut(md5($row['name']), 10)."\", data = \"".$row['data']."\", video_movie = \"".$row['video_movie']."\", descr = \"".$descr."\", subtitles = \"".$row['subtitles']."\", `time`=".(time()+($last_add*60)).", added = \"".$row['added']."\", moder = \"".$row['moder']."\", cat = \"".$row['cat']."\", album = \"".$row['album']."\"");
			$tag_list = explode(" ", preg_replace("/[^\w\s]/u", "", htmlspecialchars_decode($row['name'])));
			$tags = array();
			for($i=0;$i<sizeof($tag_list); $i++) {
				if($this->nstrlen($tag_list[$i])>2) {
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
		$this->config->update("cardinal_time", time());
	}

	protected function amper($data) {
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

	function hackers($page, $referer=null) {
		if(!empty($referer)) {
			$ref = ", referer = \"".urlencode($referer)."\"";
		} else {
			$ref = "";
		}
		db::doquery("INSERT INTO hackers SET ip = \"".getenv("REMOTE_ADDR")."\", page = \"".urlencode($page)."\", post = \"".urlencode(cardinal::amper($_POST))."\", get = \"".urlencode(cardinal::amper($_GET))."\"".$ref.", activ = \"yes\"");
		location("{C_default_http_host}?hacker");
	}

	function __destruct() {
		unset($this);
	}

}

?>