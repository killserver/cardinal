<?php

class Editor extends Core {

	function __construct() {
		if(Arr::get($_GET, 'tree', false)) {
			Debug::activShow(false);
			templates::gzip(false);
			$root = ROOT_PATH;
			$_POST['dir'] = urldecode($_POST['dir']);
			if(file_exists($root.$_POST['dir'])) {
				$files = scandir($root . $_POST['dir']);
				natcasesort($files);
				if(count($files)>2) { /* The 2 accounts for . and .. */
					echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
					// All dirs
					foreach($files as $file) {
						if(file_exists($root.$_POST['dir'].$file) && $file != '.' && $file != '..' && is_dir($root.$_POST['dir'].$file)) {
							echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"".htmlentities($_POST['dir'].$file)."/\">".htmlentities($file)."</a></li>";
						}
					}
					// All files
					foreach($files as $file) {
						if(file_exists($root.$_POST['dir'].$file) && $file != '.' && $file != '..' && !is_dir($root.$_POST['dir'].$file) ) {
							$ext = preg_replace('/^.*\./', '', $file);
							echo "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"".htmlentities($_POST['dir'].$file)."\">".htmlentities($file)."</a></li>";
						}
					}
					echo "</ul>";	
				}
			}
			die();
		}
		if(Arr::get($_GET, 'load', false)) {
			Debug::activShow(false);
			templates::gzip(false);
			$load = substr(Arr::get($_GET, 'load'), 1);
			header("content-type: text/plain");
			$file = file_get_contents(ROOT_PATH.$load);
			$file = str_replace("<?", "<\?", $file);
			echo $file;
			die();
		}
		if(Arr::get($_GET, 'save', false)) {
			Debug::activShow(false);
			templates::gzip(false);
			file_put_contents(ROOT_PATH.Arr::get($_GET, 'save'), Arr::get($_POST, 'data'));
			echo "done";
			die();
		}
		if(Arr::get($_GET, 'getType', false)) {
			Debug::activShow(false);
			templates::gzip(false);
			$types = array(
				".htaccess" => "apache_conf",
				".conf" => "apache_conf",

				".as" => "actionscript",

				".cs" => "csharp",

				".css" => "css",

				".diff" => "diff",

				".env" => "dot",

				".html" => "html",
				".htm" => "html",
				".tpl" => "html",

				".ini" => "ini",

				".js" => "javascript",

				".json" => "json",

				".less" => "less",

				//".sql" => "mysql",
				".mysql" => "mysql",

				".php" => "php",
				".php5" => "php",

				".sass" => "sass",

				".scss" => "scss",

				".sql" => "sql",

				".svg" => "svg",

				".txt" => "text",

				".xml" => "xml",

				"default" => "text",
			);
			$file = Arr::get($_GET, "getType", ".text");
			$type = ".text";
			if(strpos($file, ".")!==false) {
				$type = substr($file, strrpos($file, "."), strlen($file));
			}
			if(isset($types[$type])) {
				$type = $types[$type];
			} else {
				$type = $types['default'];
			}
			echo $type;
			die();
		}
		$tpl = templates::load_templates("Editor");
		$file = Arr::get($_GET, "file");
		$tpl = str_replace("{file}", $file, $tpl);
		$this->Prints($tpl, true);
	}

}