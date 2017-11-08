<?php
if(!defined("IS_CORE")) {
	echo "403 ERROR";
	die();
}

$userlevels = array_replace($userlevels, array(
	"5" => array(
		"access_admin" => "yes", // Доступ к админ-панели
		"access_debugpanel" => "yes", // Доступ к дебаг-панели, которая будет активироваться в админ-панели и постоянно находиться на сайте в любых разделах
		"access_showLoads" => "yes", // Доступ к просмотру загрузки сервера
		"access_clearCache" => "yes", // Доступ к очистке кеша
		"access_antivirus" => "yes", // Доступ к антивирусу
		"access_atextadmin" => "yes", // Доступ к модулю AText
		"access_editor" => "yes", // Доступ к редактору кода
		"access_languages" => "yes", // Доступ к языковой панели
		"access_loginadmin" => "yes", // Доступ к списку действий в админ-панели
		"access_logs" => "yes", // Доступ к отчётам ошибок на сайте
		"access_phpinfo" => "yes", // Доступ к системной информации о сервере
		"access_seoBlock" => "yes", // Доступ к СЕО-блоку
		"access_settings" => "yes", // Доступ к настройкам
		"access_shop" => "yes", // Доступ к магазину модулей [deprecated]
		"access_updates" => "yes", // Доступ к возможности обновлять движок из админ-панели
		"access_users" => "yes", // Доступ к списку пользователей
		"access_yui_admin" => "yes", // Доступ к настройке системы YUI
		"access_site" => "yes", // Доступ к сайту
	),
	"4" => array(
		"access_admin" => "yes",
		"access_debugpanel" => "yes",
		"access_showLoads" => "yes",
		"access_clearCache" => "yes",
		"access_antivirus" => "yes",
		"access_atextadmin" => "yes",
		"access_editor" => "yes",
		"access_languages" => "yes",
		"access_loginadmin" => "yes",
		"access_logs" => "yes",
		"access_phpinfo" => "yes",
		"access_seoBlock" => "yes",
		"access_settings" => "yes",
		"access_shop" => "yes",
		"access_users" => "yes",
		"access_yui_admin" => "yes",
		"access_site" => "yes",
	),
	"3" => array(
		"access_admin" => "yes",
		"access_site" => "yes",
	),
	"2" => array(
		"access_admin" => "yes",
		"access_site" => "yes",
	),
	"1" => array(
		"access_admin" => "no",
		"access_site" => "yes",
	),
	"0" => array(
		"access_admin" => "no",
		"access_site" => "yes",
	),
));