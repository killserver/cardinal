<?php

class Customize extends Core {

	private function imageico($image, $sizes, $filename = "favicon", $type = "ico", $quality = 9, $filters = PNG_NO_FILTER) {
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
		if(is_dir($filename)) {
			$filename = rtrim($filename, DS);
			$filename .= DS."favicon";
		}
		foreach($sizes as $types => $size) {
			$imgAll = imagecreatetruecolor($size[0], $size[1]);
			imagealphablending($imgAll, false);
			imagesavealpha($imgAll, true);
			imagecopyresized($imgAll, $im, 0, 0, 0, 0, $size[0], $size[1], $width, $height);
			ob_start();
			imagesavealpha($imgAll, true);
			if($type == "ico") {
				// Write ICO header, image entry and PNG data.
				echo pack('v3', 0, 1, 1);
				echo pack('C4v2V2', $size[0], $size[1], 0, 0, 1, 32, strlen($png_data), 22);
			}
			imagepng($imgAll, null, $quality, $filters);
			// Output to file.
			if($filename) {
				file_put_contents($filename."-".$size[0]."x".$size[1].".".(!is_numeric($types) ? $types : $type), ob_get_clean());
			}
			imagedestroy($imgAll);
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
		if(isset($_GET['uploadFav'])) {
			$file = $_FILES['favicon']['tmp_name'];
			if(!file_exists(PATH_UPLOADS."icon")) {
				@mkdir(PATH_UPLOADS."icon", 0777);
			}
			if(!is_writable(PATH_UPLOADS."icon")) {
				@chmod(PATH_UPLOADS."icon", 0777);
			}
			move_uploaded_file($file, PATH_UPLOADS."icon".DS."original.tmp");
			$this->imageico($file, array(
				array(128, 128),
				array(64, 64),
				array(32, 32),
				array(16, 16),
			), PATH_UPLOADS."icon");
			return false;
		}
		if(isset($_GET['uploadLogo'])) {
			$file = $_FILES['logoSite']['tmp_name'];
			$type = substr($_FILES['logoSite']['name'], strrpos($_FILES['logoSite']['name'], "."));
			move_uploaded_file($file, PATH_UPLOADS."logo-for-site".$type);
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
		$size = 0;

		$size++;
		templates::assign_vars(array( "value" => "{L_'Просмотр темы'}<br><b>{C_skins[skins]}</b>", "link" => "", "is_now" => "0", "type_st" => "", "type_end" => "", "icon" => " ", ), "menu", $size);
		$size++;
			templates::assign_vars(array( "value" => '<div class="form-group favicon"> <div class="col-sm-12"> {L_"Выберите картинку для фавиконки"} </div> <div class="col-sm-12"> <input class="btn btn-sm btn-block" type="file" name="favicon" accept="image/*"> </div> <div class="col-sm-12"> <input class="btn btn-success btn-sm btn-block uploadFavicon" type="button" value="{L_submit}"> </div> </div>', "link" => "#", "is_now" => "0", "type_st" => "", "type_end" => "", "icon" => " ", ), "menu", $size);
		$size++;
			templates::assign_vars(array( "value" => '<div class="form-group logoSite"> <div class="col-sm-12"> {L_"Выберите картинку для лого сайта"} </div> <div class="col-sm-12"> <input class="btn btn-sm btn-block" type="file" name="logoSite" accept="image/*"> </div> <div class="col-sm-12"> <input class="btn btn-purple btn-sm btn-block uploadLogoSite" type="button" value="{L_submit}"> </div> </div>', "link" => "#", "is_now" => "0", "type_st" => "", "type_end" => "", "icon" => " ", ), "menu", $size);
		global $colors;
		if(isset($colors) && is_array($colors) && sizeof($colors)>0) {
			$size++;
			templates::assign_vars(array( "value" => 'Цвета сайта', "link" => "#", "is_now" => "0", "type" => "cat", "type_st" => "start", "type_end" => "", "icon" => " ", ), "menu", $size);
			$keys = array_keys($colors);
			for($i=0;$i<sizeof($keys);$i++) {
				$size++;
				templates::assign_vars(array( "value" => '<div class="form-group"> <label class="col-sm-12 control-label">Цвет {L_"'.$keys[$i].'"}</label> <div class="col-sm-12"> <div class="input-group"> <input type="text" class="form-control colorpicker backgrounds" data-colorId="'.$keys[$i].'" data-format="rgba" value="'.$colors[$keys[$i]].'" /> <div class="input-group-addon"> <i class="color-preview"></i> </div> </div> </div> </div>', "link" => "#", "is_now" => "0", "type_st" => "", "type_end" => "", "icon" => " ", ), "menu", $size);
			}
			if(sizeof($keys)>0) {
				$size++;
				templates::assign_vars(array( "value" => '<div class="form-group"> <div class="col-sm-12"> <input class="btn btn-success btn-sm btn-block saveBackground" type="button" value="{L_submit}"> </div> </div>', "link" => "#", "is_now" => "0", "type_st" => "", "type_end" => "end", "icon" => " ", ), "menu", $size);
			}
		}
	}
	
}