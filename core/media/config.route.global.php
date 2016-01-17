<?php
if(!defined("IS_CORE")) {
	echo "ERROR";
	die();
}

Route::Set("install_first", "install(/step<line>)")->defaults(array(
	'page' => 'install',
	'method' => 'change',
	'is_file' => true,
	'line' => "0",
	'file' => ROOT_PATH."install.php",
));

Route::Set("news", "post/<view>")->defaults(array(
	'page' => 'news',
	'view' => "",
));

Route::Set("post", "post/<action>", array("action" => "(add|edit|delete)"))->defaults(array(
	'page' => 'post',
	'action' => "",
));