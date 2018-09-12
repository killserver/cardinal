<?php
$active = true;
if(defined("DISALLOW_FILE_EDIT")) {
	if(is_bool(DISALLOW_FILE_EDIT) && DISALLOW_FILE_EDIT===true) {
		$active = false;
	}
}

$links['System']["cat"][] = array(
'link' => "#",
'title' => "{L_\"Системное\"}",
'type' => "cat",
'access' => (userlevel::get("antivirus") && is_writable(PATH_CACHE_SYSTEM.DS)) || ($active && userlevel::get("editor")) || userlevel::get("loginadmin") || userlevel::get("logs") || userlevel::get("phpinfo") || (userlevel::get("recyclebin") && file_exists(PATH_CACHE_USERDATA."trashBin.lock")) || (userlevel::get("users") && !defined("WITHOUT_DB")) || userlevel::get("userlevels") || userlevel::get("yui_admin") || userlevel::get("importexport") || userlevel::get("skins") || ((!defined("WITHOUT_DB") || db::connected()) && userlevel::get("atextadmin")),
'icon' => 'fa-dashboard',
);
$links['System']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Antivirus",
'title' => "{L_\"Антивирус\"}",
'type' => "item",
'access' => userlevel::get("antivirus") && is_writable(PATH_CACHE_SYSTEM.DS),
'icon' => 'fa-shield',
);
if($active) {
	$links['System']["item"][] = array(
		'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Editor",
		'title' => "{L_\"Editor\"}",
		'type' => "item",
		'access' => userlevel::get("editor"),
		'icon' => 'fa-folder-open-o',
	);
}
$links['System']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=logInAdmin",
'title' => "{L_\"Список действий в админ-панели\"}",
'type' => "item",
'access' => userlevel::get("loginadmin"),
'icon' => 'fa-life-saver',
);
$links['System']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Logs",
'title' => "{L_\"Журнал ошибок\"}",
'type' => "item",
'access' => userlevel::get("logs"),
'icon' => 'fa-spin fa-bug',
);
$links['System']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Phpinfo",
'title' => "{L_\"PhpInfo\"}",
'type' => "item",
'access' => userlevel::get("phpinfo"),
'icon' => 'fa-fighter-jet',
);
$links['System']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=ImportExport",
'title' => "{L_\"Импорт/Экспорт\"}",
'type' => "item",
'access' => userlevel::get("importexport"),
'icon' => 'fa-database',
);
$links['System']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=RecycleBin",
'title' => "{L_\"Корзина данных\"}",
'type' => "item",
'access' => userlevel::get("recyclebin") && file_exists(PATH_CACHE_USERDATA."trashBin.lock"),
'icon' => 'fa-trash',
);
$links['System']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Users",
'title' => "{L_Users}",
'type' => "item",
'access' => userlevel::get("users") && !defined("WITHOUT_DB"),
'icon' => '',
);
$links['System']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=UserLevels",
'title' => "{L_\"Уровни доступа\"}",
'type' => "item",
'access' => userlevel::get("userlevels"),
'icon' => 'fa-keyboard-o',
);
$links['System']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Yui",
'title' => "{L_\"Администрирование Yui\"}",
'type' => "item",
'access' => userlevel::get("yui_admin"),
'icon' => 'fa-book',
);
$links['System']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Skins",
'title' => "{L_\"Переключение шаблонов\"}",
'type' => "item",
'access' => userlevel::get("skins"),
'icon' => 'fa-eye',
);
?>