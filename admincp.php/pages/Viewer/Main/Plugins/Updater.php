<?php
if(!defined("IS_ADMIN")) {
echo "403 ERROR";
die();
}


class Main_Updater extends Main {
	
	private function Creator($text) {
		preg_match_all("#([0-9]{4}+)-([0-9]{2}+)-([0-9]{2}+) ([0-9]{2}+):([0-9]{2}+):([0-9]{2}+) (.+?)\n#is", $text, $arr);
		if(is_array($arr) && isset($arr[7]) && is_array($arr[7])) {
			for($i=0;$i<sizeof($arr[7]);$i++) {
				$text = str_replace($arr[1][$i]."-".$arr[2][$i]."-".$arr[3][$i]." ".$arr[4][$i].":".$arr[5][$i].":".$arr[6][$i]." ".$arr[7][$i],
									$arr[1][$i]."-".$arr[2][$i]."-".$arr[3][$i]." ".$arr[4][$i].":".$arr[5][$i].":".$arr[6][$i]." "."<span class=\"label label-red\">".$arr[7][$i]."</span>",
						$text);
			}
		}
		$text = str_replace("[!]", "<span class=\"label label-blue\">[!]</span>", $text);
		$text = str_replace("[+]", "<span class=\"label label-success\">[+]</span>", $text);
		$text = str_replace("[~]", "<span class=\"label label-purple\">[~]</span>", $text);
		$text = str_replace("[%]", "<span class=\"label label-warning\">[%]</span>", $text);
		$text = str_replace("[@]", "<span class=\"label label-default\">[@]</span>", $text);
		$text = str_replace("[b]", "<b>", $text);
		$text = str_replace("[/b]", "</b>", $text);
		$text = str_replace("[s]", "<s>", $text);
		$text = str_replace("[/s]", "</s>", $text);
		$text = str_replace("[u]", "<u>", $text);
		$text = str_replace("[/u]", "</u>", $text);
		$text = str_replace("[i]", "<i>", $text);
		$text = str_replace("[/i]", "</i>", $text);
		$text = str_replace("[center]", "<center>", $text);
		$text = str_replace("[/center]", "</center>", $text);
		$text = str_replace("[left]", "<span style=\"text-align:left;\">", $text);
		$text = str_replace("[/left]", "</span>", $text);
		$text = str_replace("[right]", "<span style=\"text-align:right;\">", $text);
		$text = str_replace("[/right]", "</span>", $text);
		$text = str_replace("[color=default]", "<span class=\"label label-blue\">", $text);
		$text = str_replace("[color=default]", "<span class=\"label label-success\">", $text);
		$text = str_replace("[color=default]", "<span class=\"label label-purple\">", $text);
		$text = str_replace("[color=default]", "<span class=\"label label-warning\">", $text);
		$text = str_replace("[color=default]", "<span class=\"label label-default\">", $text);
		$text = str_replace("[/color]", "</span>", $text);
		return $text;
	}

	public function __construct() {
		if((defined("CLOSE_FUNCTION") && strpos(CLOSE_FUNCTION, "curl")!==false) || !userlevel::get("updates")) {
			templates::assign_var("is_new", "0");
			return;
		}
		$vid = parser_url('https://raw.githubusercontent.com/killserver/cardinal/trunk/version/version.txt?'.date("d-m-Y-H"));
		$if = cardinal_version($vid);
		if($if) {
			$file = ROOT_PATH."core".DS."cache".DS."system".DS."version_".str_replace("-", "_", $vid).".txt";
			$changelog = "";
			if(!file_exists($file) && is_writable(ROOT_PATH."core".DS."cache".DS."system".DS)) {
				$vid = parser_url('https://raw.githubusercontent.com/killserver/cardinal/trunk/changelog/list.txt?'.date("d-m-Y-H"));
				$changelog = "";
				$list = explode("\n", $vid);
				for($i=sizeof($list)-1;$i>0;$i--) {
					$if = cardinal_version($list[$i]);
					if($if) {
						$changelog .= parser_url('https://raw.githubusercontent.com/killserver/cardinal/trunk/changelog/'.$list[$i].'.txt')."\n\n\n\n";
					}
				}
				if(!empty($changelog)) {
					templates::assign_var("new_version", $vid);
					templates::assign_var("is_new", "1");
					if(is_writable($file)) {
						file_put_contents($file, $changelog, FILE_APPEND);
					}
				}
			} else if(file_exists($file)) {
				$changelog = file_get_contents($file);
			}
			templates::assign_var("changelog", nl2br($this->Creator(str_replace(array("{", "}"), array("&#123;", "&#125;"), htmlspecialchars($changelog)))));
		} else {
			templates::assign_var("is_new", "0");
		}
	}

}

?>