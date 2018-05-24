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
				$arr[$i]['Image'] = 'data:img/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAQAAABuBnYAAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAAAmJLR0QA/4ePzL8AAAAHdElNRQfiBRUMCyt+LeEbAAAAI0lEQVQI12O895+BgYGBgUGREUIzMaABwgKMUCMY7v8n2wwAv+QE6yFMzH8AAAAASUVORK5CYII==';
			} else {
				$arr[$i]['IS_Image'] = "true";
			}
			templates::assign_vars($arr[$i], "skins");
		}
		$this->title("{L_\"Шаблоны\"}");
		$this->Prints("SkinsAdmin");
	}

}