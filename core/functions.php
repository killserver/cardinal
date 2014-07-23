<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

require_once(ROOT_PATH."core/media/config.".ROOT_EX);
require_once(ROOT_PATH."core/media/db.".ROOT_EX);

spl_autoload_register(function($class) {
	if(file_exists(ROOT_PATH."core/class/".$class.".".ROOT_EX)) {
		include_once(ROOT_PATH."core/class/".$class.".".ROOT_EX);
	} elseif(file_exists(ROOT_PATH."core/class/system/".$class.".".ROOT_EX)) {
		include_once(ROOT_PATH."core/class/system/".$class.".".ROOT_EX);
	} elseif(file_exists(ROOT_PATH."core/modules/".$class.".".ROOT_EX)) {
		include_once(ROOT_PATH."core/modules/".$class.".".ROOT_EX);
	}
});

function include_dir($dir = null, $modules = null) {
	if(empty($dir)) {
		$dir = ROOT_PATH."core/functions/";
	}
	if(!empty($modules)) {
		$strpos = ".class.".ROOT_EX;
	} else {
		$strpos = ".".ROOT_EX;
	}
	if(is_dir($dir)) {
		if($dh = dir($dir)) {
			while(($file = $dh->read()) !== false) {
				if($file != "index.".ROOT_EX && $file != "." && $file != ".." && strpos($file, $strpos) !== false) {
					require_once($dir.$file);
					if(!empty($modules)) {
						$class = str_replace($strpos, "", $file);
						$classes = new $class();
						unset($classes);
					}
				}
			}
		$dh->close();
		}
	}
}
include_dir(ROOT_PATH."core/modules/", true);
include_dir();

?>