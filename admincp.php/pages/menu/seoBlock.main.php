<?php
$links['seoBlock']["cat"][] = array(
'link' => "#",
'title' => (defined("ADMINCP_DIRECTORY") ? "{L_\"SEO\"}" : "SEO"),
'type' => "cat",
'access' => (userlevel::get("seo") || userlevel::get("seoBlock")),
'icon' => 'fa-bicycle',
);
if(userlevel::get("seoBlock") && db::connected() && db::getTable("seoBlock")) {
$links['seoBlock']["cat"][] = array(
'link' => "#",
'title' => (defined("ADMINCP_DIRECTORY") ? "{L_\"SEO Block\"}" : "SEO Block"),
'type' => "cat",
'access' => (userlevel::get("seo") || userlevel::get("seoBlock")),
'icon' => '',
);
}
$links['seoBlock']["item"][] = array(
'link' => "{C_default_http_host}".(!defined("ADMINCP_DIRECTORY") ? "admincp.php" : ADMINCP_DIRECTORY)."/?pages=SEO",
'title' => (defined("ADMINCP_DIRECTORY") ? "{L_\"SEO\"}" : "SEO"),
'type' => "item",
'access' => userlevel::get("seo"),
'icon' => '',
);
?>