<?php

class KernelArcher {
	
	private $selectTable = "";
	private static $callbackFunc = array();
	private $localModel = false;
	private static $excl = array();
	public static $editModel = array();
	private $countCall = array();
	public static $sortBy = array();
	public static $orderBy = false;
	public static $disabledQuickEditor = true;
	
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
			errorHeader();
			throw new Exception("Error type kernal #1 parameter");
			die();
		}
		$request = new Request();
		if(sizeof($request->post)==0) {
			errorHeader();
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
		if(isset($model->pathForUpload)) {
			$uploads = $model->pathForUpload;
			unset($model->pathForUpload);
		} else {
			$uploads = str_Replace(ROOT_PATH, "", PATH_UPLOADS);
		}
		foreach($list as $k => $v) {
			$files = $request->files->get($k, false);
			$post = $request->post->get($k, false);
			$post = $this->rebuildData($post);
			$files = $this->rebuildData($files);
			if(!empty($files) && ($model->getAttribute($k, "type")=="imageAccess" || $model->getAttribute($k, "type")=="fileAccess" || $model->getAttribute($k, "type")=="file" || $model->getAttribute($k, "type")=="fileArray" || $model->getAttribute($k, "type")=="image" || $model->getAttribute($k, "type")=="imageArray")) {
				$type = $files;
				if((!isset($type['error']) || is_array($type['error'])) && (!isset($type['name']) || is_array($type['name']))) {
					$viewI = 1;
					$type = Files::reArrayFiles($type);
					$types = array();
					if(is_serialized($v)) {
						$v = unserialize($v);
					}
					$counter = 0;
					foreach($type as $ks => $vs) {
						$upload = $this->UploadFile($model, $ks, $selectId, $vs, (is_array($uploads) && isset($uploads[$k]) ? $uploads[$k] : $uploads), $model->getAttribute($k, "allowUpload"), $viewI);
						if(!empty($upload) || !empty($v)) {
							$types[$ks] = (!$upload ? (is_array($v) ? $v[$counter] : $v) : $upload."?".time());
							$types[$ks] = str_replace(DS, "/", $types[$ks]);
							$viewI++;
						}
						$counter++;
					}
					$type = $types;
				} else {
					$upload = $this->UploadFile($model, $k, $selectId, $type, $uploads, $model->getAttribute($k, "allowUpload"));
					$type = (!$upload ? $v : $upload."?".time());
					$type = str_replace(DS, "/", $type);
				}
				$type = str_replace(DS, "/", $type);
			} else if($model->getAttribute($k, "type")=="imageAccess" || $model->getAttribute($k, "type")=="fileAccess" || $model->getAttribute($k, "type")=="file" || $model->getAttribute($k, "type")=="fileArray" || $model->getAttribute($k, "type")=="image" || $model->getAttribute($k, "type")=="imageArray" || $model->getAttribute($k, "type")=="fileArrayAccess" || $model->getAttribute($k, "type")=="imageArrayAccess") {
				if(!empty($post)) {
					$type = $post;
				} else {
					$type = $v;
				}
				$type = str_replace(DS, "/", $type);
			} else if($model->getAttribute($k, "type")=="date") {
                $post = str_replace("/", "-", $post);
				$type = strtotime((isset($post) && !empty($post) ? $post : date("d/m/Y"))." ".date("H:i:s"));
			} else if($model->getAttribute($k, "type")=="time") {
				$type = strtotime(date("d/m/Y")." ".(isset($post) && !empty($post) ? $post : date("H:i:s")));
			} else if($model->getAttribute($k, "type")=="datetime") {
                $post[0] = str_replace("/", "-", $post[0]);
				$type = strtotime((isset($post[0]) && !empty($post[0]) ? $post[0] : date("d/m/Y"))." ".(isset($post[1]) && !empty($post[1]) ? $post[1] : date("H:i:s")));
			} else if(!is_bool($post) && $post != $v) {
				$type = $post;
			} else {
				$type = $v;
			}
			if(!is_string($type) && !is_numeric($type) && ((is_array($type) || is_object($type) ? sizeof($type)>0 : false) || (is_string($type) ? strlen($type)>0 : false))) {
				$type = serialize($type);
			}
			$type = trim($type);
			$model->{$k} = $type;
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
		$addition = "";
		if(Arr::get($_GET, "ShowPages", false)) {
			$addition .= "&ShowPages=".Arr::get($_GET, "ShowPages");
		}
		if(Arr::get($_GET, "orderBy", false)) {
			$addition .= "&orderBy=".Arr::get($_GET, "orderBy");
		}
		if(Arr::get($_GET, "orderTo", false)) {
			$addition .= "&orderTo=".Arr::get($_GET, "orderTo");
		}
		if(!empty($objTemplate)) {
			if(isset($_GET['type'])) {
				location("{C_default_http_local}".(defined("ADMINCP_DIRECTORY") ? "{D_ADMINCP_DIRECTORY}" : "admincp.php")."?pages=Archer&type=".Saves::SaveOld($_GET['type']).$addition, 3, false);
			}
			$this->UnlimitedBladeWorks($objTemplate, $template, $load);
		} else if(isset($_GET['type'])) {
			location("{C_default_http_local}".(defined("ADMINCP_DIRECTORY") ? "{D_ADMINCP_DIRECTORY}" : "admincp.php")."?pages=Archer&type=".Saves::SaveOld($_GET['type']).$addition);
		}
	}
	
	function Add($model = "", $objTemplate = "") {
		if((empty($model) && (gettype($model)!=="object" || !method_exists($model, "getArray"))) && (gettype($this->localModel)!=="object" || !method_exists($this->localModel, "getArray"))) {
			errorHeader();
			throw new Exception("Error type kernal #1 parameter");
			die();
		}
		$listOld = get_object_vars($model);
		$model->loadTable($this->selectTable);
		$listNew = get_object_vars($model);
		foreach($listNew as $k => $v) {
			if(!array_key_exists($k, $listOld)) {
				unset($model->{$k});
			}
		}
		$exc = array();
		$model = $this->callArr($model, "AddModel", array($model, &$exc));
		$exc = array_values($exc);
		for($i=0;$i<sizeof($exc);$i++) {
			if(isset($model->{$exc[$i]})) {
				unset($model->{$exc[$i]});
			}
		}
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
		$file = $this->callArr($file, "TakeUpload", func_get_args(), array(), false);
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
			errorHeader();
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
		if(isset($file['type'])) {
			$typeFile = $file['type'];
			$typeFile = explode("/", $typeFile);
			$typeFile = end($typeFile);
		} else {
			$typeFile = "";
		}
		$fileName = uniqid().$fileName;
		$path = ROOT_PATH.$path;
		Files::$switchException = true;
		//Files::$simulate = true;
		if(is_array($file)) {
			return Files::saveFile($file, $fileName.".".$typeFile, $path);
		} else {
			return false;
		}
	}
	
	function TakeEdit($model = "", $objTemplate = "", $template = "", $load = true) {
		if((empty($model) && (gettype($model)!=="object" || !method_exists($model, "getArray"))) && (gettype($this->localModel)!=="object" || !method_exists($this->localModel, "getArray"))) {
			errorHeader();
			throw new Exception("Error type kernal #1 parameter");
			die();
		}
		$request = new Request();
		if(sizeof($request->post)==0) {
			errorHeader();
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
		if(isset($model->pathForUpload)) {
			$uploads = $model->pathForUpload;
			unset($model->pathForUpload);
		} else {
			$uploads = "uploads".DS;
		}
		$delArray = $request->post->get("deleteArray", array());
		$delArray = array_map(function($v) { return explode(",", $v); }, ($delArray));
		foreach($list as $k => $v) {
			$files = $request->files->get($k, false);
			$post = $request->post->get($k, false);
			
			$post = $this->rebuildData($post);
			$files = $this->rebuildData($files);
			if(!empty($files) && ($models->getAttribute($k, "type")=="imageAccess" || $models->getAttribute($k, "type")=="fileAccess" || $models->getAttribute($k, "type")=="file" || $models->getAttribute($k, "type")=="fileArray" || $models->getAttribute($k, "type")=="image" || $models->getAttribute($k, "type")=="imageArray")) {
				$type = $files;
				if((!isset($type['error']) || is_array($type['error'])) && (!isset($type['name']) || is_array($type['name']))) {
					$viewI = 1;
					$type = Files::reArrayFiles($type);
					if(is_serialized($v)) {
						$v = unserialize($v);
					}
					$counter = 0;
					$types = array();
					/////////
					///
					if(isset($delArray[$k])) {
						for($countFilesArray=0;$countFilesArray<sizeof($delArray[$k]);$countFilesArray++) {
							if(!empty($delArray[$k][$countFilesArray]) && isset($v[$delArray[$k][$countFilesArray]])) {
								unset($v[$delArray[$k][$countFilesArray]]);
							}
						}
						$v = array_values($v);
					}
					///
					/////////
					foreach($type as $ks => $vs) {
						$upload = $this->UploadFile($models, $ks, $selectId, $vs, (is_array($uploads) && isset($uploads[$k]) ? $uploads[$k] : $uploads), $models->getAttribute($k, "allowUpload"), $viewI);
						if(!empty($upload) || !empty($v)) {
							$types[$ks] = (!$upload ? (is_array($v) && isset($v[$counter]) ? $v[$counter] : $v) : $upload."?".time());
							$types[$ks] = str_replace(DS, "/", $types[$ks]);
							$viewI++;
						}
						$counter++;
					}
					$type = $types;
				} else {
					$upload = $this->UploadFile($models, $k, $selectId, $type, $uploads, $models->getAttribute($k, "allowUpload"));
					$type = (!$upload ? $v : $upload."?".time());
					$type = str_replace(DS, "/", $type);
				}
			} else if($model->getAttribute($k, "type")=="imageAccess" || $model->getAttribute($k, "type")=="fileAccess" || $model->getAttribute($k, "type")=="file" || $model->getAttribute($k, "type")=="fileArray" || $model->getAttribute($k, "type")=="image" || $model->getAttribute($k, "type")=="imageArray" || $model->getAttribute($k, "type")=="fileArrayAccess" || $model->getAttribute($k, "type")=="imageArrayAccess") {
				if(!empty($post)) {
					$type = $post;
				} else {
					$type = $v;
				}
				$type = str_replace(DS, "/", $type);
			} else if($models->getAttribute($k, "type")=="date") {
                $post = str_replace("/", "-", $post);
				$type = strtotime((isset($post) && !empty($post) ? $post : date("d/m/Y"))." ".date("H:i:s"));
			} else if($models->getAttribute($k, "type")=="time") {
				$type = strtotime(date("d/m/Y")." ".(isset($post) && !empty($post) ? $post : date("H:i:s")));
			} else if($models->getAttribute($k, "type")=="datetime") {
                $post[0] = str_replace("/", "-", $post[0]);
				$type = strtotime((isset($post[0]) && !empty($post[0]) ? $post[0] : date("d/m/Y"))." ".(isset($post[1]) && !empty($post[1]) ? $post[1] : date("H:i:s")));
			} else if(!is_bool($post) && $post != $v) {
				$type = $post;
			} else {
				$type = $v;
			}
			if(!is_string($type) && !is_numeric($type)) {
				$type = serialize($type);
			}
			$type = trim($type);
			$model->{$k} = $type;
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
		$addition = "";
		if(Arr::get($_GET, "ShowPages", false)) {
			$addition .= "&ShowPages=".Arr::get($_GET, "ShowPages");
		}
		if(Arr::get($_GET, "orderBy", false)) {
			$addition .= "&orderBy=".Arr::get($_GET, "orderBy");
		}
		if(Arr::get($_GET, "orderTo", false)) {
			$addition .= "&orderTo=".Arr::get($_GET, "orderTo");
		}
		$ref = Arr::get($_GET, "ref", false);
		if($ref===false) {
			$ref = "{C_default_http_local}".(defined("ADMINCP_DIRECTORY") ? "{D_ADMINCP_DIRECTORY}" : "admincp.php")."?pages=Archer&type=".Saves::SaveOld($_GET['type']).$addition;
		}
		if(!empty($objTemplate)) {
			if(isset($_GET['type'])) {
				location($ref, 3, false);
			}
			$this->UnlimitedBladeWorks($objTemplate, $template, $load);
		} else if(isset($_GET['type'])) {
			location($ref);
		}
	}
	
	function Edit($model = "", $objTemplate = "") {
		if((empty($model) && (gettype($model)!=="object" || !method_exists($model, "getArray"))) && (gettype($this->localModel)!=="object" || !method_exists($this->localModel, "getArray"))) {
			errorHeader();
			throw new Exception("Error type kernal #1 parameter");
			die();
		}
		$model = $model->Select();
		$model->SetTable($this->selectTable);
		$exc = array();
		$model = $this->callArr($model, "EditModel", array($model, &$exc), array(), true);
		$exc = array_values($exc);
		for($i=0;$i<sizeof($exc);$i++) {
			if(isset($model->{$exc[$i]})) {
				unset($model->{$exc[$i]});
			}
		}
		$list = $model->getArray();
		foreach($list as $k => $v) {
			if($model->getAttribute($k, "type")=="fileArray" || $model->getAttribute($k, "type")=="imageArray" || $model->getAttribute($k, "type")=="fileArrayAccess" || $model->getAttribute($k, "type")=="imageArrayAccess") {
				$t = unserialize($v);
				if(isset($t[0]) && is_array($t[0])) {
					$t = array();
				}
				$model->{$k} = $list[$k] = implode(",", $t);
			}
		}
		$list = $model->getArray();
		$firstId = current($list);
		if(empty($firstId)) {
			errorHeader();
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
			errorHeader();
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
			errorHeader();
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
		if(is_array($del) && isset($del['model'])) {
			$model = $del['model'];
		}
		if(is_array($del) && isset($del['models'])) {
			$models = $del['models'];
		}
		/*if(is_object($del)) {
			$models = $del;
		}*/
		$models = $models->getArray();
		$trash = false;
		if(defined("PATH_CACHE_USERDATA")) {
			if(!is_writeable(PATH_CACHE_USERDATA)) {
				@chmod(PATH_CACHE_USERDATA, 0777);
			}
			if(!file_exists(PATH_CACHE_USERDATA."trashBin.lock")) {
				db::query("CREATE TABLE IF NOT EXISTS {{trashBin}} ( `tId` int not null auto_increment, `tTable` varchar(255) not null, `tData` longtext not null, `tTime` int(11) not null, `tIp` varchar(255) not null, primary key `id`(`tId`) ) ENGINE=MyISAM;");
				file_put_contents(PATH_CACHE_USERDATA."trashBin.lock", "");
			}
			$trash = true;
		}
		$first = current($models);
		$days = 30;
		if(defined("EMPTY_TRASH_DAYS")) {
			if(is_numeric(EMPTY_TRASH_DAYS) && EMPTY_TRASH_DAYS>0) {
				$days = EMPTY_TRASH_DAYS;
			} else if(is_bool(EMPTY_TRASH_DAYS) && EMPTY_TRASH_DAYS===false) {
				$days = 0;
			}
		}
		if($days==0 || !$trash) {
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
				if($type=="image" || $type=="file") {
					$val = str_replace("/", DS, $val);
					$exp = explode("?", $val);
					if((is_array($exp) && isset($exp[0]) && file_exists(ROOT_PATH.$exp[0])) || (file_exists(ROOT_PATH.$val))) {
						unlink(ROOT_PATH.(is_array($exp) && isset($exp[0]) ? $exp[0] : $val));
					}
				} else if($type=="imageArray" || $type=="fileArray" || $type=="fileArrayAccess" || $type=="imageArrayAccess") {
					$exp = explode(",", $val);
					for($i=0;$i<sizeof($exp);$i++) {
						$exp[$i] = str_replace("/", DS, $exp[$i]);
						$exps = explode("?", $exp[$i]);
						if((is_array($exps) && isset($exps[0]) && file_exists(ROOT_PATH.$exps[0])) || (file_exists(ROOT_PATH.$exp[$i]))) {
							unlink(ROOT_PATH.(is_array($exps) && isset($exps[0]) ? $exps[0] : $exp[$i]));
						}
					}
				}
			}
			cardinal::RegAction("Удаление данных в Арчере. Модель \"".$modelName."\". ИД: \"".$first."\"");
		} else {
			db::doquery("INSERT INTO {{trashBin}} SET `tTable` = ".db::escape($this->selectTable).", `tData` = ".db::escape(json_encode($models)).", `tTime`= UNIX_TIMESTAMP(), `tIp` = '".HTTP::getip()."'");
			cardinal::RegAction("Перемещение данных в Арчере в корзину. Модель \"".$modelName."\". ИД: \"".$first."\"");
		}
		$list = $model->Deletes();
		$addition = "";
		if(Arr::get($_GET, "ShowPages", false)) {
			$addition .= "&ShowPages=".Arr::get($_GET, "ShowPages");
		}
		if(!empty($objTemplate)) {
			if(isset($_GET['type'])) {
				location("{C_default_http_local}".(defined("ADMINCP_DIRECTORY") ? "{D_ADMINCP_DIRECTORY}" : "admincp.php")."?pages=Archer&type=".Saves::SaveOld($_GET['type']).$addition, 3, false);
			}
			$this->UnlimitedBladeWorks($objTemplate, $template, $load);
		} else if(isset($_GET['type'])) {
			location("{C_default_http_local}".(defined("ADMINCP_DIRECTORY") ? "{D_ADMINCP_DIRECTORY}" : "admincp.php")."?pages=Archer&type=".Saves::SaveOld($_GET['type']).$addition);
		}
	}
	
	function Show($model = "", $objTemplate = "", $template = "") {
		if((empty($model) && (gettype($model)!=="object" || !method_exists($model, "getArray"))) && (gettype($this->localModel)!=="object" || !method_exists($this->localModel, "getArray"))) {
			errorHeader();
			throw new Exception("Error type kernal #1 parameter");
			die();
		}
		$model->SetTable($this->selectTable);
		$lists = $model->Select();
		$list = $lists->getArray();
		$firstId = current($list);
		if(empty($firstId)) {
			errorHeader();
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
			errorHeader();
			throw new Exception("Error type kernal #1 parameter");
			die();
		}
		$objName = get_class($model);
		$model->SetTable($this->selectTable);
		$model->multiple(true);
		$list = $model->Select();
		if(is_object($list)) {
			$list = $list->getArray();
			$list = $this->callArr($list, "ShieldFunc", array($list));
			$this->AddBlocks("Mains", $list, $objName, $objName."-".current($list));
		} elseif(is_array($list)) {
			for($i=0;$i<sizeof($list);$i++) {
				$subList = $list[$i]->getArray();
				$subList = $this->callArr($subList, "ShieldFunc", array($subList), array());
				$this->AddBlocks("Mains", $subList, $objName, $objName."-".current($subList));
			}
		}
		if(!empty($objTemplate)) {
			$this->UnlimitedBladeWorks($objTemplate, $template, $load);
		}
	}
	
	private function callArr($return, $page, $func, $params = array(), $single = true) {
		if(!isset($this->countCall[$page]) || !is_numeric($this->countCall[$page])) {
			$this->countCall[$page] = 0;
		}
		$this->countCall[$page]++;
		if(is_array($params) && array_key_exists("countCall", $params) && is_string($func)) {
			$params = array_merge($params, array("countCall" => $this->countCall[$page]));
		} elseif(is_array($func) && array_key_exists("countCall", $func)) {
			$func = array_merge($func, array("countCall" => $this->countCall[$page]));
		}
		if(isset(self::$callbackFunc[$page]) && is_string($func) && isset(self::$callbackFunc[$page][$func])) {
			$return = $params;
			for($i=0;$i<sizeof(self::$callbackFunc[$page][$func]);$i++) {
				$return = call_user_func_array(self::$callbackFunc[$page][$func][$i], $return);
			}
		} else if(isset(self::$callbackFunc[$page]) && is_array($func)) {
			$call = $func;
			for($i=0;$i<sizeof(self::$callbackFunc[$page]);$i++) {
				$call1 = call_user_func_array(self::$callbackFunc[$page][$i], $call);
				if(is_array($call1)) {
					$call = $call1;
				} else {
					trigger_error(var_export(self::$callbackFunc[$page][$i], true)." return is not array - ignore");
				}
			}
			if($single) {
				$return = current($call);
			} else {
				$return = $call;
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
			errorHeader();
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
	
	public static function Viewing($type, $name, $val, $default = "", $block = false, $isAjax = false) {
		$open = defined("ADMINCP_DIRECTORY");
		$retType = "";
		$hide = false;
		$type = execEvent("KernelArcher::Viewing", $type);
		switch($type) {
			case "tinyint":
			case "smallint":
			case "mediumint":
			case "int":
			case "bigint":
				$retType = "<input id=\"".$name."\" class=\"form-control\" type=\"number\" name=\"".$name."\" placeholder=\"".($open ? "{L_'" : "")."Введите".($open ? "'}" : "")."&nbsp;".($open ? "{L_'" : "").$name.($open ? "'}" : "")."\" value=\"".htmlspecialchars($val)."\"".($block ? " disabled=\"disabled\"" : "").">";
			break;
			case "float":
			case "double":
			case "decimal":
			case "real":
				$retType = "<input id=\"".$name."\" class=\"form-control\" type=\"number\" step=\"0.01\" name=\"".$name."\" placeholder=\"".($open ? "{L_'" : "")."Введите".($open ? "'}" : "")."&nbsp;".($open ? "{L_'" : "").$name.($open ? "'}" : "")."\" value=\"".htmlspecialchars($val)."\"".($block ? " disabled=\"disabled\"" : "").">";
			break;
			case "enum":
				$enum = explode(",", $val);
				$enum = array_map("trim", $enum);
				$retType = "<select id=\"".$name."\" data-select=\"true\" name=\"".$name."\" class=\"form-control\"".($block ? " disabled=\"disabled\"" : "").">".(!defined("WITHOUT_NULL") ? "<option value=\"\">".($open ? "{L_'" : "")."Выберите".($open ? "'}" : "")."&nbsp;".($open ? "{L_'" : "").$name.($open ? "'}" : "")."</option>" : "");
				for($i=0;$i<sizeof($enum);$i++) {
					$retType .= "<option value=\"".htmlspecialchars($enum[$i])."\"".(!empty($default) && $default==$enum[$i] ? " selected=\"selected\"" : "").">".($open ? "{L_'" : "").htmlspecialchars($enum[$i]).($open ? "'}" : "")."</option>";
				}
				$retType .= "</select>";
			break;
			case "array":
				$enum = array_map("trim", $val);
				$retType = "<select id=\"".$name."\" data-select=\"true\" name=\"".$name."\" class=\"form-control\"".($block ? " disabled=\"disabled\"" : "").">".(!defined("WITHOUT_NULL") ? "<option value=\"\">".($open ? "{L_'" : "")."Выберите".($open ? "'}" : "")."&nbsp;".($open ? "{L_'" : "").$name.($open ? "'}" : "")."</option>" : "");
				for($i=0;$i<sizeof($enum);$i++) {
					$type = "o";
					if(is_array($enum[$i])) {
						if(isset($enum[$i]['type']) && $enum[$i]['type']=="opt") {
							$type = "opt";
						}
						if(isset($enum[$i]['name'])) {
							$enum[$i] = $enum[$i]['name'];
						} else {
							$enum[$i] = end($enum[$i]);
						}
					}
					$enum[$i] = trim($enum[$i]);
					if($type=="opt") {
						$retType .= "<option class='bold' value=\"".htmlspecialchars($enum[$i])."\"".(!empty($default) && $default==$enum[$i] ? " selected=\"selected\"" : "").">".($open ? "{L_'" : "").htmlspecialchars($enum[$i])."".($open ? "'}" : "")."</option>\n";
					} else {
						$retType .= "<option value=\"".htmlspecialchars($enum[$i])."\"".(!empty($default) && $default==$enum[$i] ? " selected=\"selected\"" : "").">".($open ? "{L_'" : "").htmlspecialchars($enum[$i])."".($open ? "'}" : "")."</option>\n";
					}
				}
				$retType .= "</select>";
			break;
			case "varchar":
				$retType = "<input id=\"".$name."\" class=\"form-control\" type=\"text\" name=\"".$name."\" placeholder=\"".($open ? "{L_'" : "")."Введите".($open ? "'}" : "")."&nbsp;".($open ? "{L_'" : "").$name.($open ? "'}" : "")."\" value=\"".htmlspecialchars($val)."\"".($block ? " disabled=\"disabled\"" : "").">";
			break;
			case "price":
				$retType = "<div class=\"input-group\"><span class=\"input-group-addon\">$</span><input id=\"".$name."\" type=\"number\" step=\"0.01\" class=\"form-control\" name=\"".$name."\" placeholder=\"".($open ? "{L_'" : "")."Введите".($open ? "'}" : "")."&nbsp;".($open ? "{L_'" : "").$name.($open ? "'}" : "")."\" value=\"".htmlspecialchars($val)."\"".($block ? " disabled=\"disabled\"" : "")."></div>";
			break;
			case "image":
			case "file":
				if(strpos($val, "http")===false) {
					$vals = "{C_default_http_local}".$val;
				} else {
					$vals = $val;
				}
				$retType = "<input id=\"".$name."\" class=\"form-control\" type=\"file\" name=\"".$name."\" placeholder=\"".($open ? "{L_'" : "")."Выберите".($open ? "'}" : "")."&nbsp;".($open ? "{L_'" : "").$name.($open ? "'}" : "")."\"".($block ? " disabled=\"disabled\"" : "").($type=="image" ? " accept=\"image/*\"" : "").">".(!empty($val) ? "&nbsp;&nbsp;<a href=\"".$vals."\"".($type=="image" ? " class=\"showPreview\"" : "")." target=\"_blank\">".($open ? "{L_'" : "")."Просмотреть".($open ? "'}" : "")."</a>" : "")."<br>";
			break;
			case "imageAccess":
			case "fileAccess":
				if(strpos($val, "http")===false) {
					$vals = "{C_default_http_local}".$val;
				} else {
					$vals = $val;
				}
				$parent = uniqid();
				$datas = '<div class="row"><div class="col-sm-10"><input class="form-control imageAccess" id="'.$name.'" name="'.$name.'" type="text" value="'.$val.'" placeholder="'.($open ? "{L_'" : "")."Выберите".($open ? "'}" : "")."&nbsp;".($open ? "{L_'" : "").$name.($open ? "'}" : "").'"'.($block ? " disabled=\"disabled\"" : "").($type=="imageAccess" ? " data-accept=\"image\"" : "").' value="'.htmlspecialchars($val).'"><br>'.(!empty($val) ? '&nbsp;&nbsp;<a href="'.$vals.'"'.($type=="imageAccess" ? " class=\"showPreview\"" : "").' target="_blank">'.($open ? "{L_'" : "")."Просмотреть".($open ? "'}" : "")."</a>" : "").'</div><div class="col-sm-1"><a href="{C_default_http_host}{D_ADMINCP_DIRECTORY}/assets/tinymce/filemanager/dialog.php?type='.($type=="imageAccess" ? "1" : "2").'&field_id='.$name.'&relative_url=0" class="btn btn-icon btn-success iframe-btn"><i class="fa-plus"></i></a></div><div class="col-sm-1"><a href="#" class="btn btn-icon btn-red accessRemove" data-parent="'.$parent.'"><i class="fa-remove"></i></a></div></div>';
				$container = "<div class=\"containerFiles container-".$type."\" data-parent=\"".$parent."\">".$datas."</div>";
				$retType = $container;
			break;
			case "imageArray":
			case "fileArray":
				$retType = '<input type="hidden" name="deleteArray['.$name.']">';
				$enum = explode(",", $val);
				$enum = array_map("trim", $enum);
				$retType .= "<div id=\"inputForFile\" class=\"row\" data-accept=\"".($type=="imageArray" ? "image/*" : "")."\">";
				for($i=0;$i<sizeof($enum);$i++) {
					$retType .= "<div class='array'>".(sizeof($enum)>1 ? "<div class='col-sm-1'>#".($i+1)."</div><div class='col-sm-9'>" : "<div class='col-sm-10'>")."<input class=\"form-control\" type=\"file\"".(sizeof($enum)==1 ? " multiple=\"multiple\"" : "")." name=\"".$name.(sizeof($enum)>1 ? "[".$i."]" : "[]")."\" placeholder=\"".($open ? "{L_'" : "")."Выберите".($open ? "'}" : "")."&nbsp;".($open ? "{L_'" : "").$name.($open ? "'}" : "")."\"".($block ? " disabled=\"disabled\"" : "").($type=="imageArray" ? " accept=\"image/*\"" : "")."></div><div class='col-sm-2'><a class='btn btn-red btn-block fa-remove' onclick='removeInputFile(this,\"".$name."\",\"".$i."\")'></a></div>".(!empty($val) ? "<div class='col-sm-12'><a href=\"{C_default_http_local}".$enum[$i]."\" class=\"showPreview\" target=\"_blank\">".($open ? "{L_'" : "")."Просмотреть".($open ? "'}" : "")."</a></div>" : "")."</div>";
				}
				$retType .= "</div><br><a href=\"#\" onclick=\"addInputFile(this, '".$name."');return false;\" class=\"btn btn-white btn-block btn-icon btn-icon-standalone\"><i class=\"fa-upload\"></i><span>".($open ? "{L_'" : "")."Добавить".($open ? "'}" : "")."</span></a>";
			break;
			case "imageArrayAccess":
			case "fileArrayAccess":
				$retType = '<input type="hidden" name="deleteArray['.$name.']">';
				$enum = explode(",", $val);
				$enum = array_map("trim", $enum);
				$retType .= "<div id=\"inputForFile\" class=\"row\" data-accept=\"".($type=="imageArrayAccess" ? "image/*" : "")."\">";
				for($i=0;$i<sizeof($enum);$i++) {
					$uid = rand();
					$retType .= "<div class='array'>".(sizeof($enum)>1 ? "<div class='col-sm-1'>#".($i+1)."</div><div class='col-sm-8'>" : "<div class='col-sm-9'>")."<input class=\"form-control\" id=\"".$uid."\" type=\"text\"".(sizeof($enum)==1 ? " multiple=\"multiple\"" : "")."".($block ? " disabled=\"disabled\"" : "")." name=\"".$name.(sizeof($enum)>1 ? "[".$i."]" : "[]")."\" placeholder=\"".($open ? "{L_'" : "")."Выберите".($open ? "'}" : "")."&nbsp;".($open ? "{L_'" : "").$name.($open ? "'}" : "")."\"".($block ? " disabled=\"disabled\"" : "").($type=="imageArrayAccess" ? " accept=\"image/*\" data-accept=\"image\"" : "")." value=\"".htmlspecialchars($enum[$i])."\"></div>".'<div class="col-sm-1"><a href="{C_default_http_host}{D_ADMINCP_DIRECTORY}/assets/xenon/js/tinymce/filemanager/dialog.php?type='.($type=="imageAccess" ? "1" : "2").'&field_id='.$uid.'&relative_url=0" class="btn btn-icon btn-success iframe-btn"><i class="fa-plus"></i></a></div>'."<div class='col-sm-2'><a class='btn btn-red btn-block fa-remove' onclick='removeInputFile(this,\"".$name."\",\"".$i."\")'></a></div>".(!empty($val) ? "<div class='col-sm-12'><a href=\"{C_default_http_local}".$enum[$i]."\" class=\"showPreview\" id=\"img".$uid."\" target=\"_blank\">".($open ? "{L_'" : "")."Просмотреть".($open ? "'}" : "")."</a></div>" : "")."</div>";
				}
				$retType .= "</div><br><a href=\"#\" onclick=\"addInputFile(this, '".$name."');return false;\" class=\"btn btn-white btn-block btn-icon btn-icon-standalone\"><i class=\"fa-upload\"></i><span>".($open ? "{L_'" : "")."Добавить".($open ? "'}" : "")."</span></a>";
			break;
			case "shorttext":
			case "mediumtext":
			case "text":
			case "longtext":
				$retType = "<textarea id=\"".$name."\" name=\"".$name."\" placeholder=\"".($open ? "{L_'" : "")."Введите".($open ? "'}" : "")."&nbsp;".($open ? "{L_'" : "").$name.($open ? "'}" : "")."\" class=\"form-control ckeditor\" rows=\"10\"".($block ? " disabled=\"disabled\"" : "").">".htmlspecialchars($val)."</textarea>";
			break;
			case "onlytextareatext":
				$retType = "<textarea class=\"onlyText form-control\" id=\"".$name."\" name=\"".$name."\" placeholder=\"".($open ? "{L_'" : "")."Введите".($open ? "'}" : "")."&nbsp;".($open ? "{L_'" : "").$name.($open ? "'}" : "")."\" rows=\"10\"".($block ? " disabled=\"disabled\"" : "").">".htmlspecialchars($val)."</textarea>";
			break;
			case "email":
				$retType = "<div class=\"input-group\"><span class=\"input-group-addon\">@</span><input id=\"".$name."\" class=\"form-control\" type=\"email\" name=\"".$name."\" placeholder=\"".($open ? "{L_'" : "")."Введите".($open ? "'}" : "")."&nbsp;".($open ? "{L_'" : "").$name.($open ? "'}" : "")."\" value=\"".htmlspecialchars($val)."\"".($block ? " disabled=\"disabled\"" : "")."></div>";
			break;
			case "link":
				$retType = "<div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"fa fa-link\"></i></span><input id=\"".$name."\" class=\"form-control\" type=\"text\" name=\"".$name."\" placeholder=\"".($open ? "{L_'" : "")."Введите".($open ? "'}" : "")."&nbsp;".($open ? "{L_'" : "").$name.($open ? "'}" : "")."\" value=\"".htmlspecialchars($val)."\"".($block ? " disabled=\"disabled\"" : "")."></div>";
			break;
			case "password":
				$retType = "<input id=\"".$name."\" class=\"form-control\" type=\"password\" name=\"".$name."\" placeholder=\"".($open ? "{L_'" : "")."Введите".($open ? "'}" : "")."&nbsp;".($open ? "{L_'" : "").$name.($open ? "'}" : "")."\" value=\"".htmlspecialchars($val)."\"".($block ? " disabled=\"disabled\"" : "").">";
			break;
			case "hidden":
			case "hide":
				$hide = true;
				$retType = "<input id=\"".$name."\" type=\"hidden\" name=\"".$name."\" value=\"".htmlspecialchars($val)."\"".($block ? " disabled=\"disabled\"" : "").">";
			break;
			case "date":
				$retType = "<div class=\"date-and-time\"><input id=\"".$name."\" type=\"text\" class=\"form-control datepicker\" name=\"".$name."\" placeholder=\"".($open ? "{L_'" : "")."Введите".($open ? "'}" : "")."&nbsp;".($open ? "{L_'" : "").$name.($open ? "'}" : "")."\" value=\"".($val!=="" && $val!==0 ? date("d/m/Y", $val) : date("d/m/Y"))."\"".($block ? " disabled=\"disabled\"" : "")." data-format=\"dd/mm/yyyy\"></div>";
			break;
			case "time":
				$retType = "<div class=\"date-and-time\"><input id=\"".$name."\" type=\"text\" class=\"form-control timepicker\" name=\"".$name."\" placeholder=\"".($open ? "{L_'" : "")."Введите".($open ? "'}" : "")."&nbsp;".($open ? "{L_'" : "").$name.($open ? "'}" : "")."\" value=\"".($val!=="" && $val!==0 ? date("H:i:s", $val) : date("H:i:s"))."\"".($block ? " disabled=\"disabled\"" : "")." data-template=\"dropdown\" data-show-seconds=\"true\" data-default-time=\"".($val!=="" && $val!==0 ? date("H:i:s", $val) : date("H:i:s"))."\" data-show-meridian=\"false\" data-minute-step=\"5\" data-second-step=\"5\"></div>";
			break;
			case "datetime":
				$retType = "<div class=\"col-sm-12\"><div class=\"date-and-time\"><input type=\"text\"".($block ? " disabled=\"disabled\"" : "")." name=\"".$name."[]\" class=\"form-control datepicker\" data-format=\"dd/mm/yyyy\" value=\"".($val!=="" && $val!==0 ? date("d/m/Y", $val) : date("d/m/Y"))."\"><input type=\"text\"".($block ? " disabled=\"disabled\"" : "")." name=\"".$name."[]\" class=\"form-control timepicker\" data-template=\"dropdown\" data-show-seconds=\"true\" value=\"".($val!=="" && $val!==0 ? date("H:i:s", $val) : date("H:i:s"))."\" data-default-time=\"".($val!=="" && $val!==0 ? date("H:i:s", $val) : date("H:i:s"))."\" data-show-meridian=\"false\" data-minute-step=\"5\" data-second-step=\"5\" /></div></div>";
			break;
		}
		if(is_array($val) && !isset($val['type'])) {
			$enum = array_values($val);
			//$enum = array_map("trim", $enum);
			$retType = "<select id=\"".$name."\" data-select=\"true\" name=\"".$name."\" class=\"form-control\"".($block ? " disabled=\"disabled\"" : "")."><option value=\"\">".($open ? "{L_'" : "")."Выберите".($open ? "'}" : "")."&nbsp;".($open ? "{L_'" : "").$name.($open ? "'}" : "")."</option>";
			for($i=0;$i<sizeof($enum);$i++) {
				$type = "o";
				if(is_array($enum[$i])) {
					if(isset($enum[$i]['type']) && $enum[$i]['type']=="opt") {
						$type = "opt";
					}
					if(isset($enum[$i]['name'])) {
						$enum[$i] = $enum[$i]['name'];
					} else {
						$enum[$i] = end($enum[$i]);
					}
				}
				$enum[$i] = trim($enum[$i]);
				if($type=="opt") {
					$retType .= "<option class='bold' value=\"".htmlspecialchars($enum[$i])."\"".(!empty($default) && $default==$enum[$i] ? " selected=\"selected\"" : "").">".($open ? "{L_'" : "").htmlspecialchars($enum[$i])."".($open ? "'}" : "")."</option>\n";
				} else {
					$retType .= "<option value=\"".htmlspecialchars($enum[$i])."\"".(!empty($default) && $default==$enum[$i] ? " selected=\"selected\"" : "").">".($open ? "{L_'" : "").htmlspecialchars($enum[$i])."".($open ? "'}" : "")."</option>\n";
				}
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
		$ret = (!$hide ? "<div class=\"".(!$block ? "form-group" : "row")." block-".$name."\"><label class=\"col-sm-".($isAjax ? "2" : "3")." control-label\" for=\"".$name."\">{L_".$name."}</label><div class=\"col-sm-".($isAjax ? "10" : "9")."\">" : "").$retType.(!$hide ? "</div></div>\n" : "");
		return $ret;
	}
	
	function UnlimitedBladeWorks() {
		$num = func_num_args();
		if(!Validate::range($num, 1, 3)) {
			errorHeader();
			throw new Exception("Error num parameters for UnlimitedBladeWorks");
			die();
		}
		$list = func_get_args();
		$load = true;
		$template = "";
		if($num==1) {
			$objTemplate = $list[0];
			if(!is_string($objTemplate) && !is_object($objTemplate) && !is_array($objTemplate)) {
				errorHeader();
				throw new Exception("Error first parameter for UnlimitedBladeWorks");
				die();
			}
		} elseif($num==2) {
			$objTemplate = $list[0];
			if(!is_string($objTemplate) && !is_object($objTemplate) && !is_array($objTemplate)) {
				errorHeader();
				throw new Exception("Error first parameter for UnlimitedBladeWorks");
				die();
			}
			$template = $list[1];
			if(!is_bool($template) && !is_string($template)) {
				errorHeader();
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
				errorHeader();
				throw new Exception("Error first parameter for UnlimitedBladeWorks");
				die();
			}
			$template = $list[1];
			if(!is_bool($template) && !is_string($template)) {
				errorHeader();
				throw new Exception("Error second parameter for UnlimitedBladeWorks");
				die();
			}
			$load = $list[2];
			if(!is_bool($load)) {
				errorHeader();
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