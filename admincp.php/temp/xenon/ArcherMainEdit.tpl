<center><a href="./?pages=Archer&type={ArcherTable}&pageType=Add{addition}" class="btn btn-secondary quickAdd">{L_add}</a>[if {D_DisableSort}!=0]<a href="./?pages=Archer&type={ArcherTable}&pageType=Sort&orderBy={LinkOrderBy}{addition}" class="btn btn-secondary">{L_"Сортировать"}</a>[/if {D_DisableSort}!=0]</center>
<table id="example-1" class="table table-striped table-bordered" cellspacing="0" width="100%">
<thead>
	<tr>
		{ArcherMind}
	</tr>
</thead>
<tfoot>
	<tr>
		{ArcherMind}
	</tr>
</tfoot>
<tbody>
[foreach block={ArcherPage}]<tr>
	{ArcherData}
	<td>
		<a href="./?pages=Archer&type={ArcherTable}&pageType=Edit&viewId={{ArcherPage}.{ArcherFirst}}{addition}" class="btn btn-purple btn-block quickView">{L_quickEdit}</a>
		<a href="./?pages=Archer&type={ArcherTable}&pageType=Delete&viewId={{ArcherPage}.{ArcherFirst}}{addition}" onclick="return confirmDelete();" class="btn btn-red btn-block">{L_delete}</a>
	</td>
</tr>[/foreach]
</tbody>
</table>
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
				{ArcherNotTouch}
			]
		}]
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
	jQuery(".quickAdd").click(function() {
		jQuery("#title_video").html("{L_"Быстрое добавление"}");
		jQuery.post(jQuery(this).attr("href"), function(data) {
			jQuery("#content_video").css({"overflow": "auto", "overflow-x": "hidden", "padding": "20px 50px 20px 20px"}).html(data);
			jQuery("#modal-3 .modal-dialog").css("width", "60%");
			jQuery("#modal-3 .modal-header .close:not(#closeIco)").remove();
			jQuery('#modal-3').modal('show');
			jQuery('.timepicker').each(function(i, elem) {
				jQuery(elem).timepicker(jQuery(elem).data());
			});
			jQuery('.datepicker').each(function(i, elem) {
				jQuery(elem).datepicker(jQuery(elem).data());
			});
			jQuery("body .removeTempStyle").remove();
			jQuery("body").append("<style class='removeTempStyle'>.bootstrap-timepicker-widget.dropdown-menu{z-index:10000;}</style>");
			tinymce.remove(editorTextarea.selector);
			tinymce.init(editorTextarea);
			jQuery("#modal-3 .modal-footer .btn-savePage").remove();
			jQuery("#modal-3 .modal-content").addClass("form-horizontal");
			jQuery("#modal-3 .modal-content").attr("action", linkForSubmit);
			jQuery("#modal-3 .modal-content").attr("method", "post");
			jQuery("#modal-3 .modal-content").attr("role", "form");
			jQuery("#modal-3 .modal-content").attr("enctype", "multipart/form-data");
			jQuery("#modal-3 .modal-footer").prepend('<button class="btn btn-savePage btn-icon btn-icon-standalone btn-icon-standalone-right btn-sm pull-left"><i class="fa-save"></i><span>{L_save}</span></button>');
			jQuery("#modal-3 .modal-content").unbind("submit").submit(function(event) {
				var forSend = new FormData();
				jQuery("#modal-3 .modal-content .modal-body :input").each(function(i, elem) {
					if(jQuery(elem).attr("type")=="file") {
						if(elem.files.length>1) {
							for(var s=0;s<elem.files.length;s++) {
								if(typeof(elem.files[s]) !== "undefined") {
									forSend.append(jQuery(elem).attr("name"), elem.files[s]);
								}
							}
						} else if(elem.files.length==1 && typeof(elem.files[0]) !== "undefined") {
							forSend.append(jQuery(elem).attr("name"), elem.files[0]);
						}
					} else {
						forSend.append(jQuery(elem).attr("name"), jQuery(elem).val());
					}
				});
				jQuery.ajax({
					url: linkForSubmit+"&jajax=true",
					data: forSend,
					processData: false,
					contentType: false,
					cache: false,
					type: 'POST',
					success: function(data) {
						jQuery('#modal-3').modal('hide');
						toastr.success("{L_"Данные обновлены"}");
					},
					error: function(data, textStatus, errorThrown) {
						if(data.status==302) {
							jQuery('#modal-3').modal('hide');
							toastr.success("{L_"Данные обновлены"}");
						} else {
							toastr.error("{L_"Данные не обновлены"}");
						}
					}
				});
				return false;
			});
		});
		return false;
	});
	jQuery(".quickView").click(function() {
		jQuery("#title_video").html("{L_"Быстрое редактирование"}");
		jQuery.post(jQuery(this).attr("href"), function(data) {
			jQuery("#content_video").css({"overflow": "auto", "overflow-x": "hidden", "padding": "20px 50px 20px 20px"}).html(data);
			jQuery("#modal-3 .modal-dialog").css("width", "60%");
			jQuery("#modal-3 .modal-header .close:not(#closeIco)").remove();
			jQuery('#modal-3').modal('show');
			jQuery('.timepicker').each(function(i, elem) {
				jQuery(elem).timepicker(jQuery(elem).data());
			});
			jQuery('.datepicker').each(function(i, elem) {
				jQuery(elem).datepicker(jQuery(elem).data());
			});
			jQuery("body .removeTempStyle").remove();
			jQuery("body").append("<style class='removeTempStyle'>.bootstrap-timepicker-widget.dropdown-menu{z-index:10000;}</style>");
			tinymce.remove(editorTextarea.selector);
			tinymce.init(editorTextarea);
			jQuery("#modal-3 .modal-footer .btn-savePage").remove();
			jQuery("#modal-3 .modal-content").addClass("form-horizontal");
			jQuery("#modal-3 .modal-content").attr("action", linkForSubmit);
			jQuery("#modal-3 .modal-content").attr("method", "post");
			jQuery("#modal-3 .modal-content").attr("role", "form");
			jQuery("#modal-3 .modal-content").attr("enctype", "multipart/form-data");
			jQuery("#modal-3 .modal-footer").prepend('<button class="btn btn-savePage btn-icon btn-icon-standalone btn-icon-standalone-right btn-sm pull-left"><i class="fa-save"></i><span>{L_save}</span></button>');
			jQuery("#modal-3 .modal-content").unbind("submit").submit(function(event) {
				var forSend = new FormData();
				jQuery("#modal-3 .modal-content .modal-body :input").each(function(i, elem) {
					if(jQuery(elem).attr("type")=="file") {
						if(elem.files.length>1) {
							for(var s=0;s<elem.files.length;s++) {
								if(typeof(elem.files[s]) !== "undefined") {
									forSend.append(jQuery(elem).attr("name"), elem.files[s]);
								}
							}
						} else if(elem.files.length==1 && typeof(elem.files[0]) !== "undefined") {
							forSend.append(jQuery(elem).attr("name"), elem.files[0]);
						}
					} else {
						forSend.append(jQuery(elem).attr("name"), jQuery(elem).val());
					}
				});
				jQuery.ajax({
					url: linkForSubmit+"&jajax=true",
					data: forSend,
					processData: false,
					contentType: false,
					cache: false,
					type: 'POST',
					success: function(data) {
						jQuery('#modal-3').modal('hide');
						toastr.success("{L_"Данные обновлены"}");
					},
					error: function(data, textStatus, errorThrown) {
						if(data.status==302) {
							jQuery('#modal-3').modal('hide');
							toastr.success("{L_"Данные обновлены"}");
						} else {
							toastr.error("{L_"Данные не обновлены"}");
						}
					}
				});
				return false;
			});
		});
		return false;
	});
});
function confirmDelete() {
	if (confirm("{L_"Вы подтверждаете удаление?(Данную операцию невозможно будет обратить)"}")) {
		return true;
	} else {
		return false;
	}
}
</script>