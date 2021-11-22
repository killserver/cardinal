[if {C_disableAdd}!=1&&{C_disableProfileAdd}!=1]<center><a href="./?pages=ProfileSettings&Add" class="btn btn-secondary">{L_add}</a><a href="./?pages=ProfileSettings&Settings" class="btn btn-purple pull-right">{L_"Настроить поля"}</a></center>[/if {C_disableAdd}!=1&&{C_disableProfileAdd}!=1]
{E_[customHeaders][type={ArcherTable}]}
<form method="post" action="./?pages=Archer&type={ArcherTable}&pageType=MultiAction" class="form_profile_list">
	<table id="example-1" class="table table-striped table-bordered" cellspacing="0" width="100%">
		<thead>
			{head}
		</thead>
		<tfoot>
			{head}
		</tfoot>
		<tbody>
		{body}
		</tbody>
	</table>
</form>
{E_[KernalArcher::AfterMain][table={ArcherTable};type=not_ajax]}
<script type="text/javascript">
var dTable;
var settingDataTable = {
	"sScrollX": "100%",
	"scrollY": false,
	"sScrollXInner": "110%",
	"bScrollCollapse": true,
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
};
jQuery(document).ready(function() {
	dTable = jQuery("#example-1").dataTable(settingDataTable);
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
	if(typeof($.fn.editableform)!=="undefined") {
		console.log("Test");
		$.fn.editableform.buttons = '<button type="submit" class="btn btn-primary btn-sm editable-submit"><i class="fa fa-check"></i></button><button type="button" class="btn btn-default btn-sm editable-cancel"><i class="fa fa-close"></i></button>';
		$('.quickEdit span').editable({
			url: '{C_default_http_local}{D_ADMINCP_DIRECTORY}/?pages=Archer&type={ArcherTable}&pageType=QuickEdit&Save=true',
			validate: function(value) {
				if($.trim(value) == '') {
					return '{L_"Данное поле не может быть пустым"}';
				}
			}
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
<style>
	table.dataTable td, 
	table.dataTable th {
		white-space: nowrap;
	}
</style>