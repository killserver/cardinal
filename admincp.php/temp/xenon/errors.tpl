<table class="table table-bordered table-striped" id="example-2">
	<thead>
		<tr>
			<td colspan="6" align="right"><form method="post" action="{C_default_http_host}admincp.php/?pages=Logs&delete"><button class="btn btn-red btn-icon btn-icon-standalone"><i class="fa-remove"></i><span>{L_"Удалить всё"}</span></button></form></td>
		</tr>
		<tr>
			<th width="100">{L_"Дата"}/{L_"Время"}</th>
			<th width="110">{L_"Тип ошибки"}</th>
			<th>{L_"Описание ошибки"}</th>
			<th>{L_"Файл"}</th>
			<th width="70">{L_"Строка"}</th>
			<th width="150">IP</th>
		</tr>
	</thead>
	
	<tbody class="middle-align">
		[foreach block=logs]
			<tr>
				<td>{logs.time}</td>
				<td>{logs.errorno}</td>
				<td>{logs.error}</td>
				<td>{logs.path}</td>
				<td>{logs.line}</td>
				<td>{logs.ip}</td>
			</tr>
			<tr style="text-align:center;">
				<td colspan="6"><a href="javascript:;" onclick="getDescr(this);return false;">{L_descr}</a><div class="spoiler-body" style="display:none;"><pre>{logs.descr}</pre></div></td>
			</tr>
		[/foreach]
		[if {count[logs]}==0]
			<tr style="text-align:center;">
				<td colspan="6">{L_"Ошибок в системе не обнаружено"}</td>
			</tr>
		[/if {count[logs]}==0]
	</tbody>
</table>
<script type="text/javascript">
function getDescr(tt) {
	jQuery('#modal-4').modal('show', {backdrop: 'static'});
	var descr = jQuery(tt).parent().children('.spoiler-body').html();
	jQuery('#error-body').html(descr);
}
jQuery(document).ready(function() {
	jQuery(".modal .modal-dialog .modal-content .modal-footer .btn").html("{L_"Закрыть"}");
})
</script>
<style type="text/css">
	pre br+br {
		display: none;
	}
</style>