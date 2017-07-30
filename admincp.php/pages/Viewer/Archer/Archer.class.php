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
		$page = $request->get->get('pageType', false);
		$viewId = $request->get->get('viewId', false);
		$viewId = intval($viewId);
		$typeUni = $request->get->get('type', 'posts');
		$andWhere = $request->get->get("Where", false);
		$typeWhere = $request->get->get("WhereType", false);
		$dataWhere = $request->get->get("WhereData", false);
		$orderBy = $request->get->get("orderBy", false);
		$orderTo = $request->get->get("orderTo", "ASC");
		$removePrefix = false;
		if(strpos($typeUni, PREFIX_DB)!==false) {
			$typeUni = str_replace(PREFIX_DB, "", $typeUni);
			$removePrefix = true;
		}
		$upFirst = (function_exists("nucfirst") ? nucfirst($typeUni) : $this->nucfirst($typeUni));
		if($removePrefix) {
			$typeUni = PREFIX_DB.$typeUni;
		}
		switch($page) {
			/*
			Save data in database
			*/
			case "TakeAdd":
				$model = modules::loadModels("Model".$upFirst, $typeUni);
				$model->SetTable($typeUni);
				$univ = new KernelArcher($typeUni, $model);
				$tpl = $univ->TraceOn("TakeAdd", "ArcherAdd");
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
			/*
			Sorting data in database
			*/
			case "Sort":
				$model = modules::loadModels("Model".$upFirst, $typeUni);
				$model->SetTable($typeUni);
				$model->SetLimit(-1);
				$model->OrderByTo($orderBy, $orderTo);
				if(isset($_GET['catid'])) {
					$model->WhereTo("catId", intval($_GET['catid']));
				}
				if(!empty($andWhere) && !empty($typeWhere) && !empty($dataWhere)) {
					$model->Where($andWhere, $typeWhere, $dataWhere);
				} else if(!empty($andWhere) && !empty($dataWhere)) {
					$model->Where($andWhere, $dataWhere);
				}
				$univ = new KernelArcher($typeUni, $model);
				$univ->Sorting($model, array(&$this, "View"));
			break;
			/*
			View data in database
			*/
			case "Show":
				$model = modules::loadModels("Model".$upFirst, $typeUni);
				$model->SetTable($typeUni);
				$model->SetLimit(1);
				$model->WhereTo("", $viewId);
				$univ = new KernelArcher($typeUni, $model);
				$univ->Show($model, array(&$this, "View"));
			break;
			/*
			Show all data in database for type
			*/
			case "Shield":
			default:
				$model = modules::loadModels("Model".$upFirst, $typeUni);
				$model->SetTable($typeUni);
				$model->SetLimit(-1);
				if(isset($_GET['catid'])) {
					$model->WhereTo("catId", intval($_GET['catid']));
				}
				if(!empty($andWhere) && !empty($typeWhere) && !empty($dataWhere)) {
					$model->Where($andWhere, $typeWhere, $dataWhere);
				} else if(!empty($andWhere) && !empty($dataWhere)) {
					$model->Where($andWhere, $dataWhere);
				}
				templates::assign_var("LinkOrderBy", (empty($orderBy) ? $model->getFirst() : $orderBy));
				templates::assign_var("LinkorderTo", $orderTo);
				$univ = new KernelArcher($typeUni, $model);
				$tpl = $univ->TraceOn("Shield", "ArcherMain");
				$univ->Shield($model, array(&$this, "View"), $tpl, false);
			break;
		}
	}
	
	function View($echo, $prints = false) {
		$this->Prints($echo, $prints);
	}
	
}

ReadPlugins(dirname(__FILE__)."/Plugins/", "Archer");