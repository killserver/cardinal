<?php

class Archer extends Core {
	
	private function nsubstr($text, $start, $end = "") {
		if(empty($end)) {
			$end = nstrlen($text);
		}
		if(function_exists("mb_substr") && defined('MB_OVERLOAD_STRING') && ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING) {
			return mb_substr($text, $start, $end, config::Select('charset'));
		} elseif(function_exists("iconv_substr")) {
			return iconv_substr($text, $start, $end, config::Select('charset'));
		} else {
			return substr($text, $start, $end);
		}
	}
	
	private function nucfirst($text, $all = false) {
		$fc = strtouppers($this->nsubstr($text, 0, 1));
		if(!$all) {
			$fc .= $this->nsubstr($text, 1);
		} else {
			$fc .= strtolowers($this->nsubstr($text, 1));
		}
		return $fc;
	}
	
	function __construct() {
		$request = new Request();
		$nameTableWithoutPrefix = $typeUni = $request->get->get('type', 'posts');
		$page = $request->get->get('pageType', false);
		$page = execEvent("change-page", $page, $typeUni);
		$viewId = $request->get->get('viewId', false);
		$viewId = execEvent("change-view-id", $viewId, $typeUni);
		$viewId = (config::Select("db", "driver")==="db_mongo" ? $viewId : intval($viewId));
		$andWhere = $request->get->get("Where", false);
		$andWhere = execEvent("change-Where", $andWhere, $typeUni);
		$typeWhere = $request->get->get("WhereType", false);
		$typeWhere = execEvent("change-WhereType", $typeWhere, $typeUni);
		$dataWhere = $request->get->get("WhereData", false);
		$dataWhere = execEvent("change-WhereData", $dataWhere, $typeUni);
		$orderBy = $request->get->get("orderBy", false);
		$orderBy = execEvent("change-orderBy", $orderBy, $typeUni);
		$orderTo = $request->get->get("orderTo", "ASC");
		$orderTo = execEvent("change-orderTo", $orderTo, $typeUni);
		execEventRef("changeInfoArcher", $typeUni, $page, $viewId, $andWhere, $typeWhere, $dataWhere, $orderBy, $orderTo);
		if(defined("PREFIX_DB") && PREFIX_DB!=="" && strpos($typeUni, PREFIX_DB)!==false) {
			$typeUni = str_replace(PREFIX_DB, "", $typeUni);
			$nameTableWithoutPrefix = str_replace(PREFIX_DB, "", $nameTableWithoutPrefix);
		}
		$upFirst = (function_exists("nucfirst") ? nucfirst($typeUni) : $this->nucfirst($typeUni));
		if(defined("PREFIX_DB") && PREFIX_DB!=="") {
			$typeUni = PREFIX_DB.$typeUni;
		}
		switch($page) {
			case "sleep":
				execEvent("ArcherSleep", $this);
			break;
			/*
			Quick save data in database
			*/
			case "QuickEdit":
				$model = modules::loadModels("Model".$upFirst, $typeUni);
				$model->SetTable($typeUni);
				if($request->get->get("Save", false)) {
					$id = $request->post->get("pk");
					$name = $request->post->get("name");
					$value = $request->post->get("value");
					$model->SetLimit(1);
					$model->Where("", $id);
					$mod = $model->Select();
					$model = $model->getInstance();
					foreach($mod as $k => $v) {
						$model->{$k} = $v;
					}
					$model->{$name} = $value;
					$model->WhereTo("", $id);
					$model->Update();
				}
				return;
			break;
			/*
			Save data in database
			*/
			case "TakeAdd":
				$model = modules::loadModels("Model".$upFirst, $typeUni);
				$model->SetTable($typeUni);
				$univ = new KernelArcher($typeUni, $model);
				$univ->TakeAdd($model, array(&$this, "View"), "ArcherTakeAdd");
			break;
			/*
			View model for add data
			*/
			case "Add":
				$model = modules::loadModels("Model".$upFirst, $typeUni);
				$model->SetTable($typeUni);
				$univ = new KernelArcher($typeUni, $model);
				$univ->Add($model, array(&$this, "View"));
			break;
			case "CustomAdd":
				$model = modules::loadModels("Model".$upFirst, $typeUni);
				$model->SetTable($typeUni);
				$univ = new KernelArcher($typeUni, $model);
				$univ->CustomAdd($model, array(&$this, "View"));
			break;
			/*
			Edit data in database
			*/
			case "TakeEdit":
				$model = modules::loadModels("Model".$upFirst, $typeUni);
				$model->SetTable($typeUni);
				$model->SetLimit(1);
				$model->WhereTo("", $viewId);
				$univ = new KernelArcher($typeUni, $model);
				$univ->TakeEdit($model, array(&$this, "View"), "ArcherTakeAdd");
			break;
			case "Copy":
				$model = modules::loadModels("Model".$upFirst, $typeUni);
				$model->SetTable($typeUni);
				$model->SetLimit(1);
				$model->WhereTo("", $viewId);
				$modelData = $model->Select();
				$firstId = $model->getFirst();
				$modelData = $modelData->getArray();
				$creator = strtolowers($upFirst);
				$uniq = "";
				if(file_exists(PATH_CACHE_USERDATA."struct".DS."file_".$creator.".txt")) {
					$m = file_get_contents(PATH_CACHE_USERDATA."struct".DS."file_".$creator.".txt");
					if(Validate::json($m)) {
						$m = json_decode($m, true);
						if(isset($m['data']) && is_array($m['data'])) {
							$m = $m['data'];
							$notIgnoreType = array(
								'varchar',
								'email',
								'link',
								'password',
								'onlytextareatext',
								'longtext',
							);
							for($i=0;$i<sizeof($m);$i++) {
								if(isset($m[$i]['alttitle']) && in_array($m[$i]['type'], $notIgnoreType)) {
									$uniq = $m[$i]['alttitle'];
									break;
								}
							}
						}
					}
				}
				unset($modelData[$firstId]);
				$models = $model->getInstance(true);
				$iterable = 1;
				foreach($modelData as $k => $v) {
					if(!empty($uniq) && $k==$uniq) {
						//$v .= "_-_1";
						preg_match("#_-_(.*?)$#is", $v, $find);
						if($find) {
							$v = str_replace($find[0], "", $v);
							$iterable += floatval($find[1]);
						}
						$v .= "_-_".$iterable;
					}
					$models->{$k} = $v;
				}
				$models->Insert();
				cardinal::RegAction("Клонирована запись ID:".$viewId." в таблице ".$typeUni);
				location(html_entity_decode(HTTP::getServer("HTTP_REFERER")));
			break;
			case "CopyEdit":
				$model = modules::loadModels("Model".$upFirst, $typeUni);
				$model->SetTable($typeUni);
				$model->SetLimit(1);
				$model->WhereTo("", $viewId);
				$modelData = $model->Select();
				$firstId = $model->getFirst();
				$modelData = $modelData->getArray();
				$creator = strtolowers($upFirst);
				$uniq = "";
				if(file_exists(PATH_CACHE_USERDATA."struct".DS."file_".$creator.".txt")) {
					$m = file_get_contents(PATH_CACHE_USERDATA."struct".DS."file_".$creator.".txt");
					if(Validate::json($m)) {
						$m = json_decode($m, true);
						if(isset($m['data']) && is_array($m['data'])) {
							$m = $m['data'];
							$notIgnoreType = array(
								'varchar',
								'email',
								'link',
								'password',
								'onlytextareatext',
								'longtext',
							);
							for($i=0;$i<sizeof($m);$i++) {
								if(isset($m[$i]['alttitle']) && in_array($m[$i]['type'], $notIgnoreType)) {
									$uniq = $m[$i]['alttitle'];
									break;
								}
							}
						}
					}
				}
				unset($modelData[$firstId]);
				$models = $model->getInstance(true);
				$iterable = 1;
				foreach($modelData as $k => $v) {
					if(!empty($uniq) && $k==$uniq) {
						//$v .= "_-_1";
						preg_match("#_-_(.*?)$#is", $v, $find);
						if($find) {
							$v = str_replace($find[0], "", $v);
							$iterable += floatval($find[1]);
						}
						$v .= "_-_".$iterable;
					}
					$models->{$k} = $v;
				}
				$id = db::last_id($models->loadedTable(), true);
				$models->Insert();
				cardinal::RegAction("Клонирована запись ID:".$viewId." в таблице ".$typeUni);
				location("./?pages=Archer&type=".$nameTableWithoutPrefix."&pageType=Edit&viewId=".$id);
			break;
			/*
			View model for edit data
			*/
			case "Edit":
				$model = modules::loadModels("Model".$upFirst, $typeUni);
				$model->SetTable($typeUni);
				$model->SetLimit(1);
				$model->WhereTo("", $viewId);
				$univ = new KernelArcher($typeUni, $model);
				$univ->Edit($model, array(&$this, "View"));
			break;
			case "CustomEdit":
				$model = modules::loadModels("Model".$upFirst, $typeUni);
				$model->SetTable($typeUni);
				$model->SetLimit(1);
				$model->WhereTo("", $viewId);
				$univ = new KernelArcher($typeUni, $model);
				$univ->CustomEdit($model, array(&$this, "View"));
			break;
			/*
			Delete data in database
			*/
			case "Delete":
				$model = modules::loadModels("Model".$upFirst, $typeUni);
				$model->SetTable($typeUni);
				$model->SetLimit(1);
				$model->WhereTo("", $viewId);
				$univ = new KernelArcher($typeUni, $model);
				$tpl = $univ->TraceOn("Delete", "ArcherTakeDelete");
				$univ->TakeDelete($model, array(&$this, "View"), $tpl, false);
			break;
			case "MultiAction":
				if($request->post->get("action", false)=="delete") {
					$ids = $request->post->get("delete", array());
					if(sizeof($ids)>0) {
						$trash = false;
						if(defined("PATH_CACHE_USERDATA")) {
							if(!is_writeable(PATH_CACHE_USERDATA)) {
								@chmod(PATH_CACHE_USERDATA, 0777);
							}
							if(!file_exists(PATH_CACHE_USERDATA."trash_bin.lock")) {
								db::query("CREATE TABLE IF NOT EXISTS {{trash_bin}} ( `tId` int not null auto_increment, `tTable` varchar(255) not null, `tData` longtext not null, `tTime` int(11) not null, `tIp` varchar(255) not null, primary key `id`(`tId`) ) ENGINE=MyISAM;");
								file_put_contents(PATH_CACHE_USERDATA."trash_bin.lock", "");
							}
							$trash = true;
						}
						$days = 30;
						if(defined("EMPTY_TRASH_DAYS")) {
							if(is_numeric(EMPTY_TRASH_DAYS) && EMPTY_TRASH_DAYS>0) {
								$days = EMPTY_TRASH_DAYS;
							} else if(is_bool(EMPTY_TRASH_DAYS) && EMPTY_TRASH_DAYS===false) {
								$days = 0;
							}
						}
						if($days==0 || !$trash) {
							$model = modules::loadModels("Model".$upFirst, $typeUni);
							$model->SetTable($typeUni);
							$model->WhereTo("", "", "IN(".implode(",", $ids).")", "AND", false);
							$model->multiple();
							$model->Deletes();
							cardinal::RegAction("Удалены записи ID:".implode(",", $ids)." в таблице ".$typeUni);
						} else {
							$model = modules::loadModels("Model".$upFirst, $typeUni);
							$model->SetTable($typeUni);
							$model->WhereTo("", "", "IN(".implode(",", $ids).")", "AND", false);
							$model->multiple();
							$data = $model->Select();
							for($i=0;$i<sizeof($data);$i++) {
								db::doquery("INSERT INTO {{trash_bin}} SET `tTable` = ".db::escape($typeUni).", `tData` = ".db::escape(json_encode($data[$i])).", `tTime`= UNIX_TIMESTAMP(), `tIp` = '".HTTP::getip()."'");
								cardinal::RegAction("Перемещение данных в Арчере в корзину. Модель \"".$typeUni."\". ИД: \"".current($data[$i])."\"");
							}
							$model->Deletes();
						}
					}
				}
				location(html_entity_decode(HTTP::getServer("HTTP_REFERER")));
			break;
			/*
			Show all data in database for type
			*/
			case "Shield":
			default:
				$model = modules::loadModels("Model".$upFirst, $typeUni);
				$model->SetTable($typeUni);
				$model->multiple(true);
				$model->SetLimit(-1);
				$model = execEvent("loadModel", $model, $typeUni);
				if(($get = Arr::get($_GET, "quickViewId", false))!==false) {
					if($get>0) {
						$model->Where($model->getFirst(), $get);
					} else if($get==-1) {
						$model->SetLimit(1);
						$model->OrderBy($model->getFirst(), "DESC");
					}
				} else {
					if($request->get->get("ShowPages", false)) {
						if($andWhere!==false && $typeWhere!==false && $dataWhere!==false) {
							$model->Where($andWhere, $typeWhere, $dataWhere);
						} else if($andWhere!==false && $dataWhere!==false) {
							$model->Where($andWhere, $dataWhere);
						}
						if(strpos($_SERVER['REQUEST_URI'], "page=")!==false) {
							$now = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "&page="));
						} else {
							$now = $_SERVER['REQUEST_URI'];
						}
						$pager = new pager($request->get->get("page", 1)-1, $model->getMax(), 10, $now, "&page=", 3);
						$get = $pager->get();
						$get = array_values($get);
						for($i=0;$i<sizeof($get);$i++) {
							templates::assign_vars($get[$i], "pager", "page".$i);
						}
						templates::assign_var("prevLinkPager", $pager->prevLink());
						templates::assign_var("nextLinkPager", $pager->nextLink());
						$limit = $pager->getLimit();
						$model->SetLimit($limit[1], $limit[0]);
						$model->OrderByTo((empty($orderBy) ? $model->getFirst() : $orderBy), ($orderTo ? $orderTo : "ASC"));
					}
					if(isset($_GET['catid'])) {
						$model->WhereTo("catId", intval($_GET['catid']));
					}
					if($andWhere!==false && $typeWhere!==false && $dataWhere!==false) {
						$model->Where($andWhere, $typeWhere, $dataWhere);
					} else if($andWhere!==false && $dataWhere!==false) {
						$model->Where($andWhere, $dataWhere);
					}
				}
				templates::assign_var("LinkOrderBy", (empty($orderBy) ? $model->getFirst() : $orderBy));
				templates::assign_var("LinkorderTo", $orderTo);
				$univ = new KernelArcher($typeUni, $model);
				$tmps = "ArcherMain";
				if($request->get->get("ShowPages", false)) {
					templates::assign_var("activate_pager", "yes");
				} else {
					templates::assign_var("activate_pager", "no");
				}
				if($request->get->get("tmp", false)) {
					$tmps = $request->get->get("tmp");
				}
				execEventRef("change_archer_shield_template", $tmps, $typeUni);
				$tpl = $univ->TraceOn("Shield", $tmps);
				$univ->Shield($model, array(&$this, "View"), $tpl, false);
			break;
		}
	}
	
	function View($echo, $prints = false) {
		if(ajax_check()=="ajax") {
			callAjax();
			echo templates::view($echo);
		} else {
			$this->Prints($echo, $prints);
		}
	}
	
}

ReadPlugins(dirname(__FILE__).DIRECTORY_SEPARATOR."Plugins".DIRECTORY_SEPARATOR, "Archer");