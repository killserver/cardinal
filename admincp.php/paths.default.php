<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

if(!defined("ADMIN_SKINS")) {
	define("ADMIN_SKINS", ROOT_PATH.ADMINCP_DIRECTORY.DS."temp".DS);
}
if(!defined("ADMIN_VIEWER")) {
	define("ADMIN_VIEWER", ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS);
}
if(!defined("ADMIN_LANGS")) {
	define("ADMIN_LANGS", ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Lang".DS);
}
if(!defined("ADMIN_MENU")) {
	define("ADMIN_MENU", ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS);
}
if(!defined("ADMIN_FLAGS_UI")) {
	define("ADMIN_FLAGS_UI", "https://killserver.github.io/ForCardinal/flags/");
}