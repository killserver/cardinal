<?php
/*
 *
 * @version 1.25.7-a5
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.7-a5
 * Version File: 1
 *
 * 1.1
 * create autoupdater
 * 1.2
 * fix cache information of new version on server
 * 1.3
 * add support "speed update"
 * 1.4
 * add block update on localhost(read changelog)
 *
*/
if(!defined("IS_ADMIN")) {
echo "403 ERROR";
die();
}


class Updaters extends Core {
	
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
	
	function __construct() {
		callAjax();
		$this->ParseLang();
		if(isset($_GET['download'])) {
			if(file_exists(PATH_CACHE_SYSTEM."lastest.tar.gz")) {
				unlink(PATH_CACHE_SYSTEM."lastest.tar.gz");
			}
			$prs = new Parser("https://codeload.github.com/killserver/cardinal/tar.gz/trunk?".time());
			$prs->timeout(30);
			file_put_contents(PATH_CACHE_SYSTEM."lastest.tar.gz", $prs->get());
			cardinal::RegAction("Скачивание свежей версии движка");
			HTTP::echos("1");
			return;
		}
		if(isset($_GET['install'])) {
			if(strpos(getenv("HTTP_HOST"), "localhost")!==false) {
				header("HTTP/1.0 404 Not Found");
				HTTP::echos(lang::get_lang("install_update_fail_localhost"));
				die();
			}
			if(!file_exists(PATH_CACHE_SYSTEM."lastest.tar.gz")) {
				header("HTTP/1.0 404 Not Found");
				HTTP::echos(lang::get_lang("install_update_fail_file"));
				die();
			}
			$tar_object = new Archive_Tar(PATH_CACHE_SYSTEM."lastest.tar.gz", "gz");
			$list = $tar_object->listContent();
			if(!is_array($list) || sizeof($list)==0) {
				header("HTTP/1.0 404 Not Found");
			}
			cardinal::RegAction("Обновление движка");
			$tr = $tar_object->extractModify(ROOT_PATH, "cardinal-trunk/");
			if($tr === true) {
				unlink(PATH_CACHE_SYSTEM."lastest.tar.gz");
				echo "1";
			} else {
				header("HTTP/1.1 406 Not Acceptable");
			}
			return;
		}
		if(!is_writable(ROOT_PATH."core".DS."cache".DS) || !is_writable(PATH_CACHE_SYSTEM)) {
			templates::assign_var("is_locked", "1");
		} else {
			templates::assign_var("is_locked", "0");
		}
		if(file_exists(PATH_CACHE_SYSTEM."lastest.tar.gz")) {
			templates::assign_var("is_download", "1");
		} else {
			templates::assign_var("is_download", "0");
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
			templates::assign_var("new_version", $vid);
			templates::assign_var("is_new", "1");
			$file = PATH_CACHE_SYSTEM."version_".str_replace("-", "_", $vid).".txt";
			if(!file_exists($file)) {
				$prs = new Parser('https://raw.githubusercontent.com/killserver/cardinal/trunk/changelog/list.txt?'.date("d-m-Y-H"));
				$vid = $prs->get();
				$changelog = "";
				$list = explode("\n", $vid);
				for($i=sizeof($list)-1;$i>0;$i--) {
					if(config::Select("speed_update")) {
						$if = ($list[$i])>(VERSION);
					} else {
						$if = intval(str_replace(".", "", $list[$i]))>intval(str_replace(".", "", VERSION));
					}
					if($if) {
						$prs = new Parser('https://raw.githubusercontent.com/killserver/cardinal/trunk/changelog/'.$list[$i].'.txt');
						$changelog .= $prs->get()."\n\n\n\n";
					}
				}
				@file_put_contents($file, $changelog, FILE_APPEND);
			} else {
				$changelog = file_get_contents($file);
			}
			templates::assign_var("changelog", nl2br($this->Creator(str_replace(array("{", "}"), array("&#123;", "&#125;"), htmlspecialchars($changelog)))));
			$this->Prints("Updaters");
		} else {
			$this->Prints("{L_done_install}", true);
		}
	}
	
}

?>