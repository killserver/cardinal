[!ajax]<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{ArcherMind}</h3>
			</div>
			<div class="panel-body">
				<form method="post" role="form" action="./?pages=Archer&type={ArcherPath}&pageType=Take{ArcherPage}{addition}" class="form-horizontal" enctype="multipart/form-data">[/!ajax]
					{ArcherData}
					[!ajax]<button class="btn btn-savePage btn-icon btn-icon-standalone btn-icon-standalone-right btn-sm">
						<i class="fa-save"></i>
						<span>{L_save}</span>
					</button>
				</form>
			</div>
		</div>
	</div>
</div>[/!ajax]
<style>
#inputForFile .array {
	padding-bottom: 2.5em;
	border-bottom: 0.1em solid #e4e4e4;
	margin-bottom: 2em;
	display: inline-block;
	width: 100%;
}
#inputForFile .array:last-of-type {
    border-bottom: 0px
}
</style>
<script type="text/javascript">
[ajax]var linkForSubmit = "./?pages=Archer&type={ArcherPath}&pageType=Take{ArcherPage}{addition}";[/ajax]
var i = 1;
function removeInputFile(th, name, val) {
	var bef = jQuery('input[name="deleteArray['+name+']"]').val();
	jQuery('input[name="deleteArray['+name+']"]').val(val+","+bef);
	jQuery(th).parent().parent().remove();
}
jQuery(document).ready(function() {
	jQuery(".showPreview").each(function(i, elem) {
		jQuery(elem).after("<br><img src='"+jQuery(elem).attr("href")+"' width='200'>");
	});
	jQuery("body").on("click", ".accessRemove", function() {
		var count = jQuery(".containerFiles[data-parent='"+jQuery(this).attr("data-parent")+"'] input").length;
		if(count==1) {
			jQuery(this).parent().parent().find("input").val("");
			jQuery(this).parent().parent().find("img").remove();
			jQuery(this).parent().parent().find("a[data-link]").remove();
		} else {
			jQuery(this).parent().parent().remove();
		}
		return false;
	});
});
function addInputFile(th, name) {
	var elem = jQuery(th).parent().find("div#inputForFile");
	elem.append('<div><div class="col-sm-10"><input class="form-control" type="file" multiple="multiple"'+(elem.attr("data-accept") ? 'accept="'+elem.attr("data-accept")+'"' : "")+' name="'+name+'[]" placeholder="Выберите файл"></div><div class=\'col-sm-2\'><a class=\'btn btn-red btn-block fa-remove\' onclick=\'jQuery(this).parent().parent().remove();\'></a></div></div>');
	jQuery("input[type='file'][accept*='image']").unbind("change").change(function() {
		readURL(this);
	});
	i++;
}
function readURL(input) {
	if(input.files && input.files[input.files.length-1]) {
		var reader = new FileReader();
		reader.onload = function(e) {
			jQuery(input).parent().find(".tmpImage").remove();
			jQuery(input).parent().append('<div class="tmpImage" style="background: #333; display: table;"><img src="'+e.target.result+'" width="200" style="opacity: 0.75;"></div>');
		}
		reader.readAsDataURL(input.files[input.files.length-1]);
	}
}
jQuery("input[type='file'][accept*='image']").change(function() {
	readURL(this);
});
</script>