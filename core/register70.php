<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}
if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
	spl_autoload_register('cardinalAutoload', true, true);
} else {
	spl_autoload_register('cardinalAutoload');
}