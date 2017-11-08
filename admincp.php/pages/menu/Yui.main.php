<?php
$links['Yui']["cat"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Yui",
'title' => "{L_\"Администрирование Yui\"}",
'type' => "cat",
'access' => userlevel::get("yui_admin"),
'icon' => 'fa-book',
);
$links['Yui']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Yui",
'title' => "{L_\"Администрирование Yui\"}",
'type' => "item",
'access' => userlevel::get("yui_admin"),
'icon' => '',
);
?>