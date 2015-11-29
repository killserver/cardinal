<?php
/*
 *
 * @version 1.25.6-rc6
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc6
 * Version File: 1
 *
 * 1.1
 * add revision
 * 1.2
 * add support moder and admin
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

define("CACHE_MEMCACHE", 1);
define("CACHE_MEMCACHED", 2);
define("CACHE_FILE", 3);
define("CACHE_FTP", 4);
define("CACHE_NONE", 0);

define("ERROR_FILE", 0);
define("ERROR_DB", 1);

define("VERSION", "1.25.7-a5");
define("LEVEL_ADMIN", 3);
define("LEVEL_MODER", 2);
define("LEVEL_USER", 1);
define("LEVEL_GUEST", 0);
define("S_TIME_VIEW", "d-m-Y H:i:s");

$config = array_merge($config, array(
	"git_install" => true,
	"git_beta" => true,
));

?>