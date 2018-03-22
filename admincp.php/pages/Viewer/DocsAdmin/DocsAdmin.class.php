<?php
if(!defined("IS_ADMIN")) {
echo "403 ERROR";
die();
}

class DocsAdmin extends Core {
	
	function __construct() {
		if(!Docs::init()) {
			$this->Prints(templates::view("{L_\"Не возможно запустить модуль, так как отсутствует подключение к базе данных, либо отсутствуют требуемые права на папки\"}"), true);
			die();
		}
		$request = new Request();
		$page = $request->get->get("mod");
		$viewId = $request->get->get('viewId', false);
		$viewId = intval($viewId);
		switch($page) {
			case "TakeAdd":
				$model = modules::loadModels("ModelDocs", "docs");
				$model->SetTable("Docs");
				$model->page = Saves::SaveOld($request->post->get("page"), true);
				$model->version = $request->post->get("version");
				$post = $request->post->get("descr");
				$post = array_map("trim", $post);
				$model->text = db::escape(json_encode($post));
				cardinal::RegAction("Добавление данных в Docs");
				$model->Insert();
				location("{C_default_http_local}{D_ADMINCP_DIRECTORY}?pages=DocsAdmin");
			break;
			case "Add":
				$model = modules::loadModels("ModelDocs", "Docs");
				templates::assign_var("typePage", "Add");
				templates::assign_var("textarea", '<div class="row"><div class="col-sm-11"><textarea id="editor1" name="descr[]"></textarea></div><div class="col-sm-1"><a href="#" class="btn btn-red" onclick="return removed(this);">{L_delete}</a></div></div>');
				templates::assign_vars($model->getArray());
				$this->Prints("DocsAdd");
				return;
			break;
			case "TakeEdit":
				$model = modules::loadModels("ModelDocs", "Docs");
				$model->SetTable("Docs");
				$model->SetLimit(1);
				$model->WhereTo($viewId);
				
				$sel = $model->Select();
				if(is_object($sel)) {
					$model->aId = $sel->aId;
					$page = Saves::SaveOld($request->post->get("page"), true);
					if($page!=$sel->page) {
						$model->page = $page;
					} else {
						$model->page = $sel->page;
					}
					$ver = $request->post->get("version");
					if($ver!=$sel->version) {
						$model->version = $ver;
					}
					$post = $request->post->get("descr");
					$post = array_map("trim", $post);
					$post = json_encode($post);
					if($post!=$sel->text) {
						$model->text = db::escape($post);
					} else {
						$model->text = db::escape($sel->text);
					}
					cardinal::RegAction("Редактирование данных в Docs. ИД: \"".$viewId."\"");
					$model->Update();
					location("{C_default_http_local}{D_ADMINCP_DIRECTORY}?pages=DocsAdmin");
				}
			break;
			case "Edit":
				$model = modules::loadModels("ModelDocs", "Docs");
				$model->SetTable("Docs");
				$model->SetLimit(1);
				$model->WhereTo($viewId);
				$model = $model->Select();
				templates::assign_var("typePage", "Edit&viewId=".$viewId);
				$descr = json_decode($model->text, true);
				$descr = array_map(array(&$this, "buildDescr"), array_keys($descr), array_values($descr));
				$model->textarea = implode("", $descr);
				$data = $model->getArray();
				templates::assign_vars($data);
				$this->Prints("DocsAdd");
			break;
			case "Delete":
				$model = modules::loadModels("ModelDocs", "Docs");
				$model->SetTable("Docs");
				$model->SetLimit(1);
				$model->WhereTo($viewId);
				$sel = $model->Select();
				if(is_object($sel) && method_exists($sel, "getArray")) {
					$first = $sel->getFirst();
					$sel = $sel->getArray();
					if(is_array($sel) && Arr::get($sel, $first, false)) {
						cardinal::RegAction("Удаление данных в Docs. ИД: \"".$viewId."\"");
						$model->Deletes();
						location("{C_default_http_local}{D_ADMINCP_DIRECTORY}?pages=DocsAdmin");
					}
				}
			break;
			default:
				$model = modules::loadModels("ModelDocs", "Docs");
				$model->SetTable("Docs");
				$model->SetLimit(-1);
				$list = $model->Select();
				if(is_array($list)) {
					for($i=0;$i<sizeof($list);$i++) {
						$subList = $list[$i]->getArray();
						templates::assign_vars($subList, "Docs", "Docs".$i);
					}
				} elseif(is_object($list)) {
					$subList = $list->getArray();
					templates::assign_vars($subList, "Docs", "Docs0");
				}
				$this->Prints("DocsMain");
			break;
		}
		
		function buildDescr($k, $d) {
			return '<div class="row"><div class="col-sm-11"><textarea id="editor'.($k+1).'" name="descr[]">'.$d.'</textarea></div><div class="col-sm-1"><a href="#" class="btn btn-red" onclick="return removed(this);">{L_delete}</a></div></div>';
		}
	}
	
}