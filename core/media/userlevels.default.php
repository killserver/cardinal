<?php
if(!defined("IS_CORE")) {
	echo "403 ERROR";
	die();
}

$userlevels = array_replace($userlevels, array(
	"5" => array(
		"access_admin" => "yes", // Доступ к админ-панели
		"access_antivirus" => "yes", // Доступ к антивирусу
		"access_atextadmin" => "yes", // Доступ к модулю AText
		"access_clearcache_all" => "yes", // Доступ к очистке всего кеша
		"access_clearcache_data" => "yes", // Доступ к очистке кеша данных
		"access_clearcache_tmp" => "yes", // Доступ к очистке кеша шаблонов
		"access_customize" => "yes", // Доступ к кастомизации шаблона
		"access_debugpanel" => "yes", // Доступ к дебаг-панели, которая будет активироваться в админ-панели и постоянно находиться на сайте в любых разделах
		"access_editor" => "yes", // Доступ к редактору кода
		"access_languages" => "yes", // Доступ к языковой панели
		"access_loginadmin" => "yes", // Доступ к списку действий в админ-панели
		"access_logs" => "yes", // Доступ к отчётам ошибок на сайте
		"access_phpinfo" => "yes", // Доступ к системной информации о сервере
		"access_seo" => "yes", // Доступ к СЕО-мета
		"access_seoBlock" => "yes", // Доступ к СЕО-блоку
		"access_settings" => "yes", // Доступ к настройкам
		"access_showloads" => "yes", // Доступ к просмотру загрузки сервера
		"access_updates" => "yes", // Доступ к возможности обновлять движок из админ-панели
		"access_users" => "yes", // Доступ к списку пользователей
		"access_yui_admin" => "yes", // Доступ к настройке системы YUI
		"access_site" => "yes", // Доступ к сайту
	),
	"4" => array(
		"access_admin" => "yes",
		"access_antivirus" => "yes",
		"access_atextadmin" => "yes",
		"access_clearcache_all" => "yes",
		"access_clearcache_data" => "yes",
		"access_clearcache_tmp" => "yes",
		"access_customize" => "yes",
		"access_debugpanel" => "yes",
		"access_editor" => "yes",
		"access_languages" => "yes",
		"access_loginadmin" => "yes",
		"access_logs" => "yes",
		"access_phpinfo" => "yes",
		"access_seo" => "yes",
		"access_seoBlock" => "yes",
		"access_settings" => "yes",
		"access_showloads" => "yes",
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