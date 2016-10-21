<?php
$links['Users']["cat"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Users",
'title' => "{L_Users}",
'type' => "cat",
'access' => LEVEL_ADMIN && !defined("WITHOUT_DB"),
'icon' => 'fa-users',
);
$links['Users']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Users",
'title' => "{L_Users}",
'type' => "item",
'access' => LEVEL_ADMIN && !defined("WITHOUT_DB"),
'icon' => '',
);
?>