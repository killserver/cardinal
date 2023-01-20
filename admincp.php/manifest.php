<?php
define("IS_CORE", true);
define("IS_ADMINCP", true);
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."core.php");

$link = DS."favicon";
$size = array(
	"32x32",
	"64x64",
	"128x128",
);
$keys = array();
for($i=0;$i<sizeof($size);$i++) {
	if(file_exists(ROOT_PATH.$link.$size[$i].".ico")) {
		$keys[$size[$i]] = array("src" => get_site_path(ROOT_PATH.$link.$size[$i].".ico"), "type" => "image/ico", "sizes" => $size[$i]);
	}
	if(file_exists(ROOT_PATH.$link.$size[$i].".png")) {
		$keys[$size[$i]] = array("src" => get_site_path(ROOT_PATH.$link.$size[$i].".png"), "type" => "image/png", "sizes" => $size[$i]);
	}
	if(file_exists(ROOT_PATH.$link.$size[$i].".jpg")) {
		$keys[$size[$i]] = array("src" => get_site_path(ROOT_PATH.$link.$size[$i].".jpg"), "type" => "image/jpg", "sizes" => $size[$i]);
	}
	if(file_exists(ROOT_PATH.$link.$size[$i].".jpeg")) {
		$keys[$size[$i]] = array("src" => get_site_path(ROOT_PATH.$link.$size[$i].".jpeg"), "type" => "image/jpeg", "sizes" => $size[$i]);
	}
}
$link = DS."uploads".DS."icon".DS."favicon-";
for($i=0;$i<sizeof($size);$i++) {
	if(file_exists(ROOT_PATH.$link.$size[$i].".ico")) {
		$keys[$size[$i]] = array("src" => get_site_path(ROOT_PATH.$link.$size[$i].".ico"), "type" => "image/ico", "sizes" => $size[$i]);
	}
	if(file_exists(ROOT_PATH.$link.$size[$i].".png")) {
		$keys[$size[$i]] = array("src" => get_site_path(ROOT_PATH.$link.$size[$i].".png"), "type" => "image/png", "sizes" => $size[$i]);
	}
	if(file_exists(ROOT_PATH.$link.$size[$i].".jpg")) {
		$keys[$size[$i]] = array("src" => get_site_path(ROOT_PATH.$link.$size[$i].".jpg"), "type" => "image/jpg", "sizes" => $size[$i]);
	}
	if(file_exists(ROOT_PATH.$link.$size[$i].".jpeg")) {
		$keys[$size[$i]] = array("src" => get_site_path(ROOT_PATH.$link.$size[$i].".jpeg"), "type" => "image/jpeg", "sizes" => $size[$i]);
	}
}
$keys = array_values($keys);
if(sizeof($keys)===0) {
	$keys = array(
		array("src" => config::Select("default_http_host").ADMINCP_DIRECTORY."/".config::Select("logoAdminMain"), "type" => "image/svg", "sizes" => "32x32"),
	);
}
$arr = array(
	"background_color" => "#000",
	"name" => lang::get_lang("sitename"),
	"short_name" => lang::get_lang("sitename"),
	"description" => lang::get_lang("sitename"),
	"display" => "fullscreen",
	"start_url" => config::Select("default_http_host").ADMINCP_DIRECTORY."/".config::Select("mainPageAdmin"),
	"theme_color" => "#000",
	"icons" => $keys,
);
HTTP::ajax($arr);
/*
		
{
  "background_color": "#000",
  "description": "Auto-Star",
  "display": "fullscreen",
  "name": "Auto-Star",
  "short_name": "Auto-Star",
  "start_url": "https://autostar.sigma-studio.pp.ua/admincp.php/?pages=Archer&type=auto",
  "theme_color": "#000",
  "icons": [
    {
      "src": "https://autostar.sigma-studio.pp.ua/pwa_icon.png?1",
      "type": "image/png",
      "sizes": "72x72"
    },
    {
      "src": "https://autostar.sigma-studio.pp.ua/pwa_icon.png?1",
      "type": "image/png",
      "sizes": "180x180"
    },
    {
      "src": "https://autostar.sigma-studio.pp.ua/pwa_icon.png?1",
      "type": "image/png",
      "sizes": "600x600"
    },
    {
      "src": "https://autostar.sigma-studio.pp.ua/pwa_icon.png?1",
      "type": "image/png",
      "sizes": "512x512"
    }
  ]
}

		 */