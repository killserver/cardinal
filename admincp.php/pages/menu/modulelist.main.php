<?php
$links['ModuleList']["cat"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=ModuleList",
'title' => "{L_ModuleList}",
'type' => "cat",
'access' => userlevel::get("modulelist") && !defined("WITHOUT_DB"),
'icon' => 'fa-folder',
);
$links['ModuleList']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=ModuleList",
'title' => "{L_ModuleList}",
'type' => "item",
'access' => userlevel::get("modulelist") && !defined("WITHOUT_DB"),
'icon' => '',
);
?>