<?php
define("ERROR_VIEW", true);
class ATextAdmin extends Core {
	
	function __construct() {
		if(!AText::init()) {
			$this->Prints(templates::view("{L_\"Не возможно запустить модуль, так как отсутствует подключение к базе данных, либо отсутствуют требуемые права на папки\"}"), true);
			die();
		}
		$request = new Request();
		$page = $request->get->get("mod");
		$viewId = $request->get->get('viewId', false);
		$viewId = intval($viewId);
		switch($page) {
			case "Editor":
echo <<<HTML
CKEDITOR.editorConfig = function( config ) {
	config.toolbarGroups = [
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'forms', groups: [ 'forms' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		'/',
		{ name: 'styles', groups: [ 'styles' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'tools', groups: [ 'tools' ] },
		{ name: 'others', groups: [ 'others' ] },
		{ name: 'about', groups: [ 'about' ] }
	];

	config.removeButtons = 'Save,NewPage,Preview,Print,Templates,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Subscript,Superscript,Indent,Outdent,Blockquote,CreateDiv,BidiLtr,BidiRtl,Language,Anchor,Flash,Table,Smiley,SpecialChar,PageBreak,Iframe,Styles,Format,Font,FontSize,BGColor,TextColor,Maximize,ShowBlocks,About';
};
HTML;
die();
			break;
			case "TakeAdd":
				$model = modules::loadModels("ModelaText", PREFIX_DB."aText");
				$model->SetTable(PREFIX_DB."aText");
				$model->page = Saves::SaveOld($request->post->get("page"), true);
				$post = $request->post->get("descr");
				$post = array_map("trim", $post);
				$model->text = serialize($post);
				cardinal::RegAction("Добавление данных в AText");
				$model->Insert();
				location("{C_default_http_local}{D_ADMINCP_DIRECTORY}?pages=ATextAdmin");
			break;
			case "Add":
				$model = modules::loadModels("ModelaText", PREFIX_DB."aText");
				templates::assign_var("typePage", "Add");
				templates::assign_var("textarea", '<div class="row"><div class="col-sm-11"><textarea id="editor1" name="descr[]"></textarea></div><div class="col-sm-1"><a href="#" class="btn btn-red" onclick="return removed(this);">{L_delete}</a></div></div>');
				templates::assign_vars($model->getArray());
				$this->Prints("aTextAdd");
				return;
			break;
			case "TakeEdit":
				$model = modules::loadModels("ModelaText", PREFIX_DB."aText");
				$model->SetTable(PREFIX_DB."aText");
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
					$post = $request->post->get("descr");
					$post = array_map("trim", $post);
					$post = serialize($post);
					if($post!=$sel->text) {
						$model->text = $post;
					} else {
						$model->text = $sel->text;
					}
					cardinal::RegAction("Редактирование данных в AText. ИД: \"".$viewId."\"");
					$model->Update();
					location("{C_default_http_local}{D_ADMINCP_DIRECTORY}?pages=ATextAdmin");
				}
			break;
			case "Edit":
				$model = modules::loadModels("ModelaText", PREFIX_DB."aText");
				$model->SetTable(PREFIX_DB."aText");
				$model->SetLimit(1);
				$model->WhereTo($viewId);
				$model = $model->Select();
				templates::assign_var("typePage", "Edit&viewId=".$viewId);
				$descr = unserialize($model->text);
				$descr = array_map(array(&$this, "buildDescr"), array_keys($descr), array_values($descr));
				$model->textarea = implode("", $descr);
				$data = $model->getArray();
				templates::assign_vars($data);
				$this->Prints("aTextAdd");
			break;
			case "Delete":
				$model = modules::loadModels("ModelaText", PREFIX_DB."aText");
				$model->SetTable(PREFIX_DB."aText");
				$model->SetLimit(1);
				$model->WhereTo($viewId);
				$sel = $model->Select();
				if(is_object($sel) && method_exists($sel, "getArray")) {
					$first = $sel->getFirst();
					$sel = $sel->getArray();
					if(is_array($sel) && Arr::get($sel, $first, false)) {
						cardinal::RegAction("Удаление данных в AText. ИД: \"".$viewId."\"");
						$model->Deletes();
						location("{C_default_http_local}{D_ADMINCP_DIRECTORY}?pages=ATextAdmin");
					}
				}
			break;
			default:
				$model = modules::loadModels("ModelaText", PREFIX_DB."aText");
				$model->SetTable(PREFIX_DB."aText");
				$model->SetLimit(-1);
				$list = $model->Select();
				if(is_array($list)) {
					for($i=0;$i<sizeof($list);$i++) {
						$subList = $list[$i]->getArray();
						templates::assign_vars($subList, "aText", "aText".$i);
					}
				} elseif(is_object($list)) {
					$subList = $list->getArray();
					templates::assign_vars($subList, "aText", "aText0");
				}
				$this->Prints("aTextMain");
			break;
		}
		
		function buildDescr($k, $d) {
			return '<div class="row"><div class="col-sm-11"><textarea id="editor'.($k+1).'" name="descr[]">'.$d.'</textarea></div><div class="col-sm-1"><a href="#" class="btn btn-red" onclick="return removed(this);">{L_delete}</a></div></div>';
		}
	}
	
}