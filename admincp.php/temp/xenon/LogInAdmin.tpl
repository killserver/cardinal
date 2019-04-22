<table class="table table-bordered table-striped" id="example-2">
	<thead>
		<tr>
			<th width="100">{L_"id"}</th>
			<th>{L_"Действие"}</th>
			<th width="110">{L_"IP"}</th>
			<th width="200">{L_"Дата"}/{L_"Время"}</th>
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
	[if {count[logs]}==0]
		<tr style="text-align:center;">
			<td colspan="6">{L_"Действий в системе не зарегистрировано"}</td>
		</tr>
	[/if {count[logs]}==0]
	</tbody>
</table>