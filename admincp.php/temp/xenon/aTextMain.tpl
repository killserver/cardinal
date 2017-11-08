<center><a href="./?pages=ATextAdmin&mod=Add" class="btn btn-secondary">{L_add}</a></center>
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
[foreach block=aText]<tr>
	<td>{aText.aId}</td>
	<td>{aText.page}</td>
	<td>
		<a href="./?pages=ATextAdmin&mod=Edit&viewId={aText.aId}" class="btn btn-turquoise">{L_edit}</a><br>
		<a href="./?pages=ATextAdmin&mod=Delete&viewId={aText.aId}" onclick="return confirmDelete();" class="btn btn-red">{L_delete}</a>
	</td>
</tr>[/foreach]
</tbody>
</table>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("#example-1").dataTable({
		language: {
			"processing": "{L_"Подождите"}...",
			"search": "{L_"Поиск"}:",
			"lengthMenu": "{L_"Показать"} _MENU_ {L_"записей"}",
			"info": "{L_"Записи с"} _START_ {L_"до"} _END_ {L_"из"} _TOTAL_ {L_"записей"}",
			"infoEmpty": "{L_"Записи с"} 0 {L_"до"} 0 {L_"из"} 0 {L_"записей"}",
			"infoFiltered": "({L_"отфильтровано"} {L_"из"} _MAX_ {L_"записей"})",
			"infoPostFix": "",
			"loadingRecords": "{L_"Загрузка записей"}...",
			"zeroRecords": "{L_"Записи отсутствуют"}.",
			"emptyTable": "{L_"В таблице отсутствуют данные"}",
			"paginate": {
				"first": "{L_"Первая"}",
				"previous": "{L_"Предыдущая"}",
				"next": "{L_"Следующая"}",
				"last": "{L_"Последняя"}"
			},
			"aria": {
				"sortAscending": ": {L_"активировать для сортировки столбца по возрастанию"}",
				"sortDescending": ": {L_"активировать для сортировки столбца по убыванию"}"
			}
		},
		aLengthMenu: [
			[10, 25, 50, 100, -1], [10, 25, 50, 100, "{L_"Всё"}"]
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