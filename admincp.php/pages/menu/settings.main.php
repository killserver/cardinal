<?php
$links['Settings1']["cat"][] = array(
'link' => "#",
'title' => "{L_\"Настройки\"}",
'type' => "cat",
'access' => userlevel::get("settings"),
'icon' => 'fa-dashboard',
);
$links['Settings1']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=SettingUser",
'title' => "{L_\"Настройки\"}",
'type' => "item",
'access' => userlevel::get("settings"),
'icon' => '',
);
$links['Settings2']["cat"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Settings",
'title' => "{L_\"Настройки системы\"}",
'type' => "cat",
'access' => userlevel::get("settingsSystem"),
'icon' => '',
);
$links['Settings2']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=SettingUser",
'title' => "{L_\"Настройки\"}",
'type' => "item",
'access' => userlevel::get("settingsSystem"),
'icon' => '',
);
?>