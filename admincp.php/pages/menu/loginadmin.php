<?php
$links['loginadmin']["cat"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=logInAdmin",
'title' => "{L_logInAdmin}",
'type' => "cat",
'access' => LEVEL_ADMIN || LEVEL_CREATOR,
'icon' => 'fa-life-saver',
);
$links['loginadmin']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=logInAdmin",
'title' => "{L_logInAdmin}",
'type' => "item",
'access' => LEVEL_ADMIN || LEVEL_CREATOR,
'icon' => '',
);
?>