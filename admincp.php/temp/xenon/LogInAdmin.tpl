<table class="table table-bordered table-striped" id="example-2">
	<thead>
		<tr>
			<th width="100">lId</th>
			<th>lAction</th>
			<th width="110">lIp</th>
			<th width="200">lTime</th>
		</tr>
	</thead>
	
	<tbody class="middle-align">
	[foreach block=logs]
		<tr>
			<td>{logs.lId}</td>
			<td>{logs.lAction}</td>
			<td>{logs.lIp}</td>
			<td>{S_langdata="{logs.lTime}","H:i:s j-m-Y",true}</td>
		</tr>
	[/foreach]
	</tbody>
</table>