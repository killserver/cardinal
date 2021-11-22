<?php

class ProfileSettings extends Core {

	private function getUserFields($d) {
		$data = array(
			"mode" => "add",
			"data" => array(
				"username" => array(
					"beforeAltName" => "username",
					"depth" => "0",
					"parent_id" => "0",
					"name" => "Логин",
					"altName" => "username",
					"type" => "varchar",
					"default" => "",
					"placeholder" => "",
					"alttitle" => "username",
					"translate" => "1",
					"id" => 0,
					"onlyAttr" => "onlyAttr",
					"applyInLogin" => "applyInLogin",
				),
				"pass" => array(
					"beforeAltName" => "pass",
					"depth" => "0",
					"parent_id" => "0",
					"name" => "Пароль",
					"altName" => "pass",
					"type" => "password",
					"default" => "",
					"placeholder" => "",
					"alttitle" => "pass",
					"translate" => "1",
					"id" => 0,
					"onlyAttr" => "onlyAttr",
					"hideOnMain" => "yes",
					"applyInLogin" => "applyInLogin",
				),
				"level" => array(
					"beforeAltName" => "level",
					"depth" => "0",
					"parent_id" => "0",
					"name" => "Уровнь доступа",
					"altName" => "level",
					"type" => "array",
					"selectedData" => "dataOnInput",
					"field" => $d,
					"default" => "",
					"placeholder" => "",
					"alttitle" => "level",
					"translate" => "1",
					"id" => 0,
					"onlyAttr" => "onlyAttr",
					"disApplyInReg" => "disApplyInReg",
				),
				"email" => array(
					"beforeAltName" => "email",
					"depth" => "0",
					"parent_id" => "0",
					"name" => "Почта",
					"altName" => "email",
					"type" => "email",
					"default" => "",
					"placeholder" => "",
					"alttitle" => "email",
					"translate" => "1",
					"id" => 0,
					"onlyAttr" => "onlyAttr",
				),
			),
		);
		if(file_exists(PROFILE_SETTINGS) && is_readable(PROFILE_SETTINGS)) {
			$f = file_get_contents(PROFILE_SETTINGS);
			$f = str_replace('<?php die(); ?>', "", $f);
			$f = json_decode($f, true);
			foreach($f['data'] as $k => &$v) {
                if(isset($data['data'][$v["altName"]])) {
                    $v = array_merge($data['data'][$v["altName"]], $v);
                }
            }
		} else {
            $data['data'] = array_Values($data['data']);
			$f = $data;
		}
		return $f;
	}
	
	private function UploadFile($model, $key, $id, $file, $path, $type = "", $i = -1) {
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
		$key = isset($file['key']) ? $file['key'] : $file[1];
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
		$fileName = isset($file['fileName']) ? $file['fileName'] : "";
		$file = isset($file['file']) ? $file['file'] : $file[3];
		if(isset($file['type'])) {
			$typeFile = $file['type'];
			$typeFile = explode("/", $typeFile);
			$typeFile = end($typeFile);
		} else {
			$typeFile = "";
		}
		if(strpos($typeFile, "+")!==false) {
			$typeFile = explode("+", $typeFile);
			$typeFile = current($typeFile);
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
	
	private function rebuildData(&$arr) {
		if(is_array($arr)) {
			$ret = array();
			foreach($arr as $k => $v) {
				if(empty($v)) {
					$ret[$k] = "";
				} else {
					$ret[$k] = $v;
				}
			}
			$arr = $ret;
		}
		return $arr;
	}

	private function Worker($f, $d, $isUpdate = false) {
		$request = new Request();
		$delArray = $request->post->get("deleteArray", array());
		$delArray = array_map(function($v) { return explode(",", $v); }, ($delArray));
		$data = array();
		foreach($f['data'] as $v) {
			$name = $v['name'];
			$name = str_replace(" ", "_", $name);
			$files = $request->files->get($name, "");
			$post = $request->post->get($name, "");
			$files = $request->files->get($name, "");
			$post = $request->post->get($name, "");
			

			$post = $this->rebuildData($post);
			$files = $this->rebuildData($files);

			$value = $post;

			/*if(isset($_POST[$name])) {
				$value = $_POST[$name];
				unset($_POST[$name]);
			} else {
				$value = $v['default'];
			}*/
			/////////
			///
			if(isset($delArray[$v['altName']]) && !empty($delArray[$v['altName']])) {
				if(is_serialized($value)) {
					$value = unserialize($value);
				}
				if(!is_Array($value)) {
					$value = array($value);
				}
				for($countFilesArray=0;$countFilesArray<sizeof($delArray[$v['altName']]);$countFilesArray++) {
					if(!empty($delArray[$v['altName']][$countFilesArray]) && isset($value[$delArray[$v['altName']][$countFilesArray]])) {
						unset($value[$delArray[$v['altName']][$countFilesArray]]);
					}
				}
				$value = array_values($value);
				$value = serialize($value);
			}

			if($v['type']=="linkToAdmin") {
				continue;
			} else if(!empty($files) && ($v['type']=="imageAccess" || $v['type']=="fileAccess" || $v['type']=="file" || $v['type']=="fileArray" || $v['type']=="image" || $v['type']=="imageArray")) {
				$v = $files;
				if((!isset($v['error']) || is_array($v['error'])) && (!isset($v['name']) || is_array($v['name']))) {
					$viewI = 1;
					$v = Files::reArrayFiles($v);
					if(is_serialized($v)) {
						$v = unserialize($v);
					}
					$counter = 0;
					$values = array();
					foreach($v as $ks => $vs) {
						$upload = $this->UploadFile($models, $ks, $selectId, $vs, (is_array($uploads) && isset($uploads[$v['altName']]) ? $uploads[$v['altName']] : $uploads), $v['allowUpload'], $viewI);
						if(!empty($upload) || !empty($v)) {
							$values[$ks] = (!$upload ? (is_array($v) && isset($v[$counter]) ? $v[$counter] : $v) : $upload."?".time());
							$values[$ks] = str_replace(DS, "/", $values[$ks]);
							$viewI++;
						}
						$counter++;
					}
					$v = $values;
				} else {
					$upload = $this->UploadFile($models, $v['altName'], $selectId, $v, $uploads, $v['allowUpload']);
					$v = (isset($emptyFiles[$k]) ? "" : (!$upload ? $v : $upload."?".time()));
					$v = str_replace(DS, "/", $v);
				}
			} else if($v['type']=="imageAccess" || $v['type']=="fileAccess" || $v['type']=="file" || $v['type']=="fileArray" || $v['type']=="image" || $v['type']=="imageArray" || $v['type']=="fileArrayAccess" || $v['type']=="imageArrayAccess") {
				if(!empty($post)) {
					$value = $post;
				} else {
					$value = $value;
				}
				$value = str_replace(DS, "/", $value);
			} else if($v['type']=="date") {
                $post = str_replace("/", "-", $post);
				$value = strtotime((isset($post) && !empty($post) ? $post : date("d/m/Y"))." ".date("H:i:s"));
			} else if($v['type']=="time") {
				$value = strtotime(date("d/m/Y")." ".(isset($post) && !empty($post) ? $post : date("H:i:s")));
			} else if($v['type']=="datetime") {
                $post[0] = str_replace("/", "-", $post[0]);
				$value = strtotime((isset($post[0]) && !empty($post[0]) ? $post[0] : date("d/m/Y"))." ".(isset($post[1]) && !empty($post[1]) ? $post[1] : date("H:i:s")));
			} else if($v['type']=="array" && isset($v['selectedData']) && $v['selectedData']=="dataOnInput") {
				$field = array_flip($v['field']);
				if(isset($field[$value])) {
					$saveVal = $field[$value];
				} else if(isset($data[$v['altName']])) {
					$saveVal = $data[$v['altName']];
				} else if($v['altName']=="level" && empty($value)) {
					$saveVal = (!empty($v['default']) ? $data[$v['altName']] : 2);
				} else {
					$saveVal = (isset($data[$v['altName']]) ? $data[$v['altName']] : $v['default']);
				}
				$value = $saveVal;
			} else if(!is_bool($post)) {
				$value = $post;
			}
			$value = execEvent("profile-user-changed-".$name, $value);
			if($v['altName']=="pass") {
				$pad = str_pad($value, nstrlen($value));
				if(is_array($isUpdate) && isset($isUpdate['light']) && nstrlen($isUpdate['light'])==nstrlen($value) && $isUpdate['light']==$pad) {
					$value = $isUpdate['light'];
				}
				$data['admin_pass'] = User::create_pass($value);
				$data['pass'] = User::create_pass($value);
				$data['light'] = ($value);
			} else {
				$data[$v['altName']] = $value;
			}
		}
		return $data;
	}
	
	public function __construct() {
		define("PROFILE_SETTINGS", PATH_CACHE_USERDATA."userSettings.php");
		$d = array();
		$s = userlevel::all();
		$s = array_keys($s);
        $langs = lang::get_lang("level");
		for($i=0;$i<sizeof($s);$i++) {
			$d[$s[$i]] = $langs[$s[$i]];
		}
		$f = $this->getUserFields($d);

		if(isset($_GET['Settings'])) {
			if(sizeof($_POST)>0) {
				foreach($_POST['data'] as &$v) {
					if(in_array($v['altName'], array("username","pass","level","email"))) {
						$v['onlyAttr'] = true;
					}
				}
				@file_put_contents(PROFILE_SETTINGS, '<?php die(); ?>'.json_encode($_POST));
				location("./?pages=ProfileSettings");
				return;
			}
			templates::assign_var("struct", json_encode($f));
			$this->Prints("ProfileSettings");
			return;
		}
        if(isset($_GET['Delete'])) {
			$users = User::getUserById($_GET['Delete']);
			if($users) {
				User::remove($users['username']);
				location("./?pages=ProfileSettings");
				return;
			} else {
				location("./?pages=ProfileSettings");
                return;
            }
        }
		if(isset($_GET['Edit'])) {
			$users = execEvent("profile-user-load", array());
			if(sizeof($users)==0) {
				$users = User::getUserById($_GET['Edit']);
			}
			$users = execEvent("profile-user-loaded", $users);
			if(sizeof($_POST)>0) {
				$worker = $this->Worker($f, $d, $users);
				$worker = execEvent("profile-user-change-compiled", $worker);
				User::update($worker);
				location("./?pages=ProfileSettings");
				return;
			}
			$tmp = "";
			$users = execEvent("profile-user-change::before", $users);
			foreach($f['data'] as $v) {
				if(isset($v['hideAlways'])) {
					continue;
				}
				$value = (isset($users[$v['altName']]) ? $users[$v['altName']] : $v['default']);
				if(isset($users[$v['altName']]) && $v['altName']=="level") {
					$value += 1;
				}
				$args = array();
				if($v['altName']=="pass") {
					$value = (isset($users['light']) ? $users['light'] : "");
					$value = str_pad("", nstrlen($value), "*");
				} else if(isset($v['type']) && $v['type']=="linkToAdmin") {
					$link = $v['field']['link'];
					$link = str_replace("{uid}", $_GET['Edit'], $link);
					$args = array("linkLink" => $link, "titleLink" => $v['field']['title']);
				} else if(isset($v['type']) && $v['type']=="array" && isset($v['selectedData']) && isset($v['field'])) {
					$field = array_values($v['field']);
					$v['default'] = (isset($field[$value]) ? $field[$value] : $v['default']);
					$value = $v['field'];
				}
				$arrViewing = array($v['type'], $v['name'], $value, "auto", $v['default'], isset($v['required']), ($v['altName']=="username" && isset($users['typeUserInSystem']) && $users['typeUserInSystem']=="file"), false, "", "", false, $args);
				$arrViewing = execEvent("profile-user-change-".$v['name'], $arrViewing);
				$call = call_user_func_array("KernelArcher::Viewing", $arrViewing);
				$tmp .= $call;
			}
			templates::assign_var("data", $tmp);
			templates::assign_var("typeForm", execEvent("profile-user-change-title-edit", "Edit=".$_GET['Edit'], $users));
			templates::assign_var("id_edit", $_GET['Edit']);
			$this->Prints("ProfileSettingsAdd");
			return;
		}
		if(isset($_GET['Add'])) {
			if(sizeof($_POST)>0) {
				$worker = $this->Worker($f, $d);
				User::create($worker);
				location("./?pages=ProfileSettings");
				return;
			}
			$tmp = "";
			foreach($f['data'] as $v) {
				if(isset($v['hideAlways'])) {
					continue;
				}
				$value = "";
				$args = array();
				if(isset($v['type']) && $v['type']=="linkToAdmin") {
					$args = array("linkLink" => $v['field']['link'], "titleLink" => $v['field']['title']);
					if(strpos($v['field']['link'], "{uid}")!==false) {
						continue;
					}
				} else if(isset($v['type']) && $v['type']=="array" && isset($v['selectedData']) && isset($v['field'])) {
					$v['default'] = $value;
					$value = $v['field'];
				}
				$arrViewing = array($v['type'], $v['name'], $value, $v['default'], isset($v['required']), false, false, "", "", false, $args);
				$arrViewing = execEvent("profile-user-change-".$v['name'], $arrViewing);
				$tmp .= call_user_func_array("KernelArcher::Viewing", $arrViewing);
			}
			templates::assign_var("data", $tmp);
			templates::assign_var("typeForm", execEvent("profile-user-change-title-add", "Add"));
			templates::assign_var("id_edit", "");
			$this->Prints("ProfileSettingsAdd");
			return;
		}

		$head = $body = "";
		$names = array();
		$head .= "<th>ID</th>";
		foreach($f['data'] as $v) {
			if(isset($v['hideOnMain'])) continue;
			if(isset($v['hideAlways'])) {
				continue;
			}
			$names[] = $v;
			$head .= "<th>".$v['name']."</th>";
		}
		$head .= "<th>options</th>";
		execEvent("before_load_users");
		if(!defined("ADMINCP_USERS_NOT_LOAD")) {
			$users = User::All(true);
		} else {
			$users = array();
		}
		$users = execEvent("after_load_users", $users);
		foreach($users as $username => $v) {
			$v = execEvent("profile-user-show", $v);
			$body .= "<tr>";
			$body .= "<td>".$v['id']."</td>";
			for($i=0;$i<sizeof($names);$i++) {
				if($names[$i]['type']=="linkToAdmin") {
					$data = "<a href=\"".str_replace("{uid}", $v['id'], $names[$i]['field']['link'])."\">".$names[$i]['field']['title']."</a>";
				} else {
					$data = (isset($v[$names[$i]['altName']]) ? ($names[$i]['altName']=="level" ? $d[$v[$names[$i]['altName']]] : $v[$names[$i]['altName']]) : "{L_'Не задано'}");
				}
				$body .= "<td>".execEvent("profile-user-show-field-".$names[$i]['name'], $data)."</td>";
			}
			$body .= '<td>[if {C_disableEdit}!=1]<a href="./?pages=ProfileSettings&Edit='.$v['id'].'" class="btn btn-edit btn-block">{L_"Редактировать"}</a>[/if {C_disableEdit}!=1]
				[if {C_disableDelete}!=1]<a href="./?pages=ProfileSettings&Delete='.$v['id'].'" onclick="return confirmDelete();" class="btn btn-red btn-block">{L_"Удалить"}</a>[/if {C_disableDelete}!=1]
				{E_[customOptions][type=ProfileSettings;id='.$v['id'].']}</td>';
			$body .= "</tr>";
		}
		templates::assign_var("head", $head);
		templates::assign_var("body", $body);
		templates::assign_var("orderById", "0");
		templates::assign_var("orderBySort", "desc");
		templates::assign_var("ArcherSort", "");
		templates::assign_var("ArcherTable", "ProfileSettings");
		templates::assign_var("ArcherNotTouch", sizeof($names)+1);
		$this->Prints("ProfileSettingsMain");
	}
	
}

?>