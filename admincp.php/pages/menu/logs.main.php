<?php
$links['logs']["cat"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Logs",
'title' => "{L_Logs}",
'type' => "cat",
'access' => LEVEL_ADMIN || LEVEL_CREATOR,
'icon' => 'fa-spin fa-bug',
);
$links['logs']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Logs",
'title' => "{L_Logs}",
'type' => "item",
'access' => LEVEL_ADMIN || LEVEL_CREATOR,
'icon' => '',
);
?>