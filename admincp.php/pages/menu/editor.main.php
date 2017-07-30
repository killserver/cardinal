<?php
$links['editor']["cat"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Editor",
'title' => "{L_Editor}",
'type' => "cat",
'access' => userlevel::get("editor"),
'icon' => 'fa-folder-open-o',
);
$links['editor']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Editor",
'title' => "{L_Editor}",
'type' => "item",
'access' => userlevel::get("editor"),
'icon' => '',
);
?>