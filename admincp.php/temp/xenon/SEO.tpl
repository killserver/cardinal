<form method="post" role="form" class="form-horizontal">
	<div class="panel panel-default">
		<div class="panel-heading">Мета-поля</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-12 meta">

				</div>
			</div>
			<a href="#" class="btn btn-blue pull-right addMeta">{L_add}</a>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">Метрика, счётчики и прочие коды</div>
		<div class="panel-body">
			<div class="col-sm-6">
				<label class="col-sm-12" for="text-1">&lt;/head&gt;</label>
				<div class="col-sm-12">
					<textarea class="form-control onlyText" name="head" rows="10" id="text-1">{head}</textarea>
				</div>
			</div>
			<div class="col-sm-6">
				<label class="col-sm-12" for="text-2">&lt;/body&gt;</label>
				<div class="col-sm-12">
					<textarea class="form-control onlyText" name="body" rows="10" id="text-2">{body}</textarea>
				</div>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="row">
			<input type="submit" class="btn btn-single btn-success pull-right" value="{L_submit}">
		</div>
	</div>
</form>
[if {db_connected}==1]<div id="seoBlockANDAText"></div>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">SEO Block &amp; AText</h3>
		<div class="panel-options">
			<a href="#" data-toggle="panel">
				<span class="collapse-icon">–</span>
				<span class="expand-icon">+</span>
			</a>
		</div>
	</div>
	<div class="panel-body">
		<div class="grouping-data"></div>
	</div>
</div>[/if {db_connected}==1]
<style type="text/css">
	.table > tbody > tr[class*='html'] > td {
		padding: 0px;
	}
	.table > tbody > tr.editor > td > div {
		width: 100% !important;
	}
	.form-horizontal .form-group {
		display: block;
		margin-bottom: 15px;
	}
	.form-inline table .input-group, .form-inline table .form-control {
		width: 100%;
	}
	.form-inline table .input-group .input-group-addon {
		width: 2%;
	}
</style>
<script type="text/template" id="temp">
	<div class="form-group id-show-{uid}">
		<label class="col-sm-2 control-label" for="field-{uid}">Мета-поле</label>
		<div class="col-sm-10">
			<div class="col-sm-5">&lt;meta name='...'</div>
			<div class="col-sm-5">content='...' /&gt;</div>
			<div class="col-sm-5">
				<input type="text" class="form-control" name="meta[{uid}][name]" placeholder="name" value="{name}">
			</div>
			<div class="col-sm-5">
				<input type="text" class="form-control" name="meta[{uid}][content]" placeholder="content" value="{content}">
			</div>
			<div class="col-sm-2">
				<a href="#" class="btn btn-red btn-block removed" data-id-show="{uid}">{L_delete}</a>
			</div>
		</div>
	</div>
	<div class="form-group-separator id-show-{uid}"></div>
</script>
<script type="text/template" class="tableGrouping">
	<table id="example-1" class="table table-striped table-bordered" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>ID</th>
				<th>Page</th>
				<th>Lang</th>
				<th width="10%">{L_options}</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>ID</th>
				<th>Page</th>
				<th>Lang</th>
				<th width="10%">{L_options}</th>
			</tr>
			<tr>
				<th colspan="4">
					<div class="row">
						<div class="col-sm-2 col-xs-offset-10">
							<a href="#" class="btn btn-success btn-block addSEO">{L_add}</a>
						</div>
					</div>
				</th>
			</tr>
		</tfoot>
		<tbody>
			{getHTML}
		</tbody>
	</table>
</script>
<script type="text/template" class="tableGroupingData">
	<tr class="get-{id}" data-id="{id}" data-link="{page}" data-lang="{lang}">
		<td>{id}</td>
		<td class="page">{page}</td>
		<td class="lang">{lang}</td>
		<td width="10%">
			<a href="./?pages=SEO&page=Grouping&mod=Delete&viewId={id}" data-id="{id}" class="btn btn-red removeSEO">{L_delete}</a>
		</td>
	</tr>
	<tr class="editor html-{id}">
		<td width="100%" colspan="4"><div style="display:table;"></div></td><td style="display: none"></td><td style="display: none"></td><td style="display: none"></td>
	</tr>
</script>
<script type="text/template" id="descr">
	<div class="row">
		<div class="col-sm-11"><textarea class="form-horizontal" name="aText[descr][]">{descr}</textarea></div>
		<div class="col-sm-1"><a href="#" class="btn btn-red btn-block btn-icon" onclick="return removed(this);"><i class="fa fa-remove"></i></a></div>
	</div>
</script>
<script type="text/template" id="descrAdd">
	<br>
	<a href="#" class="btn findAdd btn-success" onclick="return add();">{L_add}</a>
</script>
<script type="text/template" class="editorMark">
	<div class="panel" data-link="{link}" data-lang="{lang}">
		<div class="panel-heading">{L_"Редактировать страницу"}&nbsp;<span class="lang" data-link="{link}" data-lang="{lang}">{lang}</span><span class="link" data-link="{link}" data-lang="{lang}">{link}</span></div>
		<div class="panel-body">
			<form method="post" role="form" action="./?pages=SEO&mod=Take" method="post" class="form-horizontal" enctype="multipart/form-data" data-link="{link}" data-lang="{lang}" data-id="{id}">
				{editor}
				<button class="btn btn-blue btn-icon btn-icon-standalone btn-icon-standalone-right btn-sm">
					<i class="fa-save"></i>
					<span>{L_save}</span>
				</button>
			</form>
		</div>
	</div>
</script>
<script type="text/template" class="editorSeoBlock">
	<input type='hidden' name='seoBlock[sId]' value='{sId}'>
	<div class="form-group">
		<label class="col-sm-2 control-label" for="field-1">page</label>
		<div class="col-sm-10">
			<div class="input-group">
				<span class="input-group-addon">/</span>
				<input type="text" class="form-control" name="page" value="{pageShow}" data-link="{page}" data-lang="{lang}">
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label" for="field-1">lang</label>
		<div class="col-sm-10">
			<input type='text' class="form-control" name='lang' value='{lang}' data-link="{page}" data-lang="{lang}">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label" for="field-1">sTitle</label>
		<div class="col-sm-10">
			<input type='text' class="form-control" name='seoBlock[sTitle]' value='{sTitle}'>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label" for="field-1">sMetaDescr</label>
		<div class="col-sm-10">
			<input type='text' class="form-control" name='seoBlock[sMetaDescr]' value='{sMetaDescr}'>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label" for="field-1">sMetaRobots</label>
		<div class="col-sm-10">
			<input type='text' class="form-control" name='seoBlock[sMetaRobots]' value='{sMetaRobots}'>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label" for="field-1">sMetaKeywords</label>
		<div class="col-sm-10">
			<input type='text' class="form-control" name='seoBlock[sMetaKeywords]' value='{sMetaKeywords}'>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label" for="field-1">sRedirect</label>
		<div class="col-sm-10">
			<input type='text' class="form-control" name='seoBlock[sRedirect]' value='{sRedirect}'>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label" for="field-1">sImage</label>
		<div class="col-sm-10">
			<input type='text' class="form-control" name='seoBlock[sImage]' value='{sImage}'>
		</div>
	</div>
</script>
<script type="text/template" class="editorAText">
	<input type='hidden' name='aText[aId]' value='{aId}'>
	<div class="form-group">
		<label class="col-sm-2 control-label" for="field-1">text</label>
		<div class="col-sm-10">
			{html}
		</div>
	</div>
</script>
<script type="text/javascript">
	var tableStruct = {
		language: {
			"processing": "{L_"Подождите"}...",
			"search": "{L_"Поиск"}:",
			"lengthMenu": "{L_"Показать"} _MENU_ {L_"записей"}",
			"info": "{L_"Записи с"} _START_ {L_"до"} _END_ {L_"из"} _TOTAL_ {L_"записей"}",
			"infoEmpty": "{L_"Записи с"} 0 {L_"до"} 0 {L_"из"} 0 {L_"записей"}",
			"infoFiltered": "({L_"отфильтровано"} {L_"из"} _MAX_ {L_"записей"})",
			"infoPostFix": "",
			"loadingRecords": "{L_"Загрузка записей"}...",
			"zeroRecords": "{L_"Записи отсутствуют"}.",
			"emptyTable": "{L_"В таблице отсутствуют данные"}",
			"paginate": {
				"first": "{L_"Первая"}",
				"previous": "{L_"Предыдущая"}",
				"next": "{L_"Следующая"}",
				"last": "{L_"Последняя"}"
			},
			"aria": {
				"sortAscending": ": {L_"активировать для сортировки столбца по возрастанию"}",
				"sortDescending": ": {L_"активировать для сортировки столбца по убыванию"}"
			}
		},
		aLengthMenu: [
			[10, 25, 50, 100, -1], [10, 25, 50, 100, "{L_"Всё"}"]
		],
		"aoColumnDefs": [{
			'bSortable': false,
			'aTargets': [
				0
			]
		}],
		"order": [[ 0, false ], [ 0, "desc" ]]
	};
	var template = jQuery("#descr").html();
	jQuery("#descr").remove();
	var lengthElem = 0;
	var jsonData = '{json}';
	var resElems = {};
	jsonData = JSON.parse(jsonData);
	var globId = 0;
	Object.keys(jsonData).forEach(function(lang) {
		Object.keys(jsonData[lang]).forEach(function(uri) {
			var html = "";
			var seoBlockExists = false, aTextExists = false;
			Object.keys(jsonData[lang][uri]).forEach(function(type) {
				var dd = readJsonData(jsonData[lang][uri][type], type, uri, lang);
				html += dd['html'];
				seoBlockExists = dd['seoBlockExists'];
				aTextExists = dd['aTextExists'];
			});
			if(typeof(resElems[lang])==="undefined") {
				resElems[lang] = {};
			}
			if(typeof(resElems[lang][uri])==="undefined") {
				resElems[lang][uri] = {};
			}
			resElems[lang][uri] = {"editor": html, "seoBlockExists": seoBlockExists, "aTextExists": aTextExists};
		});
	});
	function readJsonData(datas, type, key, lang) {
		var seoBlockExists = false, aTextExists = false;
		var hiddenElem = "", html = "";
		if(type=="seoBlock") {
			var html = jQuery(".editorSeoBlock").html();
			Object.keys(datas).forEach(function(k) {
				if(k=="sId") {
					html = html.replace(/\{sId\}/g, datas[k]);
				} else if(k=="sPage") {
					html = html.replace(/\{page\}/g, key);
					html = html.replace(/\{pageShow\}/g, key.substr(1));
				} else if(k=="sLang") {
					html = html.replace(/\{lang\}/g, lang);
				} else if(k=="sTitle") {
					html = html.replace(/\{sTitle\}/g, datas[k]);
				} else if(k=="sMetaDescr") {
					html = html.replace(/\{sMetaDescr\}/g, datas[k]);
				} else if(k=="sMetaRobots") {
					html = html.replace(/\{sMetaRobots\}/g, datas[k]);
				} else if(k=="sMetaKeywords") {
					html = html.replace(/\{sMetaKeywords\}/g, datas[k]);
				} else if(k=="sRedirect") {
					html = html.replace(/\{sRedirect\}/g, datas[k]);
				} else if(k=="sImage") {
					html = html.replace(/\{sImage\}/g, datas[k]);
				}
			});
			seoBlockExists = true;
		} else if(type=="aText") {
			var html = jQuery(".editorAText").html();
			Object.keys(datas).forEach(function(k) {
				if(k=="text") {
					var temp = "";
					for(var i=0;i<datas[k].length;i++) {
						var descr = datas[k][i];
						var tmp = template;
						tmp = tmp.replace(/\{descr\}/g, descr);
						tmp += jQuery("#descrAdd").html();
						temp += tmp;
					}
					html = html.replace(/\{html\}/g, temp);
				} else if(k=="aId") {
					html = html.replace(/\{aId\}/g, datas[k]);
				}
			});
			aTextExists = true;
		}
		return {"html": html, "seoBlockExists": seoBlockExists, "aTextExists": aTextExists};
	}
	var html = "", editDatas = "";
	var idSEO = 0;
	Object.keys(jsonData).forEach(function(lang) {
		Object.keys(jsonData[lang]).forEach(function(uri) {
			idSEO++;
			var tmp = jQuery(".tableGroupingData").html();
			tmp = tmp.replace(/\{id\}/g, idSEO);
			tmp = tmp.replace(/\{page\}/g, uri);
			tmp = tmp.replace(/\{lang\}/g, lang);
			html = tmp;
		});
	});
	var tmp = jQuery(".tableGrouping").html();
	tmp = tmp.replace(/\{getHTML\}/g, html);
	jQuery(".grouping-data").html(tmp);
	jQuery(document).ready(function() {
		jQuery(".grouping-data table").dataTable(tableStruct);
	});
	var isI = 0;
	var metaData = {meta};
	if(metaData.length>0) {
		for(var i=0;i<metaData.length;i++) {
			isI++;
			var tmp = jQuery("#temp").html();
			tmp = tmp.replace(/{uid}/g, isI);
			tmp = tmp.replace(/{name}/g, metaData[i].name);
			tmp = tmp.replace(/{content}/g, metaData[i].content);
			jQuery(".meta").append(tmp);
		}
	}
	jQuery(".addMeta").on("click", function() {
		isI++;
		var tmp = jQuery("#temp").html();
		tmp = tmp.replace(/{uid}/g, isI);
		tmp = tmp.replace(/{name}/g, "");
		tmp = tmp.replace(/{content}/g, "");
		jQuery(".meta").append(tmp);
		return false;
	});
	jQuery("body").on("click", ".removed", function(ev) {
		jQuery(".id-show-"+jQuery(this).attr("data-id-show")).remove();
		return false;
	});
	jQuery(".table > tbody > tr.editor td div").hide();
	jQuery("body").on("click", ".table > tbody > tr[class*='get-']", function(ev) {
		var id = jQuery(this).attr("data-id");
		var link = jQuery(this).attr("data-link");
		var lang = jQuery(this).attr("data-lang");
		var getElem = jQuery(".table > tbody > tr.html-"+id+".editor td > div");
		if(getElem.html().length==0) {
			var html = "";
			if(typeof(resElems[lang])!=="undefined" && typeof(resElems[lang][link])!=="undefined" && typeof(resElems[lang][link].editor)!=="undefined") {
				var tmp = jQuery(".editorMark").html();
				tmp = tmp.replace(/\{id\}/g, id);
				tmp = tmp.replace(/\{link\}/g, link);
				tmp = tmp.replace(/\{lang\}/g, lang);
				tmp = tmp.replace(/\{editor\}/g, resElems[lang][link].editor);
				html += tmp;
			} else {
				var tmp = jQuery(".editorMark").html();
				tmp = tmp.replace(/\{id\}/g, id);
				tmp = tmp.replace(/\{link\}/g, link);
				tmp = tmp.replace(/\{lang\}/g, lang);
				var editor = jQuery(".editorSeoBlock").html();
				var editorAText = jQuery(".editorAText").html();
				var tpl = template;
				tpl = tpl.replace(/\{descr\}/g, "");
				tpl += jQuery("#descrAdd").html();
				editorAText = editorAText.replace(/\{html\}/g, tpl);
				tmp = tmp.replace(/\{editor\}/g, editor+editorAText);
				tmp = tmp.replace(/\{(.+?)\}/g, "");
				html += tmp;
			}
			getElem.html(html);
			var editor = jQuery.extend(editorTextarea, {selector: ".table > tbody > tr.html-"+id+".editor td > div textarea"});
			tinymce.init(editor);
		}
		if(getElem.css("display")=="none" || getElem.css("display")=="block") {
			getElem.toggle(600);
		} else {
			getElem.attr("style", "");
		}
		return false;
	});
	jQuery("body").on("input", "form[data-link] input.form-control[name='page']", function(e) {
		var elem = jQuery(this);
		var page = elem.attr("data-link");
		var lang = elem.attr("data-lang");
		jQuery(".panel[data-link='"+page+"'][data-lang='"+lang+"'] span.link").html("/"+elem.val());
		jQuery("tr[data-link='"+page+"'][data-lang='"+lang+"'] td.page").html("/"+elem.val());
	});
	jQuery("body").on("input", "form[data-link] input.form-control[name='lang']", function(e) {
		var elem = jQuery(this);
		var page = elem.attr("data-link");
		var lang = elem.attr("data-lang");
		var text = elem.val();
		if(text.length==0) {
			text = "";
		} else {
			text = "/"+text;
		}
		jQuery(".panel[data-link='"+page+"'][data-lang='"+lang+"'] span.lang").html(text);
		jQuery("tr[data-link='"+page+"'][data-lang='"+lang+"'] td.lang").html(text);
	});
	jQuery("body").on("submit", ".panel form", function(event) {
		var id = jQuery(this).attr("data-id");
		var link = jQuery(this).attr("data-link");
		var lang = jQuery(this).attr("data-lang");
		var getElem = jQuery(".table > tbody > tr.html-"+id+".editor td > div");
		getElem.toggle(600);
		saveData(id, lang, link, jQuery(this).serialize());
		/*jQuery(".grouping-data table").dataTable().fnDestroy();
		jQuery(".grouping-data table").dataTable(tableStruct);*/
		return false;
	});
	function add() {
		tmp = jQuery(template.trim());
		lengthElem++;
		tmp.find("textarea").attr("id", tmp.find("textarea").attr("id")+""+lengthElem).val("");
		jQuery(".findAdd").before(tmp);
		tinymce.init(editorTextarea);
		return false;
	}
	jQuery("body").on("click", ".addSEO", function() {
		idSEO++;
		var tmp = jQuery(".tableGroupingData").html();
		tmp = tmp.replace(/\{id\}/g, idSEO);
		tmp = tmp.replace(/\{page\}/g, "");
		tmp = tmp.replace(/\{lang\}/g, "");
		jQuery(".grouping-data tbody").append(tmp);
		return false;
	});
	jQuery("body").on("click", ".removeSEO", function() {
		var id = jQuery(this).attr("data-id");
		jQuery(".grouping-data tbody").find("tr.get-"+id).remove();
		jQuery(".grouping-data tbody").find("tr.html-"+id+".editor").remove();
		return false;
	});
	function saveData(id, lang, link, data) {
		jQuery.post("./?pages=SEO&merge=1", "langOR="+lang+"&linkOR="+link+"&"+data, function(d){});
	}
</script>