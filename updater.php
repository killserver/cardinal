<?php
if(!defined("IS_CORE")) {
	echo "403 ERROR";
	die();
}
function safeSave($file, $d) {
	$ret = false;
	$fp = fopen($file, "w+");
	if(flock($fp, LOCK_EX)) {
		ftruncate($fp, 0);
		fwrite($fp, $d);
		fflush($fp);
		flock($fp, LOCK_UN);
		$ret = true;
	}
	fclose($fp);
	return $ret;
}
function is_json($str) {
	return $str === '""' || $str === '[]' || $str === '{}' || $str[0] === '"' && substr($str, -1) === '"' || $str[0] === '[' && substr($str, -1) === ']' || $str[0] === '{' && substr($str, -1) === '}';
}
function is_serialized($data) {
	if(!is_string($data)) {
		return false;
	}
	$data = trim($data);
	if('N;' == $data) {
		return true;
	}
	if(!preg_match('/^([adObis]):/', $data, $badions)) {
		return false;
	}
	switch($badions[1]) {
		case 'a':
		case 'O':
		case 's':
			if (preg_match("/^".$badions[1].":[0-9]+:.*[;}]\$/s", $data)) {
				return true;
			}
		break;
		case 'b':
		case 'i':
		case 'd':
			if(preg_match("/^".$badions[1].":[0-9.E-]+;\$/", $data)) {
				return true;
			}
		break;
	}
	return false;
}
function normalizerJSON($data) {
	$arr = array();
	$tab = 1;
	$d = false;
	for($f=0;$f<strlen($data);$f++) {
		$bytes = $data[$f];
		if($d && $bytes === $d) {
			$data[$f - 1] !== "\\" && ($d = !1);
		} else if(!$d && ($bytes === '"' || $bytes === "'")) {
			$d = $bytes;
		} else if(!$d && ($bytes === " " || $bytes === "\t")) {
			$bytes = "";
		} else if(!$d && $bytes === ":") {
			$bytes = $bytes." ";
		} else if(!$d && $bytes === ",") {
			$bytes = $bytes."\n";
			$bytes = str_pad($bytes, ($tab * 2), " ");
		} else if(!$d && ($bytes === "[" || $bytes === "{")) {
			$tab++;
			$bytes .= "\n";
			$bytes = str_pad($bytes, ($tab * 2), " ");
		} else if(!$d && ($bytes === "]" || $bytes === "}")) {
			$tab--;
			$bytes = str_pad("\n", ($tab * 2), " ").$bytes;
		}
		array_push($arr, $bytes);
	}
	return implode("", $arr);
}
function jsonEncode($arr) {
	if(defined('JSON_UNESCAPED_UNICODE')) {
		return json_encode($arr, JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
	} else {
		return preg_replace_callback('/(?<!\\\\)\\\\u([0-9a-f]{4})/i', "json_encode_unicode_fn", json_encode($arr));
	}
}
function json_encode_unicode_fn($m) {
	$d = pack("H*", $m[1]);
	$r = mb_convert_encoding($d, "UTF8", "UTF-16BE");
	return $r!=="?" && $r!=="" ? $r : $m[0];
}
if(file_exists(ROOT_PATH."core".DS."modules".DS)) {
	if(file_exists(ROOT_PATH."core".DS."modules".DS."loader.default.".ROOT_EX)) {
		$file = file_get_contents(ROOT_PATH."core".DS."modules".DS."loader.default.".ROOT_EX);
		$file = str_replace('"core\\', '"application".DS."', $file);
		$file = str_replace('"core/', '"application".DS."', $file);
		$file = str_replace('"core".DS', '"application".DS', $file);
		file_put_contents(ROOT_PATH."core".DS."modules".DS."loader.default.".ROOT_EX, $file);
	}
	if(file_exists(ROOT_PATH."core".DS."modules".DS."loader.".ROOT_EX)) {
		$file = file_get_contents(ROOT_PATH."core".DS."modules".DS."loader.".ROOT_EX);
		$file = str_replace('"core\\', '"application".DS."', $file);
		$file = str_replace('"core/', '"application".DS."', $file);
		$file = str_replace('"core".DS', '"application".DS', $file);
		file_put_contents(ROOT_PATH."core".DS."modules".DS."loader.".ROOT_EX, $file);
	}
	if(!file_exists(ROOT_PATH."core".DS."cache".DS."system".DS."application.lock")) {
		function rrmdirModules($dir) {
			if(is_dir($dir)) {
				$files = @scandir($dir);
				foreach($files as $file) {
					if($file != "." && $file != "..") {
						if(is_file($dir.DS.$file)) {
							@unlink($dir.DS.$file);
						} else if(is_dir($dir.DS.$file.DS)) {
							rrmdirModules($dir.DS.$file.DS);
							@rmdir($dir.DS.$file.DS);
						}
					}
				}
				if($dir != "." && $dir != "..") {
					@unlink($dir);
				}
			}
		}
		function rcopyModules($src, $dst) {
			if(is_dir($src)) {
				@mkdir($dst, 0777);
				$files = @scandir($src);
				foreach($files as $file) {
					if($file != "." && $file != "..") {
						rcopyModules($src.DS.$file, $dst.DS.$file);
					}
				}
			} else if(file_exists($src)) {
				@copy($src, $dst);
			}
		}
		rcopyModules(ROOT_PATH."core".DS."modules", ROOT_PATH."application");
		rrmdirModules(ROOT_PATH."core".DS."modules");
		@mkdir(ROOT_PATH."core".DS."modules");
		if(!file_exists(ROOT_PATH."application".DS."modules".DS)) {
			if(!file_exists(ROOT_PATH."application".DS."modules".DS)) {
				@mkdir(ROOT_PATH."application".DS."modules".DS, 0777, true);
			}
			$files = @scandir(ROOT_PATH."application".DS);
			foreach($files as $file) {
				if(is_file(ROOT_PATH."application".DS.$file)) {
					@copy(ROOT_PATH."application".DS.$file, ROOT_PATH."application".DS."modules".DS.$file);
					@unlink(ROOT_PATH."application".DS.$file);
				}
			}
		}
		@file_put_contents(ROOT_PATH."core".DS."cache".DS."system".DS."application.lock", "");
	}
	if(!defined("PATH_MODULES")) {
		define("PATH_MODULES", ROOT_PATH."application".DS."modules".DS);
	}
	if(!defined("PATH_AUTOLOADS")) {
		define("PATH_AUTOLOADS", ROOT_PATH."application".DS."autoload".DS);
	}
	if(!defined("PATH_HOOKS")) {
		define("PATH_HOOKS", ROOT_PATH."application".DS."hooks".DS);
	}
	if(!defined("PATH_LOAD_LIBRARY")) {
		define("PATH_LOAD_LIBRARY", ROOT_PATH."application".DS."library".DS);
	}
	if(!defined("PATH_MODELS")) {
		define("PATH_MODELS", ROOT_PATH."application".DS."models".DS);
	}
	if(!defined("PATH_CRON_FILES")) {
		define("PATH_CRON_FILES", ROOT_PATH."application".DS."cron".DS);
	}
}
if(defined("PATH_CACHE_USERDATA") && file_exists(PATH_CACHE_USERDATA."userlevels.txt")) {
	$f = file_get_contents(PATH_CACHE_USERDATA."userlevels.txt");
	if(is_serialized($f)) {
		$usersFile = unserialize($f);
		$users = array_merge($users, $usersFile);
	} else if(is_json($f)) {
		$usersFile = json_decode($f, true);
		$users = array_merge($users, $usersFile);
	}
	$path = dirname(PATH_CACHE_USERDATA."userlevels.txt");
	if(!is_writable($path)) {
		@chmod($path, 0777);
	}
	$d = safeSave(PATH_CACHE_USERDATA."userlevels.php", "<?php die(); ?>".normalizerJSON(jsonEncode($usersFile)));
	if($d) {
		unlink(PATH_CACHE_USERDATA."userlevels.txt");
	}
}
if(defined("PATH_CACHE_USERDATA") && file_exists(PATH_CACHE_USERDATA."userlevels.php")) {
	$file = file_get_contents(PATH_CACHE_USERDATA."userlevels.php");
	$file = preg_replace("#\<\?(.*?)\?\>#is", "", $file);
	if(is_serialized($file)) {
		$usersFile = unserialize($file);
		$path = dirname(PATH_CACHE_USERDATA."userlevels.php");
		if(!is_writable($path)) {
			@chmod($path, 0777);
		}
		$d = safeSave(PATH_CACHE_USERDATA."userlevels.php", "<?php die(); ?>".normalizerJSON(jsonEncode($usersFile)));
		if($d && file_exists(PATH_CACHE_USERDATA."userlevels.txt")) {
			unlink($txt);
		}
		$users = array_merge($users, $usersFile);
	}
}
if(defined("PATH_CACHE_USERDATA") && file_exists(PATH_CACHE_USERDATA."userList.txt")) {
	$f = file_get_contents(PATH_CACHE_USERDATA."userList.txt");
	if(is_serialized($f)) {
		$usersFile = unserialize($f);
		$users = array_merge($users, $usersFile);
	} else if(is_json($f)) {
		$usersFile = json_decode($f, true);
		$users = array_merge($users, $usersFile);
	}
	$path = dirname(PATH_CACHE_USERDATA."userList.txt");
	if(!is_writable($path)) {
		@chmod($path, 0777);
	}
	$d = safeSave(PATH_CACHE_USERDATA."userList.php", "<?php die(); ?>".normalizerJSON(jsonEncode($usersFile)));
	if($d) {
		unlink(PATH_CACHE_USERDATA."userList.txt");
	}
}
if(defined("PATH_CACHE_USERDATA") && file_exists(PATH_CACHE_USERDATA."userList.php")) {
	$file = file_get_contents(PATH_CACHE_USERDATA."userList.php");
	$file = preg_replace("#\<\?(.*?)\?\>#is", "", $file);
	if(is_serialized($file)) {
		$usersFile = unserialize($file);
		$path = dirname(PATH_CACHE_USERDATA."userList.php");
		if(!is_writable($path)) {
			@chmod($path, 0777);
		}
		$d = safeSave(PATH_CACHE_USERDATA."userList.php", "<?php die(); ?>".normalizerJSON(jsonEncode($usersFile)));
		if($d && file_exists(PATH_CACHE_USERDATA."userList.txt")) {
			unlink($txt);
		}
		$users = array_merge($users, $usersFile);
	}
}
if(defined("PATH_CACHE_USERDATA") && file_exists(PATH_CACHE_USERDATA."configWithoutDB.txt") && !file_exists(PATH_CACHE_USERDATA."configWithoutDB.php")) {
	$file = file_get_contents(PATH_CACHE_USERDATA."configWithoutDB.txt");
	$file = '<?php die(); ?>'.$file;
	@file_put_contents(PATH_CACHE_USERDATA."configWithoutDB.php", $file);
	@unlink(PATH_CACHE_USERDATA."configWithoutDB.txt");
}
if(defined("PATH_FUNCTIONS") && file_exists(PATH_FUNCTIONS."login.".ROOT_EX)) {
	@unlink(PATH_FUNCTIONS."login.".ROOT_EX);
}
if(defined("PATH_FUNCTIONS") && file_exists(PATH_FUNCTIONS."parser.".ROOT_EX)) {
	@unlink(PATH_FUNCTIONS."parser.".ROOT_EX);
}
if(defined("PATH_PAGES") && file_exists(PATH_PAGES."reg.".ROOT_EX)) {
	@unlink(PATH_PAGES."reg.".ROOT_EX);
}
if(defined("PATH_PAGES") && file_exists(PATH_PAGES."search.".ROOT_EX)) {
	@unlink(PATH_PAGES."search.".ROOT_EX);
}
if(defined("PATH_PAGES") && file_exists(PATH_PAGES."view.".ROOT_EX)) {
	@unlink(PATH_PAGES."view.".ROOT_EX);
}
if(defined("PATH_PAGES") && file_exists(PATH_PAGES."post.".ROOT_EX)) {
	@unlink(PATH_PAGES."post.".ROOT_EX);
}
if(defined("ADMINCP_DIRECTORY") && file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS."antivirus.main.".ROOT_EX)) {
	@unlink(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS."antivirus.main.".ROOT_EX);
}
if(defined("ADMINCP_DIRECTORY") && file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS."customizer.main.".ROOT_EX)) {
	@unlink(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS."customizer.main.".ROOT_EX);
}
if(defined("ADMINCP_DIRECTORY") && file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS."editor.main.".ROOT_EX)) {
	@unlink(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS."editor.main.".ROOT_EX);
}
if(defined("ADMINCP_DIRECTORY") && file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS."editor.main.".ROOT_EX)) {
	@unlink(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS."editor.main.".ROOT_EX);
}
if(defined("ADMINCP_DIRECTORY") && file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS."loginadmin.main.".ROOT_EX)) {
	@unlink(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS."loginadmin.main.".ROOT_EX);
}
if(defined("ADMINCP_DIRECTORY") && file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS."logs.main.".ROOT_EX)) {
	@unlink(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS."logs.main.".ROOT_EX);
}
if(defined("ADMINCP_DIRECTORY") && file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS."phpinfo.main.".ROOT_EX)) {
	@unlink(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS."phpinfo.main.".ROOT_EX);
}
if(defined("ADMINCP_DIRECTORY") && file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS."Recyclebin.main.".ROOT_EX)) {
	@unlink(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS."Recyclebin.main.".ROOT_EX);
}
if(defined("ADMINCP_DIRECTORY") && file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS."users.main.".ROOT_EX)) {
	@unlink(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."menu".DS."users.main.".ROOT_EX);
}
if(defined("ADMINCP_DIRECTORY") && file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS."Antivirus".DS."Antivirus.class.".ROOT_EX)) {
	@unlink(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS."Antivirus".DS."Antivirus.class.".ROOT_EX);
}
if(defined("ADMINCP_DIRECTORY") && file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS."Antivirus".DS.".htaccess")) {
	@unlink(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS."Antivirus".DS.".htaccess");
}
if(defined("ADMINCP_DIRECTORY") && file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS."Antivirus".DS."index.".ROOT_EX)) {
	@unlink(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS."Antivirus".DS."index.".ROOT_EX);
}
if(defined("ADMINCP_DIRECTORY") && file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS."Antivirus".DS."index.html")) {
	@unlink(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS."Antivirus".DS."index.html");
}
if(defined("ADMINCP_DIRECTORY") && file_exists(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS."Antivirus".DS)) {
	@rmdir(ROOT_PATH.ADMINCP_DIRECTORY.DS."pages".DS."Viewer".DS."Antivirus".DS);
}
if(file_exists(PATH_MEDIA."config.settings.".ROOT_EX) && !file_exists(PATH_MEDIA."config.init.".ROOT_EX)) {
	@rename(PATH_MEDIA."config.settings.".ROOT_EX, PATH_MEDIA."config.init.".ROOT_EX);
}
if(defined("WITHOUT_DB")) {
	if(!defined("PREFIX_DB")) {
		$file = "cardinal_";
		if(file_exists(PATH_MEDIA."prefix_db.lock") && is_readable(PATH_MEDIA."prefix_db.lock")) {
			$file = file_get_contents(PATH_MEDIA."prefix_db.lock");
		} elseif(is_writable(PATH_MEDIA."prefix_db.lock")) {
			$file = "cd".uniqid();
			@file_put_contents(PATH_MEDIA."prefix_db.lock", $file);
		}
		define("PREFIX_DB", $file);
	}
}
if(file_exists(PATH_CACHE_USERDATA."trashBin.lock") && file_exists(PATH_MEDIA."db.php")) {
	function cardinalAutoload($class) {
	    if(stripos(ini_get('include_path'), $class)!==false && class_exists($class, false)) {
	        return false;
	    }
	    $class = str_replace("\\", DIRECTORY_SEPARATOR, $class);
	    if(file_exists(PATH_AUTOLOADS.$class.".".ROOT_EX)) {
	        include_once(PATH_AUTOLOADS.$class.".".ROOT_EX);
	    } elseif(file_exists(PATH_CLASS.$class.".".ROOT_EX)) {
	        include_once(PATH_CLASS.$class.".".ROOT_EX);
	    } elseif(file_exists(PATH_SYSTEM.$class.".".ROOT_EX)) {
	        include_once(PATH_SYSTEM.$class.".".ROOT_EX);
	    } elseif(file_exists(PATH_DB_DRIVERS.$class.".".ROOT_EX)) {
	        include_once(PATH_DB_DRIVERS.$class.".".ROOT_EX);
	    }
	}
	if(version_compare(PHP_VERSION, '5.1.2', '>=')) {
		include(dirname(__FILE__).DIRECTORY_SEPARATOR."core".DIRECTORY_SEPARATOR."register70.php");
	} else {
		include(dirname(__FILE__).DIRECTORY_SEPARATOR."core".DIRECTORY_SEPARATOR."register53.php");
	}
	$config = array();
	include(PATH_MEDIA."db.php");
	db::config($config);
	new db();
	$row = db::doquery("SELECT * FROM `information_schema`.`tables` WHERE table_schema = '".$config['db']['db']."' AND table_name = '".(defined("PREFIX_DB") ? PREFIX_DB : "")."trashBin' LIMIT 1");
	if(!is_null($row)) {
		db::query("ALTER TABLE {{trashBin}} RENAME TO {{trashbin}}");
	}
}//
$f = file_get_contents(PATH_SKINS."core".DS."updater.html");
$f = str_replace("{path}", (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], "admincp.php")!==false ? "../" : "./"), $f);
echo $f;
@unlink(__FILE__);
die();