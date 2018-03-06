<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class SEO extends Core {

	function __construct() {
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
		$this->Prints("SEO");
	}

}