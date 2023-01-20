<?php

class Archer_Shield {
	
	private function in_array_strpos($str, $arr, $rebuild = false) {
		$ret = false;
		$arr = array_values($arr);
		for($i=0;$i<sizeof($arr);$i++) {
			if($rebuild) {
				$res = strpos($arr[$i], $str)!==false;
			} else {
				$res = strpos($str, $arr[$i])!==false;
			}
			if($res) {
				$ret = true;
				break;
			}
		}
		return $ret;
	}
	
	public function __construct() {
		KernelArcher::callback("Shield", "TraceOn", array(&$this, "Headers"));
	}

	private function ordering($orderByName, $nowField) {
		$ret = false;
		if(is_array($orderByName)) {
			$arr = array_keys($orderByName);
			for($i=0;$i<sizeof($arr);$i++) {
				$name = $orderByName[$arr[$i]];
				if(isset($name[0]) && ($name[0]==$nowField || "{L_\"".$name[0]."\"}"==$nowField || "{L_'".$name[0]."'}"==$nowField)) {
					$ret = $name[1];
					break;
				} else if(isset($name['name']) && ($name['name']==$nowField || "{L_\"".$name['name']."\"}"==$nowField || "{L_'".$name['name']."'}"==$nowField)) {
					$ret = $name['order'];
					break;
				}
			}
			return $ret;
		} else if($orderByName==$nowField || "{L_\"".$orderByName."\"}"==$nowField || "{L_'".$orderByName."'}"==$nowField) {
			return $orderByName[1];
		} else {
			return false;
		}
	}
	
	public function Headers($table, $page, $model, $tpl) {
		$table = $model->loadedTable;
		if(defined("PREFIX_DB")) {
			if(PREFIX_DB!=='') {
				$table = nsubstr($table, nstrlen(PREFIX_DB));
			} 
		}
		$disableOptions = config::Select("disableOptions")=="true";
		$deactiveMassAction = config::Select("disableMassAction")!==false;
		$modelName = get_class($model);
		$getExclude = KernelArcher::excludeField("get", "Shield");
		execEventRef("Archer-Shield", $modelName, $table, $model);
		/*$first = $model->getFirst();*/
		$h = $model->getComments(true);
		$h = array_values($h);
		$head = "";
		$sortBy = array();
		if(isset(KernelArcher::$sortBy) && is_array(KernelArcher::$sortBy) && sizeof(KernelArcher::$sortBy)>0) {
			KernelArcher::$sortBy = array_values(KernelArcher::$sortBy);
			for($i=0;$i<sizeof(KernelArcher::$sortBy);$i++) {
				KernelArcher::$sortBy[$i] = "{L_\"".KernelArcher::$sortBy[$i]."\"}";
			}
		}
		$orderById = 0;
		$orderBySort = "desc";
		$myOrderName = "";
		$myOrder = false;
		if(isset(KernelArcher::$orderBy) && is_array(KernelArcher::$orderBy)) {
			$myOrder = (isset(KernelArcher::$orderBy['name']) || isset(KernelArcher::$orderBy[0]) ? true : false);
			if(isset(KernelArcher::$orderBy['name'])) {
				$myOrderName = KernelArcher::$orderBy['name'];
			} else {
				if(isset(KernelArcher::$orderBy[0]) && is_array(KernelArcher::$orderBy[0])) {
					$myOrderName = KernelArcher::$orderBy;
					//$orderBySort = "";
				} else if(isset(KernelArcher::$orderBy[0])) {
					$myOrderName = KernelArcher::$orderBy[0];
				}
			}
			//$orderBySort = (isset(KernelArcher::$orderBy['order']) ? KernelArcher::$orderBy['order'] : (isset(KernelArcher::$orderBy[1]) ? KernelArcher::$orderBy[1] : ""));
		}
		$counts = 0;
		for($i=0;$i<sizeof($h);$i++) {
			$altName = str_replace(array("{L_\"", "\"}"), "", $h[$i]);
			if($this->in_array_strpos($altName, $getExclude, true)) {
				continue;
			}
			if($myOrder && ($res = $this->ordering($myOrderName, $h[$i]))!==false) {
				$orderById = $i;
				$orderBySort = $res;
			}
			if(isset(KernelArcher::$sortBy) && is_array(KernelArcher::$sortBy) && sizeof(KernelArcher::$sortBy)>0 && in_array($h[$i], KernelArcher::$sortBy)) {
				$sortBy[] = "'".$altName."'";
			}
			$head .= "<th data-altName=\"".$altName."\">".$h[$i]."</th>";
			$counts++;
		}
		$head .= $disableOptions ? "" : (!defined("ADMINCP_DIRECTORY") ? "<th>Options</th>" : "<th>{L_\"options\"}</th>");
		$d = $model->getArray();
		$first = $model->getFirst();
		$d = array_keys($d);
		$data = "";
		$tplCustom = execEvent("custom_template_shield", "", $modelName, $table);
		$isCustom = (!empty($tplCustom));

		$tempItem = $listDataTpl = array();
		if($isCustom) {
			$objName = get_class($model);
			$model->SetTable($model->loadedTable);
			$model = execEvent("KernelArcher-Shield-Before-Data", $model, $objName);
			$list = $model->Select();
			if(is_array($list)) {
				$list = execEvent("KernelArcher-Shield-Data", $list, $objName);
				for($i=0;$i<sizeof($list);$i++) {
					$subList = $list[$i]->getArray(false);
					$subList = execEvent("KernelArcher-Shield-Data-Item", $subList, $objName);
					$firstItem = current($subList);
					if(is_null($firstItem)) {
						continue;
					}
					$listDataTpl[] = $subList;
				}
			}
		}

		for($i=0;$i<sizeof($d);$i++) {
			if($this->in_array_strpos($d[$i], $getExclude, true)) {
				continue;
			}
			$type = $model->getAttribute($d[$i], "type", $table, true);
			$infoField = "infoField";
            $activeQ = false;
			$activeQuickEditor = (is_bool(KernelArcher::$disabledQuickEditor) && KernelArcher::$disabledQuickEditor === false);
			if(!$activeQuickEditor) {
				$activeQ = false;
				if(is_array(KernelArcher::$disabledQuickEditor) && isset(KernelArcher::$disabledQuickEditor[$d[$i]]) && (
					(is_bool(KernelArcher::$disabledQuickEditor[$d[$i]]) && KernelArcher::$disabledQuickEditor[$d[$i]]===true)
					||
					(is_string(KernelArcher::$disabledQuickEditor[$d[$i]]) && KernelArcher::$disabledQuickEditor[$d[$i]]==="yes")
					||
					(is_numeric(KernelArcher::$disabledQuickEditor[$d[$i]]) && KernelArcher::$disabledQuickEditor[$d[$i]]===1)
					||
					in_array($d[$i], KernelArcher::$disabledQuickEditor)
				)) {
					$activeQ = true;
				}
			}
			if(is_array(KernelArcher::$quickEditNew)) {
				if(isset(KernelArcher::$quickEditNew[$modelName])) {
					if(
						(isset(KernelArcher::$quickEditNew[$modelName][$d[$i]]) && is_bool(KernelArcher::$quickEditNew[$modelName][$d[$i]]) && KernelArcher::$quickEditNew[$modelName][$d[$i]]===true)
						||
						(isset(KernelArcher::$quickEditNew[$modelName][$d[$i]]) && is_string(KernelArcher::$quickEditNew[$modelName][$d[$i]]) && KernelArcher::$quickEditNew[$modelName][$d[$i]]==="yes")
						||
						(isset(KernelArcher::$quickEditNew[$modelName][$d[$i]]) && is_numeric(KernelArcher::$quickEditNew[$modelName][$d[$i]]) && KernelArcher::$quickEditNew[$modelName][$d[$i]]===1)
						||
						in_array($d[$i], KernelArcher::$quickEditNew[$modelName])
					) {
						$activeQ = true;
					}
				} else if(isset(KernelArcher::$quickEditNew['default'])) {
					if(
						(isset(KernelArcher::$quickEditNew['default'][$d[$i]]) && is_bool(KernelArcher::$quickEditNew['default'][$d[$i]]) && KernelArcher::$quickEditNew['default'][$d[$i]]===true)
						||
						(isset(KernelArcher::$quickEditNew['default'][$d[$i]]) && is_string(KernelArcher::$quickEditNew['default'][$d[$i]]) && KernelArcher::$quickEditNew['default'][$d[$i]]==="yes")
						||
						(isset(KernelArcher::$quickEditNew['default'][$d[$i]]) && is_numeric(KernelArcher::$quickEditNew['default'][$d[$i]]) && KernelArcher::$quickEditNew['default'][$d[$i]]===1)
						||
						in_array($d[$i], KernelArcher::$quickEditNew['default'])
					) {
						$activeQ = true;
					}
				}
			}
			/*if(isset($_GET['test']) && $activeQ) {
				var_dump($val, $type);die();
			}*/
			if($type=="date") {
				$val = "{S_langdata=\"{".$modelName.".".$d[$i]."}\",\"d F Y\",true}";
			} else if($type=="time") {
				$val = "{S_langdata=\"{".$modelName.".".$d[$i]."}\",\"H:i:s\",true}";
			} else if($type=="datetime") {
				$val = "{S_langdata=\"{".$modelName.".".$d[$i]."}\",\"d F Y H:i:s\",true}";
			} else {
				$val = "{L_\"{".$modelName.".".$d[$i]."}\"}";
			}
			$val = execEvent("KernelArcher::Shield::Element", $val, $modelName, $first, $d[$i], $infoField);
			$quickEditor = "";
			if($i!=0 && $activeQ) {
				$quickEditor = " data-pk=\"{".$modelName.".".$first."}\" data-placeholder=\"{L_'Не задано'}\" data-emptytext=\"{L_\"Не задано\"}\" data-value=\"".$val."\" data-name=\"".$d[$i]."\""; // data-sub-id=\"test\" 
				if($type=="select" || $type=="array" || $type=="enum") {
					$infoField = "";
					$quickEditor .= " data-sub-id=\"{".$modelName.".".$first."}\" data-type=\"select\" data-source=\"{C_default_http_local}{D_ADMINCP_DIRECTORY}/?pages=Archer&type=".$table."&pageType=QuickEdit&loadSelect=".$d[$i]."\"";
				} else if($type=="date") {
					$quickEditor .= " data-type=\"date\"";
				} else if($type=="time") {
					$quickEditor .= " data-type=\"time\"";
				} else if($type=="datetime") {
					$quickEditor .= " data-type=\"datetime\"";
				} else if($type=="int" || $type=="price" || $type=="tinyint" || $type=="smallint" || $type=="mediumint" || $type=="bigint") {
					$quickEditor .= " data-type=\"number\"";
				} else if($type=="shorttext" || $type=="mediumtext" || $type=="text" || $type=="longtext") {
				} else if($type=="varchar") {
					$quickEditor .= " data-type=\"text\"";
				} else {
					$quickEditor .= " data-type=\"text\"";
				}
				$infoField .= " quickEdit";
			}
			if($isCustom) {
				$tempItem[$d[$i]] = array(
					// "core-data-id" => "{".$modelName.".".$first."}",
					"core-data-table" => $modelName,
					"core-data-name" => $d[$i],
					"core-class" => $infoField,
					"core-quickEditor" => $quickEditor,
					"core-value" => $val,
				);
				// templates::assign_vars(array(
				// 	"core-data-id" => "{".$modelName.".".$first."}",
				// 	"core-data-table" => $modelName,
				// 	"core-data-name" => $d[$i],
				// 	"core-class" => $infoField,
				// 	"core-quickEditor" => $quickEditor,
				// 	"".$d[$i] => $val,
				// ), $modelName);
			} else {
				execEventRef("KernelArcher::Shield::Element-before", $modelName, $first, $d[$i], $infoField);
				$data .= execEvent("KernelArcher::Shield::Element-before", "", $modelName, $first, $d[$i], $infoField);
				$data .= execEvent("KernelArcher::Shield::Element-".$i."-before", "", $modelName, $first, $d[$i], $infoField);
				$data .= "<td data-id=\"{".$modelName.".".$first."}\" data-table=\"".$modelName."\" data-name=\"".$d[$i]."\" class=\"".$infoField."\"".$quickEditor."><span".$quickEditor.">".$val."</span></td>";
				$data .= execEvent("KernelArcher::Shield::Element-".$i."-after", "", $modelName, $first, $d[$i], $infoField);
				$data .= execEvent("KernelArcher::Shield::Element-after", "", $modelName, $first, $d[$i], $infoField);
				execEventRef("KernelArcher::Shield::Element-after", $modelName, $first, $d[$i], $infoField);
			}
		}
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
		if($isCustom) {
			for($i=0;$i<sizeof($listDataTpl);$i++) {
				$keys = array_keys($listDataTpl[$i]);
				$firstItemData = current($listDataTpl[$i]);
				$arr = array();
				$arr["core-data-id"] = $firstItemData;
				$arr['core-options'] = '{E_[customOptionsBefore][type='.str_replace(PREFIX_DB, "", $table).';id='.$firstItemData.']}
					[if {C_disableCopy}!=1]
						[if {C_disableCopyEdit}!=1]
							<a href="./?pages=Archer&type='.str_replace(PREFIX_DB, "", $table).'&pageType=CopyEdit&viewId='.$firstItemData.''.$addition.'" class="btn btn-turquoise btn-block btn-copy btn-copy-edit"><span>{L_"Клонировать и редактировать"}</span></a>
						[/if {C_disableCopyEdit}!=1]
					[/if {C_disableCopy}!=1]
					[if {C_disableCopy}!=1]<a href="./?pages=Archer&type='.str_replace(PREFIX_DB, "", $table).'&pageType=Copy&viewId='.$firstItemData.''.$addition.'" class="btn btn-turquoise btn-block btn-copy"><span>{L_"Клонировать"}</span></a>[/if {C_disableCopy}!=1]
					[if {C_disableEdit}!=1]<a href="./?pages=Archer&type='.str_replace(PREFIX_DB, "", $table).'&pageType=Edit&viewId='.$firstItemData.''.$addition.'" class="btn btn-block btn-edit"><span>{L_"Редактировать"}</span></a>[/if {C_disableEdit}!=1]
					[if {C_disableDelete}!=1]<a href="./?pages=Archer&type='.str_replace(PREFIX_DB, "", $table).'&pageType=Delete&viewId='.$firstItemData.''.$addition.'" data-type="'.str_replace(PREFIX_DB, "", $table).'" data-id="'.$firstItemData.'" class="btn btn-red btn-block btn-remove"><span>{L_"Удалить"}</span></a>[/if {C_disableDelete}!=1]
					{E_[customOptions][type='.str_replace(PREFIX_DB, "", $table).';id='.$firstItemData.']}';
				for($x=0;$x<sizeof($keys);$x++) {
					if(!isset($tempItem[$keys[$x]])) {
						continue;
					}
					$insideKeys = array_keys($tempItem[$keys[$x]]);
					for($z=0;$z<sizeof($insideKeys);$z++) {
						$arr[$insideKeys[$z]] = $tempItem[$keys[$x]][$insideKeys[$z]];
					}
					if(isset($arr['core-value'])) {
						$arr[$keys[$x]."-core"] = str_replace('{'.$modelName.'.'.$keys[$x].'}', $listDataTpl[$i][$keys[$x]], $arr['core-value']);
						unset($arr['core-value']);
					}
					$arr[$keys[$x]."-value"] = $listDataTpl[$i][$keys[$x]];
				}
				templates::assign_vars($arr, "ArcherField");
			}
			templates::assign_vars(array(
				"orderById" => ($orderById+1),
				"orderBySort" => $orderBySort,
				"addition" => $addition,
				"ArcherFirst" => $first,
				"ArcherMind" => $head,
				"ArcherData" => $data,
				"ArcherPage" => $modelName,
				"ArcherSort" => implode(",", $sortBy),
				"ArcherTable" => str_replace(PREFIX_DB, "", $table)
			));
		} else {
			$tpl = $this->replaceField($tpl, $orderById, $orderBySort, $addition, $first, $head, $data, $modelName, $sortBy, $table);
		}
		$countAll = $counts+(!$disableOptions ? 1 : 0);
		if($deactiveMassAction) {
			$countAll++;
		}
		if($isCustom) {
			templates::assign_vars(array(
				"ArcherAll" => ($countAll+1),
				"ArcherNotTouch" => ($countAll-($deactiveMassAction ? 2 : 0)),
			));
		} else {
			$tpl = str_replace("{ArcherAll}", ($countAll+1), $tpl);
			// var_dump($deactiveMassAction);die();
			$tpl = str_replace("{ArcherNotTouch}", ($countAll-($deactiveMassAction ? 2 : 0)), $tpl);
		}
		if(($get = Arr::get($_GET, "quickViewId", false))!==false) {
			$archerCore = new KernelArcher();
			$objName = get_class($model);
			$model = execEvent("KernelArcher-Shield-Before-Data", $model, $objName);
			$list = $model->Select();
			$list = execEvent("KernelArcher-Shield-Data", $list, $objName);
			if(is_object($list)) {
				$list = $list->getArray();
				$list = execEvent("KernelArcher-Shield-Data-Item", $list, $objName);
				$firsts = current($list);
				if(!is_null($firsts)) {
					$list = $archerCore->callArr($list, "ShieldFunc", array($list, $objName), array());
					call_user_func_array("templates::assign_vars", array($list, $objName, $objName."-".current($list)));
				}
			} elseif(is_array($list)) {
				for($i=0;$i<sizeof($list);$i++) {
					$subList = $list[$i]->getArray();
					$subList = execEvent("KernelArcher-Shield-Data-Item", $subList, $objName);
					$firsts = current($subList);
					if(is_null($firsts)) {
						continue;
					}
					$subList = $archerCore->callArr($subList, "ShieldFunc", array($subList, $objName), array());
					call_user_func_array("templates::assign_vars", array($subList, $objName, $objName."-".current($subList)));
				}
			}
			$isQuick = Arr::get($_GET, "quick", false);
			if($isQuick) {
				$add = '
				[if {C_disableCopy}!=1&&{'.$modelName.'.DisableCopy}!="yes"]<a href="./?pages=Archer&type={ArcherTable}&pageType=Copy&viewId={'.$modelName.'.'.$first.'}{addition}" class="btn btn-turquoise btn-block btn-copy"><span>{L_"Клонировать"}</span></span></a>[/if {C_disableCopy}!=1&&{'.$modelName.'.DisableCopy}!="yes"]
				[if {'.$modelName.'.DisableEdit}!="yes"]<a href="./?pages=Archer&type={ArcherTable}&pageType=Edit&viewId={'.$modelName.'.'.$first.'}{addition}" class="btn btn-purple btn-block quickView btn-edit"><span>{L_quickEdit}</span></a>[/if {'.$modelName.'.DisableEdit}!="yes"]
				[if {'.$modelName.'.DisableRemove}!="yes"]<a href="./?pages=Archer&type={ArcherTable}&pageType=Delete&viewId={'.$modelName.'.'.$first.'}{addition}" onclick="return confirmDelete();" class="btn btn-red btn-block btn-remove"><span>{L_delete}</span></a>[/if {'.$modelName.'.DisableRemove}!="yes"]
				{E_[customOptions][type={ArcherTable};id={'.$modelName.'.'.$first.'}]}
			';
			} else {
				$add = '
				[if {C_disableCopy}!=1&&{'.$modelName.'.DisableCopy}!="yes"]<a href="./?pages=Archer&type={ArcherTable}&pageType=Copy&viewId={'.$modelName.'.'.$first.'}{addition}" class="btn btn-turquoise btn-block btn-copy"><span>{L_"Клонировать"}</span></a>[/if {C_disableCopy}!=1&&{'.$modelName.'.DisableCopy}!="yes"]
				[if {C_disableEdit}!=1&&{'.$modelName.'.DisableEdit}!="yes"]<a href="./?pages=Archer&type={ArcherTable}&pageType=Edit&viewId={'.$modelName.'.'.$first.'}{addition}" class="btn btn-edit btn-block btn-edit"><span>{L_"Редактировать"}</span></a>[/if {C_disableEdit}!=1&&{'.$modelName.'.DisableEdit}!="yes"]
				[if {C_disableDelete}!=1&&{'.$modelName.'.DisableRemove}!="yes"]<a href="./?pages=Archer&type={ArcherTable}&pageType=Delete&viewId={'.$modelName.'.'.$first.'}{addition}" onclick="return confirmDelete();" class="btn btn-red btn-block btn-remove"><span>{L_"Удалить"}</span></a>[/if {C_disableDelete}!=1&&{'.$modelName.'.DisableRemove}!="yes"]
				{E_[customOptions][type={ArcherTable};id={'.$modelName.'.'.$first.'}]}
			';
			}
			$datas = templates::view(templates::comp_datas("[foreach block=".$modelName."]".$data.($disableOptions ? "" : '<td class="td_options">'.$add.'</td>')."[/foreach]"));
			$datas = $this->replaceField($datas, $orderById, $orderBySort, $addition, $first, $head, $data, $modelName, $sortBy, $table);
			HTTP::ajax(array("tpl" => $datas));
			die();
		}
		if($isCustom) {
			$tpl = templates::completed_assign_vars($tplCustom, "");
		}
		$tpl = execEvent("changeAdminArcherShield", $tpl, $table);
		return $tpl;
	}

	private function replaceField($tpl, $orderById, $orderBySort, $addition, $first, $head, $data, $modelName, $sortBy, $table) {
		$tpl = str_replace("{orderById}", ($orderById+1), $tpl);
		$tpl = str_replace("{orderBySort}", $orderBySort, $tpl);
		$tpl = str_replace("{addition}", $addition, $tpl);
		$tpl = str_replace("{ArcherFirst}", $first, $tpl);
		$tpl = str_replace("{ArcherMind}", $head, $tpl);
		$tpl = str_replace("{ArcherData}", $data, $tpl);
		$tpl = str_replace("{ArcherPage}", $modelName, $tpl);
		$tpl = str_replace("{ArcherSort}", implode(",", $sortBy), $tpl);
		$tpl = str_replace("{ArcherTable}", str_replace(PREFIX_DB, "", $table), $tpl);
		return $tpl;
	}
	
}