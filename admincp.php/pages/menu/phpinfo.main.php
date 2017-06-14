<?php
$links['phpinfo']["cat"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Phpinfo",
'title' => "PhpInfo",
'type' => "cat",
'access' => LEVEL_ADMIN || LEVEL_CREATOR,
'icon' => 'fa-fighter-jet',
);
$links['phpinfo']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Phpinfo",
'title' => "PhpInfo",
'type' => "item",
'access' => LEVEL_ADMIN || LEVEL_CREATOR,
'icon' => '',
);
?>