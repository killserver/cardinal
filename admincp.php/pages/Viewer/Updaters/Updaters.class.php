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
		return $text;
	}
	
	function __construct() {
		$this->ParseLang();
		if(isset($_GET['download'])) {
			if(file_exists(ROOT_PATH."core".DS."cache".DS."system".DS."lastest.tar.gz")) {
				unlink(ROOT_PATH."core".DS."cache".DS."system".DS."lastest.tar.gz");
			}
			$prs = new Parser("https://codeload.github.com/killserver/cardinal/tar.gz/trunk?".time());
			$prs->timeout(30);
			file_put_contents(ROOT_PATH."core".DS."cache".DS."system".DS."lastest.tar.gz", $prs->get());
			HTTP::echos("1");
			return;
		}
		if(isset($_GET['install'])) {
			if(strpos(getenv("HTTP_HOST"), "localhost")!==false) {
				header("HTTP/1.0 404 Not Found");
				HTTP::echos(lang::get_lang("install_update_fail_localhost"));
				die();
			}
			if(!file_exists(ROOT_PATH."core".DS."cache".DS."system".DS."lastest.tar.gz")) {
				header("HTTP/1.0 404 Not Found");
				HTTP::echos(lang::get_lang("install_update_fail_file"));
				die();
			}
			$tar_object = new Archive_Tar(ROOT_PATH."core".DS."cache".DS."system".DS."lastest.tar.gz", "gz");
			$list = $tar_object->listContent();
			if(!is_array($list) || sizeof($list)==0) {
				header("HTTP/1.0 404 Not Found");
			}
			$tr = $tar_object->extractModify(ROOT_PATH, "cardinal-trunk/");
			if($tr===true) {
				unlink(ROOT_PATH."core".DS."cache".DS."system".DS."lastest.tar.gz");
				echo "1";
			} else {
				header("HTTP/1.1 406 Not Acceptable");
			}
			return;
		}
		if(file_exists(ROOT_PATH."core".DS."cache".DS."system".DS."lastest.tar.gz")) {
			templates::assign_var("is_download", "1");
		} else {
			templates::assign_var("is_download", "0");
		}
		$vid = parser_url('https://raw.githubusercontent.com/killserver/cardinal/trunk/version/version.txt?'.date("d-m-Y-H"));
		if(config::Select("speed_update")) {
			$if = ($vid)>(VERSION);
		} else {
			$if = intval(str_replace(".", "", $vid))>intval(str_replace(".", "", VERSION));
		}
		if($if) {
			templates::assign_var("new_version", $vid);
			templates::assign_var("is_new", "1");
			$file = ROOT_PATH."core".DS."cache".DS."system".DS."version_".str_replace("-", "_", $vid).".txt";
			if(!file_exists($file)) {
				$vid = parser_url('https://raw.githubusercontent.com/killserver/cardinal/trunk/changelog/list.txt?'.date("d-m-Y-H"));
				$changelog = "";
				$list = explode("\n", $vid);
				for($i=sizeof($list)-1;$i>0;$i--) {
					if(config::Select("speed_update")) {
						$if = ($list[$i])>(VERSION);
					} else {
						$if = intval(str_replace(".", "", $list[$i]))>intval(str_replace(".", "", VERSION));
					}
					if($if) {
						$changelog .= parser_url('https://raw.githubusercontent.com/killserver/cardinal/trunk/changelog/'.$list[$i].'.txt')."\n\n\n\n";
					}
				}
				file_put_contents($file, $changelog, FILE_APPEND);
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