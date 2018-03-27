<table class="table table-bordered table-striped" id="example-2">
	<thead>
		<tr>
			<th width="100">Данные удалены из раздела</th>
			<th width="70">Время удаления</th>
			<th width="150">IP</th>
			<th width="150">Операции</th>
		</tr>
	</thead>
	
	<tbody class="middle-align">
	[foreach block=trash]
		<tr>
			<td>{trash.tTable}</td>
			<td>{S_langdata="{trash.tTime}","d-m-Y H:i:s",true}</td>
			<td>{trash.tIp}</td>
			<td><a href="./?pages=RecycleBin&recover={trash.tId}" class="btn btn-purple">Восстановить</a></td>
		</tr>
		<tr style="text-align:center;">
			<td colspan="6"><a href="javascript:;" onclick="getDescr(this);return false;" data-table="{trash.tTable}">{L_descr}</a><div class="spoiler-body" style="display:none;">{trash.tData}</div></td>
		</tr>
	[/foreach]
	</tbody>
</table>
<script type="text/javascript">
function getDescr(tt) {
	jQuery(".modal-title").html("Данные из \""+jQuery(tt).attr("data-table")+"\"");
	jQuery('#modal-4').modal('show', {backdrop: 'static'});
	var descr = jQuery(tt).parent().children('.spoiler-body').html();
	descr = JSON.parse(descr);
	var data = "<table width=\"100%\" class=\"table table-bordered table-striped\">";
	Object.keys(descr).forEach(function(k) {
		data += "<tr><td>"+k+"</td><td>"+descr[k]+"</td></tr>";
	});
	data += "</table>";
	jQuery('#error-body').html(data);
}
</script>