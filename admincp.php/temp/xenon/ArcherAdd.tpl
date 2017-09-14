<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{ArcherMind}</h3>
			</div>
			<div class="panel-body">
				<form method="post" role="form" action="./?pages=Archer&type={ArcherPath}&pageType=Take{ArcherPage}{addition}" method="post" class="form-horizontal" enctype="multipart/form-data">
					{ArcherData}
					<button class="btn btn-savePage btn-icon btn-icon-standalone btn-icon-standalone-right btn-sm">
						<i class="fa-save"></i>
						<span>{L_save}</span>
					</button>
				</form>
			</div>
		</div>
	</div>
</div>
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
var i = 1;
function removeInputFile(th, name, val) {
	var bef = $('input[name="deleteArray['+name+']"]').val();
	$('input[name="deleteArray['+name+']"]').val(val+","+bef);
	$(th).parent().parent().remove();
}
$(document).ready(function() {
	$(".showPreview").each(function(i, elem) {
		$(elem).after("<br><img src='"+$(elem).attr("href")+"' width='200'>");
	})
});
function addInputFile(name) {
	jQuery("span#inputForFile").append('<div><div class="col-sm-10"><input class="form-control" type="file" multiple="multiple" name="'+name+'[]" placeholder="{L_"Выберите"} {L_"файл"}"></div><div class=\'col-sm-2\'><a class=\'btn btn-red btn-block fa-remove\' onclick=\'$(this).parent().parent().remove();\'></a></div></div>');
	i++;
}
</script>