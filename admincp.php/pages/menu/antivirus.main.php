<?php
$links['Antivirus']["cat"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Antivirus",
'title' => "{L_Antivirus}",
'type' => "cat",
'access' => (LEVEL_ADMIN || LEVEL_CREATOR) && is_writable(PATH_CACHE_SYSTEM.DS),
'icon' => 'fa-users',
);
$links['Antivirus']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Antivirus",
'title' => "{L_Antivirus}",
'type' => "item",
'access' => (LEVEL_ADMIN || LEVEL_CREATOR) && is_writable(PATH_CACHE_SYSTEM.DS),
'icon' => '',
);
?>