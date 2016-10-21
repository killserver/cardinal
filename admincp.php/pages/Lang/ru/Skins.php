<?php
if(!defined("IS_ADMIN")) {
echo "403 ERROR";
die();
}

$lang = array_merge($lang, array(
	"skins" => "Управление шаблонами",
	"skin_site" => "Шаблон сайта",
	"skin_admin" => "Шаблон админ-панели",
));

?>