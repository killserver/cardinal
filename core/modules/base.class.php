<?php
if(!defined("IS_CORE")) {
die();
}

class base {

	private static $menu = array();

	function __construct() {
		modules::manifest_log('load_modules', 'blocks');
		$this->blocks();
	}

	private function set_menu($name, $html = null, $block = null) {
		if(empty($block)) {
			self::$menu[$name] = $html;
		} else {
			self::$menu[$name][$block] = array("name" => $block, "value" => $html);
		}
	}

	private function strtolowers($text) {
	global $config;
		if(function_exists("mb_strtolower")) {
			return mb_strtolower($text, $config['charset']);
		} else {
			return strtolower($text);
		}
	}

	private function ToTranslit($var, $rep=false, $norm=false) {
		if(empty($var)) {
			return;
		}
		$lang = modules::init_lang();
		$lang->set_lang("ru");
		$lang = $lang->init_lang();
		if($rep) {
			if($norm) {
				$lang['translate_en'] = array_flip($lang['translate_en']);
			}
			$lang['translate'] = array_merge($lang['translate_en'], array("\\" => "", "/" => "", "$" => "", "#" => "", "@" => "", "!" => "", "%" => "", "^" => "", "&" => "", "*" => "", "(" => "", ")" => "", "?" => "", ":" => "", "=" => "", "+" => ""));
		} else {
			$lang['translate'] = array_merge($lang['translate'], array(" " => "_", "\\" => "", "/" => "", "'" => "", "$" => "", "#" => "", "@" => "", "!" => "", "%" => "", "^" => "", "&" => "", "*" => "", "(" => "", ")" => "", "," => "", "." => "", "?" => "", ":" => "", "=" => "", "+" => "", "\"" => "'"));
		}
		$return = str_replace(array_keys($lang['translate']), array_values($lang['translate']), self::strtolowers($var));
		unset($lang);
	return $return;
	}

	private function blocks() {
		$skins = modules::init_templates()->get_skins();
		if(!file_exists(ROOT_PATH."skins/".$skins."/blocks.tpl")) {
			return;
		}
		if(!modules::init_cache()->exists("menu")) {
			$db = modules::init_db();
			$rows = $db->select_query("SELECT id, name, data, menu FROM menu WHERE activ = \"yes\" ORDER BY position ASC");
			modules::init_cache()->set("menu", $rows);
		} else {
			$rows = modules::init_cache()->get("menu");
		}

		$file_left = $file_center = $file_right = $left = $center = $right = "";
		if(!file_exists(ROOT_PATH."skins/".$skins."/block_left.tpl") && !file_exists(ROOT_PATH."skins/".$skins."/block_right.tpl")) {
			$file_left = $file_center = $file_right = file_get_contents(ROOT_PATH."skins/".$skins."/blocks.tpl");
		}
		if(sizeof($rows)>0) {
			foreach($rows as $row) {
				if($row['menu']=="left") {
					$left .= str_replace(array("{title}", "{content}"), array($row['name'], (urldecode($row['data']))), $file_left)."\n";
				} elseif($row['menu']=="center") {
					$center .= str_replace(array("{title}", "{content}"), array($row['name'], (urldecode($row['data']))), $file_center)."\n";
				} elseif($row['menu']=="right") {
					$right = str_replace(array("{title}", "{content}"), array($row['name'], (urldecode($row['data']))), $file_right)."\n";
					self::set_menu("right_block", $right, self::ToTranslit($row['name']));
				}
			}
		} else {
			self::set_menu("right_block", $right);
		}
		self::set_menu("left_block", $left);
		self::set_menu("center_block", $center);
		modules::manifest_set(array("temp", "menu"), self::$menu);
		unset($file_left, $file_right, $file_center, $right, $left, $center);
	}

}