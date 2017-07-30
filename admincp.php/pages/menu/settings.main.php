<?php
$links['Settings']["cat"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Settings",
'title' => "{L_Settings}",
'type' => "cat",
'access' => userlevel::get("settings"),
'icon' => 'fa-dashboard',
);
$links['Settings']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Settings",
'title' => "{L_Settings}",
'type' => "item",
'access' => userlevel::get("settings"),
'icon' => '',
);
?>