<?php

class Main extends Core {

	public function __construct() {
		$message = "";
		if(file_exists(ROOT_PATH.".htaccess") && get_chmod(ROOT_PATH.".htaccess")=="0777") {
			$message .= "<div><div class=\"label label-red\">{L_\"Внимание\"}</div>&nbsp;{L_\"Файл\"}&nbsp;<b>{C_default_http_local}.htaccess</b>&nbsp;{L_\"разрешен для записи\"}&nbsp;<span style=\"color:red;\"><b>0777</b></span>.</div>";
		}
		if(file_exists(ROOT_PATH."index.php") && get_chmod(ROOT_PATH."index.php")=="0777") {
			$message .= "<div><div class=\"label label-red\">{L_\"Внимание\"}</div>&nbsp;{L_\"Файл\"}&nbsp;<b>{C_default_http_local}index.php</b>&nbsp;{L_\"разрешен для записи\"}&nbsp;<span style=\"color:red;\"><b>0777</b></span>.</div>";
		}
		if(file_exists(ROOT_PATH."uploads".DS.".htaccess") && get_chmod(ROOT_PATH."uploads".DS.".htaccess")=="0777") {
			$message .= "<div><div class=\"label label-red\">{L_\"Внимание\"}</div>&nbsp;{L_\"Файл\"}&nbsp;<b>{C_default_http_local}uploads".DS_DB.".htaccess</b>&nbsp;{L_\"разрешен для записи\"}&nbsp;<span style=\"color:red;\"><b>0777</b></span>.</div>";
		}
		if(file_exists(ROOT_PATH."uploads".DS."index.".ROOT_EX) && get_chmod(ROOT_PATH."uploads".DS."index.php")=="0777") {
			$message .= "<div><div class=\"label label-red\">{L_\"Внимание\"}</div>&nbsp;{L_\"Файл\"}&nbsp;<b>{C_default_http_local}uploads".DS_DB."index.".ROOT_EX."</b>&nbsp;{L_\"разрешен для записи\"}&nbsp;<span style=\"color:red;\"><b>0777</b></span>.</div>";
		}
		if(file_exists(ROOT_PATH."uploads".DS."index.html") && get_chmod(ROOT_PATH."uploads".DS."index.html")=="0777") {
			$message .= "<div><div class=\"label label-red\">{L_\"Внимание\"}</div>&nbsp;{L_\"Файл\"}&nbsp;<b>{C_default_http_local}uploads".DS_DB."index.html</b>&nbsp;{L_\"разрешен для записи\"}&nbsp;<span style=\"color:red;\"><b>0777</b></span>.</div>";
		}
		if(file_exists(ROOT_PATH."install.".ROOT_EX)) {
			$message .= "<div><div class=\"label label-warning\">{L_\"Обратите внимание\"}</div>&nbsp;{L_\"Файл\"}&nbsp;<b>{C_default_http_local}install.php</b>&nbsp;{L_\"присутствует на сервере\"}.&nbsp;{L_\"Рекомендуется удалить его для повышения безопасности.\"}</div>";
		}
		if(!is_writable(ROOT_PATH."core".DS."cache".DS."system".DS)) {
			$message .= "<div><div class=\"label label-warning\">{L_\"Обратите внимание\"}</div>&nbsp;{L_\"Каталог\"}&nbsp;<b>{C_default_http_local}core".DS_DB."cache".DS_DB."system".DS_DB."</b>&nbsp;{L_\"не разрешен для записи\"}&nbsp;{L_\"Рекомендуется установить права 0777 для активации антивируса.\"}</div>";
		}
		if(file_exists(ROOT_PATH."core".DS."media".DS."error.lock")) {
			$message .= "<div><div class=\"label label-warning\">{L_\"Обратите внимание\"}</div>&nbsp;{L_\"Файл\"}&nbsp;<b>{C_default_http_local}core".DS_DB."media".DS_DB."error.lock</b>&nbsp;{L_\"присутствует на сервере\"}.&nbsp;{L_\"Рекомендуется удалить его прежде чем сдать проект.\"}</div>";
		}
		if(!db::connected()) {
			$message .= "<div><div class=\"label label-warning\">{L_\"Обратите внимание\"}</div>&nbsp;{L_\"Подключение к базе данных - отсутствует\"}.&nbsp;{L_\"Рекомендуется создать подключение для активации СЕО-модуля.\"}</div>";
		}
		if(!empty($message)) {
			templates::assign_var("is_messagesAdmin", "1");
			templates::assign_var("messagesAdmin", $message);
		}
		$this->Prints("index");
	}

}
ReadPlugins(dirname(__FILE__)."/Plugins/", "Main");

?>