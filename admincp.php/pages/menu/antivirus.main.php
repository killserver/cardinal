<?php
$links['Antivirus']["cat"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Antivirus",
'title' => "{L_Antivirus}",
'type' => "cat",
'access' => LEVEL_ADMIN && is_writable(ROOT_PATH."core".DS."cache".DS."system".DS),
'icon' => 'fa-users',
);
$links['Antivirus']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Antivirus",
'title' => "{L_Antivirus}",
'type' => "item",
'access' => LEVEL_ADMIN && is_writable(ROOT_PATH."core".DS."cache".DS."system".DS),
'icon' => '',
);
?>