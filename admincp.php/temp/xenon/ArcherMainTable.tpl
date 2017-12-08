<center><a href="./?pages=Archer&type={ArcherTable}&pageType=Add{addition}" class="btn btn-secondary">{L_add}</a>[if {D_DisableSort}!=0]<a href="./?pages=Archer&type={ArcherTable}&pageType=Sort&orderBy={LinkOrderBy}" class="btn btn-secondary">{L_"Сортировать"}</a>[/if {D_DisableSort}!=0]</center>
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
		<a href="./?pages=Archer&type={ArcherTable}&pageType=Show&viewId={{ArcherPage}.{ArcherFirst}}{addition}" class="btn btn-secondary btn-block">{L_view}</a>
		<a href="./?pages=Archer&type={ArcherTable}&pageType=Edit&viewId={{ArcherPage}.{ArcherFirst}}{addition}" class="btn btn-edit btn-block">{L_edit}</a>
		<a href="./?pages=Archer&type={ArcherTable}&pageType=Delete&viewId={{ArcherPage}.{ArcherFirst}}{addition}" onclick="confirmDelete();" class="btn btn-red btn-block">{L_delete}</a>
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
function confirmDelete() {
	if (confirm("{L_"Вы подтверждаете удаление?(Данную операцию невозможно будет обратить)"}")) {
		return true;
	} else {
		return false;
	}
}
</script>