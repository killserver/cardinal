<center><a href="./?pages=Archer&type={ArcherTable}&pageType=Add{addition}" class="btn btn-secondary">{L_add}</a>[if {D_DisableSort}!=0]<a href="./?pages=Archer&type={ArcherTable}&pageType=Sort&orderBy={LinkOrderBy}{addition}" class="btn btn-secondary">{L_"Сортировать"}</a>[/if {D_DisableSort}!=0]</center>
<form method="post" action="./?pages=Archer&type={ArcherTable}&pageType=MultiAction">
	<table id="example-1" class="table table-striped table-bordered" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th><label class="checkbox"><input type="checkbox" class="cbr deleteAll"></label></th>
				{ArcherMind}
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><label class="checkbox"><input type="checkbox" class="cbr deleteAll"></label></th>
				{ArcherMind}
			</tr>
			<tr><td colspan="{ArcherAll}"><div class="row"><div class="col-sm-offset-9"><div class="col-sm-7"><select name="action" class="form-control" style="width:100%;"><option value="">{L_"Выберите действие"}</option><option value="delete">{L_delete}</option></select></div><div class="col-sm-5"><input type="submit" class="btn btn-purple" value="{L_"Выполнить"}"></div></div></div></td></tr>
		</tfoot>
		<tbody>
		[foreach block={ArcherPage}]<tr>
			<td><label class="checkbox"><input type="checkbox" class="cbr" name="delete[]" value="{{ArcherPage}.{ArcherFirst}}"></label></td>
			{ArcherData}
			<td>
				<a href="./?pages=Archer&type={ArcherTable}&pageType=Show&viewId={{ArcherPage}.{ArcherFirst}}{addition}" class="btn btn-secondary btn-block">{L_view}</a>
				<a href="./?pages=Archer&type={ArcherTable}&pageType=Edit&viewId={{ArcherPage}.{ArcherFirst}}{addition}" class="btn btn-edit btn-block">{L_edit}</a>
				<a href="./?pages=Archer&type={ArcherTable}&pageType=Delete&viewId={{ArcherPage}.{ArcherFirst}}{addition}" onclick="return confirmDelete();" class="btn btn-red btn-block">{L_delete}</a>
			</td>
		</tr>[/foreach]
		</tbody>
	</table>
</form>
<script type="text/javascript">
jQuery(document).ready(function() {
	var dTable = jQuery("#example-1").dataTable({
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
				0, {ArcherNotTouch}
			]
		}],
		"order": [[ 0, false ], [ {orderById}, "{orderBySort}" ]]
	});
	var sorted = [{ArcherSort}];
	if(sorted.length>0) {
		for(var i=0;i<sorted.length;i++) {
			var th = $("table#example-1").find('th');
			var getId = -1;
			var count = 0;
			th.each(function(is, k) {
				if($(k).attr("data-AltName") == sorted[i] && getId===-1) {
					getId = count;
					return;
				}
				count++;
			});
			dTable.yadcf([{column_number: getId}]);
		}
	}
	if(typeof($.fn.editableform)!=="undefined") {
		$.fn.editableform.buttons = '<button type="submit" class="btn btn-primary btn-sm editable-submit"><i class="fa fa-check"></i></button><button type="button" class="btn btn-default btn-sm editable-cancel"><i class="fa fa-close"></i></button>';
		$('.quickEdit').editable({
			url: '{C_default_http_local}{D_ADMINCP_DIRECTORY}/?pages=Archer&type={ArcherTable}&pageType=QuickEdit&Save=true',
			validate: function(value) {
				if($.trim(value) == '') {
					return '{L_"Данное поле не может быть пустым"}';
				}
			}
		});
	}
	var arrToSave = {};
	var linkForAutoSave = encodeURIComponent(window.location.href.split(default_admin_link)[1])+"&v=1";
	if(localStorage.getItem(linkForAutoSave)===null) {
		$("[aria-controls='example-1'],.yadcf-filter").each(function(i, elem) {
			if(elem.nodeName!=="TH"&&elem.nodeName!=="LI") {
				arrToSave[elem.nodeName.toLowerCase()+"[aria-controls='"+$(elem).attr("aria-controls")+"']"] = elem.value;
			}
		});
		localStorage.setItem(linkForAutoSave, JSON.stringify(arrToSave));
		Object.keys(arrToSave).forEach(function(k) {
			$(k).bind("change input", function() {
				arrToSave[k] = $(this).val();
				localStorage.setItem(linkForAutoSave, JSON.stringify(arrToSave));
			});
		});
	} else {
		var strForAutoSave = localStorage.getItem(linkForAutoSave);
		arrToSave = JSON.parse(strForAutoSave);
		Object.keys(arrToSave).forEach(function(k) {
			$(k).val(arrToSave[k]).change().keyup();
			$(k).bind("change input", function() {
				arrToSave[k] = $(this).val();
				localStorage.setItem(linkForAutoSave, JSON.stringify(arrToSave));
			});
		});
	}
	jQuery(".deleteAll").click(function() {
		jQuery("label.checkbox").click();
	});
	cbr_replace();
});
function confirmDelete() {
	if (confirm("{L_"Вы подтверждаете удаление?(Данную операцию невозможно будет обратить)"}")) {
		return true;
	} else {
		return false;
	}
}
</script>