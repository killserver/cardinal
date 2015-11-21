<?php
if(!defined("IS_ADMIN")) {
echo "403 ERROR";
die();
}

$lang = array_merge($lang, array(
	"list_changelog" => "Список изменений в новой версии",
	"download" => "Загрузить",
	"install" => "Установить",
	"new_versions" => "новую версию",
	"fail_update" => "Ошибка обновления",
	"done_update" => "Успешное обновление",
	"done_updates" => "Шаг обновления успешен",
));

?>