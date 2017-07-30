<?php
$links['phpinfo']["cat"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Phpinfo",
'title' => "PhpInfo",
'type' => "cat",
'access' => userlevel::get("phpinfo"),
'icon' => 'fa-fighter-jet',
);
$links['phpinfo']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Phpinfo",
'title' => "PhpInfo",
'type' => "item",
'access' => userlevel::get("phpinfo"),
'icon' => '',
);
?>