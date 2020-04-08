<?php

class Archer_CustomEdit {
	
	public function __construct() {
		KernelArcher::callback("CustomAdd", "TraceOn", array(&$this, "Headers"));
		KernelArcher::callback("CustomEdit", "TraceOn", array(&$this, "Headers"));
	}
	
	public function Headers($table, $page, $models, $tpl) {
		$getExclude = KernelArcher::excludeField("get", "Edit");
		$list = $models->getArray(false);
		$list = execEvent("archer_list_ready", $list);
		$first = $models->getFirst();
		$isId = $list[$first];
		unset($list[$first]);
		if(isset(KernelArcher::$editModel["Edit"])) {
			for($i=0;$i<sizeof(KernelArcher::$editModel["Edit"]);$i++) {
				$list = call_user_func_array(KernelArcher::$editModel["Edit"][$i], array($list, $getExclude));
			}
		}
		$list = execEvent("archer_list_done", $list, $table);
		if(isset($_GET['get'])) {
			$k = $_GET['get'];
			$l = $models->getAttribute($k, 'type');
			$r = $models->getAttribute($k, 'required');
			$field = KernelArcher::Viewing($l, $k, "", "", $r, false, false, "", true);
			$field = templates::view($field);
			vdump($field);
			die();
		}
		$where = $whereData = "";
		$addition = "";
		if(Arr::get($_GET, "Where", false)) {
			$where = Arr::get($_GET, "Where");
			$addition .= "&Where=".$where;
		}
		if(Arr::get($_GET, "WhereData", false)) {
			$whereData = Arr::get($_GET, "WhereData");
			$addition .= "&WhereData=".$whereData;
		}
		if(Arr::get($_GET, "WhereType", false)) {
			$WhereType = Arr::get($_GET, "WhereType");
			$addition .= "&WhereType=".$WhereType;
		}
		if(Arr::get($_GET, "ShowPages", false)) {
			$addition .= "&ShowPages=".Arr::get($_GET, "ShowPages");
		}
		if(Arr::get($_GET, "orderBy", false)) {
			$addition .= "&orderBy=".Arr::get($_GET, "orderBy");
		}
		if(Arr::get($_GET, "orderTo", false)) {
			$addition .= "&orderTo=".Arr::get($_GET, "orderTo");
		}
		if(Arr::get($_GET, "tmp", false)) {
			$addition .= "&tmp=".Arr::get($_GET, "tmp");
		}
		$isAjax = false;
		if(ajax_check()=="ajax") {
			$isAjax = true;
		}
		$body = array();
		$supportedLang = array();
		$fields = array();
		foreach($list as $k => $v) {
			if(in_array($k, $getExclude)) {
				continue;
			}
			$attr = $models->getAttribute($k, "Attr");
			$lang = "";
			if($attr==="supportLang") {
				$lang = $models->getAttribute($k, "Lang");
				$supportedLang[$lang] = array("lang" => $lang);
			}
			$l = $models->getAttribute($k, 'type');
			$height = $models->getAttribute($k, 'height');
			if(empty($height)) {
				$height = "auto";
			}
			$fields[$k] = array("type" => $l, "value" => $v);
			$default = ($where==$k ? $whereData : $models->getAttribute($k, 'default'));
			$typeData = $models->getAttribute($k, "typeData");
			$r = $models->getAttribute($k, 'required');
			if(is_array($v) && (!empty($where) && !empty($whereData) && $where==$k && isset($v[$whereData]) || isset($v['default']))) {
                if($page=="Add" && !empty($where) && !empty($whereData) && $where==$k && isset($v[$whereData])) {
                    if(!is_array($v[$whereData])) {
                        $default = $v[$whereData];
                    } else {
                        $keys = array_keys($v);
                        for($i=0;$i<sizeof($keys);$i++) {
                            $data = $v[$keys[$i]];
                            $key = key($data);
                            if($key == $default) {
                                $default = $data;
                                break;
                            }
                        }
                    }
                } else {
                    $default = $v['default'];
                }
				if(isset($v['default'])) {
					unset($v['default']);
				}
				$v = array_values($v);
			} elseif(is_array($typeData) && sizeof($typeData)>0) {
				$default = (!empty($where) && !empty($whereData) && $where==$k && isset($v[$whereData]) ? $v[$whereData] : (empty($v) ? $default : $v));
				$v = implode(",", $typeData);
			}
			$body[$k] = array(
				"html" => KernelArcher::Viewing($l, $k, $v, $height, $default, $r, false, $isAjax, $lang, $models, true),
				"value" => $v,
				"default" => $default,
				"height" => $height
			);
		}
		sortByKey($supportedLang);
		execEvent("archer_data_ready", $fields, $supportedLang);
		$addition = execEvent("archer_addition", $addition, $page, $table);
		$ref = execEvent("archer_get_ref", Arr::get($_GET, "ref", false), $page, $table);
		$body = execEvent("archer_custom_edit", "", array(
			"fields" => $body,
			"supportedLang" => $supportedLang,
			"addition" => $addition,
			"ArcherPageNow" => $page,
			"ArcherPage" => $page.($page!="Add" ? "&viewId=".$isId : ""),
			"table" => str_replace(PREFIX_DB, "", $table),
			"title" => execEvent("archer_print_head", "{L_".$page."}&nbsp;{L_".str_replace(PREFIX_DB, "", $table)."}", $page, $table),
			"ref" => ($ref!==false ? "&ref=".urlencode(htmlspecialchars(urldecode($ref))) : ""),
		));
		if(!defined("ARCHER_CUSTOM_SUPPORT_LANGUAGES")) {
			foreach($supportedLang as $v) {
				templates::assign_vars($v, "supportedLang");
			}
		}
		$tpl = str_replace("{addition}", execEvent("archer_addition", $addition, $page, $table), $tpl);
		$tpl = str_replace("{ArcherPageNow}", $page, $tpl);
		$tpl = str_replace("{ArcherPage}", $page.($page!="Add" ? "&viewId=".$isId : ""), $tpl);
		$tpl = str_replace("{ArcherPath}", str_replace(PREFIX_DB, "", $table), $tpl);
		$tpl = str_replace("{ArcherMind}", execEvent("archer_print_head", "{L_".$page."}&nbsp;{L_".str_replace(PREFIX_DB, "", $table)."}", $page, $table), $tpl);
		$tpl = str_replace("{ArcherData}", "\n".$body, $tpl);
		$ref = execEvent("archer_get_ref", Arr::get($_GET, "ref", false), $page, $table);
		$tpl = str_replace("{ref}", ($ref!==false ? "&ref=".urlencode(htmlspecialchars(urldecode($ref))) : ""), $tpl);
		//var_dump($tpl);die();
		return $tpl;
	}
	
}