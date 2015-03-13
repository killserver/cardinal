<?php
define("IS_ADMIN", true);
include_once(dirname(__FILE__)."/core.php");
$Timer = microtime()-$Timer;
GzipOut(templates::$gzip);
unset($templates);
unset($db);


?>