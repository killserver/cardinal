<?php

class KernelArcher {
	
	private $selectTable = "";
	private static $callbackFunc = array();
	private $localModel = false;
	private static $excl = array();
	public static $editModel = array();
	private $countCall = array();
	
	function __construct($table, $model = false) {
		$this->selectTable = $table;
		if(is_object($model)) {
			$this->localModel = $model;
		}
	}
	
	public static function callback($page, $name, $call = "") {
		if(!empty($call)) {
			if(!isset(self::$callbackFunc[$page])) {
				self::$callbackFunc[$page] = array();
			}
			if(!isset(self::$callbackFunc[$page][$name])) {
				self::$callbackFunc[$page][$name] = array();
			}
			self::$callbackFunc[$page][$name][] = $call;
		} else {
			self::$callbackFunc[$page][] = $name;
		}
	}

	public static function excludeField($page, $field = "", $mod = "") {
		$editMod = "edit";
		if(!empty($mod)) {
			$editMod = $page;
			$workPage = $field;
			$workField = $mod;
		} elseif(in_array($page, array("select", "get"))) {
			$editMod = $page;
			$workPage = $field;
			$workField = $mod;
		} else {
			$workPage = $page;
			$workField = $field;
		}
		$ret = false;
		switch($editMod) {
			case "add":
			case "edit":
				if(is_array($workField)) {
					$arr = array_values($workField);
					for($i=0;$i<sizeof($arr);$i++) {
						self::$excl[$workPage][$arr[$i]] = true;
					}
					$ret = true;
				} elseif(is_string($workField)) {
					self::$excl[$workPage][$workField] = true;
					$ret = true;
				} else {
					$ret = false;
				}
			break;
			case "remove":
			case "delete":
				if(isset(self::$excl[$workPage]) && isset(self::$excl[$workPage][$workField])) {
					unset(self::$excl[$workPage][$workField]);
					$ret = true;
				}
			break;
			case "clear":
				if(isset(self::$excl[$workPage]) && is_array(self::$excl[$workPage])) {
					$k = array_keys(self::$excl[$workPage]);
					for($i=0;$i<sizeof($k);$i++) {
						unset(self::$excl[$workPage][$k[$i]]);
					}
					$ret = true;
				}
			break;
			case "select":
			case "get":
				if(isset(self::$excl[$workPage]) && is_array(self::$excl[$workPage])) {
					$ret = array_keys(self::$excl[$workPage]);
				} else {
					$ret = array();
				}
			break;
		}
		return $ret;
	}
	
	function TakeAdd($model = "", $objTemplate = "", $template = "", $load = true) {
		if((empty($model) && (gettype($model)!=="object" || !method_exists($model, "getArray"))) && (gettype($this->localModel)!=="object" || !method_exists($this->localModel, "getArray"))) {
			throw new Exception("Error type kernal #1 parameter");
			die();
		}
		$request = new Request();
		if(sizeof($request->post)==0) {
			throw new Exception("Error post data to kernal");
			die();
		}
		$modelName = get_class($model);
		$model->SetTable($this->selectTable);
		$model->loadTable();
		$model->SetTable($this->selectTable);
		$firstId = $model->getFirst();
		$selectId = $model->{$firstId};
		$model = $this->callArr($model, "TakeAddModel", array($model, $firstId, "countCall" => ""));
		unset($model->{$firstId});
		$list = $model->getArray();
		if(isset($list['pathForUpload'])) {
			$uploads = $list['pathForUpload'];
			unset($list['pathForUpload']);
		} else {
			$uploads = "uploads".DS;
		}
		foreach($list as $k => $v) {
			$files = $request->files->get($k, false);
			$post = $request->post->get($k, false);
			$post = $this->rebuildData($post);
			$files = $this->rebuildData($files);
			if(!empty($files) && ($model->getAttribute($k, "type")=="file" || $model->getAttribute($k, "type")=="fileArray")) {
				$type = $files;
				if(isset($type['name']) && is_array($type['name'])) {
					$viewI = 1;
					$type = Files::reArrayFiles($type);
					$types = array();
					foreach($type as $ks => $vs) {
						$upload = $this->UploadFile($model, $ks, $selectId, $vs, $uploads, $model->getAttribute($k, "allowUpload"), $viewI);
						if(!empty($upload) || !empty($v)) {
							$types[$ks] = (!$upload ? $v : $upload."?".time());
							$viewI++;
						}
					}
					$type = $types;
				} else {
					$upload = $this->UploadFile($model, $k, $selectId, $type, $uploads, $model->getAttribute($k, "allowUpload"));
					$type = (!$upload ? $v : $upload."?".time());
				}
			} else if(!empty($post)) {
				$type = $post;
			} else {
				$type = $v;
			}
			$remove = false;
			if(!is_string($type)) {
				$type = serialize($type);
				$remove = true;
			}
			$type = trim($type);
			if(!$remove && Validate::not_empty($type)) {
				$model->{$k} = $type;
			} else {
				unset($model->{$k});
			}
		}
		$model = $this->callArr($model, "TakeAddModel", array($model, $firstId, "countCall" => ""));
		$getExclude = KernelArcher::excludeField("get", "Edit");
		for($i=0;$i<sizeof($getExclude);$i++) {
			if(isset($model->{$getExclude[$i]})) {
				unset($model->{$getExclude[$i]});
			}
		}
		$model->WhereTo($firstId, $selectId);
		unset($model->{$firstId});
		if(isset($model->pathForUpload)) {
			unset($model->pathForUpload);
		}
		cardinal::RegAction("Добавление данных в Арчере. Модель \"".$modelName."\"");
		$model->Insert();
		if(!empty($objTemplate)) {
			if(isset($_GET['type'])) {
				location("{C_default_http_local}{D_ADMINCP_DIRECTORY}?pages=Archer&type=".Saves::SaveOld($_GET['type']), 3, false);
			}
			$this->UnlimitedBladeWorks($objTemplate, $template, $load);
		} else if(isset($_GET['type'])) {
			location("{C_default_http_local}{D_ADMINCP_DIRECTORY}?pages=Archer&type=".Saves::SaveOld($_GET['type']));
		}
	}
	
	function Add($model = "", $objTemplate = "") {
		if((empty($model) && (gettype($model)!=="object" || !method_exists($model, "getArray"))) && (gettype($this->localModel)!=="object" || !method_exists($this->localModel, "getArray"))) {
			throw new Exception("Error type kernal #1 parameter");
			die();
		}
		$model->loadTable($this->selectTable);
		$model = $this->callArr($model, "AddModel", array($model));
		$list = $model->getArray();
		$this->AddBlocks("AddData", $list);
		$tpl = $this->TraceOn("Add", $model, "ArcherAdd");
		if(!empty($objTemplate)) {
			$this->UnlimitedBladeWorks($objTemplate, $tpl, false);
		}
	}
	
	function rebuildData(&$arr) {
		if(is_array($arr)) {
			foreach($arr as $k => $v) {
				if(empty($v)) {
					unset($arr[$k]);
				}
			}
		}
		return $arr;
	}
	
	private function UploadFile($model, $key, $id, $file, $path, $type = "", $i = -1) {
		$file = $this->callArr($file, "TakeUpload", func_get_args());
		if(!is_array($file)
			||
			(!isset($file['key']) && !isset($file[1]))
			||
			(!isset($file['id']) && !isset($file[2]))
			||
			(!isset($file['file']) && !isset($file[3]))
			||
			(!isset($file['path']) && !isset($file[4]))
			||
			(!isset($file['type']) && !isset($file[5]))
		) {
			throw new Exception("Returned data for upload");
			die();
		}
		$model = isset($file['model']) ? $file['model'] : $file[0];
		$key = isset($file['key']) ? $file['key'] : $file[1];
		$id = isset($file['id']) ? $file['id'] : $file[2];
		$path = isset($file['path']) ? $file['path'] : $file[4];
		if(is_Array($path)) {
			if(isset($path[$key])) {
				$path = $path[$key];
			} elseif(isset($path["default"])) {
				$path = $path["default"];
			} else {
				$path = current($path);
			}
		}
		$type = isset($file['key']) ? $file['type'] : $file[5];
		$fileName = isset($file['fileName']) ? $file['fileName'] : "";
		$file = isset($file['file']) ? $file['file'] : $file[3];
		$path = ROOT_PATH.$path;
		Files::$switchException = true;
		//Files::$simulate = true;
		if(is_array($file)) {
			return Files::saveFile($file, $fileName, $path);
		} else {
			return false;
		}
	}
	
	function TakeEdit($model = "", $objTemplate = "", $template = "", $load = true) {
		if((empty($model) && (gettype($model)!=="object" || !method_exists($model, "getArray"))) && (gettype($this->localModel)!=="object" || !method_exists($this->localModel, "getArray"))) {
			throw new Exception("Error type kernal #1 parameter");
			die();
		}
		$request = new Request();
		if(sizeof($request->post)==0) {
			throw new Exception("Error post data to kernal");
			die();
		}
		$modelName = get_class($model);
		$model->SetTable($this->selectTable);
		$models = $model->Select();
		$models->SetTable($this->selectTable);
		$firstId = $models->getFirst();
		$selectId = $models->{$firstId};
		$models = $this->callArr($models, "TakeEditModel", array($models, $firstId, "countCall" => ""));
		unset($model->{$firstId});
		$list = $models->getArray();
		if(isset($list['pathForUpload'])) {
			$uploads = $list['pathForUpload'];
			unset($list['pathForUpload']);
		} else {
			$uploads = "uploads".DS;
		}
		foreach($list as $k => $v) {
			$files = $request->files->get($k, false);
			$post = $request->post->get($k, false);
			$post = $this->rebuildData($post);
			$files = $this->rebuildData($files);
			if(!empty($files) && ($models->getAttribute($k, "type")=="file" || $models->getAttribute($k, "type")=="fileArray")) {
				$type = $files;
				if((isset($type['error']) && is_array($type['error'])) && (!isset($type['name']) || is_array($type['name']))) {
					$viewI = 1;
					$type = Files::reArrayFiles($type);
					$types = array();
					foreach($type as $ks => $vs) {
						$upload = $this->UploadFile($models, $ks, $selectId, $vs, $uploads, $models->getAttribute($k, "allowUpload"), $viewI);
						if(!empty($upload) || !empty($v)) {
							$types[$ks] = (!$upload ? $v : $upload."?".time());
							$viewI++;
						}
					}
					$type = $types;
				} else {
					$upload = $this->UploadFile($models, $k, $selectId, $type, $uploads, $models->getAttribute($k, "allowUpload"));
					$type = (!$upload ? $v : $upload."?".time());
				}
			} else if(!empty($post)) {
				$type = $post;
			} else {
				$type = $v;
			}
			$remove = false;
			if(!is_string($type)) {
				$type = serialize($type);
				$remove = true;
			}
			$type = trim($type);
			if(!$remove && Validate::not_empty($type)) {
				$model->{$k} = $type;
			} else {
				unset($model->{$k});
			}
		}
		$model = $this->callArr($model, "TakeEditModel", array($model, $firstId, "countCall" => ""));
		$getExclude = KernelArcher::excludeField("get", "Edit");
		for($i=0;$i<sizeof($getExclude);$i++) {
			if(isset($model->{$getExclude[$i]})) {
				unset($model->{$getExclude[$i]});
			}
		}
		$model->WhereTo($firstId, $selectId);
		unset($model->{$firstId});
		if(isset($model->pathForUpload)) {
			unset($model->pathForUpload);
		}
		cardinal::RegAction("Обновление данных в Арчере. Модель \"".$modelName."\". ИД: \"".$selectId."\"");
		$model->Update();
		if(!empty($objTemplate)) {
			if(isset($_GET['type'])) {
				location("{C_default_http_local}{D_ADMINCP_DIRECTORY}?pages=Archer&type=".Saves::SaveOld($_GET['type']), 3, false);
			}
			$this->UnlimitedBladeWorks($objTemplate, $template, $load);
		} else if(isset($_GET['type'])) {
			location("{C_default_http_local}{D_ADMINCP_DIRECTORY}?pages=Archer&type=".Saves::SaveOld($_GET['type']));
		}
	}
	
	function Edit($model = "", $objTemplate = "") {
		if((empty($model) && (gettype($model)!=="object" || !method_exists($model, "getArray"))) && (gettype($this->localModel)!=="object" || !method_exists($this->localModel, "getArray"))) {
			throw new Exception("Error type kernal #1 parameter");
			die();
		}
		$model = $model->Select();
		$model->SetTable($this->selectTable);
		$model = $this->callArr($model, "EditModel", array($model));
		$list = $model->getArray();
		$firstId = current($list);
		if(empty($firstId)) {
			throw new Exception("Error type kernal get data");
			die();
		}
		$this->AddBlocks("EditData", $list);
		$tpl = $this->TraceOn("Edit", $model, "ArcherAdd");
		if(!empty($objTemplate)) {
			$this->UnlimitedBladeWorks($objTemplate, $tpl, false);
		}
	}
	
	function Sorting($model, $objTemplate = "", $template = "") {
		if((empty($model) && (gettype($model)!=="object" || !method_exists($model, "getArray"))) && (gettype($this->localModel)!=="object" || !method_exists($this->localModel, "getArray"))) {
			throw new Exception("Error type kernal #1 parameter");
			die();
		}
		$objName = get_class($model);
		if(Arr::get($_GET, 'save', false)) {
			$first = $model->getFirst();
			$orderBy = Arr::get($_GET, 'orderBy', false);
			if(!$orderBy) {
				HTTP::echos("Проверьте корректность данных");
				die();
			}
			$data = array_keys($_POST);
			$data = explode("&", $data[0]);
			for($i=0;$i<sizeof($data);$i++) {
				db::doquery("UPDATE `".$objName."` SET `".$orderBy."` = ".($i+1)." WHERE `".$first."` = ".$data[$i], true);
			}
			cardinal::RegAction("Сортировка данных в Арчере. Модель \"".$objName."\"");
			HTTP::echos("Успешно обновили сортировку");
			die();
		}
		$model->SetTable($this->selectTable);
		$list = $model->Select();
		if($list===null) {
			$list = $model;
		}
		if(is_object($list)) {
			$subList = $list->getArray();
			$subList = $this->callArr($subList, "SortingFunc", array($subList));
			$this->AddBlocks("Sort", $subList, $objName, $objName."-".current($subList));
		} elseif(is_array($list)) {
			for($i=0;$i<sizeof($list);$i++) {
				$subList = $list[$i]->getArray();
				$subList = $this->callArr($subList, "SortingFunc", array($subList));
				$this->AddBlocks("Sort", $subList, $objName, $objName."-".current($subList));
			}
		}
		if(empty($template)) {
			$template = $this->TraceOn("Sorting", $list, "ArcherSorting");
		}
		if(!empty($objTemplate)) {
			$this->UnlimitedBladeWorks($objTemplate, $template, false);
		}
	}
	
	function TakeDelete($model = "", $objTemplate = "", $template = "", $load = true) {
		if((empty($model) && (gettype($model)!=="object" || !method_exists($model, "getArray"))) && (gettype($this->localModel)!=="object" || !method_exists($this->localModel, "getArray"))) {
			throw new Exception("Error type kernal #1 parameter");
			die();
		}
		$modelName = get_class($model);
		$model->SetTable($this->selectTable);
		$models = $model->Select();
		if($models===null) {
			$models = $model;
		}
		$del = $this->callArr($model, "TakeDelete", array($model, $models));
		if(isset($del['model'])) {
			$model = $del['model'];
		}
		if(isset($del['models'])) {
			$models = $del['models'];
		}
		$models = $models->getArray();
		$first = current($models);
		if(isset(self::$excl[__FUNCTION__])) {
			foreach(self::$excl[__FUNCTION__] as $k => $v) {
				if(isset($models[$k])) {
					unset($models[$k]);
				}
			}
		}
		foreach($models as $name => $val) {
			if(empty($val)) {
				continue;
			}
			$type = $model->getAttribute($name, "type");
			if($type=="image") {
				$exp = explode("?", $val);
				if((is_array($exp) && isset($exp[0]) && file_exists(ROOT_PATH.$exp[0])) || (file_exists(ROOT_PATH.$val))) {
					unlink(ROOT_PATH.(is_array($exp) && isset($exp[0]) ? $exp[0] : $val));
				}
			} else if($type=="imageArray") {
				$exp = explode(",", $val);
				for($i=0;$i<sizeof($exp);$i++) {
					$exps = explode("?", $exp[$i]);
					if((is_array($exps) && isset($exps[0]) && file_exists(ROOT_PATH.$exps[0])) || (file_exists(ROOT_PATH.$exp[$i]))) {
						unlink(ROOT_PATH.(is_array($exps) && isset($exps[0]) ? $exps[0] : $exp[$i]));
					}
				}
			}
		}
		cardinal::RegAction("Удаление данных в Арчере. Модель \"".$modelName."\". ИД: \"".$first."\"");
		$list = $model->Deletes();
		if(!empty($objTemplate)) {
			if(isset($_GET['type'])) {
				location("{C_default_http_local}{D_ADMINCP_DIRECTORY}?pages=Archer&type=".Saves::SaveOld($_GET['type']), 3, false);
			}
			$this->UnlimitedBladeWorks($objTemplate, $template, $load);
		} else if(isset($_GET['type'])) {
			location("{C_default_http_local}{D_ADMINCP_DIRECTORY}?pages=Archer&type=".Saves::SaveOld($_GET['type']));
		}
	}
	
	function Show($model = "", $objTemplate = "", $template = "") {
		if((empty($model) && (gettype($model)!=="object" || !method_exists($model, "getArray"))) && (gettype($this->localModel)!=="object" || !method_exists($this->localModel, "getArray"))) {
			throw new Exception("Error type kernal #1 parameter");
			die();
		}
		$model->SetTable($this->selectTable);
		$lists = $model->Select();
		$list = $lists->getArray();
		$firstId = current($list);
		if(empty($firstId)) {
			throw new Exception("Error type kernal get data");
			die();
		}
		$load = false;
		$template = "";
		if(isset(self::$callbackFunc["Show"])) {
			$ret = $this->callArr($lists, "Show", array($this->selectTable, $objTemplate, $lists));
			if(isset($ret['list'])) {
				$list = $ret['list'];
			}
			if(isset($ret['objTemplate'])) {
				$objTemplate = $ret['objTemplate'];
			}
			if(isset($ret['template'])) {
				$template = $ret['template'];
			}
			if(isset($ret['load'])) {
				$load = $ret['load'];
			}
		}
		$this->AddBlocks("Show", $list);
		if(empty($template)) {
			$tpl = $this->TraceOn("Show", $list, "ArcherShow");
		}
		if(!empty($objTemplate)) {
			$this->UnlimitedBladeWorks($objTemplate, $tpl, $load);
		}
	}
	
	function Shield($model = "", $objTemplate = "", $template = "", $load = true) {
		if((empty($model) && (gettype($model)!=="object" || !method_exists($model, "getArray"))) && (gettype($this->localModel)!=="object" || !method_exists($this->localModel, "getArray"))) {
			throw new Exception("Error type kernal #1 parameter");
			die();
		}
		$objName = get_class($model);
		$model->SetTable($this->selectTable);
		$list = $model->Select();
		if(is_object($list)) {
			$list = $list->getArray();
			$list = $this->callArr($list, "ShieldFunc", array($list));
			$this->AddBlocks("Mains", $list, $objName, $objName."-".current($list));
		} elseif(is_array($list)) {
			for($i=0;$i<sizeof($list);$i++) {
				$subList = $list[$i]->getArray();
				$subList = $this->callArr($subList, "ShieldFunc", array($subList));
				$this->AddBlocks("Mains", $subList, $objName, $objName."-".current($subList));
			}
		}
		if(!empty($objTemplate)) {
			$this->UnlimitedBladeWorks($objTemplate, $template, $load);
		}
	}
	
	private function callArr($return, $page, $func, $params = array()) {
		if(!isset($this->countCall[$page]) || !is_numeric($this->countCall[$page])) {
			$this->countCall[$page] = 0;
		}
		$this->countCall[$page]++;
		if(array_key_exists("countCall", $params) && is_string($func)) {
			$params = array_merge($params, array("countCall" => $this->countCall[$page]));
		} elseif(is_array($func) && array_key_exists("countCall", $func)) {
			$func = array_merge($func, array("countCall" => $this->countCall[$page]));
		}
		if(isset(self::$callbackFunc[$page]) && is_string($func) && isset(self::$callbackFunc[$page][$func])) {
			for($i=0;$i<sizeof(self::$callbackFunc[$page][$func]);$i++) {
				$return = call_user_func_array(self::$callbackFunc[$page][$func][$i], $params);
			}
		} else if(isset(self::$callbackFunc[$page]) && is_array($func)) {
			for($i=0;$i<sizeof(self::$callbackFunc[$page]);$i++) {
				$return = call_user_func_array(self::$callbackFunc[$page][$i], $func);
			}
		}
		return $return;
	}
	
	function TraceOn($page, $model = "", $tpl = "", $objTemplate = "") {
		if(!empty($model) && empty($tpl) && empty($objTemplate)) {
			$tpl = $model;
			$model = $this->localModel;
		}
		if((empty($model) && (gettype($model)!=="object" || !method_exists($model, "getArray"))) && (gettype($this->localModel)!=="object" || !method_exists($this->localModel, "getArray"))) {
			throw new Exception("Error type kernal #1 parameter");
			die();
		}
		if(!empty($objTemplate) && is_object($objTemplate) && modules::checkObject($objTemplate, "templates")) {
			$tpl = call_user_func_array(array($objTemplate, "load_templates"), array($tpl));
		} else {
			$tpl = templates::load_templates($tpl);
		}
		$tpl = $this->callArr($tpl, $page, "TraceOn", array($this->selectTable, $page, $model, $tpl));
		return $tpl;
	}
	
	function AddBlocks($page, $arr, $block = "", $id = "") {
		foreach($arr as $k => $v) {
			if(isset(self::$callbackFunc[$page][$k])) {
				$arr[$k] = $this->callArr($arr[$k], $page, $k, array($v));
			}
		}
		if(!empty($block) && !empty($id)) {
			call_user_func_array("templates::assign_vars", array($arr, $block, $id));
		} else {
			call_user_func_array("templates::assign_vars", array($arr));
		}
	}
	
	function AddBlock($page, $name, $val) {
		$val = $this->callArr($val, $page, $name, array($val));
		$arr = array("name" => $name, "value" => $val);
		$val = $this->callArr($val, $page, array($arr));
		call_user_func_array("templates::assign_var", array($arr['name'], $arr['value']));
	}
	
	function Viewing($type, $name, $val, $default = "", $block = false) {
		$retType = "";
		switch($type) {
			case "int":
			case "bigint":
				$retType = "<input id=\"".$name."\" class=\"form-control\" type=\"numeric\" name=\"".$name."\" placeholder=\"{L_\"Введите&nbsp;".$name."\"}\" value=\"".htmlspecialchars($val)."\"".($block ? " disabled=\"disabled\"" : "").">";
			break;
			case "enum":
				$enum = explode(",", $val);
				$enum = array_map("trim", $enum);
				$retType = "<select id=\"".$name."\" data-select=\"true\" name=\"".$name."\" class=\"form-control\"".($block ? " disabled=\"disabled\"" : "")."><option value=\"\">{L_\"Выберите\"}&nbsp;{L_".$name."}</option>";
				for($i=0;$i<sizeof($enum);$i++) {
					$retType .= "<option value=\"{L_\"".htmlspecialchars($enum[$i])."\"}\"".(!empty($default) && $default==$enum[$i] ? " selected=\"selected\"" : "").">{L_\"".htmlspecialchars($enum[$i])."\"}</option>";
				}
				$retType .= "</select>";
			break;
			case "array":
				$enum = array_map("trim", $val);
				$retType = "<select id=\"".$name."\" data-select=\"true\" name=\"".$name."\" class=\"form-control\"".($block ? " disabled=\"disabled\"" : "")."><option value=\"\">{L_\"Выберите\"}&nbsp;{L_".$name."}</option>";
				for($i=0;$i<sizeof($enum);$i++) {
					$retType .= "<option value=\"{L_\"".htmlspecialchars($enum[$i])."\"}\"".(!empty($default) && $default==$enum[$i] ? " selected=\"selected\"" : "").">{L_\"".htmlspecialchars($enum[$i])."\"}</option>";
				}
				$retType .= "</select>";
			break;
			case "varchar":
				$retType = "<input id=\"".$name."\" class=\"form-control\" type=\"text\" name=\"".$name."\" placeholder=\"{L_\"Введите\"}&nbsp;{L_".$name."}\" value=\"".htmlspecialchars($val)."\"".($block ? " disabled=\"disabled\"" : "").">";
			break;
			case "file":
				$retType = "<input id=\"".$name."\" class=\"form-control\" type=\"file\" name=\"".$name."\" placeholder=\"{L_\"Выберите\"}&nbsp;{L_".$name."}\"".($block ? " disabled=\"disabled\"" : "").">".(!empty($val) ? "&nbsp;&nbsp;<a href=\"{C_default_http_local}".$val."\" target=\"_blank\">{L_\"просмотреть\"}</a>" : "")."<br>";
			break;
			case "image":
				$retType = (!empty($val) ? "<img src=\"{C_default_http_local}".$val."\" srcset=\"{C_default_http_local}".$val." 2x\" width=\"100%\">" : "")."<br>";
			break;
			case "fileArray":
				$enum = explode(",", $val);
				$enum = array_map("trim", $enum);
				$retType = "<span id=\"inputForFile\">";
				for($i=0;$i<sizeof($enum);$i++) {
					$retType .= "<input class=\"form-control\" type=\"file\" name=\"".$name."[".$i."]\" placeholder=\"{L_\"Выберите\"}&nbsp;{L_".$name."}\"".($block ? " disabled=\"disabled\"" : "").">".(!empty($val) ? "&nbsp;&nbsp;<a href=\"{C_default_http_local}".$enum[$i]."\" target=\"_blank\">{L_\"просмотреть\"}</a>" : "")."<br>";
				}
				$retType .= "</span><br><a href=\"javascript:addInputFile('".$name."')\">{L_\"Добавить\"}</a>";
			break;
			case "imageArray":
				$enum = explode(",", $val);
				$enum = array_map("trim", $enum);
				$retType = "";
				for($i=0;$i<sizeof($enum);$i++) {
					$retType .= "<img src=\"".(!empty($val) ? "{C_default_http_local}".$enum[$i] : "")."\" srcset=\"{C_default_http_local}".$enum[$i]." 2x\" width=\"100%\"><br>";
				}
			break;
			case "shorttext":
			case "mediumtext":
			case "text":
			case "longtext":
				$retType = "<textarea id=\"".$name."\" name=\"".$name."\" placeholder=\"{L_\"Введите\"}&nbsp;{L_".$name."}\" class=\"form-control ckeditor\" rows=\"10\"".($block ? " disabled=\"disabled\"" : "").">".htmlspecialchars($val)."</textarea>";
			break;
		}
		if(is_array($val) && !isset($val['type'])) {
			$enum = array_values($val);
			$enum = array_map("trim", $enum);
			$retType = "<select id=\"".$name."\" data-select=\"true\" name=\"".$name."\" class=\"form-control\"".($block ? " disabled=\"disabled\"" : "")."><option value=\"\">{L_\"Выберите\"}&nbsp;{L_".$name."}</option>";
			for($i=0;$i<sizeof($enum);$i++) {
				$retType .= "<option value=\"{L_\"".htmlspecialchars($enum[$i])."\"}\"".(!empty($default) && $default==$enum[$i] ? " selected=\"selected\"" : "").">{L_\"".htmlspecialchars($enum[$i])."\"}</option>";
			}
			$retType .= "</select>";
		} else if(is_array($val) && isset($val['type'])) {
			$type = $val['type'];
			unset($val['type']);
			if(isset($val['val'])) {
				$val = $val['val'];
			} else if(sizeof($val)>1) {
				$val = array_values($val);
			} else {
				$val = array_values($val);
				$val = current($val);
			}
			$retType = $this->view($type, $name, $val, $default, $block);
		}
		$ret = "<div class=\"".(!$block ? "form-group" : "row")."\"><label class=\"col-sm-3 control-label\" for=\"".$name."\">{L_".$name."}</label><div class=\"col-sm-9\">".$retType."</div></div>\n";
		return $ret;
	}
	
	function UnlimitedBladeWorks() {
		$num = func_num_args();
		if(!Validate::range($num, 1, 3)) {
			throw new Exception("Error num parameters for UnlimitedBladeWorks");
			die();
		}
		$list = func_get_args();
		$load = true;
		$template = "";
		if($num==1) {
			$objTemplate = $list[0];
			if(!is_string($objTemplate) && !is_object($objTemplate) && !is_array($objTemplate)) {
				throw new Exception("Error first parameter for UnlimitedBladeWorks");
				die();
			}
		} elseif($num==2) {
			$objTemplate = $list[0];
			if(!is_string($objTemplate) && !is_object($objTemplate) && !is_array($objTemplate)) {
				throw new Exception("Error first parameter for UnlimitedBladeWorks");
				die();
			}
			$template = $list[1];
			if(!is_bool($template) && !is_string($template)) {
				throw new Exception("Error second parameter for UnlimitedBladeWorks");
				die();
			}
			if(is_bool($template)) {
				$load = $template;
				$template = "";
			}
		} elseif($num==3) {
			$objTemplate = $list[0];
			if(!is_string($objTemplate) && !is_object($objTemplate) && !is_array($objTemplate)) {
				throw new Exception("Error first parameter for UnlimitedBladeWorks");
				die();
			}
			$template = $list[1];
			if(!is_bool($template) && !is_string($template)) {
				throw new Exception("Error second parameter for UnlimitedBladeWorks");
				die();
			}
			$load = $list[2];
			if(!is_bool($load)) {
				throw new Exception("Error third parameter for UnlimitedBladeWorks");
				die();
			}
		}
		if(defined("IS_ADMIN") && is_callable($objTemplate)) {
			$call = array();
			$call[] = $template;
			if($load === false) {
				$call[] = true;
			}
			call_user_func_array($objTemplate, $call);
		} elseif(modules::checkObject($objTemplate, "templates")) {
			if($load === false) {
				$call = array();
				$call[] = $template;
				$tpl = call_user_func_array(array($objTemplate, "completed_assign_vars"), $call);
			} else {
				$tpl = $template;
			}
			call_user_func_array(array($objTemplate, "completed"), array($tpl));
			call_user_func(array($objTemplate, "display"));
		} else {
			if($load === false) {
				$tpl = $objTemplate;
			} else {
				$tpl = templates::completed_assign_vars($objTemplate);
			}
			templates::completed($tpl);
			templates::display();
		}
	}
	
}