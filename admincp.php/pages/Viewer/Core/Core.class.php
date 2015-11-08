<?php
/*
 *
 * @version 2015-10-07 17:50:38 1.25.6-rc3
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc3
 * Version File: 3
 *
 * 3.1
 * fix admin templates
 * 3.2
 * add admin cookie
 * 3.3
 * add support referer. Good idea for save?
 *
*/
class Core {
	
	private $count_unmoder = 0;
	private $title = "{L_adminpanel}";
	
	private function vsort(&$array) {
		$arrs = array();
		foreach($array as $key => $val) {
			sort($val);
			$arrs[$key] = $val;
		}
		$array = $arrs;
	}
	
	private function unix($time) {
		return timespan($time);
	}
	
	public function unmoder($tick=null) {
		if(!empty($tick)) {
			$this->count_unmoder = $tick;
		} else {
			return $this->count_unmoder;
		}
	}
	
	public function title($titles=null) {
		if(!empty($titles)) {
			$this->title = $titles;
		} else {
			return $this->title;
		}
	}
	
	public function Prints($echo, $print=false) {
	global $lang, $user, $in_page;
		if(!isset($_COOKIE[COOK_ADMIN_USER]) || !isset($_COOKIE[COOK_ADMIN_PASS])) {
			$ref = urlencode(str_replace(array(ROOT_PATH, "/admincp.php/"), "", getenv("REQUEST_URI")));
			location("{C_default_http_host}admincp.php/?pages=Login".(!empty($ref) ? "&ref=".$ref : ""));
			return;
		}
		$dir = ROOT_PATH."admincp.php/pages/Lang/".lang::get_lg()."/";
		if(is_dir($dir)) {
			if($dh = dir($dir)) {
				while(($file = $dh->read()) !== false) {
					if($file != "index.".ROOT_EX && $file != "." && $file != ".." && strpos($file, ".".ROOT_EX) !== false) {
						require_once($dir.$file);
					}
				}
			$dh->close();
			}
		}
		if(!$print) {
			$echo = (templates::complited_assing_vars($echo, null));
		}
		if(isset($_POST['jajax'])) {
			HTTP::echos(templates::view($echo));
			return;
		}
		$dir = ROOT_PATH."core/media/smiles/";
		if(is_dir($dir)) {
			$files = array();
			if($dh = dir($dir)) {
				$i=1;
				while(($file = $dh->read()) !== false) {
					if(strpos($file, ".gif") !== false && $file != "." && $file != "..") {
						$sm = strtr($file, array(".gif" => ""));
						templates::assign_vars(array(
							"smile" => $sm,
						), "smiles", "smile_".$i);
						$i++;
					}
				}
			$dh->close();
			}
		}
		templates::assign_var("count_unmoder", $this->unmoder());
		templates::assign_var("title_admin", $this->title());
		$links = array();
		if($dh = dir(ROOT_PATH."admincp.php/pages/menu/")) {
			$i=1;
			while(($file = $dh->read()) !== false) {
				if($file != "index.".ROOT_EX && $file != "." && $file != "..") {
					include_once(ROOT_PATH."admincp.php/pages/menu/".$file);
				}
			}
			$dh->close();
		}
		$this->vsort($links);
		$all=0;
		$page_v = getenv("REQUEST_URI");
		$now = substr($page_v, 1, strlen($page_v));
		foreach($links as $datas) {
			$end = "";
			for($i=0;$i<sizeof($datas);$i++) {
				for($is=0;$is<sizeof($datas[$i]);$is++) {
					if(sizeof($datas[$i])==1) {
						$count = 0;
					} else {
						$count = sizeof($datas[$i])-1;
					}
					templates::assign_vars(array(
						"value" => $datas[$i][$is]['title'],
						"link" => $datas[$i][$is]['link'],
						"is_now" => (($datas[$i][$is]['link']==$now) ? "1" : "0"),
						"type_st" => ($datas[$i][$is]['type']=="cat" ? "start" : ""),
						"type_end" => ($count==$is&&$datas[$i][$is]['type']=="item" ? "end" : ""),
						"icon" => (isset($datas[$i][$is]['icon']) ? $datas[$i][$is]['icon'] : " "),
					), "menu", "m".$all.$i.$is);
				}
			}
			$all++;
		}
		templates::assign_vars(array(
			"main_admin" => $echo,
		));
		echo templates::view(templates::complited_assing_vars("main", null));
	}
	
}

?>