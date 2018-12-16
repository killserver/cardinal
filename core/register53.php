<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}
function __autoload($class) {
	cardinalAutoload($class);
}