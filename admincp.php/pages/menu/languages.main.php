<?php
$links['languages']["cat"][] = array(
	'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Languages",
	'title' => "{L_Languages}",
	'type' => "cat",
	'access' => LEVEL_ADMIN || LEVEL_CREATOR,
	'icon' => 'fa-language',
);
$links['languages']["item"][] = array(
	'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Languages",
	'title' => "{L_Languages}",
	'type' => "item",
	'access' => LEVEL_ADMIN || LEVEL_CREATOR,
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
		'access' => LEVEL_ADMIN || LEVEL_CREATOR,
		'icon' => '',
	);
}
?>