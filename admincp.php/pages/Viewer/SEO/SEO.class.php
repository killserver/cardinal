<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class SEO extends Core {

	function __construct() {
		if(isset($_GET['merge']) && sizeof($_POST)>0) {
			callAjax();
			if(!db::connected()) {
				return false;
			}
			$linkOR = $_POST['linkOR'];
			$langOR = $_POST['langOR'];

			$page = $_POST['page'];
			$lang = $_POST['lang'];

			$seoBlock = $_POST['seoBlock'];
			$seoBlockID = $_POST['seoBlock']['sId'];
			$aText = $_POST['aText'];
			$aTextId = $_POST['aText']['aId'];

			$model = modules::loadModel("aText");
			$model->Where("aId", $aTextId);
			$model->Where("lang", $langOR);
			$model->Where("page", $linkOR);
			$models = $model->Select();

			$model = $model->getInstance(true);
			$model->lang = $lang;
			$model->page = "/".$page;
			$model->text = serialize($aText['descr']);
			if(!empty($models->aId)) {
				//update
				$model->Where("aId", $aTextId);
				$model->Where("lang", $langOR);
				$model->Where("page", $linkOR);
				$model->Update();
			} else {
				//insert
				$model->Insert();
			}



			$model = modules::loadModel("seoBlock");
			$model->Where("sId", $seoBlockID);
			$model->Where("sLang", $langOR);
			$model->Where("sPage", $linkOR);
			$models = $model->Select();

			$model = $model->getInstance(true);
			$model->sLang = $lang;
			$model->sPage = "/".$page;
			$model->sTitle = $seoBlock['sTitle'];
			$model->sMetaDescr = $seoBlock['sMetaDescr'];
			$model->sMetaRobots = $seoBlock['sMetaRobots'];
			$model->sMetaKeywords = $seoBlock['sMetaKeywords'];
			$model->sRedirect = $seoBlock['sRedirect'];
			$model->sImage = $seoBlock['sImage'];
			if(!empty($models->sId)) {
				//update
				$model->Where("sId", $seoBlockID);
				$model->Where("sLang", $langOR);
				$model->Where("sPage", $linkOR);
				$model->Update();
			} else {
				//insert
				$model->Insert();
			}
			return false;
		}
		if(sizeof($_POST)>0) {
			if(Arr::get($_POST, "meta", false)) {
				$_POST['meta'] = array_values($_POST['meta']);
			}
			config::Update("configMetaData", json_encode($_POST));
			cardinal::RegAction("Обновлены данные раздела СЕО мета-информации");
			location("./?pages=SEO");
			return false;
		}
		$configMetaData = config::Select("configMetaData");
		$meta = "[]";
		$head = $body = "";
		if(strlen($configMetaData)>0) {
			$configMetaData = json_decode($configMetaData, true);
			if(isset($configMetaData['head'])) {
				$head = $configMetaData['head'];
				unset($configMetaData['head']);
			}
			if(isset($configMetaData['body'])) {
				$body = $configMetaData['body'];
				unset($configMetaData['body']);
			}
			if(isset($configMetaData['meta'])) {
				$meta = json_encode($configMetaData['meta']);
				unset($configMetaData['meta']);
			}
		}
		templates::assign_var("head", $head);
		templates::assign_var("body", $body);
		templates::assign_var("meta", $meta);
		$groups = array();
		if(db::connected()) {
			$group = $this->initSEO();
			if(!is_bool($group)) {
				$groups = $group;
				templates::assign_var("db_connected", "1");
			}
		} else {
			templates::assign_var("db_connected", "0");
		}
		templates::assign_var("json", json_encode($groups));
		$this->Prints("SEO");
	}

	function initSEO() {
		AText::init();
		$groups = array();
		if(db::getTable("seoBlock")===false || db::getTable("aText")===false || !userlevel::get("atextadmin") || !userlevel::get("seoBlock")) {
			return false;
		}
		db::doquery("SELECT * FROM {{seoBlock}}", true);
		while($row = db::fetch_assoc()) {
			if(!isset($groups[$row['sLang']])) {
				$groups[$row['sLang']] = array();
			}
			if(!isset($groups[$row['sLang']][$row['sPage']])) {
				$groups[$row['sLang']][$row['sPage']] = array();
			}
			$groups[$row['sLang']][$row['sPage']]["seoBlock"] = $row;
		}
		db::doquery("SELECT * FROM {{aText}}", true);
		while($row = db::fetch_assoc()) {
			if(is_serialized($row['text'])) {
				$row['text'] = unserialize($row['text']);
			}
			if(!isset($groups[$row['lang']])) {
				$groups[$row['lang']] = array();
			}
			if(!isset($groups[$row['lang']][$row['page']])) {
				$groups[$row['lang']][$row['page']] = array();
			}
			$groups[$row['lang']][$row['page']]["aText"] = $row;
		}
		return $groups;
	}

}