<table id="example-1" class="table table-striped table-bordered" cellspacing="0" width="100%">
<thead>
	<tr>
		<th>{L_original}</th>
		<th>{L_translates}</th>
		<th>{L_options}</th>
	</tr>
</thead>
<tfoot>
	<tr>
		<th>{L_original}</th>
		<th>{L_translates}</th>
		<th>{L_options}</th>
	</tr>
</tfoot>
<tbody>
[foreach block=langList]<tr class="language_element">
	<td class="col-md-5 original_languages">{langList.or}</td>
	<td class="col-md-5"><textarea class="changed col-md-8" name="{langList.or}" style="height:100px;">{langList.lang}</textarea></td>
	<td class="col-md-2">
		<a href="./?pages=Languages&" onclick="return confirmClear(this);" class="btn btn-red btn-sm">{L_"Сбросить"}</a>
	</td>
</tr>[/foreach]
</tbody>
</table>
<div class="btn btn-secondary add" onclick="AddToLang()">{L_"Добавить перевод"}</div>
<script type="text/javascript">
var disableAllEditors = true;
function confirmClear(th) {
	if (confirm("{L_"Вы подтверждаете сброс?(Данную операцию невозможно будет обратить)"}")) {
		var ret = new FormData();
		ret.append("orLang", jQuery(th).parents(".language_element").find(".original_languages").html());
		jQuery.ajax({
			url: "./?pages=Languages&lang={initLang}&resetLang=true",
			data: ret,
			processData: false,
			contentType: false,
			type: 'POST',
			success: function(data) {
				toastr.options = {
					"closeButton": false,
					"debug": false,
					"newestOnTop": false,
					"progressBar": false,
					"positionClass": "toast-top-right",
					"preventDuplicates": false,
					"onclick": null,
					"showDuration": "300",
					"hideDuration": "1000",
					"timeOut": "5000",
					"extendedTimeOut": "1000",
					"showEasing": "swing",
					"hideEasing": "linear",
					"showMethod": "fadeIn",
					"hideMethod": "fadeOut"
				}
				toastr.success("{L_"Успешно удалили элемент из языковой панели"}");
				jQuery(th).parents(".language_element").remove();
			}
		});
	}
	return false;
}
function saveLang(th) {
	var elem = jQuery(th).parent().parent();
	var ret = new FormData();
	$.each($(elem).find(':input'), function() {
		ret.append(this.name, $(this).val());
	});
	jQuery.ajax({
		url: "./?pages=Languages&lang={initLang}&saveLang=true",
		data: ret,
		processData: false,
		contentType: false,
		type: 'POST',
		success: function(data) {
			jQuery(elem).find("input").each(function() {
				jQuery(this).parent().html(jQuery(this).val());
			});
			jQuery(elem).find("textarea").each(function() {
				jQuery(this).addClass("changed");
			});
			jQuery(elem).find(".save").after('<a href="./?pages=Languages&" onclick="confirmClear();" class="btn btn-red btn-sm">{L_"Сбросить"}</a>').remove();
			toastr.options = {
				"closeButton": false,
				"debug": false,
				"newestOnTop": false,
				"progressBar": false,
				"positionClass": "toast-top-right",
				"preventDuplicates": false,
				"onclick": null,
				"showDuration": "300",
				"hideDuration": "1000",
				"timeOut": "5000",
				"extendedTimeOut": "1000",
				"showEasing": "swing",
				"hideEasing": "linear",
				"showMethod": "fadeIn",
				"hideMethod": "fadeOut"
			}
			if(data=="1") {
				toastr.success("{L_"Успешно добавили элемент в языковую панель"}");
			} else {
				toastr.error("{L_"Произошла ошибка при попытке добавить элемент в языковую панель!"}");
			}
		}
	});
	console.log(ret);
}
function AddToLang() {
	console.log(jQuery("#example-1").find("tr").last().after("<tr><td class=\"col-md-5\"><input type=\"text\" name=\"orLang\" class=\"col-md-12\"></td><td class=\"col-md-5\"><textarea class=\"col-md-8\" name=\"translate\" style=\"height:100px;\"></textarea></td><td class=\"col-md-2\"><a href=\"#\" onclick=\"saveLang(this);return false;\" class=\"btn btn-secondary btn-sm save\">{L_"Сохранить"}</a></td></tr>"));
}
jQuery(".changed").change(function() {
	var orig = jQuery(this).parents(".language_element").find(".original_languages").html();
	var translate = jQuery(this).val();

	var ret = new FormData();
	ret.append("orLang", orig);
	ret.append("translate", translate);
	jQuery.ajax({
		url: "./?pages=Languages&lang={initLang}&saveLang=true",
		data: ret,
		processData: false,
		contentType: false,
		type: 'POST',
		success: function(data) {
			toastr.options = {
				"closeButton": false,
				"debug": false,
				"newestOnTop": false,
				"progressBar": false,
				"positionClass": "toast-top-right",
				"preventDuplicates": false,
				"onclick": null,
				"showDuration": "300",
				"hideDuration": "1000",
				"timeOut": "5000",
				"extendedTimeOut": "1000",
				"showEasing": "swing",
				"hideEasing": "linear",
				"showMethod": "fadeIn",
				"hideMethod": "fadeOut"
			}
			if(data=="1") {
				toastr.success("{L_"Успешно отредактировали элемент в языковой панели"}");
			} else {
				toastr.error("{L_"Произошла ошибка при попытке отредактировать элемент в языковой панели!"}");
			}
		}
	});
});
</script>
<style>
	table.dataTable td, 
	table.dataTable th {
		white-space: nowrap;
	}
</style>
<script>
jQuery(document).ready(function($){	
	jQuery("#example-1").dataTable({
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
				2
			]
		}]
	});
});
</script>