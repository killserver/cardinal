<?php

class Customize extends Core {

	private function imageico($image, $sizes, $filename = null, $type = "ico", $quality = 9, $filters = PNG_NO_FILTER) {
		$link = file_get_contents($image);
		$im = @imagecreatefromstring($link);
		$width = imagesx($im);
		$height = imagesy($im);
		if($width > 500 || $height > 500) {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
			throw new Exception('ICO images cannot be larger than 500 pixels wide/tall', E_USER_WARNING);
			return;
		}
		foreach($sizes as $size) {
			$imgAll = imagecreatetruecolor($size[0], $size[1]);
			imagealphablending($imgAll, false);
			imagesavealpha($imgAll, true);
			imagecopyresized($imgAll, $im, 0, 0, 0, 0, $size[0], $size[1], $width, $height);
			if($filename) {
				ob_start();
			}
			// Collect PNG data.
			ob_start();
			imagesavealpha($imgAll, true);
			imagepng($imgAll, null, $quality, $filters);
			if($type == "ico") {
				$png_data = ob_get_clean();
				// Write ICO header, image entry and PNG data.
				echo pack('v3', 0, 1, 1);
				echo pack('C4v2V2', $size[0], $size[1], 0, 0, 1, 32, strlen($png_data), 22);
				echo $png_data;
			}
			// Output to file.
			if($filename) {
				file_put_contents($filename."-".$size[0]."x".$size[1].".".$type, ob_get_clean());
			}
			imagedestroy($img);
		}
		imagedestroy($im);
	}
	
	function __construct() {
		if(isset($_GET['saveIcon'])) {
			$this->imageico($_GET['saveIcon'], array(
				array(128, 128),
				array(64, 64),
				array(32, 32),
				array(16, 16),
			), PATH_UPLOADS."icon");
			return false;
		}
		if(isset($_GET['saveCss'])) {
			$css = array();
			if(isset($_POST['backgrounds'])) {
				foreach($_POST['backgrounds'] as $k => $v) {
					$css[] = ".colorSize-".$k." { background-color: ".$v." }";
				}
			}
			if(function_exists("callAjax")) {
				callAjax();
			} else {
				templates::$gzip = false;
				Debug::activShow(false);
			}
			if(@file_put_contents(PATH_SKINS.config::Select("skins", "skins").DS."customizeStyle.css", implode(" ", $css))!==false) {
				cardinal::RegAction("Внесение изменений в файл \"".PATH_SKINS.config::Select("skins", "skins").DS."customizeStyle.css"."\" пользователем \"".User::get("username")."\"");
				HTTP::echos("done");
			} else {
			if(!isset($_SERVER['HTTP_CF_VISITOR'])) {
				header("HTTP/1.0 520 Unknown Error");
			} else {
				header("HTTP/1.0 404 Not found");
			}
				HTTP::echos("notSave");
			}
			return false;
		}
		$this->ModuleList("Customize", array(&$this, "changeMenu"));
		config::Set("FullMenu", "1");
		$this->Prints("getCustomize");
	}
	
	function changeMenu() {
		templates::resetVars("menu");
		templates::assign_vars(array(
			"value" => "{L_'Просмотр темы'}<br><b>{C_skins[skins]}</b>",
			"link" => "",
			"is_now" => "0",
			"type_st" => "",
			"type_end" => "",
			"icon" => " ",
		), "menu", 1);
	global $colors;
	if(isset($colors) && is_array($colors) && sizeof($colors)>0) {
		templates::assign_vars(array(
			"value" => 'Цвета сайта',
			"link" => "#",
			"is_now" => "0",
			"type" => "cat",
			"type_st" => "start",
			"type_end" => "",
			"icon" => " ",
		), "menu", 2);
		$size = sizeof($colors);
		$keys = array_keys($colors);
		for($i=0;$i<$size;$i++) {
			templates::assign_vars(array(
				"value" => '<div class="form-group">
		<label class="col-sm-12 control-label">Цвет {L_"'.$keys[$i].'"}</label>
		<div class="col-sm-12">
			<div class="input-group">
				<input type="text" class="form-control colorpicker backgrounds" data-colorId="'.$keys[$i].'" data-format="rgba" value="'.$colors[$keys[$i]].'" />
				<div class="input-group-addon">
					<i class="color-preview"></i>
				</div>
			</div>
		</div>
	</div>',
				"link" => "#",
				"is_now" => "0",
				"type_st" => "",
				"type_end" => "",
				"icon" => " ",
			), "menu", ($i+3));
		}
		if($size>0) {
			templates::assign_vars(array(
				"value" => '<div class="form-group">
		<div class="col-sm-12">
			<input class="btn btn-success btn-sm btn-block saveBackground" type="button" value="{L_submit}">
		</div>
	</div>',
				"link" => "#",
				"is_now" => "0",
				"type_st" => "",
				"type_end" => "end",
				"icon" => " ",
			), "menu", ($size+4));
		}
		}
	}
	
}