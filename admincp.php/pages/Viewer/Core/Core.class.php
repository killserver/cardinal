<?php

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
		if(!empty($tick)) {
			$this->title = $titles;
		} else {
			return $this->title;
		}
	}
	
	public function Prints($echo, $print=false) {
	global $user, $in_page;
		if(!isset($_COOKIE['admin_username']) || !isset($_COOKIE['admin_password'])) {
			location("{C_default_http_host}admincp.php/?pages=Login");
			return;
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