<script type="text/javascript">
function DebugPanel_Toggle(id) {
	var e = document.getElementById(id);
	e.style.display = (e.style.display == 'none' ? 'block' : 'none');
}
function DebugPanel_ToggleTable(id) {
	var e = document.getElementById(id);
	e.style.display = (e.style.display == 'none' ? 'table' : 'none');
}

function DebugPanel_ShowHidePanel() {
	DebugPanel_Toggle('i-debug-panel-all-panels');
	DebugPanel_ToggleTable('i-debug-panel-list');
	DebugPanel_Toggle('i-show-profile');
}
</script>
<link rel="stylesheet" type="text/css" href="{C_default_http_host}skins/core/debug_panel.min.css" />


<div style="clear: both"></div>
<div id="i-debug-panel">
	<span class="icon show-profile" id="i-show-profile" onclick="DebugPanel_ShowHidePanel()"></span>

	<ul id="i-debug-panel-list" style="display: none">
		<li class="show-hide-elem">
			<span class="icon hide-profile" onclick="DebugPanel_ShowHidePanel()"></span>
		</li>
		<li>
			<span class="icon time"></span>
			{time_work} s
		</li>
		<li>
			<span class="icon mem"></span>
			{memory}
		</li>
		<li>
			<span class="icon db"></span>
			<a href="javascript:DebugPanel_Toggle('i-databasa-log')">sql <span class="small">({db_count})</span></a>
		</li>
		<li>
			<span class="icon vars"></span>
			<a href="javascript:DebugPanel_Toggle('i-vars-log')">vars <span class="small">(G: {count_get} / P: {count_post} / C: {count_cookie} / R: {count_router})</span></a>
		</li>
		<li>
			<span class="icon engine"></span>
			<a href="javascript:DebugPanel_Toggle('i-engine')">engine <span class="small">({count_include})</span></a>
		</li>
		<li>
			<span class="icon events"></span>
			<a href="javascript:DebugPanel_Toggle('i-events')">events <span class="small">({count_events})</span></a>
		</li>
	</ul>

	<div id="i-debug-panel-all-panels" style="display: none">
		<div id="i-databasa-log" class="panel" style="display: none">
			<table id='iDbLogger' cellpadding="0" cellspacing="0">
				<tr>
					<th>#</th>
					<th>query</th>
					<th>time</th>
				</tr>
				[foreach block=db_query]<tr>
					<td class="num">{db_query.$id}</td>
					<td style='color: green;'>
						<pre>{db_query.query}</pre>
						<pre style="color:#aaa;font-size:95%;">{db_query.file}&nbsp;[{db_query.line}]</pre>
					</td>
					<td>{db_query.time}</td>
				</tr>[/foreach]
				<tr class="total">
					<td colspan='2' class="center">{db_count} <span>queries</span></td>
					<td>{db_time}</td>
				</tr>
			</table>
		</div>

		<div id="i-vars-log" class="panel" style="display: none">
			<ul>
				<li>
					<a href="javascript:DebugPanel_Toggle('i-get-log')">$_GET <span>({count_get})</span></a>
					<div id="i-get-log" style="display: none">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<th>#</th>
								<th>key</th>
								<th>value</th>
							</tr>
							[if {count_get}>=1]
								[foreach block=gets]
									<tr>
										<td class="num">{gets.$id}</td>
										<td>{gets.key}</td>
										<td>{gets.val}</td>
									</tr>
								[/foreach]
							[else {count_get}>=1]
								<tr>
									<td colspan="3" class="center">Empty</td>
								</tr>
							[/if {count_get}>=1]
						</table>
					</div>
				</li>
				<li>
					<a href="javascript:DebugPanel_Toggle('i-post-log')">$_POST <span>({count_post})</span></a>
					<div id="i-post-log" style="display: none">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<th>#</th>
								<th>key</th>
								<th>value</th>
							</tr>
							[if {count_post}>=1]
								[foreach block=posts]
									<tr>
										<td class="num">{posts.$id}</td>
										<td>{posts.key}</td>
										<td>{posts.val}</td>
									</tr>
								[/foreach]
							[else {count_post}>=1]
								<tr>
									<td colspan="3" class="center">Empty</td>
								</tr>
							[/if {count_post}>=1]
						</table>
					</div>
				</li>
				<li>
					<a href="javascript:DebugPanel_Toggle('i-cookie-log')">$_COOKIE <span>({count_cookie})</span></a>
					<div id="i-cookie-log" style="display: none">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<th>#</th>
								<th>key</th>
								<th>value</th>
							</tr>
							[if {count_cookie}>=1]
								[foreach block=cookies]
									<tr>
										<td class="num">{cookies.$id}</td>
										<td>{cookies.key}</td>
										<td>{cookies.val}</td>
									</tr>
								[/foreach]
							[else {count_cookie}>=1]
								<tr>
									<td colspan="3" class="center">Empty</td>
								</tr>
							[/if {count_cookie}>=1]
						</table>
					</div>
				</li>
				<li>
					<a href="javascript:DebugPanel_Toggle('i-server-log')">$_SERVER <span>({count_server})</span></a>
					<div id="i-server-log" style="display: none">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<th>#</th>
								<th>key</th>
								<th>value</th>
							</tr>
							[if {count_server}>=1]
								[foreach block=servers]
									<tr>
										<td class="num">{servers.$id}</td>
										<td>{servers.key}</td>
										<td>{servers.val}</td>
									</tr>
								[/foreach]
							[else {count_server}>=1]
								<tr>
									<td colspan="3" class="center">Empty</td>
								</tr>
							[/if {count_server}>=1]
						</table>
					</div>
				</li>
				<li>
					<a href="javascript:DebugPanel_Toggle('i-router-log')">Route <span>({count_router})</span></a>
					<div id="i-router-log" style="display: none">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<th>#</th>
								<th>key</th>
								<th>value</th>
							</tr>
							[if {count_router}>=1]
								[foreach block=router]
									<tr>
										<td class="num">{router.$id}</td>
										<td>{router.key}</td>
										<td>{router.val}</td>
									</tr>
								[/foreach]
							[else {count_router}>=1]
								<tr>
									<td colspan="3" class="center">Empty</td>
								</tr>
							[/if {count_router}>=1]
						</table>
					</div>
				</li>
			</ul>
		</div>

		<div id="i-events" class="panel" style="display: none">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<th>#</th>
					<th>event</th>
					<th>file</th>
					<th>arguments</th>
					<th>called</th>
					<th>time</th>
				</tr>
				[foreach block=events]
					<tr>
						<td class="num">{events.$id}</td>
						<td>{events.name}</td>
						<td><span>{events.file} [{events.line}]</span></td>
						<td>{events.args}</td>
						<td>{events.called}</td>
						<td>{events.time}</td>
					</tr>
				[/foreach]
				<tr class="total">
					<td></td>
					<td class="center" colspan="5"><span>Total</span> {count_events} <span>events</span></td>
				</tr>
			</table>
		</div>

		<div id="i-engine" class="panel" style="display: none">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<th>#</th>
					<th>file</th>
					<th>size</th>
					<th>lines</th>
				</tr>
				[foreach block=include]
					<tr>
						<td class="num">{include.$id}</td>
						<td><span>{include.file}</span></td>
						<td>{include.size}</td>
						<td>{include.line}</td>
					</tr>
				[/foreach]
				<tr class="total">
					<td></td>
					<td class="center"><span>Total</span> {count_include} <span>files</span></td>
					<td>{total_includesize}</td>
					<td>{total_includeline}</td>
				</tr>
			</table>
		</div>
	</div>

	<div style="clear: both"></div>
</div>