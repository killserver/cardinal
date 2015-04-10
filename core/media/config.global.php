<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

define("VERSION", "1.25.4a1");
define("LEVEL_MODER", 2);
define("LEVEL_USER", 1);
define("LEVEL_GUEST", 0);
define("S_TIME_VIEW", "d-m-Y H:i:s");

$config = array_merge($config, array(
	"git_install" => true,
));

?>