<?php
$links['AText']["cat"][] = array(
'link' => "#",
'title' => "{L_AText}",
'type' => "cat",
'access' => !defined("WITHOUT_DB") || db::connected() && userlevel::get("atextadmin"),
'icon' => 'fa-folder-o',
);
$links['AText']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=ATextAdmin",
'title' => "{L_AText}",
'type' => "item",
'access' => !defined("WITHOUT_DB") || db::connected() && userlevel::get("atextadmin"),
'icon' => '',
);
?>