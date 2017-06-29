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
							<script type="text/template" id="descr">
								<div class="row">
									<div class="col-sm-11"><textarea id="editor" name="descr[]"></textarea></div>
									<div class="col-sm-1"><a href="#" class="btn btn-red" onclick="return removed(this);">{L_delete}</a></div>
								</div>
							</script>
							<br>
							<a href="#" class="btn findAdd" onclick="return add();">{L_add}</a>
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
<script type="text/javascript">
if(typeof(editorTextarea)!="object") {
	var editorTextarea = {
		selector: 'textarea',
		height: 500,
		language : selectLang,
		plugins: [
			"advlist autolink lists link image charmap print preview anchor",
			"searchreplace visualblocks code fullscreen",
			"insertdatetime media table contextmenu paste imagetools responsivefilemanager"
		],
		toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image responsivefilemanager",
		content_css: [],
		valid_elements : "*[*]",
		forced_root_block : '',
		image_advtab: true, 
		external_filemanager_path: default_admin_link+"assets/xenon/js/tinymce/filemanager/",
		filemanager_title: "{L_"Загрузка файлов"}", 
		external_plugins: { "filemanager" : default_admin_link+"assets/xenon/js/tinymce/filemanager/plugin.min.js"}
	}
}
var template = jQuery("#descr").html();
jQuery("#descr").remove();
var lengthElem = jQuery("textarea").length;
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
	for(var i=1;i<lengthElem+1;i++) {
		tinymce.init(editorTextarea);
	}
});
</script>