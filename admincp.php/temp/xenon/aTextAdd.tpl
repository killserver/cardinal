<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{L_"Редактор текста на странице"}</h3>
			</div>
			<div class="panel-body">
				<form method="post" role="form" action="./?pages=ATextAdmin&mod=Take{typePage}" method="post" class="form-horizontal" enctype="multipart/form-data">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="field-1">{L_"Страница"}</label>
						<div class="col-sm-10">
							<input type="text" name="page" class="form-control" id="field-1" value="{page}">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="field-1">{L_descr}</label>
						<div class="col-sm-10">
							{textarea}
							<br>
							<a href="#" class="btn findAdd btn-success" onclick="return add();">{L_add}</a>
						</div>
					</div>
					<button class="btn btn-blue btn-icon btn-icon-standalone btn-icon-standalone-right btn-sm">
						<i class="fa-save"></i>
						<span>{L_save}</span>
					</button>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/template" id="descr">
	<div class="row">
		<div class="col-sm-11"><textarea id="editor" name="descr[]"></textarea></div>
		<div class="col-sm-1"><a href="#" class="btn btn-red btn-block btn-icon" onclick="return removed(this);"><i class="fa fa-remove"></i></a></div>
	</div>
</script>
<script type="text/javascript">
var template;
var lengthElem = 0;
function removed(th) {
	jQuery(th).parent().parent().remove();
	return false;
}
function add() {
	tmp = jQuery(template.trim());
	lengthElem++;
	tmp.find("textarea").attr("id", tmp.find("textarea").attr("id")+""+lengthElem);
	jQuery(".findAdd").before(tmp);
	tinymce.init(editorTextarea);
	return false;
}
jQuery(document).ready(function() {
	template = jQuery("#descr").html();
	jQuery("#descr").remove();
	lengthElem = jQuery("textarea").length;
	setTimeout(function() {
		for(var i=1;i<lengthElem+1;i++) {
			tinymce.init(editorTextarea);
		}
	}, 1000);
});
</script>