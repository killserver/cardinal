<?php
$links['seoBlock']["cat"][] = array(
'link' => "{C_default_http_host}".(!defined("ADMINCP_DIRECTORY") ? "admincp.php" : ADMINCP_DIRECTORY)."/?pages=Archer&type=".(defined("PREFIX_DB") ? PREFIX_DB : "")."seoBlock",
'title' => (defined("ADMINCP_DIRECTORY") ? "{L_\"SEO Block\"}" : "SEO Block"),
'type' => "cat",
'access' => userlevel::get("seoBlock") && db::connected(),
'icon' => 'fa-bicycle',
);
$links['seoBlock']["item"][] = array(
'link' => "{C_default_http_host}".(!defined("ADMINCP_DIRECTORY") ? "admincp.php" : ADMINCP_DIRECTORY)."/?pages=Archer&type=".(defined("PREFIX_DB") ? PREFIX_DB : "")."seoBlock",
'title' => (defined("ADMINCP_DIRECTORY") ? "{L_\"SEO Block\"}" : "SEO Block"),
'type' => "item",
'access' => userlevel::get("seoBlock") && db::connected(),
'icon' => '',
);
?>