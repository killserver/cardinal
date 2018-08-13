<?php
$links['languages']["cat"][] = array(
	'link' => "#",
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
$support = lang::support(true);
for($i=0;$i<sizeof($support);$i++) {
	$langer = nucfirst($support[$i]);
	$links['languages']["item"][] = array(
		'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Languages&lang=".$support[$i],
		'title' => "{L_Languages}&nbsp;".$langer,
		'type' => "item",
		'access' => userlevel::get("languages"),
		'icon' => '',
	);
}
?>