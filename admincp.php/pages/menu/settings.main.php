<?php
$links['Settings']["cat"][] = array(
'link' => "#",
'title' => "{L_\"Настройки\"}",
'type' => "cat",
'access' => userlevel::get("settings") || userlevel::get("settinguser"),
'icon' => 'fa-dashboard',
);
$links['Settings']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=SettingUser",
'title' => "{L_\"Настройки\"}",
'type' => "item",
'access' => userlevel::get("settinguser"),
'icon' => '',
);
$links['Settings']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Settings",
'title' => "{L_\"Настройки системы\"}",
'type' => "item",
'access' => userlevel::get("settings"),
'icon' => '',
);
?>