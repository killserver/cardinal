<?php
$links['Users']["cat"][] = array(
'link' => "#",
'title' => "{L_Users}",
'type' => "cat",
'access' => userlevel::get("users") && !defined("WITHOUT_DB"),
'icon' => 'fa-users',
);
$links['Users']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Users",
'title' => "{L_Users}",
'type' => "item",
'access' => userlevel::get("users") && !defined("WITHOUT_DB"),
'icon' => '',
);
?>