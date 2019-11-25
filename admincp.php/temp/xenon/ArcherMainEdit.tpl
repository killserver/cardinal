<center><a href="./?pages=Archer&type={ArcherTable}&pageType=Add{addition}" class="btn btn-secondary quickAdd">{L_add}</a>
[if {activate_pager}==yes]<div class="search-block-input" style="display: inline-flex;width: 31%;justify-content: space-around;margin: 0px auto;float: right;position: absolute;right: 2.8em;">
	<input type="text" name="search" id="text-search" value="" class="form-control input-sm" placeholder="Поиск"><button style="padding: 0.35em;width: 16%;" class="btn btn-edit btn-block btn-for-search">Поиск</button>
</div>
<script>
var urlParams = new URLSearchParams(window.location.search);
var myParam = urlParams.get('type');
if (myParam !="products") {
	jQuery(".search-block-input").hide()
}
jQuery('.btn-for-search').on('click', function() {window.location.href = '?pages=Archer&type=products&tmp=ArcherMainEdit&ShowPages=true&Where=pName&WhereType=LIKE&WhereData=%25'+document.getElementById('text-search').value+'%25'})
</script>[/if {activate_pager}==yes]
</center>
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
		[if {C_disableCopy}!=1&&{{ArcherPage}.DisableCopy}!="yes"]<a href="./?pages=Archer&type={ArcherTable}&pageType=Copy&viewId={{ArcherPage}.{ArcherFirst}}{addition}" class="btn btn-turquoise btn-block">{L_"Клонировать"}</a>[/if {C_disableCopy}!=1&&{{ArcherPage}.DisableCopy}!="yes"]
		[if {{ArcherPage}.DisableEdit}!="yes"]<a href="./?pages=Archer&type={ArcherTable}&pageType=Edit&viewId={{ArcherPage}.{ArcherFirst}}{addition}" class="btn btn-purple btn-block quickView">{L_quickEdit}</a>[/if {{ArcherPage}.DisableEdit}!="yes"]
		[if {{ArcherPage}.DisableRemove}!="yes"]<a href="./?pages=Archer&type={ArcherTable}&pageType=Delete&viewId={{ArcherPage}.{ArcherFirst}}{addition}" onclick="return confirmDelete();" class="btn btn-red btn-block">{L_delete}</a>[/if {{ArcherPage}.DisableRemove}!="yes"]
		{E_[customOptions][type={ArcherTable};id={{ArcherPage}.{ArcherFirst}}]}
	</td>
</tr>[/foreach]
</tbody>
</table>
{E_[KernalArcher::AfterMain][table={ArcherTable};type=ajax]}
[if {activate_pager}==yes]
	<div class="row">
		<div class="col-sm-12 col-md-8">
			<ul class="pagination">
				<li><a href="{prevLinkPager}" class="prev-page"><i class="fa-angle-left"></i></a></li>
				[foreach block=pager]
					[foreachif {pager.now}!=1]<li><a href="{pager.link}">{pager.title}</a></li>[/foreachif {pager.now}!=1]
					[foreachif {pager.now}==1]<li class="disabled"><a>{pager.title}</a></li>[/foreachif {pager.now}==1]
				[/foreach]
				<li><a href="{nextLinkPager}" class="next-page"><i class="fa-angle-right"></i></a></li>
			</ul>
		</div>
		<div class="col-sm-12 col-md-4">
			<div class="col-sm-12 col-md-12">
				<input placeholder="Введите нужную страницу" class="form-control input-sm goToPage">
				<p class="help-block text-red small"></p>
			</div>
			<div class="col-sm-12 col-md-12">
				<a href="#" class="btn btn-block btn-secondary btn-sm goToPage">Перейти на страницу</a>
			</div>
		</div>
	</div>
<style>
	.help-block:empty {
	    margin: 0;
	}
	input.goToPage {
		text-align: center;
	}
	input.goToPage::-webkit-input-placeholder { /* Chrome/Opera/Safari */
		text-align: center;
	}
	input.goToPage::-moz-placeholder { /* Firefox 19+ */
		text-align: center;
	}
	input.goToPage:-ms-input-placeholder { /* IE 10+ */
		text-align: center;
	}
	input.goToPage:-moz-placeholder { /* Firefox 18- */
		text-align: center;
	}
</style>
[/if {activate_pager}==yes]
<style>
[data-panel-lang="true"] + .panel {
    margin-top: 60px;
}
/*
.modal-dialog {
    width: 100% !important;
    height: 100% !important;
    margin: 0;
}

form.modal-content.form-horizontal {
    width: 100%;
}

.modal .modal-dialog .modal-content.form-horizontal {
    padding: 22px 5px 5px;
}

.modal .modal-dialog .modal-content .modal-footer {
    padding: 20px 10px !important;
}

[data-panel-lang="true"] .panel-body {
    padding: 5px !important;
}

.modal .modal-dialog .modal-content .modal-body {
    padding: 20px 10px !important;
}
 */
</style>
<script type="text/javascript">
	var dTable;
	var referer = decodeURIComponent("{S_referer}").replace(new RegExp("&amp;", "g"), "&");
	function getQueryArgs(url) {
		var argsObj = {};
		var queryRef = url.split("?");
		if(typeof(queryRef[1])==="undefined") {
			return argsObj;
		}
		var arrQuery = queryRef[1].split("&");
		for(var i=0;i<arrQuery.length;i++) {
			var s = arrQuery[i].split("=");
			argsObj[s[0]] = s[1];
		}
		return argsObj;
	}
	var isObject = function(args) { return (typeof(args)===typeof({}) && typeof(args.length)==="undefined"); };
	function compileArgs(args) {
		if(isObject(args)) {
			var arr = [];
			Object.keys(args).forEach(function(key) {
				arr[arr.length] = key+"="+args[key];
			})
			return arr.join("&");
		} else {
			return false;
		}
	}
	var isEqual = function(value, other) {
		// Get the value type
		var type = Object.prototype.toString.call(value);
		// If the two objects are not the same type, return false
		if(type !== Object.prototype.toString.call(other)) {
			return false;
		}
		// If items are not an object or array, return false
		if(['[object Array]', '[object Object]'].indexOf(type) < 0) {
			return false;
		}
		// Compare the length of the length of the two items
		var valueLen = type === '[object Array]' ? value.length : Object.keys(value).length;
		var otherLen = type === '[object Array]' ? other.length : Object.keys(other).length;
		if(valueLen !== otherLen) {
			return false;
		}
		// Compare two items
		var compare = function(item1, item2) {
			// Get the object type
			var itemType = Object.prototype.toString.call(item1);
			// If an object or array, compare recursively
			if(['[object Array]', '[object Object]'].indexOf(itemType) >= 0) {
				if (!isEqual(item1, item2)) return false;
			} else { // Otherwise, do a simple comparison
				// If the two items are not the same type, return false
				if(itemType !== Object.prototype.toString.call(item2)) {
					return false;
				}
				// If it's a function, convert to a string and compare
				// Otherwise, just compare
				if(itemType === '[object Function]') {
					if(item1.toString() !== item2.toString()) {
						return false;
					}
				} else {
					if(item1 !== item2) {
						return false;
					}
				}
			}
		};
		// Compare properties
		var match;
		if(type === '[object Array]') {
			for(var i = 0; i < valueLen; i++) {
				if(compare(value[i], other[i]) === false) {
					return false;
				}
			}
		} else {
			for(var key in value) {
				if(value.hasOwnProperty(key)) {
					if(compare(value[key], other[key]) === false) {
						return false;
					}
				}
			}
		}
		// If nothing failed, return true
		return true;
	};
	var argsRef = getQueryArgs(referer);
	var argsNow = getQueryArgs(window.location.href);
	if(typeof(argsRef.viewId)!=="undefined") {
		delete argsRef.viewId;
	}
	if(typeof(argsRef.pageType)!=="undefined") {
		delete argsRef.pageType;
	}
	if(typeof(argsNow.viewId)!=="undefined") {
		delete argsNow.viewId;
	}
	if(typeof(argsNow.pageType)!=="undefined") {
		delete argsNow.pageType;
	}
	var savedDataTable = compileArgs(argsNow);
	if(referer.length==0 || isEqual(argsNow, argsRef)) {
		var arrToLoad = localStorage.getItem("info_archer_"+savedDataTable);
		if(arrToLoad!=null) {
			try {
				arrToLoad = JSON.parse(arrToLoad);
			} catch(e) {
				arrToLoad = null;
			}
		}
	} else if(window.performance && window.performance.navigation && window.performance.navigation.type==1) {
		var arrToLoad = localStorage.getItem("info_archer_now");
		if(arrToLoad!=null) {
			try {
				arrToLoad = JSON.parse(arrToLoad);
			} catch(e) {
				arrToLoad = null;
			}
		}
	}
var min = "", max = "";
jQuery(document).ready(function() {
[if {activate_pager}==yes]
	var btns = jQuery(".pagination a:not(.prev-page):not(.next-page)");
	min = btns.eq(0).html();
	max = btns.last().html();
	if(typeof(min.length)!=="undefined" && min.length>0) {
		min = parseInt(min);
	} else {
		min = 0;
	}
	if(typeof(max.length)!=="undefined" && max.length>0) {
		max = parseInt(max);
	} else {
		max = 0;
	}
	jQuery("input.goToPage").on("input", function() {
		this.value = this.value.replace(new RegExp("[^0-9]", "g"), "");
		if(this.value.length>0 && this.value>max) {
			$(".help-block").html("Число больше максимального");
		} else if(this.value.length>0 && this.value<min) {
			$(".help-block").html("Число меньше минимального");
		} else {
			$(".help-block").html("");
		}
	});
	jQuery("input.goToPage").on("keyup", function(e) {
		if(e.keyCode==13) {
			e.preventDefault();
			var inputPage = jQuery("input.goToPage").val();
			if(inputPage.length>0) {
				var url = window.location.href.split("#");
				url = url[0];
				window.location.href = url.replace(new RegExp("&page=([0-9]+)", "g"), "")+"&page="+inputPage;
			} else {
				jQuery("input.goToPage").focus();
			}
		}
	});
	jQuery("a.goToPage").on("click", function(e) {
		e.preventDefault();
		var inputPage = jQuery("input.goToPage").val();
		if(inputPage.length>0) {
			var url = window.location.href.split("#");
			url = url[0];
			window.location.href = url.replace(new RegExp("&page=([0-9]+)", "g"), "")+"&page="+inputPage;
		} else {
			jQuery("input.goToPage").focus();
		}
	});
[/if {activate_pager}==yes]
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
		"order": [[ 0, false ], [ 0, "desc" ]]
		/*"aoColumnDefs": [{
			'bSortable': false,
			'aTargets': [
				0, {ArcherNotTouch}
			]
		}],
		"order": [[ 0, false ], [ {orderById}, "{orderBySort}" ]]*/
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
	if(arrToLoad!=null) {
		if(typeof(arrToLoad.page)!=="undefined") {
			dTable.fnPageChange(arrToLoad.page)
		}
	}
	//
	jQuery("#example-1").on('page.dt', function() {
		$("html,body").animate({scrollTop: 0}, 600);
		var arrToSave = {
			page: dTable.DataTable().page(),
		};
		localStorage.setItem("info_archer_now", JSON.stringify(arrToSave));
		localStorage.setItem("info_archer_"+savedDataTable, JSON.stringify(arrToSave));
	});
	var isAdd = false;
	var isEdit = false;
	function quickAdd(e) {
		if(isAdd) { return false; }
		if(!(jQuery(e.target).is(".quickAdd") || e.target.closest(".quickAdd"))) {
			return;
		}
		isAdd = true;
		e.preventDefault();
		jQuery("#title_video").html("{L_"Быстрое добавление"}");


		var target = (jQuery(e.target).is(".quickAdd") ? e.target : e.target.closest(".quickAdd"));
		var href = jQuery(target).attr("href");
		jQuery.post(href, function(data) {
			jQuery("#content_video").css({"overflow": "auto", "overflow-x": "hidden", "padding": "20px 50px 20px 20px"}).html(data);
			jQuery("#modal-3 .modal-dialog").css("width", "60%");
			jQuery("#modal-3 .modal-header .close:not(#closeIco)").remove();
			//jQuery('#modal-3').modal('show');
			$(".modal-backdrop").remove();
			$("<div class='modal-backdrop'/>").appendTo($("body"));
			$("body").addClass("modal-open");
			$(".modal-open #modal-3").css("display", "block");
			setTimeout(() => { $(".modal-open #modal-3,.modal-backdrop").addClass("in"); }, 100)

			
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
			jQuery(document).off("keydown").on("keydown", function(e) {
				if(e.ctrlKey && e.keyCode==13) {
					e.preventDefault();
					jQuery("#modal-3 .modal-content").submit();
					return false;
				}
			});
			jQuery("#modal-3 .modal-content").unbind("submit").submit(function(event) {
				var forSend = new FormData();
				var radioGroups = {};
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
					} else if(jQuery(elem).attr("type")=="radio") {
						var nameRadio = jQuery(elem).attr("name");
						if(typeof(radioGroups[nameRadio])!=="undefined") {
							return;
						}
						radioGroups[nameRadio] = true;
						var selector = "#modal-3 .modal-content .modal-body [name='"+nameRadio+"']:checked";
						forSend.append(jQuery(elem).attr("name"), jQuery(selector).val());
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
						closeModal("add");
						toastr.success("{L_"Данные обновлены"}");
					},
					error: function(data, textStatus, errorThrown) {
						if(data.status==302) {
							closeModal("add");
							toastr.success("{L_"Данные обновлены"}");
						} else {
							toastr.error("{L_"Данные не обновлены"}");
						}
					}
				});
				return false;
			});
			select2Init();
			reinitLang();
			cbr_replace();
			jQuery("body").trigger("quickAdd");
		});
	}
	function quickView(e) {
		if(isEdit) { return false; }
		if(!(jQuery(e.target).is(".quickView") || e.target.closest(".quickView"))) {
			return;
		}
		isEdit = true;
		e.preventDefault();
		e.stopPropagation();
		jQuery("#title_video").html("{L_"Быстрое редактирование"}");
		var target = (jQuery(e.target).is(".quickView") ? e.target : e.target.closest(".quickView"));
		var href = jQuery(target).attr("href");
		var id = jQuery(target).parents("tr").children("td").eq(0).attr("data-id");
		console.warn(id);
		jQuery.post(href, function(data) {
			jQuery("#content_video").css({"overflow": "auto", "overflow-x": "hidden", "padding": "20px 50px 20px 20px"}).html(data);
			jQuery("#modal-3 .modal-dialog").css("width", "60%");
			jQuery("#modal-3 .modal-header .close:not(#closeIco)").remove();
			//jQuery('#modal-3').modal('show');
			$(".modal-backdrop").remove();
			$("<div class='modal-backdrop'/>").appendTo($("body"));
			$("body").addClass("modal-open");
			$(".modal-open #modal-3").css("display", "block");
			setTimeout(() => { $(".modal-open #modal-3,.modal-backdrop").addClass("in"); }, 100)


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
			jQuery(document).off("keydown").on("keydown", function(e) {
				if(e.ctrlKey && e.keyCode==13) {
					e.preventDefault();
					jQuery("#modal-3 .modal-content").submit();
					return false;
				}
			});
			jQuery("#modal-3 .modal-content").unbind("submit").submit(function(event) {
				var forSend = new FormData();
				var radioGroups = {};
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
					} else if(jQuery(elem).attr("type")=="radio") {
						var nameRadio = jQuery(elem).attr("name");
						if(typeof(radioGroups[nameRadio])!=="undefined") {
							return;
						}
						radioGroups[nameRadio] = true;
						var selector = "#modal-3 .modal-content .modal-body [name='"+nameRadio+"']:checked";
						forSend.append(jQuery(elem).attr("name"), jQuery(selector).val());
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
						closeModal(id);
						toastr.success("{L_"Данные обновлены"}");
					},
					error: function(data, textStatus, errorThrown) {
						if(data.status==302) {
							closeModal(id);
							toastr.success("{L_"Данные обновлены"}");
						} else {
							toastr.error("{L_"Данные не обновлены"}");
						}
					}
				});
				return false;
			});
			select2Init();
			reinitLang();
			cbr_replace();
			jQuery("body").trigger("quickView");
		});
	}
	function closeModal(id) {
		if(typeof(id)!=="undefined") {
			if(id=="add") {
				jQuery.post("./?pages=Archer&type={ArcherTable}&quickViewId=-1&quick=1", function(d) {
					dTable.DataTable().row.add(jQuery("<tr>"+d.tpl+"</tr>")).draw()
					dTable.fnPageChange(0);
				}, "json");
			} else {
				var pageTo = dTable.DataTable().page();
				jQuery.post("./?pages=Archer&type={ArcherTable}&quickViewId="+id+"&quick=1", function(d) {
					$("td[data-id='"+id+"']").parent().html(d.tpl);
					dTable.DataTable().draw()
					dTable.fnPageChange(pageTo);
				}, "json");
			}
		}
		$(".modal-open .modal,.modal-backdrop").removeClass("in");
		setTimeout(function() {
			$(".modal-open .modal").css("display", "none");
			$("body").removeClass("modal-open");
			$(".modal-backdrop").remove();
		}, 150);
		initQuickEdit();
		isAdd = false;
		isEdit = false;
	}
	function closeModalEvent(e) {
		if(!(jQuery(e.target).is(".modal .modal-dialog .modal-content .modal-header .close, .modal .modal-dialog .modal-content .modal-footer .btn#close") || e.target.closest(".modal .modal-dialog .modal-content .modal-header .close, .modal .modal-dialog .modal-content .modal-footer .btn#close"))) {
			return;
		}
		e.preventDefault();
		e.stopPropagation();
		closeModal();
	}
	function initQuickEdit() {
		jQuery("body").off("click");
		document.body.removeEventListener("click", function(e) {quickAdd(e)});
		document.body.addEventListener("click", function(e) {quickAdd(e)});
		document.body.removeEventListener("click", function(e) {quickView(e)});
		document.body.addEventListener("click", function(e) {quickView(e)});
		document.body.removeEventListener("click", function(e) {closeModalEvent(e)});
		document.body.addEventListener("click", function(e) {closeModalEvent(e)});
	}
	initQuickEdit();
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
});
function confirmDelete() {
	if (confirm("{L_"Вы подтверждаете удаление?(Данную операцию невозможно будет обратить)"}")) {
		return true;
	} else {
		return false;
	}
}
</script>