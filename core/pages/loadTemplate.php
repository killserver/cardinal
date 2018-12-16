<?php

class page {
	
	function __construct() {
		execEvent("pre_load_template", Route::param("template"));
		if(Route::param("only")===false) {
			$tpl = templates::completed_assign_vars(Route::param("template"));
			templates::completed($tpl);
			templates::display();
		} else {
			$tmp = Route::param("path");
			$dir = dirname($tmp);
			$arr = read_dir($dir.DS, "dir", true);
			$newArr = array();
			for($i=0;$i<sizeof($arr);$i++) {
				$arr[$i] = config::Select("default_http_local").get_site_path($arr[$i].DS);
				$newArr[$i] = basename($arr[$i])."/";
			}
			$load = file_get_contents($tmp);
			$load = str_replace($newArr, $arr, $load);
			$load = preg_Replace("#///\*\*\*(.+?)\*\*\*///#is", "", $load);
			$load = trim($load);
			HTTP::echos($load);
		}
	}
	
}

?>