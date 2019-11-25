[if {db_connected}==false]<p class="well text-center">
	<span class="text-primary">Внимание! Подключение к базе данных не обнаружено. Все действия будут иметь подготовительный характер!</span>
</p>[/if {db_connected}==false]
<div class="row">
	<div class="col-md-12">
		<form role="form" class="form-horizontal formCreator" method="post" novalidate="true">
			<input type="hidden" class="mode" name="mode" value="add">
			<div class="panel panel-default">
				<div class="panel-heading">Управление полями пользователя</div>
				<div class="panel-body">
					<ul class="creator uk-nestable row" data-uk-nestable="{maxDepth:999999}"></ul>
					<input type="submit" class="btn btn-success" value="{L_submit}" disabled="disabled">
					<a href="#" class="btn btn-info addCreator pull-right">{L_"Добавить"}</a>
				</div>
			</div>
		</form>
	</div>
</div>
<style type="text/css">
	.uk-nestable-placeholder { float: left; float: left; width: 97%; margin-left: 15px; margin-right: 15px; }
	.collapse { margin-top: 2rem; margin-bottom: 2rem; }
	.togglePanel { cursor: pointer; background: #eeeeee; padding: 5px 8px; margin-left: 15px; margin-right: 0; width: calc(100% - 30px); }
	.errorInput { border: 1px solid red; animation: errorInput 300ms ease-in-out 1500ms infinite; }
	.removeBtn { position: absolute; top: 0px; right: 15px; z-index: 10; padding-top: 3.5px; padding-bottom: 3.5px; }
	.uk-nestable-item .uk-nestable-handle {
		padding: 0;
	}
	@keyframes errorInput {
	    0% {
	        border-color: rgba(255,0,0,0);
	    }
	    50% {
	        border-color: rgba(255,0,0,1);
	    }
	    100% {
	        border-color: rgba(255,0,0,0);
	    }
	}
	.translated {
		color: #fff;
	    font-size: 120%;
	    background: #2c2e2f;
	    padding: 0.4em 0.4em 0.3em;
	}
	.warn-icon {
		font-size: 1.3em;
		line-height: normal;
		color: #F80;
	}
	.param-route {
		display: flex;
		justify-content: space-around;
		margin-top: 0.8em;
	}
	.param-route input {
		border: 0;
		outline: 0;
		font-weight: bold;
	}
	.editor {
		cursor: pointer;
	}
	a.editor {
		font-size: 1.4em;
		margin: 0.2em 0.5em;
		text-decoration: none;
	}
</style>
<script type="text/template" id="tmpCreate">
	<li class="col-xs-12" data-field="{id}">
		<div class="uk-nestable-item uk-nestable-nochildren">
			<input type="hidden" name="order[]" value="{id}">
			<input type="hidden" name="data[{id}][depth]" value="{depth}">
			<input type="hidden" name="data[{id}][parent_id]" value="{parent_id}">
			<div class="row">
				<div class="col-xs-12 togglePanel" data-toggle="{id}">
					<div class="uk-nestable-handle"></div>
					<div data-nestable-action="toggle"></div>
					<div class="list-label hereTitle" data-hereTitle-id="{id}">{L_"Не введено"}</div>
				</div>
				<a href="#" class="btn btn-icon btn-red btn-single btn-sm removeBtn remove" data-id="{id}" tabindex="-1"><i class="fa-remove"></i></a>
				<div class="col-xs-12 collapse form-horizontal" data-panel="{id}">
					<div class="col-xs-12">
						<div class="form-group">
							<label class="col-xs-12 col-md-3 control-label">Название поля</label>
							<div class="col-xs-12 col-md-9">
								<input type="text" class="form-control title" name="data[{id}][name]" placeholder="Введите название" required="required" data-input-id="{id}">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-md-3 control-label">Альтернативное название поля</label>
							<div class="col-xs-12 col-md-9">
								<input type="text" class="form-control altName" name="data[{id}][altName]" placeholder="Введите альтернативное название" required="required" data-needTranslate="true">
							</div>
						</div>
						<br>
						<div class="form-group">
							<label class="col-xs-12 col-md-3 control-label">Тип поля</label>
							<div class="col-xs-12 col-md-9">
								<select class="form-control selected" required="required" data-selectId="{id}" name="data[{id}][type]">
									<option value="" selected="selected" disabled="disabled">Выберите тип</option>
									<optgroup label="Числа">
										<option value="int">Целое число</option>
										<option value="float">Число с запятой</option>
										<option value="price">Цена</option>
									</optgroup>
									<optgroup label="Текст">
										<option value="varchar">Однострочный текст</option>
										<option value="email">E-mail</option>
										<option value="link">Ссылка</option>
										<option value="password">Пароль</option>
										<option value="onlytextareatext">Многострочный редактор текста</option>
										<option value="longtext">Визуальный редактор текста</option>
									</optgroup>
									<optgroup label="Картинки">
										<option value="image">Загрузка картинки</option>
										<option value="imageAccess">Загрузка картинки (с доступом к библиотеке)</option>
										<option value="imageArray">Загрузка нескольких картинок</option>
										<option value="imageArrayAccess">Загрузка нескольких картинок (с доступом к библиотеке)</option>
									</optgroup>
									<optgroup label="Файлы">
										<option value="file">Загрузка файла</option>
										<option value="fileAccess">Загрузка файла (с доступом к библиотеке)</option>
										<option value="fileArray">Загрузка нескольких файлов</option>
										<option value="fileArrayAccess">Загрузка нескольких файлов (с доступом к библиотеке)</option>
									</optgroup>
									<optgroup label="Дата/время">
										<option value="date">Поле для ввода даты</option>
										<option value="time">Поле для ввода времени</option>
										<option value="datetime">Поле для ввода даты/времени</option>
										<option value="systime">Автоматическое установление времени</option>
									</optgroup>
									<optgroup label="Разное">
										<option value="array">Массив данных</option>
										<option value="multiple-array">Массив данных с возможностью выбора нескольких значений</option>
										<option value="hidden">Скрытое поле</option>
										<option value="radio">Радио-группа</option>
										<option value="linkToAdmin">Ссылка на другой раздел</option>
									</optgroup>
								</select>
							</div>
						</div>
						<div class="col-xs-12 databased hide" data-hideId="{id}"></div>
						<div class="col-xs-12 selectedInput hide" data-selectedInput="{id}"></div>
						<div class="form-group">
							<label class="col-xs-12 col-md-3 control-label">Значение по-умолчанию</label>
							<div class="col-xs-12 col-md-9">
								<textarea class="form-control default onlyText" name="data[{id}][default]" placeholder="Введите значение по-умолчанию"></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-md-3 control-label">Подсказка</label>
							<div class="col-xs-12 col-md-9">
								<input type="text" class="form-control placeholder" name="data[{id}][placeholder]" placeholder="Введите подсказку">
							</div>
						</div>
						<br>
						<div class="form-group">
							<label class="col-xs-12 col-md-3 control-label">Дополнительные возможности</label>
							<div class="col-xs-12 col-md-9">
								<div class="checkbox">
									<label class="hides">
										<input type="checkbox" name="data[{id}][hideOnMain]" class="cbr cbr-primary" value="yes">Скрыть с главной
									</label>
								</div>
								<div class="checkbox">
									<label class="required">
										<input type="checkbox" name="data[{id}][required]" class="cbr cbr-primary" value="yes" data-id="{id}">Обязательное поле
									</label>
								</div>
								<div class="checkbox">
									<label class="disApplyInReg">
										<input type="checkbox" name="data[{id}][disApplyInReg]" class="cbr cbr-primary" value="yes" data-id="{id}">Не участвовать в регистрации
									</label>
								</div>
								<div class="checkbox">
									<label class="applyInLogin">
										<input type="checkbox" name="data[{id}][applyInLogin]" class="cbr cbr-primary" value="yes" data-id="{id}">Участвовать в авторизации
									</label>
								</div>
							</div>
						</div>
						<div class="createAltName" data-altname="{id}"></div>
						<div class="col-xs-12 col-md-2">
							<a href="#" class="btn btn-red remove" data-id="{id}" tabindex="-1">{L_"Удалить"}</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</li>
</script>
<script type="text/template" class="databaseSelectRadio">
	<br><label class="col-xs-12 col-md-6 text-center"><input type="radio" name="data[{id}][selectedData]" class="cbr cbr-primary" value="dataOnTable" onchange="selectedDatabaseChange(this);">Данные из базы данных</label>
	<label class="col-xs-12 col-md-6 text-center"><input type="radio" name="data[{id}][selectedData]" class="cbr cbr-primary" value="dataOnInput" onchange="selectedDatabaseChange(this);">Данные для ввода</label><br><br>
</script>
<script type="text/template" class="databaseTemplate">
	<br><select class="form-control dataBaseLoad" name="data[{id}][loadDB][name]" required="required" data-selectInputedData="{id}"><option></option>{data}</select>
	<br><div class="col-sm-12 dataBaseSelect" data-selectInputedData="{id}">
	</div>
</script>
<script type="text/template" class="databaseTemplateSelect">
	<div class="col-sm-6">
		<div class="col-sm-12">
			<b>Ключём будет:</b>
		</div>
		<div class="col-sm-12">
			<select class="form-control dataBaseLoadKey" name="data[{id}][loadDB][key]" required="required"><option></option>{data1}</select>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="col-sm-12">
			<b>Значением будет:</b>
		</div>
		<div class="col-sm-12">
			<select class="form-control dataBaseLoadValue" name="data[{id}][loadDB][value]" required="required"><option></option>{data2}</select>
		</div>
	</div>
</script>
<script type="text/template" class="databaseInput">
	<div class="row inputedData" data-selectInputedData="{id}"></div>
	<a href="#" class="btn btn-success addInputDB pull-right" data-addInputDB="{id}">{L_"Добавить"}</a>
</script>
<script type="text/template" class="databaseInputedData">
	<div class="row">
		<div class="col-sm-12" data-inputedData="{id}">
			<div class="col-sm-10"><input type="text" class="form-control" name="data[{groupId}][field][{id}]" placeholder="Введите значение" required="required" value="{val}"></div>
			<div class="col-sm-2"><a href="#" class="btn btn-red btn-block removeData" data-id="{id}" tabindex="-1">{L_"Удалить"}</a></div>
		</div>
	</div>
</script>
<script type="text/template" class="inputTranslate">
	<div class="form-group alttitle">
		<label class="col-xs-12 col-md-3 control-label">Альтернативное имя</label>
		<div class="col-xs-12 col-md-9">
			<input type="text" class="form-control" name="data[{id}][alttitle]" placeholder="" required="required">
		</div>
	</div>
</script>
<script type="text/template" class="linkToInput">
	<div class="form-group">
		<label class="col-xs-12 col-md-3 control-label">Ссылка на другой раздел</label>
		<div class="col-xs-12 col-md-9">
			<input type="text" class="form-control" name="data[{id}][field][link]" placeholder="" required="required" value="{valLink}">
		</div>
		<div class="col-xs-offset-0 col-md-offset-3 col-md-9">{uid} - для вставки ID данной записи</div>
	</div>
	<div class="form-group">
		<label class="col-xs-12 col-md-3 control-label">Название ссылки на другого раздела</label>
		<div class="col-xs-12 col-md-9">
			<input type="text" class="form-control" name="data[{id}][field][title]" placeholder="" required="required" value="{valTitle}">
		</div>
	</div>
</script>
<script type="application/json" id="json">{struct}</script>
<script type="text/javascript">
	var selectedData = {};
	var arrTranslate = {};
	var struct = document.getElementById("json").innerHTML;
	var iInputDB = 0;
	var i = 0;
	function translater(val) {
		"function"!=typeof Object.assign&&(Object.assign=function(n){if(null==n)throw new TypeError("Cannot convert undefined or null to object");for(var r=Object(n),t=1;t<arguments.length;t++){var e=arguments[t];if(null!=e)for(var o in e)e.hasOwnProperty(o)&&(r[o]=e[o])}return r});
		var _preset = "{C_lang}";
		var _firstLetterAssociations = {
			"а": "a",
			"б": "b",
			"в": "v",
			"ґ": "g",
			"г": "g",
			"д": "d",
			"е": "e",
			"ё": "e",
			"є": "ye",
			"ж": "zh",
			"з": "z",
			"и": "i",
			"і": "i",
			"ї": "yi",
			"й": "i",
			"к": "k",
			"л": "l",
			"м": "m",
			"н": "n",
			"о": "o",
			"п": "p",
			"р": "r",
			"с": "s",
			"т": "t",
			"у": "u",
			"ф": "f",
			"х": "h",
			"ц": "c",
			"ч": "ch",
			"ш": "sh",
			"щ": "sz",
			"ъ": "",
			"ы": "y",
			"ь": "",
			"э": "e",
			"ю": "yu",
			"я": "ya",
		};
		if(_preset === "uk") {
			Object.assign(_firstLetterAssociations, {
				"г": "h",
				"и": "y",
				"й": "y",
				"х": "kh",
				"ц": "ts",
				"щ": "shch",
				"'": "",
				"’": "",
				"ʼ": "",
			});
		}
		var _associations = Object.assign({}, _firstLetterAssociations);
		if(_preset === "uk") {
			Object.assign(_associations, {
				"є": "ie",
				"ї": "i",
				"й": "i",
				"ю": "iu",
				"я": "ia",
			});
		}
		function transform(input, spaceReplacement) {
			if(!input) {
				return "";
			}
			var newStr = "";
			for(var i=0;i<input.length;i++) {
				var isUpperCaseOrWhatever = input[i] === input[i].toUpperCase();
				var strLowerCase = input[i].toLowerCase();
				if(strLowerCase === " ") {
					newStr += spaceReplacement;
					continue;
				}
				var newLetter = _preset === "uk" && strLowerCase === "г" && i > 0 && input[i - 1].toLowerCase() === "з" ? "gh" : (i === 0 ? _firstLetterAssociations : _associations)[strLowerCase];
				if("undefined" === typeof newLetter) {
					newStr += isUpperCaseOrWhatever ? strLowerCase.toUpperCase() : strLowerCase;
				} else {
					newStr += isUpperCaseOrWhatever ? newLetter.toUpperCase() : newLetter;
				}
			}
			newStr = newStr.replace(/[^a-zA-Z0-9_]/g, "");
			return newStr;
		}
		return transform(val, "_");
	}
	function builder(data, parent) {
		Object.keys(data).forEach(function(k) {
			var dataField = data[k];
			console.log(dataField);
			i++;
			var tmp = jQuery("#tmpCreate").html();
			var tpl = tmp;
			tpl = tpl.replace(/\{id\}/g, i);
			tpl = tpl.replace(/\{depth\}/g, (typeof(dataField.depth)==="undefined" ? "0" : dataField.depth));
			var parent = (typeof(dataField.parent_id)==="undefined" ? parent : dataField.parent_id);
			tpl = tpl.replace(/\{parent_id\}/g, parent);
			parent = parseInt(parent);
			var appendTo = ".creator";
			if(parent>0) {
				appendTo += ' [data-field="'+(parent+1)+'"]';
			}
			var elemAddTo = jQuery(appendTo);
			if(parent>0) {
				var existsList = elemAddTo.find(".ul-nestable-list").length;
				if(existsList==0) {
					tpl = '<ul class="uk-nestable-list">'+tpl+'</ul>';
				}
			}
			elemAddTo.append(tpl);
			jQuery("[data-field='"+i+"'] .hereTitle").html(dataField.name);
			jQuery("[data-field='"+i+"'] .hereTitle").after("<small></small>");
			jQuery("[data-field='"+i+"']").prepend("<input type=\"hidden\" name=\"data["+i+"][beforeAltName]\" value=\""+dataField.altName+"\">");
			jQuery("[data-field='"+i+"']").find(".title").val(dataField.name);
			jQuery("[data-field='"+i+"']").find(".altName").val(dataField.altName);
			if(typeof(dataField.altName)!=="undefined" && dataField.altName.length>0) {
				jQuery("[data-field='"+i+"']").find(".altName").attr("data-needTranslate", "false");
			} else {
				jQuery("[data-field='"+i+"']").find(".altName").attr("data-needTranslate", "false").val(translater(dataField.name));
			}
			jQuery("[data-field='"+i+"']").find(".placeholder").val(dataField.placeholder);
			jQuery("[data-field='"+i+"']").find("select").val(dataField.type);
			jQuery("[data-field='"+i+"'] .hereTitle small").remove();
			jQuery("[data-field='"+i+"'] .hereTitle").after("<small style='font-size:75%;'>"+(jQuery("[data-field='"+i+"']").find("select option[value='"+dataField.type+"']").text())+"</small>");
			if(typeof(dataField.hideOnMain)!=="undefined" && dataField.hideOnMain=="yes") {
				jQuery("[data-field='"+i+"']").find("label.hides input[type='checkbox']").attr("checked", "checked");
			}
			if(typeof(dataField.supportLang)!=="undefined" && dataField.supportLang=="yes") {
				jQuery("[data-field='"+i+"']").find("label.supportLang input[type='checkbox']").attr("checked", "checked");
			}
			if(typeof(dataField.required)!=="undefined" && dataField.required=="yes") {
				jQuery("[data-field='"+i+"']").find("label.required input[type='checkbox']").attr("checked", "checked");
			}
			if(typeof(dataField.repeater)!=="undefined" && dataField.repeater=="yes") {
				jQuery("[data-field='"+i+"']").find("label.repeater input[type='checkbox']").attr("checked", "checked");
				jQuery("[data-field='"+i+"']").find(".uk-nestable-nochildren").removeClass('uk-nestable-nochildren')
			}
			if(typeof(dataField.selectedData)!=="undefined" && dataField.selectedData=="dataOnInput") {
				var tmp = jQuery(".databaseSelectRadio").html();
				tmp = tmp.replace(/\{id\}/g, i);
				jQuery("[data-hideId='"+i+"']").removeClass('hide').html(tmp);
				jQuery("[data-hideId='"+i+"'] input[value='dataOnInput']").attr("checked", "checked");

				tmp = jQuery(".databaseInput").html();
				tmp = tmp.replace(/\{id\}/g, i);
				jQuery("[data-field='"+i+"'] .selectedInput[data-selectedInput='"+i+"']").removeClass('hide').html(tmp);
				Object.keys(dataField.field).forEach(function(key) {
					iInputDB++;
					var tmp = jQuery(".databaseInputedData").html();
					var tpl = tmp;
					tpl = tpl.replace(/\{groupId\}/g, i);
					tpl = tpl.replace(/\{id\}/g, iInputDB);
					tpl = tpl.replace(/\{val\}/g, dataField.field[key]);
					jQuery(".inputedData[data-selectInputedData='"+i+"']").append(tpl);
				});
			}
			if(typeof(dataField.selectedData)!=="undefined" && dataField.selectedData=="dataOnTable") {
				var tmp = jQuery(".databaseSelectRadio").html();
				tmp = tmp.replace(/\{id\}/g, i);
				jQuery("[data-hideId='"+i+"']").removeClass('hide').html(tmp);
				jQuery("[data-hideId='"+i+"'] input[value='dataOnTable']").attr("checked", "checked");

				var idE = i;
				
				jQuery.post("./?pages=Creator&loadTables=1", function(d) {
					tmp = jQuery(".databaseTemplate").html();
					tmp = tmp.replace(/\{id\}/g, idE);
					var datas = "";
					selectedData = JSON.parse(d);
					Object.keys(selectedData).forEach(function(k) {
						datas += "<option value='"+selectedData[k].name+"'>"+selectedData[k].name+"</option>";
					});
					tmp = tmp.replace(/\{data\}/g, datas);
					jQuery(".selectedInput[data-selectedInput='"+idE+"']").removeClass('hide').html(tmp);
					jQuery(".dataBaseLoad[data-selectInputedData='"+idE+"']").val(dataField.loadDB.name);

					console.log(".selectedInput[data-selectedInput='"+idE+"']");

					var tmp = jQuery(".databaseTemplateSelect").html();
					var datas = "";
					if(typeof(selectedData[dataField.loadDB.name])!=="undefined") {
						var data = selectedData[dataField.loadDB.name].fields;
						for(var i=0;i<data.length;i++) {
							datas += "<option value='"+data[i]+"'>"+data[i]+"</option>";
						}
					} else {
						setTimeout(function() {
							Swal.fire({
								title: "ВНИМАНИЕ!",
								text: "Не возможно найти таблицу '"+dataField.loadDB.name+"'. Убедитесь, что она создана",
								type: 'error',
								confirmButtonText: 'Отменить и вернуться',
								cancelButtonText: "Продолжить",
								showCancelButton: true,
							}).then(function(resp) {
								if(resp.value===true) {
									window.history.back();
								}
							});
						}, 1000);
					}
					tmp = tmp.replace(/\{data1\}/g, datas);
					tmp = tmp.replace(/\{data2\}/g, datas);
					tmp = tmp.replace(/\{id\}/g, idE);
					jQuery(".dataBaseSelect[data-selectInputedData='"+idE+"']").html(tmp);
					jQuery(".dataBaseSelect[data-selectInputedData='"+idE+"'] .dataBaseLoadKey").val(dataField.loadDB.key);
					jQuery(".dataBaseSelect[data-selectInputedData='"+idE+"'] .dataBaseLoadValue").val(dataField.loadDB.value);
				});
			}
			if(dataField.type=="radio") {
				tmp = jQuery(".databaseInput").html();
				tmp = tmp.replace(/\{id\}/g, i);
				jQuery("[data-field='"+i+"'] .selectedInput[data-selectedInput='"+i+"']").removeClass('hide').html(tmp);
				Object.keys(dataField.field).forEach(function(key) {
					iInputDB++;
					var tmp = jQuery(".databaseInputedData").html();
					var tpl = tmp;
					tpl = tpl.replace(/\{groupId\}/g, i);
					tpl = tpl.replace(/\{id\}/g, iInputDB);
					tpl = tpl.replace(/\{val\}/g, dataField.field[key]);
					jQuery(".inputedData[data-selectInputedData='"+i+"']").append(tpl);
				});
			}
			if(dataField.type=="linkToAdmin") {
				tmp = jQuery(".linkToInput").html();
				tmp = tmp.replace(/\{id\}/g, i);
				tmp = tmp.replace(/\{valLink\}/g, dataField.field.link);
				tmp = tmp.replace(/\{valTitle\}/g, dataField.field.title);
				jQuery("[data-field='"+i+"'] .selectedInput[data-selectedInput='"+i+"']").removeClass('hide').html(tmp);
			}
			if(typeof(dataField.applyInLogin)!=="undefined") {
				jQuery("[data-field='"+i+"'] .applyInLogin input").prop("checked", "checked").attr("checked", "checked");
			}
			if(typeof(dataField.disApplyInReg)!=="undefined") {
				jQuery("[data-field='"+i+"'] .disApplyInReg input").prop("checked", "checked").attr("checked", "checked");
			}
			if(typeof(dataField.translate)!=="undefined" && typeof(dataField.alttitle)!=="undefined") {
				var tmp = jQuery(".inputTranslate").html();
				var tpl = tmp;
				tpl = tpl.replace(/\{id\}/g, i);
				jQuery(".createAltName[data-altname='"+i+"']").html(tpl);
				jQuery(".createAltName[data-altname='"+i+"']").find("input").val(dataField.alttitle);
				arrTranslate[i] = true;
			}
			if(typeof(dataField.children)!=="undefined") {
				builder(dataField.children, parent);
			}
			if(typeof(dataField.notRemove)!=="undefined") {
				$("[data-field='"+i+"']").attr("data-not-remove-panel", "true");
				$("[data-field='"+i+"'] .removeBtn").remove();
				$("[data-field='"+i+"'] .remove").parent().remove();
			} else if(typeof(dataField.disabled)!=="undefined") {
				$("[data-field='"+i+"'] .removeBtn").remove();
				$("[data-field='"+i+"']").attr("data-disabled-panel", "true");
				$("[data-field='"+i+"'] .remove").parent().remove();
				$("[data-field='"+i+"'] :input").attr("readonly", "readonly");
				$("[data-field='"+i+"'] .selectedInput .inputedData a.removeData").remove();
				$("[data-field='"+i+"'] .selectedInput .addInputDB").remove();
				$("[data-field='"+i+"'] .selectedInput .inputedData [data-inputeddata]").each(function(i, elem) {
					$(elem).find(".col-sm-2").remove()
					$(elem).find(".col-sm-10").removeClass('col-sm-10').addClass('col-sm-12');
				});
			} else if(typeof(dataField.notEditable)!=="undefined" || typeof(dataField.onlyAttr)!=="undefined") {
				$("[data-field='"+i+"'] .removeBtn").remove();
				$("[data-field='"+i+"'] .uk-nestable-handle").remove();
				$("[data-field='"+i+"']").attr("data-disabled-panel", "true");
				$("[data-field='"+i+"'] .remove").parent().remove();
				$("[data-field='"+i+"'] :input").attr("readonly", "readonly");
				$("[data-field='"+i+"'] .selectedInput .inputedData a.removeData").remove();
				$("[data-field='"+i+"'] .selectedInput .inputedData [data-inputeddata]").each(function(i, elem) {
					$(elem).find(".col-sm-2").remove()
					$(elem).find(".col-sm-10").removeClass('col-sm-10').addClass('col-sm-12');
				});
				$("[data-field='"+i+"'] .selectedInput .addInputDB").remove();
			}
			cbr_replace();
			if(typeof(dataField.disabled)!=="undefined" || typeof(dataField.notEditable)!=="undefined") {
				$("[data-field='"+i+"'] .cbr-replaced").addClass("cbr-disabled")
			}
			jQuery("input[type='submit']").removeAttr("disabled");
		});
	}
	jQuery(document).ready(function($) {
		if(struct.length>0) {
			struct = JSON.parse(struct);
			if(typeof(struct.data)!=="undefined") {
				console.log("!!! BUILD !!!");
				jQuery(".formCreator input[type='hidden'].mode").val("edit");
				builder(struct.data, "0");
			}
		}
		$("body").on("input", ".altName", function() {
			var regexp = /[^a-zA-Z0-9-_]/g;
			if($(this).val().match(regexp)) {
				$(this).val($(this).val().replace(regexp, ''));
			}
		});
		$("body").on("input", ".altName", function() {
			$(this).attr("data-needTranslate", "false");
		});
		$("body").on("input", ".title", function() {
			if($(this).parents(".col-xs-12 > .uk-nestable-item").find(".altName").attr("data-needTranslate")=="true") {
				$(this).parents(".col-xs-12 > .uk-nestable-item").find(".altName").val(translater(this.value));
				var text = $(this).parents(".col-xs-12 > .uk-nestable-item").find(".alttitle input").val();
				if(typeof(text)!=="undefined") {
					text = text.replace("alt_", "");
					text = text.replace(this.value, "");
					if(text.length==0) {
						$(this).parents(".col-xs-12 > .uk-nestable-item").find(".alttitle input").val("alt_"+translater(this.value)).trigger("input");
					}
				}
			}
		});
		$("body").on("click", "label.repeater", function(e) {
			if($(e.target).parents(".repeater").find("input").attr("readonly")=="readonly") {
				return false;
			}
			e.preventDefault();
			var elem = $(this);
			var input = elem.find("input");
			if(input.attr("checked")) {
				elem.find(".cbr-replaced").removeClass('cbr-checked')
				input.removeAttr('checked');
				input.parents(".uk-nestable-item").addClass('uk-nestable-nochildren')
			} else {
				elem.find(".cbr-replaced").addClass('cbr-checked')
				input.attr("checked", "checked");
				input.parents(".uk-nestable-item").removeClass('uk-nestable-nochildren')
			}
		});
		$("input[required],textarea[required],select[required]").each(function(i, elem) {
			if($(elem).val()==null || $(elem).val().length==0) {
				if(typeof($(elem).parents(".uk-nestable-item").find(".hereTitle + .warn-icon")[0])==="undefined") {
					$(elem).parents(".uk-nestable-item").find(".hereTitle").after("<div class='fa fa-exclamation-triangle warn-icon'></div>");
				}
				$(elem).addClass("errorInput");
			}
		});
		if(struct.length==0) {
			jQuery("input[required],textarea[required],select[required]").trigger("click");
		}
		$("body").on("click", ".cbr-replaced.cbr-disabled", function(e) {
			e.preventDefault();
		});
		$("body").on("click mousedown touchstart touchend", "select[readonly]", function(e) {
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();
		});
		setTimeout(function() {
			jQuery("body").off("submit").on("submit", "form", function(e) {
				if(!$(e.target).hasClass("checker")) {
					e.preventDefault();
					e.stopPropagation();
					var ret = true;
					$("input[required],textarea[required],select[required],input.required,textarea.required,select.required").each(function(i, elem) {
						if($(elem).val()==null || $(elem).val().length==0) {
							if(typeof($(elem).parents(".uk-nestable-item").find(".hereTitle + .warn-icon")[0])==="undefined") {
								$(elem).parents(".uk-nestable-item").find(".hereTitle").after("<div class='fa fa-exclamation-triangle warn-icon'></div>");
							}
							$(elem).addClass("errorInput");
							ret = false;
						}
					});
					console.log(ret);
					if(ret) {
						jQuery(e.target).addClass("checker").trigger("submit");
					}
					return false;
				}
			});
		}, 3000);
	});
	function selectedDatabaseChange(th) {
		var id = jQuery(th).parent().parent().parent().parent().attr("data-hideId");
		var tmp = "";
		if(th.value=="dataOnTable") {
			jQuery.post("./?pages=Creator&loadTables=1", function(d) {}).done(function(d) {
				tmp = jQuery(".databaseTemplate").html();
				tmp = tmp.replace(/\{id\}/g, id);
				var datas = "";
				selectedData = JSON.parse(d);
				Object.keys(selectedData).forEach(function(k) {
					datas += "<option value='"+selectedData[k].name+"'>"+selectedData[k].name+"</option>";
				});
				tmp = tmp.replace(/\{data\}/g, datas);
				jQuery(".selectedInput[data-selectedInput='"+id+"']").removeClass('hide').html(tmp);
			}).error(function(d) {

			});
		} else if(th.value=="dataOnInput") {
			tmp = jQuery(".databaseInput").html();
			tmp = tmp.replace(/\{id\}/g, id);
			jQuery(".selectedInput[data-selectedInput='"+id+"']").removeClass('hide').html(tmp);
		}
	}
	jQuery("body").on("click", ".addInputDB", function() {
		var id = jQuery(this).attr("data-addInputDB");
		iInputDB++;
		var tmp = jQuery(".databaseInputedData").html();
		var tpl = tmp;
		tpl = tpl.replace(/\{groupId\}/g, id);
		tpl = tpl.replace(/\{id\}/g, iInputDB);
		tpl = tpl.replace(/\{val\}/g, "");
		jQuery(".inputedData[data-selectInputedData='"+id+"']").append(tpl);
		return false;
	});
	jQuery("body").on("click", ".removeData", function() {
		var id = jQuery(this).attr("data-id");
		jQuery("[data-inputedData='"+id+"']").remove();
		return false;
	});
	jQuery("body").on("change", ".dataBaseLoad", function() {
		var id = jQuery(this).attr("data-selectInputedData");
		if(this.value=="") {
			jQuery(".dataBaseSelect[data-selectInputedData='"+id+"']").html();
		} else {
			//var data = 
			var tmp = jQuery(".databaseTemplateSelect").html();
			var datas = "";
			var data = selectedData[this.value].fields;
			for(var i=0;i<data.length;i++) {
				datas += "<option value='"+data[i]+"'>"+data[i]+"</option>";
			}
			tmp = tmp.replace(/\{data1\}/g, datas);
			tmp = tmp.replace(/\{data2\}/g, datas);
			tmp = tmp.replace(/\{id\}/g, id);
			jQuery(".dataBaseSelect[data-selectInputedData='"+id+"']").html(tmp);
		}
	});
	jQuery(".creator").on("change", ".selected", function() {
		var id = jQuery(this).attr("data-selectId");
		if(this.value=="multiple-array") {
			var tmp = jQuery(".databaseSelectRadio").html();
			tmp = tmp.replace(/\{id\}/g, id);
			jQuery("[data-hideId='"+id+"']").removeClass('hide').html(tmp);
			cbr_replace();
		} else if(this.value=="array") {
			var tmp = jQuery(".databaseSelectRadio").html();
			tmp = tmp.replace(/\{id\}/g, id);
			jQuery("[data-hideId='"+id+"']").removeClass('hide').html(tmp);
			cbr_replace();
		} else if(this.value=="radio") {
			tmp = jQuery(".databaseInput").html();
			tmp = tmp.replace(/\{id\}/g, id);
			jQuery(".selectedInput[data-selectedInput='"+id+"']").removeClass('hide').html(tmp);
		} else if(this.value=="linkToAdmin") {
			tmp = jQuery(".linkToInput").html();
			tmp = tmp.replace(/\{id\}/g, id);
			tmp = tmp.replace(/\{valLink\}/g, "");
			tmp = tmp.replace(/\{valTitle\}/g, "");
			jQuery(".selectedInput[data-selectedInput='"+id+"']").removeClass('hide').html(tmp);
		} else {
			jQuery(".selectedInput[data-selectedInput='"+id+"']").addClass('hide').html("");
			jQuery("[data-hideId='"+id+"']").addClass('hide').html("");
		}
		$(this).parents(".col-xs-12 > .uk-nestable-item").find(".hereTitle").parent().find("small").remove();
		$(this).parents(".col-xs-12 > .uk-nestable-item").find(".hereTitle").parent().append("<small style='font-size:75%;'>"+(jQuery(this).parent().find("select option[value='"+this.value+"']").text())+"</small>");
	});
	jQuery(".creator").on("click", ".remove", function() {
		var id = jQuery(this).attr("data-id");
		jQuery("[data-field='"+id+"']").remove();
		if(jQuery(".creator").html().trim().length==0) {
			jQuery("input[type='submit']").attr("readonly", "readonly");
		}
		return false;
	});
	jQuery(".addCreator").click(function() {
		i++;
		var tmp = $("#tmpCreate").html();
		var tpl = tmp;
		tpl = tpl.replace(/\{id\}/g, i);
		jQuery(".creator").append(tpl);
		cbr_replace();
		jQuery("input[type='submit']").removeAttr("disabled");
		
		return false;
	});
	jQuery("body").on("click", '.iconSelect', function() {
		var elem = this;
		jQuery.post("./?pages=Creator&list", function(data) {
			jQuery("#modal-3 .modal-body").html(data);
			jQuery('#modal-3').modal('show', {backdrop: 'fade'});
			jQuery('#modal-3').off("input").on("input", ".icon-find", function() {
				var val = jQuery(this).val();
				jQuery(".modal-body a").each(function(i, elem) {
					jQuery(elem).removeClass("hide");
					if(!(new RegExp(val, "g").test(jQuery(elem).attr("data-icon")))) {
						jQuery(elem).addClass("hide");
					}
				});
			});
			jQuery("#modal-3 .modal-body").css("overflow", "auto");
			jQuery("#modal-3 .modal-body").on("click", "a", function() {
				var icon = jQuery(this).attr("data-icon");
				if(icon.length>0) {
					icon = "fa-"+icon;
				}
				jQuery(elem).find("i").attr("class", "").addClass(icon);
				jQuery(elem).find("i").attr("data-icon", icon);
				jQuery(elem).find("input").val(icon);
				jQuery('#modal-3').modal('hide');
			});
		});
		return false;
	});
	function readRec(list, depth, parent_id) {
		for(var i=0;i<list.length;i++) {
			var elem = jQuery(".creator").find('[data-field="'+list[i].field+'"]');
			jQuery(elem).find("input[name*='order']").val((i+1));
			jQuery(elem).find("input[name*='depth']").val(depth);
			jQuery(elem).find("input[name*='parent_id']").val(parent_id ? parent_id : depth);
			console.log(list[i]);
			if(list[i].children) {
				depth++;
				readRec(list[i].children, depth, list[i].field);
			}
		}
	}
	jQuery(".creator").off('nestable-stop').on('nestable-stop', function(ev) {
		var list = jQuery(this).data('nestable').serialize();
		console.log(list);
		readRec(list, 0)
	});
	jQuery("body").on("click", ".togglePanel", function() {
		var id = jQuery(this).attr("data-toggle");
		jQuery("[data-panel]").each(function(i, elem) {
			if(jQuery(elem).attr("data-panel")!=id) {
				jQuery(elem).removeClass('in');
			}
		});
		jQuery("[data-panel='"+id+"']").toggleClass('in');
		return false;
	});
	jQuery("body").on("input change insert", "[data-input-id]", function() {
		var id = jQuery(this).attr("data-input-id");
		jQuery("[data-hereTitle-id='"+id+"']").html(this.value);
	});
	jQuery("body").on("click", "input.errorInput,textarea.errorInput,select.errorInput", function() {
		jQuery(this).removeClass("errorInput");
	});
	jQuery("body").on("click", ".editor", function() {
		jQuery(".routers").toggleClass("hide");
	});
</script>
<style type="text/css">
	.well {
		background-color: #ffffff;
		box-shadow: none;
		position: relative;
	}
	.well:before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		height: 100%;
		width: 4px;
		background: #e7bb1a;
	}
	.swal2-icon {
		width: 8em;
		height: 8em;
	}
	.swal2-icon.swal2-error [class^=swal2-x-mark-line] {
		top: 3.7125em;
		width: 5.9375em;
	}
	.swal2-title {
		font-size: 2.875em;
	}
	.swal2-content {
		font-size: 1.425em;
	}
	.swal2-styled.swal2-confirm {
		font-size: 1.2625em;
	}
	.swal2-styled.swal2-cancel {
		transition: all 300ms ease-in-out;
		font-size: 1.3625em;
	}
	.swal2-popup {
		width: 39em;
	}
	.swal2-styled:focus {
		box-shadow: none;
	}
	.swal2-styled.swal2-confirm:focus {
		background-color: #2566a5;
	}
	.swal2-styled.swal2-cancel:focus {
		background-color: #7d7d7d;
	}
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
