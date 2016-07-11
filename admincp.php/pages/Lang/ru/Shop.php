<?php
if(!defined("IS_ADMIN")) {
echo "403 ERROR";
die();
}

$lang = array_merge($lang, array(
	"doneInstall" => "Успешая установка",
	"installModule" => "Установка",
	"installNow" => "Сейчас будет установлен модуль",
	"confirmInstallLic" => "Вы подтверждаете установку данного модуля, а так же лицензионное соглашение по его использованию?",
));

?>