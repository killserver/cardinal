<?php

class Main_Updater extends Main {

	public function Main_Updater() {
		$vid = parser_url('https://raw.githubusercontent.com/killserver/cardinal/trunk/version/version.txt');
		if(intval(str_replace(".", "", $vid))>intval(str_replace(".", "", VERSION))) {
			templates::assign_var("new_version", $vid);
			templates::assign_var("is_new", "1");
			$file = ROOT_PATH."cache/system/version_".str_replace("-", "_", $vid).".txt";
			if(!file_exists($file)) {
				$vid = parser_url('https://raw.githubusercontent.com/killserver/cardinal/trunk/changelog/list.txt');
				$changelog = "";
				$list = explode("\n", $vid);
				for($i=0;$i<sizeof($list);$i++) {
					if(intval(str_replace(".", "", $list[$i]))>intval(str_replace(".", "", VERSION))) {
						$changelog .= parser_url('https://raw.githubusercontent.com/killserver/cardinal/trunk/changelog/'.$list[$i].'.txt')."\n\n\n\n";
					}
				}
				file_put_contents($file, $changelog, FILE_APPEND);
			} else {
				$changelog = file_get_contents($file);
			}
			templates::assign_var("changelog", $changelog);
		} else {
			templates::assign_var("is_new", "0");
		}
	}

}

?>