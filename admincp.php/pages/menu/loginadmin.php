<?php
$links['loginadmin']["cat"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=logInAdmin",
'title' => "{L_logInAdmin}",
'type' => "cat",
'access' => userlevel::get("loginadmin"),
'icon' => 'fa-life-saver',
);
$links['loginadmin']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=logInAdmin",
'title' => "{L_logInAdmin}",
'type' => "item",
'access' => userlevel::get("loginadmin"),
'icon' => '',
);
?>