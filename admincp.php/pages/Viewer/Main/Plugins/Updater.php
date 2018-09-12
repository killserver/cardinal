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
		$text = preg_replace("#\[url=['\"](.+?)['\"]\](.+?)\[/url\]#", "<a href=\"$1\">$2</a>", $text);
		$text = preg_replace("#\[url\](.+?)\[/url\]#", "<a href=\"$1\">$1</a>", $text);
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
		if((defined("CLOSE_FUNCTION") && strpos(CLOSE_FUNCTION, "curl")!==false) && userlevel::get("updaters")===false) {
			templates::assign_var("is_new", "old");
			return;
		}
		$get = false;
		if(class_exists("config", false) && method_exists("config", "Select") && config::Select("speed_update")) {
			$prs = new Parser('https://raw.githubusercontent.com/killserver/cardinal/trunk/version/intversion.txt?'.date("d-m-Y-H:i"));
			$vid = $prs->get();
			$get = true;
		} else {
			$prs = new Parser('https://raw.githubusercontent.com/killserver/cardinal/trunk/version/version.txt?'.date("d-m-Y-H"));
			$vid = $prs->get();
		}
		$if = cardinal::CheckVersion($vid);
		if($if) {
			if($get) {
				$prs = new Parser('https://raw.githubusercontent.com/killserver/cardinal/trunk/version/version.txt?'.date("d-m-Y-H"));
				$vid = $prs->get();
			}
			$dir = PATH_CACHE_SYSTEM;
			$file = $dir."version_".str_replace("-", "_", $vid).".txt";
			$changelog = "";
			if(!file_exists($file) && is_writable($dir)) {
				$prs = new Parser('https://raw.githubusercontent.com/killserver/cardinal/trunk/changelog/list.txt?'.date("d-m-Y-H"));
				$vids = $prs->get();
				$changelog = "";
				$list = explode("\n", $vids);
				$list = array_map("trim", $list);
				for($i=sizeof($list)-1;$i>0;$i--) {
					$if = cardinal::CheckVersion($list[$i]);
					if($if) {
						$prs = new Parser('https://raw.githubusercontent.com/killserver/cardinal/trunk/changelog/'.$list[$i].'.txt');
						$changelog .= $prs->get()."\n\n\n\n";
					}
				}
				if(!empty($changelog)) {
					templates::assign_var("new_version", $vid);
					templates::assign_var("is_new", "new");
					if(is_writable($dir)) {
						file_put_contents($file, $changelog, FILE_APPEND);
					}
				}
			} else if(file_exists($file)) {
				templates::assign_var("new_version", $vid);
				templates::assign_var("is_new", "new");
				$changelog = file_get_contents($file);
			}
			templates::assign_var("changelog", nl2br($this->Creator(str_replace(array("{", "}"), array("&#123;", "&#125;"), htmlspecialchars($changelog)))));
		} else {
			templates::assign_var("is_new", "old");
		}
	}

}

?>