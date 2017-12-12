<?php
$links['languages']["cat"][] = array(
	'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Languages&page=main",
	'title' => "{L_Languages}",
	'type' => "cat",
	'access' => userlevel::get("languages"),
	'icon' => 'fa-language',
);
$links['languages']["item"][] = array(
	'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Languages&page=main",
	'title' => "{L_Languages}",
	'type' => "item",
	'access' => userlevel::get("languages"),
	'icon' => '',
);
$links['languages']["item"][] = array(
	'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Languages&lang=".config::Select("lang"),
	'title' => "{L_Languages}&nbsp;".nucfirst(config::Select("lang")),
	'type' => "item",
	'access' => userlevel::get("languages"),
	'icon' => '',
);
$support = lang::support();
for($i=1;$i<sizeof($support);$i++) {
	$clearLang = nsubstr($support[$i], 4, -3);
	$langer = nucfirst($clearLang);
	$links['languages']["item"][] = array(
		'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Languages&lang=".$clearLang,
		'title' => "{L_Languages}&nbsp;".$langer,
		'type' => "item",
		'access' => userlevel::get("languages"),
		'icon' => '',
	);
}
?>