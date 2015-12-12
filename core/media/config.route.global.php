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

Route::Set("add", "post/add")->defaults(array(
	'page' => 'add',
));