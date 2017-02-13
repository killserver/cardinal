<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{ArcherMind}</h3>
			</div>
			<div class="panel-body">
				<form method="post" role="form" action="./?pages=Archer&type={ArcherPath}&pageType=Take{ArcherPage}" method="post" class="form-horizontal" enctype="multipart/form-data">
					{ArcherData}
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
function addInputFile(name) {
	jQuery("span#inputForFile").append('<input class="form-control" type="file" name="'+name+'[]" placeholder="{L_"Выберите"} {L_"файл"}">');
}
</script>