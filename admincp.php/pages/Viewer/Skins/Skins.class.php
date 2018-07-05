<?php

class Skins extends Core {

	function __construct() {
		if(Arr::get($_GET, "set", false)) {
			$skin = Arr::get($_GET, "set");
			config::Update("skins", "skins", $skin);
			return;
		}
		$arr = $this->ParseDirSkins(PATH_SKINS, true);
		for($i=0;$i<sizeof($arr);$i++) {
			if(!isset($arr[$i]['Image'])) {
				$arr[$i]['IS_Image'] = "false";
				$arr[$i]['Image'] = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAALElEQVQYGWO8d+/efwYkoKioiMRjYGBC4WHhUK6A8T8QIJt8//59ZC493AAAQssKpBK4F5AAAAAASUVORK5CYII=';
			} else {
				$arr[$i]['IS_Image'] = "true";
			}
			templates::assign_vars($arr[$i], "skins");
		}
		$this->title("{L_\"Шаблоны\"}");
		$this->Prints("SkinsAdmin");
	}

}