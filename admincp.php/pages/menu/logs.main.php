<?php
$links['logs']["cat"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Logs",
'title' => "{L_Logs}",
'type' => "cat",
'access' => userlevel::get("logs"),
'icon' => 'fa-spin fa-bug',
);
$links['logs']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Logs",
'title' => "{L_Logs}",
'type' => "item",
'access' => userlevel::get("logs"),
'icon' => '',
);
?>