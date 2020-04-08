[if {C_disableAdd}!=1]<center><a href="./?pages=Archer&type={ArcherTable}&pageType=Add{addition}" class="btn btn-secondary">{L_add}</a>
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
{E_[KernalArcher::AfterAddBtn][table={ArcherTable};type=main;data={addition}]}
</center>[/if {C_disableAdd}!=1]
{E_[customHeaders][type={ArcherTable}]}
<form method="post" action="./?pages=Archer&type={ArcherTable}&pageType=MultiAction">
	<table id="example-1" class="table table-striped table-bordered" cellspacing="0" width="100%">
		<thead>
			<tr>
				[if {C_disableMassAction}!=1]<th><label class="checkbox"><input type="checkbox" class="cbr deleteAll"></label></th>[/if {C_disableMassAction}!=1]
				{ArcherMind}
			</tr>
		</thead>
		<tfoot>
			<tr>
				[if {C_disableMassAction}!=1]<th><label class="checkbox"><input type="checkbox" class="cbr deleteAll"></label></th>[/if {C_disableMassAction}!=1]
				{ArcherMind}
			</tr>
			[if {C_disableMassAction}!=1]<tr><td colspan="{ArcherAll}"><div class="row"><div class="col-sm-offset-9"><div class="col-sm-7"><select name="action" class="form-control" style="width:100%;"><option value="">{L_"Выберите действие"}</option><option value="delete">{L_"Удалить"}</option></select></div><div class="col-sm-5"><input type="submit" class="btn btn-purple" value="{L_"Выполнить"}"></div></div></div></td></tr>[/if {C_disableMassAction}!=1]
		</tfoot>
		<tbody>
		[foreach block={ArcherPage}]<tr>
			[if {C_disableMassAction}!=1]<td><label class="checkbox"><input type="checkbox" class="cbr" name="delete[]" value="{{ArcherPage}.{ArcherFirst}}"></label></td>[/if {C_disableMassAction}!=1]
			{ArcherData}
			<td>
				[if {C_disableCopy}!=1&&{{ArcherPage}.DisableCopy}!="yes"]<a href="./?pages=Archer&type={ArcherTable}&pageType=CopyEdit&viewId={{ArcherPage}.{ArcherFirst}}{addition}" class="btn btn-turquoise btn-block">{L_"Клонировать и редактировать"}</a>[/if {C_disableCopy}!=1&&{{ArcherPage}.DisableCopy}!="yes"]
				[if {C_disableCopy}!=1&&{{ArcherPage}.DisableCopy}!="yes"]<a href="./?pages=Archer&type={ArcherTable}&pageType=Copy&viewId={{ArcherPage}.{ArcherFirst}}{addition}" class="btn btn-turquoise btn-block">{L_"Клонировать"}</a>[/if {C_disableCopy}!=1&&{{ArcherPage}.DisableCopy}!="yes"]
				[if {C_disableEdit}!=1&&{{ArcherPage}.DisableEdit}!="yes"]<a href="./?pages=Archer&type={ArcherTable}&pageType=Edit&viewId={{ArcherPage}.{ArcherFirst}}{addition}" class="btn btn-edit btn-block">{L_"Редактировать"}</a>[/if {C_disableEdit}!=1&&{{ArcherPage}.DisableEdit}!="yes"]
				[if {C_disableDelete}!=1&&{{ArcherPage}.DisableRemove}!="yes"]<a href="./?pages=Archer&type={ArcherTable}&pageType=Delete&viewId={{ArcherPage}.{ArcherFirst}}{addition}" onclick="return confirmDelete();" class="btn btn-red btn-block">{L_"Удалить"}</a>[/if {C_disableDelete}!=1&&{{ArcherPage}.DisableRemove}!="yes"]
				{E_[customOptions][type={ArcherTable};id={{ArcherPage}.{ArcherFirst}}]}
			</td>
		</tr>[/foreach]
		</tbody>
	</table>
</form>
{E_[KernalArcher::AfterMain][table={ArcherTable};type=not_ajax]}
[if {activate_pager}==yes]
	<div class="row">
		<div class="col-sm-12 col-md-9">
			<ul class="pagination">
				<li><a href="{prevLinkPager}" class="prev-page"><i class="fa-angle-left"></i></a></li>
				[foreach block=pager]
					[foreachif {pager.now}!=1]<li><a href="{pager.link}">{pager.title}</a></li>[/foreachif {pager.now}!=1]
					[foreachif {pager.now}==1]<li class="disabled"><a>{pager.title}</a></li>[/foreachif {pager.now}==1]
				[/foreach]
				<li><a href="{nextLinkPager}" class="next-page"><i class="fa-angle-right"></i></a></li>
			</ul>
		</div>
		<div class="col-sm-12 col-md-3">
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
<script type="text/javascript">
	var settingDataTable = {
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
jQuery(document).ready(function() {
[if {activate_pager}==yes]
	var btns = jQuery(".pagination a:not(.prev-page):not(.next-page)");
	min = btns.eq(0).html();
	max = btns.last().html();
	if(typeof(min) !== "undefined" && typeof(min.length) !== "undefined" && min.length>0) {
		min = parseInt(min);
	} else {
		min = 0;
	}
	if(typeof(max) !== "undefined" && typeof(max.length) !== "undefined" && max.length>0) {
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
[if {activate_pager}==no]
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
[/if {activate_pager}==no]
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