<center><a href="./?pages=DocsAdmin&mod=Add" class="btn btn-secondary">{L_add}</a></center>
<table id="example-1" class="table table-striped table-bordered" cellspacing="0" width="100%">
<thead>
	<tr>
		<th>ID</th>
		<th>Page</th>
		<th>{L_options}</th>
	</tr>
</thead>
<tfoot>
	<tr>
		<th>ID</th>
		<th>Page</th>
		<th>{L_options}</th>
	</tr>
</tfoot>
<tbody>
[foreach block=Docs]<tr>
	<td>{Docs.aId}</td>
	<td>{Docs.page}</td>
	<td>
		<a href="./?pages=DocsAdmin&mod=Edit&viewId={Docs.aId}" class="btn btn-turquoise">{L_edit}</a><br>
		<a href="./?pages=DocsAdmin&mod=Delete&viewId={Docs.aId}" onclick="confirmDelete();" class="btn btn-red">{L_delete}</a>
	</td>
</tr>[/foreach]
</tbody>
</table>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("#example-1").dataTable({
		aLengthMenu: [
			[10, 25, 50, 100, -1], [10, 25, 50, 100, "Всё"]
		],
		"aoColumnDefs": [{
			'bSortable': false,
			'aTargets': [
				2
			]
		}]
	});
});
function confirmDelete() {
	if (confirm("Вы подтверждаете удаление?(Данную операцию невозможно будет обратить)")) {
		return true;
	} else {
		return false;
	}
}
</script>