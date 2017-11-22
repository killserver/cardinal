<?php
class EditTemplate extends Core {
	
	function __construct() {
	global $manifest;
		$Edit = "";
		if(!Arr::get($_GET, 'Edit', false)) {
			location("{C_default_http_host}admincp.php/?pages=main");
			return;
		} else {
			$Edit = Arr::get($_GET, 'Edit', false);
		}
		$Patch = ROOT_PATH."/skins/".config::Select("skins", "skins")."/".$Edit;		
		if(file_exists($Patch)) {
			Debug::activShow(false);
			templates::gzip(false);
			$sRet = array();
			if(sizeof($manifest['jscss'])>0) {
				if(isset($manifest['jscss']['css']) && isset($manifest['jscss']['css']['link']) && is_array($manifest['jscss']['css']['link']) && sizeof($manifest['jscss']['css']['link'])>0) {
					foreach($manifest['jscss']['css']['link'] as $v) {
						$sRet[] = preg_replace("/{THEME}\//", config::Select("default_http_local")."skins/{C_skins[skins]}/", $v);
					}
				}
			}
			$File = file_get_contents($Patch);
			$File = preg_replace("/{C_default_http_local}/","/",$File);
			$File = preg_replace("/{THEME}\//", config::Select("default_http_local")."skins/{C_skins[skins]}/", $File);
			$File = htmlspecialchars($File);
			templates::assign_var("File", $File);
			templates::assign_var("css", json_encode($sRet));
		}		
		if(sizeof($_POST)>0) {
			Debug::activShow(false);
			templates::gzip(false);
			$File = $_POST["File"];
			$File = preg_replace("/\.\.\\//", "{C_default_http_local}", $File);
			$FileOpen = fopen($Patch, 'w');
			fputs($FileOpen, $File);
			fclose($FileOpen);
			location("{C_default_http_host}admincp.php/?pages=main");
			return;
		}
		$this->Prints("EditTemplate");
	}
	 
}
?>