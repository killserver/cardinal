<?php
/*
 *
 * @version 4.0a
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 4.0a
 * Version File: 2
 *
 * 2.1
 * add support working without connect to database
 * 2.2
 * add support last changes in system
 *
*/
if(!defined("IS_CORE")) {
die();
}

class base extends modules {

	private static $menu = array();

	function __construct() {
		$this->manifest_log('load_modules', array('base', __FILE__));
		if(!defined("WITHOUT_DB")) {
			$this->blocks();
		}
	}

	private function set_menu($name, $html = null, $block = null) {
		if(empty($block)) {
			self::$menu[$name] = $html;
		} else {
			self::$menu[$name][$block] = array("name" => $block, "value" => $html);
		}
	}

	private function strtolowers($text) {
		if(function_exists("mb_strtolower")) {
			return mb_strtolower($text, $this->get_config('charset'));
		} else {
			return strtolower($text);
		}
	}

	private function ToTranslit($var, $rep=false, $norm=false) {
		if(empty($var)) {
			return;
		}
		$lang = $this->init_lang();
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
		$tmp = $this->init_templates();
		$skins = $tmp->get_skins();
		if(!file_exists(ROOT_PATH."skins".DS.$skins.DS."blocks.tpl")) {
			return false;
		}
		$cache = $this->init_cache();
		if(!$cache->Exists("menu")) {
			$db = $this->init_db();
			$rows = $db->select_query("SELECT id, name, data, menu FROM menu WHERE activ = \"yes\" ORDER BY position ASC");
			if(!is_bool($rows)) {
				$cache->Set("menu", $rows);
			}
		} else {
			$rows = $cache->Get("menu");
		}

		$file_left = $file_center = $file_right = $left = $center = $right = "";
		if(!file_exists(ROOT_PATH."skins".DS.$skins.DS."block_left.tpl") && !file_exists(ROOT_PATH."skins".DS.$skins.DS."block_right.tpl")) {
			$file_left = $file_center = $file_right = file_get_contents(ROOT_PATH."skins".DS.$skins.DS."blocks.tpl");
		}
		if(!is_bool($rows) && sizeof($rows)>0) {
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
		$this->manifest_set(array("temp", "menu"), self::$menu);
		unset($file_left, $file_right, $file_center, $right, $left, $center);
	}

}
modules::load_hooks("base");