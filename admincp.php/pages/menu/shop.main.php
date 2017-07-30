<?php
$links['Shop']["cat"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Shop",
'title' => "{L_Shop}",
'type' => "cat",
'access' => userlevel::get("shop") && !defined("WITHOUT_DB"),
'icon' => 'fa-list-alt',
);
$links['Shop']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Shop",
'title' => "{L_Shop}",
'type' => "item",
'access' => userlevel::get("shop") && !defined("WITHOUT_DB"),
'icon' => '',
);
?>