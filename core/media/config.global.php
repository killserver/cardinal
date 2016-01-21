<?php
/*
 *
 * @version 3.2
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 3.2
 * Version File: 1
 *
 * 1.1
 * add revision
 * 1.2
 * add support moder and admin
 * 1.3
 * add support upload and debug
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

// Cache
define("CACHE_MEMCACHE", 1);
define("CACHE_MEMCACHED", 2);
define("CACHE_FILE", 3);
define("CACHE_FTP", 4);
define("CACHE_NONE", 0);

// Error
define("ERROR_FILE", 0);
define("ERROR_DB", 1);

// Debug
define("DEBUG_MEMORY", 1);
define("DEBUG_TIME", 2);
define("DEBUG_FILES", 3);
define("DEBUG_INCLUDE", 4);
define("DEBUG_DB", 5);
define("DEBUG_TEMPLATE", 6);
define("DEBUG_FILE", 12);
define("DEBUG_CORE", 24);
define("DEBUG_DBTEMP", 30);
define("DEBUG_ALL", 720);

// Upload
define("UPLOAD_CORE", 10);
define("UPLOAD_FTP", 20);
define("UPLOAD_MYSQL", 30);
define("UPLOAD_DROPBOX", 40);
define("UPLOAD_CF", 200);
define("UPLOAD_CFM", 6000);
define("UPLOAD_CM", 12000);
define("UPLOAD_CMD", 12000);
define("UPLOAD_ALL", 240000);

define("VERSION", "3.2");
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