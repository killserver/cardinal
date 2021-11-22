<?php
if(version_compare(PHP_VERSION, '5.3.0', '>=')) {
	spl_autoload_register('cardinalAutoloadAdmin', true, false);
} else {
	spl_autoload_register('cardinalAutoloadAdmin');
}