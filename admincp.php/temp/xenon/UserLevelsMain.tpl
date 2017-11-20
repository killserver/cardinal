<center><a href="./?pages=UserLevels&mod=Add" class="btn btn-secondary">{L_add}</a></center>
<table id="example-1" class="table table-striped table-bordered" cellspacing="0" width="100%">
<thead>
	<tr>
		<th>ID</th>
		<th>Название уровня</th>
		<th>Доступов</th>
		<th>{L_options}</th>
	</tr>
</thead>
<tfoot>
	<tr>
		<th>ID</th>
		<th>Доступов</th>
		<th>{L_options}</th>
	</tr>
</tfoot>
<tbody>
[foreach block=userlevelsList]<tr>
	<td class="col-md-1">{userlevelsList.id}</td>
	<td class="col-md-5">{userlevelsList.name}<br><small style="font-size:75%;">({userlevelsList.orName})</small></td>
	<td class="col-md-1">{userlevelsList.counts}</td>
	<td class="col-md-2">
		<a href="./?pages=UserLevels&mod=Edit&id={userlevelsList.id}" class="btn btn-edit btn-block btn-sm">{L_"Редактировать"}</a>
		<a href="./?pages=UserLevels&mod=Delete" onclick="return confirmClear(this);" class="btn btn-red btn-block btn-sm" disabled="disabled">{L_"Сбросить"}</a>
	</td>
</tr>[/foreach]
</tbody>
</table>
<script>
function confirmClear() {
	if (confirm("{L_"Вы подтверждаете сброс?(Данную операцию невозможно будет обратить)"}")) {
		return true;
	} else {
		return false;
	}
}
jQuery(document).ready(function($){	
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
</script>