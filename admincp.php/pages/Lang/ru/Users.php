<?php
if(!defined("IS_ADMIN")) {
echo "403 ERROR";
die();
}

$lang = array_merge($lang, array(
	"Users" => "Управление пользователями",
	"search_by_ip" => "Поиск пользователя по IP",
	"group" => "Группа",
	"settings" => "Настройки",
	"member_list" => "Общий",
	"admins" => "Администраторы",
	"moders" => "Модераторы",
	
));

?>