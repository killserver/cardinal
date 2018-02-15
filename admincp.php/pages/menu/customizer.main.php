<?php
$links['customizer']["cat"][] = array(
'link' => "#",
'title' => "{L_\"Кастомизация\"}",
'type' => "cat",
'access' => userlevel::get("customize"),
'icon' => 'fa-coffee',
);
$links['customizer']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Customize",
'title' => "{L_\"Кастомизация\"}",
'type' => "item",
'access' => userlevel::get("customize"),
'icon' => '',
);
?>