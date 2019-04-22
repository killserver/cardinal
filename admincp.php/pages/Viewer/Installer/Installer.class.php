<?php

class Installer extends Core {

	private function rebuild($arr) {
		$newArr = array();
		for($i=0;$i<sizeof($arr);$i++) {
			$arr[$i]['active'] = true;
			$res = $arr[$i];
			$res = array_merge($res, array("Name" => $arr[$i][0], "File" => $arr[$i][1]));
			$newArr[$arr[$i][0]] = $res;
		}
		return $newArr;
	}

	private function rcopyModules($src, $dst) {
        if(is_dir($src)) {
            @mkdir($dst, 0777);
            $files = @scandir($src);
            foreach($files as $file) {
                if($file != "." && $file != "..") {
                    $this->rcopyModules($src.DS.$file, $dst.DS.$file);
                    @rmdir($src);
                }
            }
        } else if(file_exists($src)) {
            @copy($src, $dst);
            @unlink($src);
        }
    }

	function get_file_data($file, $default_headers, $default = false) {
		$fp = fopen($file, 'r');
		$file_data = fread($fp, 512);
		fclose($fp);
		$file_data = str_replace("\r", "\n", $file_data);
		$ret = array();
		foreach($default_headers as $field => $regex) {
			if(preg_match('/^[ \t\/*#@]*'.preg_quote($regex, '/').':(.*)$/mi', $file_data, $match) && isset($match[1])) {
				$ret[$field] = trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $match[1]));
			} else if($default!==false) {
				$ret[$field] = '';
			}
		}
		return $ret;
	}

	function readEmptyParent($file, &$arr) {
		$check = str_replace(ROOT_PATH, "", $file);
		$check = trim($check, DS);
		if(empty($check)) {
			return;
		}
		$dir = dirname($file);
		$check2 = str_replace(ROOT_PATH, "", $dir);
		if(empty($check2)) {
			return;
		}
		$empty = read_dir($dir.DS);
		if(sizeof($empty)==0) {
			$arr[] = $dir.DS;
		}
		$this->readEmptyParent($dir.DS, $arr);
	}

	function getVersion($module) {
		if(file_exists(PATH_MODULES.$module.".class.".ROOT_EX)) {
			include_once(PATH_MODULES.$module.".class.".ROOT_EX);
		}
		return (class_exists($module) && property_exists($module, "version") ? $module::$version : "0.0");
	}
	
	function __construct() {
	global $manifest;
		callAjax();

		$owner = posix_getpwuid(fileowner(__FILE__));
		if($owner['name']!=get_current_user()) {
			self::addInfo("Установка не может быть выполнена успешно из-за того, что Вы не являетесь владельцем. Владелец <b>".$owner['name']."</b>. Обратитесь в тех.поддержку для решения данного вопроса", "error", false, 1);
		}

		if(isset($_GET['remove'])) {
			if(strpos($_GET['remove'], "/")!==false || strpos($_GET['remove'], "\\")!==false) {
				header("HTTP/1.1 406 Not Acceptable");
				echo "error 1";
				return;
			}
			if(!file_exists(PATH_CACHE_USERDATA."Installer".DS.$_GET['remove'].".json") || !file_exists(PATH_MODULES.$_GET['remove'].".class.".ROOT_EX)) {
				header("HTTP/1.1 406 Not Acceptable");
				echo "error 1";
				return;
			}
			$m = new ReflectionMethod($_GET['remove'], "installation");
			$data = "";
			$f = file(PATH_MODULES.$_GET['remove'].".class.".ROOT_EX);
			for($i=$m->getStartLine();$i<$m->getEndLine()-1;$i++) {
				$data .= $f[$i];
			}
			preg_match_all("#create_table\(['\"](.+?)['\"]#is", $data, $arr);
			$forDelete = array();
			for($i=0;$i<sizeof($arr[1]);$i++) {
				if(db::getTable($arr[1][$i])!==false) {
					$forDelete[] = $arr[1][$i];
				}
			}
			for($i=0;$i<sizeof($forDelete);$i++) {
				db::query("DROP TABLE IF EXISTS {{".$forDelete[$i]."}}");
			}
			$f = file_get_contents(PATH_CACHE_USERDATA."Installer".DS.$_GET['remove'].".json");
			$f = json_decode($f, true);
			$f = array_values($f['forDelete']);
			$f = array_reverse($f);
			$arr = array();
			for($i=0;$i<sizeof($f);$i++) {
				$file = ROOT_PATH.$f[$i];
				$info = pathinfo($file);
				if(file_exists($file) && $info['basename']!=ADMINCP_DIRECTORY) {
					@unlink($file);
				}
				if($info['basename']!=ADMINCP_DIRECTORY) {
					$this->readEmptyParent($file, $arr);
				}
			}
			$arr = array_unique($arr);
			for($i=0;$i<sizeof($arr);$i++) {
				if(file_exists($arr[$i])) {
					@rmdir($arr[$i]);
				}
			}
			@unlink(PATH_CACHE_USERDATA."Installer".DS.$_GET['remove'].".json");
			if(is_writable(PATH_CACHE_SYSTEM) && file_exists(PATH_CACHE_SYSTEM."installer.txt")) {
				@unlink(PATH_CACHE_SYSTEM."installer.txt");
			}
			cardinal::RegAction("Удаление модуля ".$_GET['remove']);
			HTTP::echos("1");
			return false;
		}
		$configs = array("https://raw.githubusercontent.com/killserver/modulesForCardinal/master/list.min.json");
		$configs = execEvent("installer_servers", $configs);
		$paths = array();
		$listAll = array();
		for($i=0;$i<sizeof($configs);$i++) {
			$path = pathinfo($configs[$i]);
			$paths[] = $path['dirname']."/";
			$listMirror = new Parser($configs[$i]."?".time());
			$listMirror->timeout(3);
			$listMirror = $listMirror->get();
			$listMirror = json_decode($listMirror, true);
			if($listMirror!==null) {
				$listAll = array_merge($listAll, $listMirror);
			}
		}
		if(isset($_GET['download'])) {
			if(!isset($listAll[$_GET['download']]) || !isset($listAll[$_GET['download']]['download'])) {
				header("HTTP/1.1 406 Not Acceptable");
				echo "not found";
				return false;
			}
			$prs = new Parser($listAll[$_GET['download']]['download']."?".time());
			$prs->timeout(30);
			file_put_contents(PATH_CACHE_SYSTEM.$_GET['download'].".zip", $prs->get());
			HTTP::echos("1");
			return false;
		}
		if(isset($_GET['install'])) {
			if(!isset($listAll[$_GET['install']]) || !isset($listAll[$_GET['install']]['download'])) {
				header("HTTP/1.1 406 Not Acceptable");
				echo "not found";
				return false;
			}
			if(!file_exists(PATH_CACHE_SYSTEM.$_GET['install'].".zip")) {
				header("HTTP/1.0 404 Not Found");
				echo "not exists: ".PATH_CACHE_SYSTEM.$_GET['install'].".zip";
				die();
			}
			$tar_object = new ZipArchive();
			$list = $tar_object->open(PATH_CACHE_SYSTEM.$_GET['install'].".zip");
			if($list!==true) {
				header("HTTP/1.0 404 Not Found");
				echo "empty: ".PATH_CACHE_SYSTEM.$_GET['install'].".zip";
				die();
			}
			$path = $listAll[$_GET['install']]['download'];
			$path = str_replace($paths, "", $path);
			$path = str_replace(".zip", "", $path);
			$listFiles = array("allList" => array(), "forDelete" => array());
			for($i=0;$i<$tar_object->numFiles;$i++) {
				$file = nsubstr($tar_object->getNameIndex($i), nstrlen($path."/"));
				if(empty($file)) { continue; }
				$fileInfo = pathinfo($file);
				$listFiles['allList'][$file] = $file;
				if(!file_exists(ROOT_PATH.$file) || isset($fileInfo['extension']) && $fileInfo['basename']!=ADMINCP_DIRECTORY) {
					$listFiles['forDelete'][$file] = $file;
				}
			}
			if(!file_exists(PATH_CACHE_USERDATA."Installer".DS)) {
				@mkdir(PATH_CACHE_USERDATA."Installer".DS, 0777);
			}
			if(!is_writeable(PATH_CACHE_USERDATA."Installer".DS)) {
				@chmod(PATH_CACHE_USERDATA."Installer".DS, 0777);
			}
			@file_put_contents(PATH_CACHE_USERDATA."Installer".DS.$path.".json", json_encode($listFiles));

			$oldPath = array(
				"core".DS."class".DS."system".DS."DBDrivers".DS => str_replace(ROOT_PATH, "", PATH_DB_DRIVERS),
				"core".DS."class".DS."system".DS => str_replace(ROOT_PATH, "", PATH_SYSTEM),
				'core'.DS.'cache'.DS.'system'.DS => str_replace(ROOT_PATH, "", PATH_CACHE_SYSTEM),
				'core'.DS.'cache'.DS.'system'.DS => str_replace(ROOT_PATH, "", PATH_LOGS),
				'core'.DS.'cache'.DS.'lang'.DS => str_replace(ROOT_PATH, "", PATH_CACHE_LANGS),
				"core".DS."cache".DS."session".DS => str_replace(ROOT_PATH, "", PATH_CACHE_SESSION),
				"core".DS."class".DS => str_replace(ROOT_PATH, "", PATH_CLASS),
				"core".DS."functions".DS => str_replace(ROOT_PATH, "", PATH_FUNCTIONS),
				"core".DS."media".DS => str_replace(ROOT_PATH, "", PATH_MEDIA),
				"core".DS."lang".DS => str_replace(ROOT_PATH, "", PATH_LANGS),
				"core".DS."cache".DS => str_replace(ROOT_PATH, "", PATH_CACHE),
				"core".DS."pages".DS => str_replace(ROOT_PATH, "", PATH_PAGES),
				"application".DS."cache".DS => str_replace(ROOT_PATH, "", PATH_CACHE_USERDATA),
				"application".DS."modules".DS => str_replace(ROOT_PATH, "", PATH_MODULES),
				"application".DS."global".DS => str_replace(ROOT_PATH, "", PATH_GLOBAL),
				"application".DS."autoload".DS => str_replace(ROOT_PATH, "", PATH_AUTOLOADS),
				"application".DS."library".DS => str_replace(ROOT_PATH, "", PATH_LOAD_LIBRARY),
				"application".DS."models".DS => str_replace(ROOT_PATH, "", PATH_MODELS),
				"application".DS."cron".DS => str_replace(ROOT_PATH, "", PATH_CRON_FILES),
				"application".DS => str_replace(ROOT_PATH, "", PATH_LOADED_CONTENT),
				"skins".DS => str_replace(ROOT_PATH, "", PATH_SKINS),
				"uploads".DS."manifest".DS => str_replace(ROOT_PATH, "", PATH_MANIFEST),
				"uploads".DS => str_replace(ROOT_PATH, "", PATH_UPLOADS),
				"admincp.php" => ADMINCP_DIRECTORY,
			);
			for($i=0;$i<$tar_object->numFiles;$i++) {
				$fileName = $tar_object->getNameIndex($i);
				if(nsubstr($fileName, -1) == '/') {
					continue; // skip directories
				}
				$fileName = str_replace(array("\\", "/"), DS, ROOT_PATH.$fileName);
				$fileName = iconv("cp866", "windows-1251//IGNORE", $fileName);
				$fileName = str_replace(array_keys($oldPath), array_values($oldPath), $fileName);
				$fileName = $this->mkNotDir($fileName);

				$file = $tar_object->getFromIndex($i);
				file_put_contents($fileName, $file);
			}


			$tr = $tar_object->extractTo(ROOT_PATH);
			$this->rcopyModules(ROOT_PATH.$_GET['install'], ROOT_PATH);
			if(is_writable(PATH_CACHE_SYSTEM) && file_exists(PATH_CACHE_SYSTEM."installer.txt")) {
				@unlink(PATH_CACHE_SYSTEM."installer.txt");
			}
			cardinal::RegAction("Установка модуля ".$_GET['install']);
			if($tr === true) {
				$tar_object->close();
				unlink(PATH_CACHE_SYSTEM.$_GET['install'].".zip");
				echo "1";
			} else {
				$tar_object->close();
				header("HTTP/1.1 406 Not Acceptable");
			}
			return false;
		}
		if(isset($_GET['active'])) {
			modules::actived($_GET['active'], (modules::actived($_GET['active'])===false ? true : false));
			return false;
		}
		if(isset($_GET['updateList'])) {
			config::Update("serverList", $_POST['serverList']);
			return false;
		}
		$default_headers = array(
			'Name' => 'Name',
			'Description' => 'Description',
			'Image' => 'Image',
			'Changelog' => 'Changelog',
			"Author" => "Author",
			'Version' => 'Version',
			"OnlyUse" => "OnlyUse",
			"Hide" => "Hide",
		);
		$lists = ($manifest['log']['init_modules']);
		$dt = read_dir(PATH_MODULES, ".class.".ROOT_EX);
		$dt = array_values($dt);
		$arr = array();
		foreach($dt as $v) {
			if("SEOBlock.class.php"!==$v && "ArcherExample.class.php"!==$v && "base.class.php"!==$v && "changelog.class.php"!==$v && "mobile.class.php"!==$v) {
				$name = nsubstr($v, 0, -nstrlen(".class.".ROOT_EX));
				$arr[$name] = array($name, PATH_MODULES.$v);
				$arr[$name]['Name'] = $name;
				$arr[$name]['File'] = PATH_MODULES.$v;
				$info = $this->get_file_data(PATH_MODULES.$v, $default_headers);
				$arr[$name] = array_merge($arr[$name], $info);
			}
		}
		//$dt = array_values($dt);
		$lists = $this->rebuild($lists);
		$newList = array();
		foreach($lists as $k => $v) {
			if("SEOBlock"!==$k && "ArcherExample"!==$k && "base"!==$k && "changelog"!==$k && "mobile"!==$k && strpos($v[1], PATH_MODULES)!==false) {
				$info = $this->get_file_data($v[1], $default_headers);
				$v['active'] = true;
				$v = array_merge($v, $info);
				$newList[$k] = $v;
			}
		}
		$lists = $newList;
		$lists = array_merge($arr, $lists);
		templates::assign_var("listServer", implode("\n", $configs));
		$list = array_values($lists);
		for($i=0;$i<sizeof($list);$i++) {
			if(isset($list[$i]['Hide']) || (class_exists($list[$i][0], false) && property_exists($list[$i][0], "onlyAdmin") && $list[$i][0]::$onlyAdmin)) {
				continue;
			}
			$info = array("name" => $list[$i]['Name'], "path" => $list[$i][1], "altName" => $list[$i]['Name']);
			if(isset($list[$i]["active"]) && $list[$i]["active"]===true) {
				$info['active'] = "active";
			} else {
				$info['active'] = "unactive";
			}
			if(isset($listAll[$list[$i][0]])) {
				$info = array_merge($info, $listAll[$list[$i][0]]);
			}
			if(isset($info['description'])) {
				$info['description'] = str_replace("{", "&#123;", $info['description']);
			} else if(isset($list[$i]['Description'])) {
				$info['description'] = str_replace("{", "&#123;", $list[$i]['Description']);
			}
			if(isset($info['changelog'])) {
				$changelog = "";
				foreach($info['changelog'] as $b => $infoz) {
					$changelog .= "<b>".$b."</b><br>".$infoz."<br><br>";
				}
				$info['changelog'] = $changelog;
				$info['changelog'] = str_replace("{", "&#123;", $info['changelog']);
				$info['noChangelog'] = "false";
			} else if(isset($list[$i]['Changelog'])) {
				$info['changelog'] = str_replace("{", "&#123;", $list[$i]['Changelog']);
				$info['noChangelog'] = "false";
			} else {
				$info['noChangelog'] = "true";
			}
			if(!isset($info['description'])) {
				$info['description'] = "";
			}
			if(isset($listAll[$list[$i][0]]) && isset($listAll[$list[$i][0]]['version'])) {
				$info['version'] = $listAll[$list[$i][0]]['version'];
			}
			if(isset($list[$i]['Image'])) {
				$info['image'] = $list[$i]['Image'];
			} else if(!isset($info['image'])) {
				$info['image'] = "https://png.icons8.com/color/540/app-symbol.png";
			}
			if(isset($info['version']) && $this->getVersion($list[$i][0])<$info['version']) {
				$info['hasUpdate'] = "true";
			} else {
				$info['hasUpdate'] = "false";
			}
			if(isset($list[$i]['OnlyUse'])) {
				$info['OnlyUse'] = "true";
			} else {
				$info['OnlyUse'] = "false";
			}
			templates::Assign_vars($info, "installed", "i".$i);
		}
		foreach($listAll as $k => $v) {
			if(isset($v['description'])) {
				$v['description'] = str_replace(array("{"), array("&#123;"), $v['description']);
			}
			$v['installed'] = "1";
			$v['subName'] = $k;
			if(isset($v['buy'])) {
				$v['installed'] = "4";
				$v['buyPrice'] = $v['buy'];
			} else if(isset($lists[$k]) && isset($v['version']) && class_exists($k, false) && property_exists($k, "version") && $k::$version<$v['version']) {
				$v['installed'] = "2";
			} else if(isset($lists[$k])) {
				$v['installed'] = "3";
			}
			$listAll[$k]['installed'] = $v['installed'];
			$listAll[$k]['description'] = $v['description'];
			templates::assign_vars($v, "listAll", $k);
		}
		$langName = array(
			"module" => "{L_'Модули'}",
			"theme" => "{L_'Шаблоны'}",
			"plugins" => "{L_'Плагины'}",
			"components" => "{L_'Разделы'}",
		);
		$langName = execEvent("installer_lang_name", $langName);
		$json = json_encode($langName);
		templates::assign_var("langName", $json);
		$json = json_encode($listAll);
		$json = str_replace("'", "\\'", $json);
		templates::assign_var("infoAll", $json);
		$this->Prints("Installer");
	}

	function mkNotDir($file) {
		$dir = pathinfo($file);
		if(isset($dir['dirname'])) {
			if(!file_exists($dir['dirname'])) {
				mkdir($dir['dirname'], 0777, true);
			}
		}
		return $file;
	}
	
}