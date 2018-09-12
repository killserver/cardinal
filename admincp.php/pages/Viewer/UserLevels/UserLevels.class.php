<?php

class UserLevels extends Core {
	
	function __construct() {
		$userlevelName = array(
			"admin" => "админ-панели",
			"antivirus" => "антивирусу",
			"clearcache_all" => "очистке всего кеша",
			"clearcache_data" => "очистке кеша данных",
			"clearcache_pages" => "очистке кеша страниц",
			"customize" => "кастомизации шаблона",
			"debugpanel" => "дебаг-панели, которая будет активироваться в админ-панели и постоянно находиться на сайте в любых разделах",
			"developer" => "разрабатываемым разделам(ТОЛЬКО ДЛЯ РАЗРАБОТЧИКОВ!!!)",
			"editor" => "редактору кода",
			"edittemplate" => "редактору частей шаблонов",
			"importexport" => "импорту/экспорту движка",
			"languages" => "языковой панели",
			"loginadmin" => "списку действий в админ-панели",
			"logs" => "отчётам ошибок на сайте",
			"phpinfo" => "системной информации о сервере",
			"recyclebin" => "корзине данных, куда помещаются данные на 30 дней после удаления их из таблиц при помощи Арчера",
			"seo" => "СЕО-мета",
			"settings" => "примитивным настройкам для заказчика",
			"settinguser" => "расширенным настройкам для системного администратора",
			"skins" => "смене внешнего вида сайта",
			"updates" => "возможности обновлять движок из админ-панели",
			"userlevels" => "редактору уровней доступа сайта",
			"users" => "списку пользователей",
			"yui_admin" => "настройке системы YUI",
			"site" => "сайту",
		);
		$userlevelName = execEvent("userlevel_all", $userlevelName);
		$userlevels = userlevel::all();
		$myLevel = User::get("level");
		if(Arr::get($_GET, 'mod', false) && Arr::get($_GET, 'mod')=="Add") {
			$userlevels = current($userlevels);
			templates::assign_var("name", "");
			templates::assign_var("typePage", "Add");
			$i = 0;
			foreach($userlevels as $key => $value) {
				templates::assign_var("level", str_replace("access_", "", $key), "levelChange", $i);
				templates::assign_var("checked", "no", "levelChange", $i);
				$i++;
			}
			$this->Prints("UserLevels");
			return false;
		}
		if(Arr::get($_GET, 'mod', false) && Arr::get($_GET, 'mod')=="Edit" && isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id']>=0) {
			if(!isset($userlevels[$_GET['id']]) || $myLevel<$_GET['id']) {
				new Errors();
				return false;
			}
			$defs = get_defined_constants(true);
			$defs = $defs['user'];
			$levels = array();
			foreach($defs as $k => $v) {
				if(strpos($k, "LEVEL_")!==false) {
					$levels[$k] = $v;
				}
			}
			$levels = array_flip($levels);
			$userlevels = $userlevels[$_GET['id']];
			$userlevels = execEvent("userlevel_".$_GET['id'], $userlevels);
			if(sizeof($_POST)>0) {
				$userlevels = userlevel::all();
				$userlevels[$_GET['id']] = $_POST['userlevels'];
				foreach($userlevels[$_GET['id']] as $k => $v) {
					if($userlevels[$_GET['id']][$k]=="no") {
						unset($userlevels[$_GET['id']][$k]);
					}
				}
				$txt = '<?php if(!defined("IS_CORE")) { echo "403 ERROR"; die(); } $userlevels = array_replace($userlevels, array(';
				foreach($userlevels as $id => $access) {
					$txt .= '"'.$id.'" => array(';
					$keys = array_keys($access);
					for($i=0;$i<sizeof($keys);$i++) {
						$txt .= ' "'.$keys[$i].'" => "'.$access[$keys[$i]].'", '.PHP_EOL;
					}
					$txt .= '),';
				}
				$txt .= '));';
				if(!@is_writable(PATH_MEDIA)) {
					@chmod(PATH_MEDIA, 0777);
				}
				if(file_exists(PATH_MEDIA."userlevels.".ROOT_EX)) {
					if(!@is_writable(PATH_MEDIA."userlevels.".ROOT_EX)) {
						@chmod(PATH_MEDIA."userlevels.".ROOT_EX, 0777);
					}
					@unlink(PATH_MEDIA."userlevels.".ROOT_EX);
				}
				file_put_contents(PATH_MEDIA."userlevels.".ROOT_EX, $txt);
				location("./?pages=UserLevels");
				return false;
			}
			templates::assign_var("level_id", intval($_GET['id']));
			templates::assign_var("name", $levels[$_GET['id']]);
			templates::assign_var("isSystem", "yes");
			templates::assign_var("typePage", "Edit&id=".$_GET['id']);
			$i = 0;
			foreach($userlevelName as $name => $lang) {
				$value = "no";
				if(isset($userlevels["access_".$name]) && $userlevels["access_".$name]=="yes") {
					$value = "yes";
				}
				templates::assign_var("name", $name, "levelChange", "lvl".($i+1));
				templates::assign_var("level", $lang, "levelChange", "lvl".($i+1));
				templates::assign_var("checked", ($value=="yes" ? "yes" : "no"), "levelChange", "lvl".($i+1));
				$i++;
			}
			$this->Prints("UserLevels");
			return false;
		}
		$arrK = array_keys($userlevels);
		$arrV = array_values($userlevels);
		$arrV = array_reverse($arrV);
		$defs = get_defined_constants(true);
		$defs = $defs['user'];
		$levels = array();
		foreach($defs as $k => $v) {
			if(strpos($k, "LEVEL_")!==false) {
				$levels[$k] = $v;
			}
		}
		$levels = array_flip($levels);
		$is = sizeof($arrV);
		for($i=0;$i<sizeof($arrV);$i++) {
			$is--;
			if($arrK[$i]>=$myLevel) {
				continue;
			}
			templates::assign_vars(array(
				"id" => $arrK[$i],
				"name" => $levels[$i],
				"orName" => $levels[$i],
				"counts" => sizeof($arrV[$is]),
			), "userlevelsList", "level".$i);
		}
		$this->Prints("UserLevelsMain");
	}
	
}