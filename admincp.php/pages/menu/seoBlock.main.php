<?php
$links['seoBlock']["cat"][] = array(
'link' => "#",
'title' => (defined("ADMINCP_DIRECTORY") ? "{L_\"SEO Block\"}" : "SEO Block"),
'type' => "cat",
'access' => (userlevel::get("seo") || userlevel::get("seoBlock")),
'icon' => 'fa-bicycle',
);
$links['seoBlock']["item"][] = array(
'link' => "{C_default_http_host}".(!defined("ADMINCP_DIRECTORY") ? "admincp.php" : ADMINCP_DIRECTORY)."/?pages=Archer&type=seoBlock",
'title' => (defined("ADMINCP_DIRECTORY") ? "{L_\"SEO Pages\"}" : "SEO Pages"),
'type' => "item",
'access' => userlevel::get("seoBlock") && db::connected(),
'icon' => '',
);
$links['seoBlock']["item"][] = array(
'link' => "{C_default_http_host}".(!defined("ADMINCP_DIRECTORY") ? "admincp.php" : ADMINCP_DIRECTORY)."/?pages=SEO",
'title' => (defined("ADMINCP_DIRECTORY") ? "{L_\"Мета-поля&nbsp;/&nbsp;счётчики\"}" : "Мета-поля&nbsp;/&nbsp;счётчики"),
'type' => "item",
'access' => userlevel::get("seo"),
'icon' => '',
);
?>