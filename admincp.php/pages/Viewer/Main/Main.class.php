<?php

class Main extends Core {

	public function __construct() {
		$message = "";
		if(file_exists(ROOT_PATH.".htaccess") && get_chmod(ROOT_PATH.".htaccess")=="0777" && userlevel::is(LEVEL_CREATOR)) {
			$message .= "<div><div class=\"label label-red\">{L_\"Внимание\"}</div>&nbsp;{L_\"Файл\"}&nbsp;<b>".DS.".htaccess</b>&nbsp;{L_\"разрешен для записи\"}&nbsp;<span style=\"color:red;\"><b>0777</b></span>.</div>";
		}
		if(file_exists(ROOT_PATH."index.php") && get_chmod(ROOT_PATH."index.php")=="0777" && userlevel::is(LEVEL_CREATOR)) {
			$message .= "<div><div class=\"label label-red\">{L_\"Внимание\"}</div>&nbsp;{L_\"Файл\"}&nbsp;<b>".DS."index.php</b>&nbsp;{L_\"разрешен для записи\"}&nbsp;<span style=\"color:red;\"><b>0777</b></span>.</div>";
		}
		if(file_exists(PATH_UPLOADS.".htaccess") && get_chmod(PATH_UPLOADS.".htaccess")=="0777" && userlevel::is(LEVEL_CREATOR)) {
			$message .= "<div><div class=\"label label-red\">{L_\"Внимание\"}</div>&nbsp;{L_\"Файл\"}&nbsp;<b>".DS."".str_replace(array(ROOT_PATH, DS), array("", DS_DB), PATH_UPLOADS).".htaccess</b>&nbsp;{L_\"разрешен для записи\"}&nbsp;<span style=\"color:red;\"><b>0777</b></span>.</div>";
		}
		if(file_exists(PATH_UPLOADS."index.".ROOT_EX) && get_chmod(PATH_UPLOADS."index.php")=="0777" && userlevel::is(LEVEL_CREATOR)) {
			$message .= "<div><div class=\"label label-red\">{L_\"Внимание\"}</div>&nbsp;{L_\"Файл\"}&nbsp;<b>".DS."".str_replace(array(ROOT_PATH, DS), array("", DS_DB), PATH_UPLOADS)."index.".ROOT_EX."</b>&nbsp;{L_\"разрешен для записи\"}&nbsp;<span style=\"color:red;\"><b>0777</b></span>.</div>";
		}
		if(file_exists(PATH_UPLOADS."index.html") && get_chmod(PATH_UPLOADS."index.html")=="0777" && userlevel::is(LEVEL_CREATOR)) {
			$message .= "<div><div class=\"label label-red\">{L_\"Внимание\"}</div>&nbsp;{L_\"Файл\"}&nbsp;<b>".DS."".str_replace(array(ROOT_PATH, DS), array("", DS_DB), PATH_UPLOADS)."index.html</b>&nbsp;{L_\"разрешен для записи\"}&nbsp;<span style=\"color:red;\"><b>0777</b></span>.</div>";
		}
		if(file_exists(ROOT_PATH."install.".ROOT_EX) && userlevel::is(LEVEL_CREATOR)) {
			$message .= "<div><div class=\"label label-warning\">{L_\"Обратите внимание\"}</div>&nbsp;{L_\"Файл\"}&nbsp;<b>".DS."install.php</b>&nbsp;{L_\"присутствует на сервере\"}.&nbsp;{L_\"Рекомендуется удалить его для повышения безопасности.\"}</div>";
		}
		if(!is_writable(PATH_CACHE_SYSTEM) && userlevel::is(LEVEL_CREATOR)) {
			$message .= "<div><div class=\"label label-warning\">{L_\"Обратите внимание\"}</div>&nbsp;{L_\"Каталог\"}&nbsp;<b>".DS."".str_replace(array(ROOT_PATH, DS), array("", DS_DB), PATH_CACHE_SYSTEM)."</b>&nbsp;{L_\"не разрешен для записи\"}&nbsp;{L_\"Рекомендуется установить права 0777 для активации антивируса.\"}</div>";
		}
		if(file_exists(PATH_MEDIA."error.lock") && userlevel::is(LEVEL_CREATOR)) {
			$message .= "<div><div class=\"label label-warning\">{L_\"Обратите внимание\"}</div>&nbsp;{L_\"Файл\"}&nbsp;<b>".DS."".str_replace(array(ROOT_PATH, DS), array("", DS_DB), PATH_MEDIA)."error.lock</b>&nbsp;{L_\"присутствует на сервере\"}.&nbsp;{L_\"Рекомендуется удалить его прежде чем сдать проект.\"}</div>";
		}
		if(Arr::get($_GET, "debugPanel", false)) {
			if(isset($_COOKIE['cardinal_debug'])) {
				HTTP::set_cookie("cardinal_debug", "", true);
			} else {
				HTTP::set_cookie("cardinal_debug", "true");
			}
			return;
		}
		if(userlevel::get("debugpanel")) {
			templates::assign_var("debugpanelshow", "1");
		} else {
			templates::assign_var("debugpanelshow", "0");
		}
		if(userlevel::get("clearcache_all")) {
			templates::assign_var("clearCacheAll", "1");
		} else {
			templates::assign_var("clearCacheAll", "0");
		}
		if(userlevel::get("clearcache_data")) {
			templates::assign_var("clearCacheData", "1");
		} else {
			templates::assign_var("clearCacheData", "0");
		}
		if(userlevel::get("clearcache_pages")) {
			templates::assign_var("clearCachePages", "1");
		} else {
			templates::assign_var("clearCachePages", "0");
		}
		templates::assign_var("debugPanel", "0");
		if(isset($_COOKIE['cardinal_debug'])) {
			templates::assign_var("debugPanel", "1");
		}
		if(!empty($message)) {
			templates::assign_var("is_messagesAdmin", "1");
			templates::assign_var("messagesAdmin", $message);
		}
		$this->Prints("index");
	}

}
ReadPlugins(dirname(__FILE__).DIRECTORY_SEPARATOR."Plugins".DIRECTORY_SEPARATOR, "Main");

?>