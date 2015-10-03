<?php

class Main_Updater extends Main {

	public function Main_Updater() {
		$vid = parser_url('https://raw.githubusercontent.com/killserver/cardinal/trunk/version/version.txt');
		if(intval(str_replace(".", "", $vid))>intval(str_replace(".", "", VERSION))) {
			templates::assign_var("new_version", $vid);
			templates::assign_var("is_new", "1");
		} else {
			templates::assign_var("is_new", "0");
		}
	}

}

?>