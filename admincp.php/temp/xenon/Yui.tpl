<div class="Yui row">
	<div class="page col-sm-12">
		<form role="form" class="form-horizontal" action="{C_default_http_local}{D_ADMINCP_DIRECTORY}/?pages=Yui&save=1" method="post">
			<div class="panel panel-default">
				<div class="panel-heading"><!--input type="text" class="link col-sm-11 input-lg" placeholder="Титулка"><span class=" col-sm-1"><span class="remove btn btn-red btn-block">x</span></span-->Инструкция по использованию админ-панелью</div>
				<div class="panel-body thisInfo">
					<a href="#" class="btn btn-icon btn-primary addInfo"><i class="fa-plus"></i></a>
				</div>
			</div>
			<input type="submit">
		</form>
	</div>
</div>
<script type="text/template" id="added">
<div class="panel panel-default panel-border panel-shadow">
	<div class="panel-body">
		<div class="col-sm-11">
			<div class="form-group">
				<label class="col-sm-2">Путь</label>
				<div class="col-sm-12">
					<div class="col-sm-12"><small>{C_default_http_local}{D_ADMINCP_DIRECTORY}/</small></div>
					<div class="col-sm-12">
						<input type="text" class="form-control link" name="link[]" placeholder="Ссылка">
					</div>
				</div>
			</div>
			<div class="form-group-separator"></div>
			<div class="form-group">
				<label class="col-sm-2">Элемент</label>
				<div class="col-sm-12">
					<span class="col-sm-2 control-label">jQuery(</span><span class="col-sm-9"><input type="text" class="form-control selector" name="selector[]" placeholder="selector"></span><span class="col-sm-1">)</span>
				</div>
			</div>
			<div class="form-group-separator"></div>
			<div class="form-group">
				<label class="col-sm-2">Заголовок</label>
				<div class="col-sm-12">
					<input type="text" class="form-control title" name="title[]" placeholder="Заголовок">
				</div>
			</div>
			<div class="form-group-separator"></div>
			<div class="form-group">
				<label class="col-sm-2">Описание</label>
				<div class="col-sm-12">
					<textarea class="form-control descr" name="descr[]" placeholder="Описание действий"></textarea>
				</div>
			</div>
		</div>
		<div class="col-sm-1 removeStep"><span class="remove btn btn-red btn-block">x</span></div>
	</div>
</div>
</script>
<script>
var dataList = '{dataList}';
if(dataList.length>0) {
	var parentElem = $(".addInfo");
	var dataElem = JSON.parse(dataList);
	for(var i=0;i<dataElem.length;i++) {
		var chld = jQuery(jQuery("#added").html());
		chld.find(".selector").val(dataElem[i].selector);
		chld.find(".link").val(dataElem[i].link);
		chld.find(".title").val(dataElem[i].title);
		chld.find(".descr").val(dataElem[i].descr);
		parentElem.before(chld);
	}
}
var disableAllEditors = true;
$(document).ready(function() {
	$(".addInfo").click(function() {
		$(this).before(jQuery("#added").html());
		return false;
	});
	$("body").on("click", ".removeStep", function() {
		$(this).parent().parent().remove();
	});
});
</script>