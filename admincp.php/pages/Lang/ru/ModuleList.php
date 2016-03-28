<?php
if(!defined("IS_ADMIN")) {
echo "403 ERROR";
die();
}

$lang = array_merge($lang, array(
	"ModuleList" => "Управление модулями",
	"ModuleDelete" => "Удалить",
	"ModuleAlert" => "Вы действительно хотите удалить данный модуль и все файлы связанные с ним? Это операцию нельзя будет прервать и обратить!",
	"ActiveModule" => "Активировать",
	"DeActiveModule" => "Деактивировать",
));

?>