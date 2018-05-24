<center><a href="./?pages=Archer&type={ArcherTable}&pageType=Add{addition}" class="btn btn-secondary">{L_add}</a></center>
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
		[if {{ArcherPage}.DisableEdit}!="yes"]<a href="./?pages=Archer&type={ArcherTable}&pageType=Edit&viewId={{ArcherPage}.{ArcherFirst}}{addition}" class="btn btn-edit btn-block">{L_edit}</a>[/if {{ArcherPage}.DisableEdit}!="yes"]
		[if {{ArcherPage}.DisableRemove}!="yes"]<a href="./?pages=Archer&type={ArcherTable}&pageType=Delete&viewId={{ArcherPage}.{ArcherFirst}}{addition}" onclick="return confirmDelete();" class="btn btn-red btn-block">{L_delete}</a>[/if {{ArcherPage}.DisableRemove}!="yes"]
	</td>
</tr>[/foreach]
</tbody>
</table>
<ul class="pagination">
	<li><a href="{prevLinkPager}"><i class="fa-angle-left"></i></a></li>
	[foreach block=pager]
		[foreachif {pager.now}!=1]<li><a href="{pager.link}">{pager.title}</a></li>[/foreachif {pager.now}!=1]
		[foreachif {pager.now}==1]<li class="disabled"><a>{pager.title}</a></li>[/foreachif {pager.now}==1]
	[/foreach]
	<li><a href="{nextLinkPager}"><i class="fa-angle-right"></i></a></li>
</ul>
<script type="text/javascript">
jQuery(document).ready(function() {
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