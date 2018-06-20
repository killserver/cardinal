<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{L_"Редактор прав доступа"}</h3>
			</div>
			<div class="panel-body">
				<form method="post" role="form" action="./?pages=UserLevels&mod={typePage}" method="post" class="form-horizontal" enctype="multipart/form-data">
					<div class="form-group">
						<label class="col-sm-3 control-label" for="field-1">{L_"Название уровня доступа"}</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="field-1" value="{name}"[if {isSystem}=="yes"] disabled="disabled"[/if {isSystem}=="yes"]>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group">
							[foreach block=levelChange]<div class="col-xs-12 col-md-4">
								<div class="form-group">
									<label class="col-xs-8 col-md-10 control-label" for="field-{levelChange.$id}">{L_"Доступ к"}&nbsp;"{L_"{levelChange.level}"}"</label>
									<div class="col-xs-4 col-md-2">
										<input type="hidden" name="userlevels[access_{levelChange.name}]" value="no">
										<input type="checkbox" id="field-{levelChange.$id}" name="userlevels[access_{levelChange.name}]" class="iswitch iswitch-primary" value="yes"[foreachif {levelChange.checked}=="yes"] checked="checked"[/foreachif {levelChange.checked}=="yes"]>
									</div>
								</div>
							</div>[/foreach]
						</div>
					</div>
					<button class="btn btn-blue btn-icon btn-icon-standalone btn-icon-standalone-right btn-sm pull-right">
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
</script>