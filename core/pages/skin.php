<?php
/*
 *
 * @version 5.4
 * @copyright 2014-2016 KilleR for Cardinal Engine
 * @author Maxim Homchuk <homchyk@yandex.ua>
 * @example examples/skin/style.css
 * @example examples/skin/index.html
 *
 * Version Engine: 5.4
 * Version File: 1
 *
 * 1.0
 * rebuild vh and vw to px
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class page {
	
	private $fileArr = array();
	private $position = false; // false - start; 0 - vertical; 1 - horizontal; 2 - null.
	private $document = array("W" => 0, "H" => 0);
	private $window = array("W" => 0, "H" => 0);
	
	function TransformPX($index, $value, $type) {
		$val = $value;
		$RealH = $this->window['W'] - ($this->window['H'] - $this->document['H']);
		switch($type) {
			case "vw":
				if($this->position==0) {
					$val = ($this->document['W']*$value)/100;
				} else if($this->position==1) {
					$val = ($HScreen*$value)/100;
				}
			break;
			case "vh":
				if($this->position==0) {
					$val = ($this->document['H']*$value)/100;
				} else if($this->position==1) {
					$val = ($RealH*$value)/100;
				}
			break;
		}
		$value = $value.$type;
		$val = $val."px";
		$this->fileArr[$index] = str_replace($value, $val, $this->fileArr[$index]);
	}
	
	function __construct() {
		HTTP::setContentType('css', config::Select("charset"));
		HTTP::lastmod(time());
		if(!file_exists(ROOT_PATH."skins".DS.templates::get_skins().DS."skin.css")) {
			die();
		}
		$W = Arr::get($_GET, 'documentWidth', false);// ширина окна браузера
		$H = Arr::get($_GET, 'documentHeight', false);// высота окна браузера
		$WScreen = Arr::get($_GET, 'windowWidth', false);// ширина экрана
		$HScreen = Arr::get($_GET, 'windowHeight', false);// высота экрана
		if(!$W || !$H || !$WScreen || !$HScreen) {
			die();
		}
		$this->window['W'] = $WScreen;
		$this->window['H'] = $HScreen;
		
		$this->document['W'] = $W;
		$this->document['H'] = $H;
		
		$this->fileArr = file(ROOT_PATH."skins".DS.templates::get_skins().DS."skin.css");
		if(!array_search("vh", $this->fileArr) || !array_search("vw", $this->fileArr)) {
			HTTP::echos(implode("\n", $this->fileArr));
			die();
		}
		$this->fileArr = array_map("trim", $this->fileArr);

		for($i=0;$i<sizeof($this->fileArr);$i++) {
			if(strpos($this->fileArr[$i], "/*start-vertical*/")!==false) {
				$this->position = 0;
			}
			if(strpos($this->fileArr[$i], "/*end-vertical*/")!==false) {
				$this->position = 2;
			}
			if(strpos($this->fileArr[$i], "/*start-horizontal*/")!==false) {
				$this->position = 1;
			}
			if(strpos($this->fileArr[$i], "/*end-horizontal*/")!==false) {
				$this->position = 2;
			}   
			$start = explode(":", $this->fileArr[$i]);
			$start = end($start);
			if(isset($start) && !empty($start)) {	
				if(strpos($start, "vw")!==false || strpos($start, "vh")!==false) {				
					$line = explode("vw", $start);
					for($j=0;$j<sizeof($line)-1;$j++) {					   
						preg_match('/(\b\d\d*\.?\d*\b)+$/s', $line[$j], $pockets);
						$this->TransformPX($i, $pockets[1], "vw");					         
					}  
					$line = explode("vh", $start);
					for($j=0;$j<sizeof($line)-1;$j++) {
						preg_match('/(\b\d\d*\.?\d*\b)+$/s', $line[$j], $pockets);
						$this->TransformPX($i, $pockets[1], "vh");                
					}             
				} 
			} 
		}
		HTTP::echos(implode("\n", $this->fileArr));
		die();
	}
	
}