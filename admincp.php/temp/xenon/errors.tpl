									<table class="table table-bordered table-striped" id="example-2">
										<thead>
											<tr>
												<td colspan="6" align="right"><form method="post" action="{C_default_http_host}admincp.php/?pages=Logs&delete"><button class="btn btn-red btn-icon btn-icon-standalone"><i class="fa-remove"></i><span>Delete All</span></button></form></td>
											</tr>
											<tr>
												<th width="90">Date/Time</th>
												<th width="110">Added</th>
												<th>Name</th>
												<th width="150">Views</th>
												<th width="70">Page</th>
												<th width="150">Page</th>
											</tr>
										</thead>
										
										<tbody class="middle-align">
										[foreach block=logs]
											<tr>
												<td>{logs.time}</td>
												<td>{logs.errorno}</td>
												<td>{logs.error}</td>
												<td>{logs.path}</td>
												<td>{logs.line}</td>
												<td>{logs.ip}</td>
											</tr>
											<tr style="text-align:center;">
												<td colspan="6"><a href="javascript:;" onclick="getDescr(this);return false;">{L_descr}</a><div class="spoiler-body" style="display:none;">{logs.descr}</div></td>
											</tr>
										[/foreach]
										</tbody>
									</table>
									<script type="text/javascript">
									function getDescr(tt) {
										jQuery('#modal-4').modal('show', {backdrop: 'static'});
										var descr = jQuery(tt).parent().children('.spoiler-body').html();
										jQuery('#error-body').html(descr);
									}
									</script>