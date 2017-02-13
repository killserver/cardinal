<?php
$links['AText']["cat"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=ATextAdmin",
'title' => "{L_AText}",
'type' => "cat",
'access' => LEVEL_MODER || LEVEL_ADMIN,
'icon' => 'fa-folder-o',
);
$links['AText']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=ATextAdmin",
'title' => "{L_AText}",
'type' => "item",
'access' => LEVEL_MODER || LEVEL_ADMIN,
'icon' => '',
);
?>