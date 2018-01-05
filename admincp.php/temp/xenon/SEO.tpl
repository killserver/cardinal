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
					<textarea class="form-control" name="head" rows="10" id="text-1">{head}</textarea>
				</div>
			</div>
			<div class="col-sm-6">
				<label class="col-sm-12" for="text-2">&lt;/body&gt;</label>
				<div class="col-sm-12">
					<textarea class="form-control" name="body" rows="10" id="text-2">{body}</textarea>
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
<script type="text/javascript">
	var disableAllEditors = true;
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
</script>