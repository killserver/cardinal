<?php
class EditTemplate extends Core {
	
	function __construct() {
	global $manifest;
		$Edit = "";
		if(!Arr::get($_GET, 'Edit', false)) {
			location("{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=main");
			return;
		} else {
			$Edit = Arr::get($_GET, 'Edit', false);
		}
		$Patch = PATH_SKINS.config::Select("skins", "skins").DS.$Edit;
		$clearPath = str_replace(ROOT_PATH, "", PATH_SKINS);
		$clearPath = substr($clearPath, 0, -(strlen(DS)));
		if(file_exists($Patch)) {
			Debug::activShow(false);
			templates::gzip(false);
			$sRet = array();
			if(sizeof($manifest['jscss'])>0) {
				if(isset($manifest['jscss']['css']) && isset($manifest['jscss']['css']['link']) && is_array($manifest['jscss']['css']['link']) && sizeof($manifest['jscss']['css']['link'])>0) {
					foreach($manifest['jscss']['css']['link'] as $v) {
						$sRet[] = preg_replace("/{THEME}\//", config::Select("default_http_local").$clearPath."/{C_skins[skins]}/", (isset($v['url']) ? $v['url'] : $v));
					}
				}
			}
			$File = file_get_contents($Patch);
			$File = preg_replace("/{C_default_http_local}/","/",$File);
			$File = preg_replace("/{THEME}\//", config::Select("default_http_local").$clearPath."/{C_skins[skins]}/", $File);
			$File = str_replace("{", '&#123;', $File);
			$File = htmlspecialchars($File);
			templates::assign_var("File", $File);
			templates::assign_var("css", json_encode($sRet));
		}		
		if(sizeof($_POST)>0) {
			Debug::activShow(false);
			templates::gzip(false);
			$File = $_POST["File"];
			$File = preg_replace("/\.\.\\//", "{C_default_http_local}", $File);
			$File = str_replace('&#123;', "{", $File);
			$File = str_replace(config::Select("default_http_local").$clearPath."/".config::Select("skins", "skins")."/", "{THEME}/", $File);
			$FileOpen = fopen($Patch, 'w');
			fputs($FileOpen, $File);
			fclose($FileOpen);
			cardinal::RegAction("Внесение изменений в шаблон \"".$clearPath."/".config::Select("skins", "skins")."\" пользователем \"".User::get("username")."\"");
			location("{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=main");
			return;
		}
		$this->Prints("EditTemplate");
	}
	 
}
?>