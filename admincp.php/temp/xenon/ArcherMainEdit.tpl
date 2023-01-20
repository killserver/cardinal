[if {C_disableAdd}!=1]<center><a href="./?pages=Archer&type={ArcherTable}&pageType=Add{addition}" class="btn btn-secondary quickAdd btn-add"><span>{L_add}</span></a></center>[/if {C_disableAdd}!=1]
{E_[customHeaders][type={ArcherTable}]}
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
	[if {C_disableOptions}!=true]<td class="td_options">
		{E_[customOptionsBefore][type={ArcherTable};id={{ArcherPage}.{ArcherFirst}}]}
		[if {C_disableCopy}!=1&&{{ArcherPage}.DisableCopy}!="yes"]<a href="./?pages=Archer&type={ArcherTable}&pageType=Copy&viewId={{ArcherPage}.{ArcherFirst}}{addition}" class="btn btn-turquoise btn-block btn-copy btn-copy-edit"><span>{L_"Клонировать"}</span></a>[/if {C_disableCopy}!=1&&{{ArcherPage}.DisableCopy}!="yes"]
		[if {C_disableEdit}!=1&&{{ArcherPage}.DisableEdit}!="yes"]<a href="./?pages=Archer&type={ArcherTable}&pageType=Edit&viewId={{ArcherPage}.{ArcherFirst}}{addition}" class="btn btn-purple btn-block quickView btn-edit"><span>{L_quickEdit}</span></a>[/if {C_disableEdit}!=1&&{{ArcherPage}.DisableEdit}!="yes"]
		[if {C_disableDelete}!=1&&{{ArcherPage}.DisableRemove}!="yes"]<a href="./?pages=Archer&type={ArcherTable}&pageType=Delete&viewId={{ArcherPage}.{ArcherFirst}}{addition}" data-type="{ArcherTable}" data-id="{{ArcherPage}.{ArcherFirst}}" class="btn btn-red btn-block btn-remove"><span>{L_delete}</span></a>[/if {C_disableDelete}!=1&&{{ArcherPage}.DisableRemove}!="yes"]
		{E_[customOptions][type={ArcherTable};id={{ArcherPage}.{ArcherFirst}}]}
	</td>[/if {C_disableOptions}!=true]
</tr>[/foreach]
</tbody>
</table>
[if {activate_pager}==yes]
<ul class="pagination">
	<li><a href="{prevLinkPager}"><i class="fa-angle-left"></i></a></li>
	[foreach block=pager]
		[foreachif {pager.now}!=1]<li><a href="{pager.link}">{pager.title}</a></li>[/foreachif {pager.now}!=1]
		[foreachif {pager.now}==1]<li class="disabled"><a>{pager.title}</a></li>[/foreachif {pager.now}==1]
	[/foreach]
	<li><a href="{nextLinkPager}"><i class="fa-angle-right"></i></a></li>
</ul>
[/if {activate_pager}==yes]
<script type="text/javascript">
	// Dynamically load images while scrolling
	// Source: github.com/ByNathan/jQuery.loadScroll
	// Version: 1.0.1

	(function($) {
	    
	    $.fn.loadScroll = function(duration, elem) {
	    
	        var $window = $(window);
	    	if(elem) {
	    		$window = $(elem);
	    	}
	        var images = this,
	            inview,
	            loaded;

	        images.one('loadScroll', function() {
	            
	            if (this.getAttribute('data-src')) {
	                this.setAttribute('src',
	                this.getAttribute('data-src'));
	                this.removeAttribute('data-src');
	                
	                if (duration) {
	                    
	                    $(this).hide()
	                           .fadeIn(duration)
	                           .add('img');
	                    
	                } else return false;
	            }
	            
	        });
	    
	        $window.scroll(function() {
	        
	            inview = images.filter(function() {
	                
	                var a = $window.scrollTop(),
	                    b = $window.height(),
	                    c = $(this).offset().top,
	                    d = $(this).height();
	                    
	                return c + d >= a && c <= a + b;
	                
	            });
	            
	            loaded = inview.trigger('loadScroll');
	            images = images.not(loaded);
	                    
	        });
	    };
	    
	})(jQuery);
</script>
<style>
	table.dataTable td, 
	table.dataTable th {
		white-space: nowrap;
	}
</style>
<script type="text/javascript">
var dTable
function showPreviewFnAnother() {
	$('img').loadScroll(500, jQuery(".modal .modal-dialog .modal-content .modal-body")[0]);
	jQuery(".modal .modal-dialog .modal-content .modal-body").animate({
		scrollTop: jQuery(".modal .modal-dialog .modal-content .modal-body").scrollTop()+1
	}, 100)
}
jQuery(document).ready(function() {
	$('img').loadScroll(500);
	var timer = setTimeout(function() {
		clearTimeout(timer);
		console.warn('scroller')
		jQuery("html").animate({
			scrollTop: jQuery("html").scrollTop()+1
		}, 100);
	}, 500);
[if {activate_pager}==no]
	function createTable() {
		dTable = jQuery("#example-1").dataTable({
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
					{ArcherNotTouch}-1
				]
			}],
			dom: 'fr<"table_container"t>ip', // l
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
	}
	createTable();
[/if {activate_pager}==no]
	jQuery(".quickAdd").click(function() {
		jQuery("#title_video").html("{L_"Быстрое добавление"}");
		jQuery.post(jQuery(this).attr("href"), function(data) {
			jQuery("#content_video").css({"overflow": "auto", "overflow-x": "hidden", "padding": "20px 50px 20px 20px"}).html(data);
			if($(window).width()>900) {
				jQuery("#modal-3 .modal-dialog").css("width", "60%");
			}
			jQuery("#modal-3 .modal-header .close:not(#closeIco)").remove();
			jQuery('#modal-3').modal('show');
			jQuery('.timepicker').each(function(i, elem) {
				jQuery(elem).timepicker(jQuery(elem).data());
			});
			jQuery('.datepicker').each(function(i, elem) {
				jQuery(elem).datepicker(jQuery(elem).data());
			});
			jQuery(".modal .modal-dialog .modal-content .modal-body").animate({
				scrollTop: jQuery(".modal .modal-dialog .modal-content .modal-body").scrollTop()+1
			}, 100);
			jQuery("body .removeTempStyle").remove();
			jQuery("body").append("<style class='removeTempStyle'>.bootstrap-timepicker-widget.dropdown-menu{z-index:10000;}</style>");
			tinymce.remove(editorTextarea.selector);
			tinymce.init(editorTextarea);
			select2Init();
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
			if($(window).width()>900) {
				jQuery("#modal-3 .modal-dialog").css("width", "60%");
			}
			jQuery("#modal-3 .modal-header .close:not(#closeIco)").remove();
			jQuery('#modal-3').modal('show');
			jQuery('.timepicker').each(function(i, elem) {
				jQuery(elem).timepicker(jQuery(elem).data());
			});
			jQuery('.datepicker').each(function(i, elem) {
				jQuery(elem).datepicker(jQuery(elem).data());
			});
			jQuery(".modal .modal-dialog .modal-content .modal-body").animate({
				scrollTop: jQuery(".modal .modal-dialog .modal-content .modal-body").scrollTop()+1
			}, 100);
			jQuery("body .removeTempStyle").remove();
			jQuery("body").append("<style class='removeTempStyle'>.bootstrap-timepicker-widget.dropdown-menu{z-index:10000;}</style>");
			tinymce.remove(editorTextarea.selector);
			tinymce.init(editorTextarea);
			select2Init();
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
	if($(window).width()<=900) {
		$(".td_options").each(function(_, option) {
			var list = [];
			var bId = 0;
			$(option).find("a").each(function(i, elem) {
				if(i>0 && i%2===0) {
					bId++;
				}
				!list[bId] && (list[bId] = [])
				list[bId].push(elem)
			});
			$(option).html(list.map(item => {
				return `<div>${item.map(elem => elem.outerHTML).join("")}</div>`
			}).join(""));
		});
	}
	jQuery("body").on('page.dt,search.dt', "#example-1", function() {
		var timer = setTimeout(function() {
			clearTimeout(timer);
			cbr_replace();
		}, 100);
		if($(window).width()<=900) {
			var timers = setTimeout(function() {
				clearTimeout(timers);
				$(".td_options").each(function(_, option) {
					var list = [];
					var bId = 0;
					$(option).find("a").each(function(i, elem) {
						if(i>0 && i%2===0) {
							bId++;
						}
						!list[bId] && (list[bId] = [])
						list[bId].push(elem)
					});
					$(option).html(list.map(item => {
						return `<div>${item.map(elem => elem.outerHTML).join("")}</div>`
					}).join(""));
				});
			}, 100);
		}
	});
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
	jQuery("body").on("click", ".btn-remove", function(e) {
		e.preventDefault();
		if(confirmDelete()) {
			var type = $(this).attr("data-type");
			var id = $(this).attr("data-id");
			var th = this;
			dTable.fnDestroy();
			$(th).parents("tr").remove();
			createTable();
			jQuery.get("./?pages=Archer&type="+type+"&pageType=Delete&viewId="+id+"&ajax", function(data) {
				if(data.success) {
					dTable.fnDestroy();
					createTable();
				}
			}, "json");
		}
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