<?php
$links['seoBlock']["cat"][] = array(
'link' => "{C_default_http_host}".(!defined("ADMINCP_DIRECTORY") ? "admincp.php" : ADMINCP_DIRECTORY)."/?pages=Archer&type=seoBlock",
'title' => (defined("ADMINCP_DIRECTORY") ? "{L_\"SEO Block\"}" : "SEO Block"),
'type' => "cat",
'access' => LEVEL_ADMIN && db::connected(),
'icon' => 'fa-bicycle',
);
$links['seoBlock']["item"][] = array(
'link' => "{C_default_http_host}".(!defined("ADMINCP_DIRECTORY") ? "admincp.php" : ADMINCP_DIRECTORY)."/?pages=Archer&type=seoBlock",
'title' => (defined("ADMINCP_DIRECTORY") ? "{L_\"SEO Block\"}" : "SEO Block"),
'type' => "item",
'access' => LEVEL_ADMIN && db::connected(),
'icon' => '',
);
?>