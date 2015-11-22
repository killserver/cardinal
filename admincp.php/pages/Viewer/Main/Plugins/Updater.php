<?php
if(!defined("IS_ADMIN")) {
echo "403 ERROR";
die();
}


class Main_Updater extends Main {
	
	private function Creator($text) {
		preg_match_all("#([0-9]+){4}-([0-9]+){2}-([0-9]+){2} ([0-9]+){2}:([0-9]+){2}:([0-9]+){2} (.+?)\n#is", $text, $arr);
		if(is_array($arr) && isset($arr[7]) && is_array($arr[7])) {
			for($i=0;$i<sizeof($arr[7]);$i++) {
				$text = str_replace($arr[7][$i], "<span class=\"label label-red\">".$arr[7][$i]."</span>", $text);
			}
		}
		$text = str_replace("[!]", "<span class=\"label label-blue\">[!]</span>", $text);
		$text = str_replace("[+]", "<span class=\"label label-success\">[+]</span>", $text);
		$text = str_replace("[~]", "<span class=\"label label-purple\">[~]</span>", $text);
		$text = str_replace("[%]", "<span class=\"label label-warning\">[%]</span>", $text);
		$text = str_replace("[@]", "<span class=\"label label-default\">[@]</span>", $text);
		return $text;
	}

	public function Main_Updater() {
		$vid = parser_url('https://raw.githubusercontent.com/killserver/cardinal/trunk/version/version.txt?'.date("d-m-Y-H"));
		if(config::Select("speed_update")) {
			$if = md5($vid)!=md5(VERSION);
		} else {
			$if = intval(str_replace(".", "", $vid))>intval(str_replace(".", "", VERSION));
		}
		if($if) {
			templates::assign_var("new_version", $vid);
			templates::assign_var("is_new", "1");
			$file = ROOT_PATH."core/cache/system/version_".str_replace("-", "_", $vid).".txt";
			if(!file_exists($file)) {
				$vid = parser_url('https://raw.githubusercontent.com/killserver/cardinal/trunk/changelog/list.txt?'.date("d-m-Y-H"));
				$changelog = "";
				$list = explode("\n", $vid);
				for($i=sizeof($list)-1;$i>0;$i--) {
					if(intval(str_replace(".", "", $list[$i]))>intval(str_replace(".", "", VERSION))) {
						$changelog .= parser_url('https://raw.githubusercontent.com/killserver/cardinal/trunk/changelog/'.$list[$i].'.txt')."\n\n\n\n";
					}
				}
				file_put_contents($file, $changelog, FILE_APPEND);
			} else {
				$changelog = file_get_contents($file);
			}
			templates::assign_var("changelog", nl2br($this->Creator(str_replace(array("{", "}"), array("&#123;", "&#125;"), htmlspecialchars($changelog)))));
		} else {
			templates::assign_var("is_new", "0");
		}
	}

}

?>