<?php
$links['AText']["cat"][] = array(
'link' => "#",
'title' => "{L_\"Редактирование текста на страницах\"}",
'type' => "cat",
'access' => !defined("WITHOUT_DB") || db::connected() && userlevel::get("atextadmin") && !userlevel::get("seoBlock") || !file_exists(PATH_CACHE_SYSTEM."seoBlock.lock"),
'icon' => 'fa-folder-o',
);
$links['AText']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=ATextAdmin",
'title' => "{L_\"Редактирование текста на страницах\"}",
'type' => "item",
'access' => !defined("WITHOUT_DB") || db::connected() && userlevel::get("atextadmin") && !userlevel::get("seoBlock") || !file_exists(PATH_CACHE_SYSTEM."seoBlock.lock"),
'icon' => '',
);
?>