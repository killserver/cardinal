
			<div class="row">
				
				[if {is_new}==1]<a href="{C_default_http_host}admincp.php/?pages=Updaters" class="col-md-[if {C_FullMenu}==1]4[/if {C_FullMenu}==1][if {C_FullMenu}!=1]3[/if {C_FullMenu}!=1] col-sm-12">
					<div class="xe-widget xe-counter xe-counter-red">
					<div class="xe-icon"><i class="linecons-params"></i></div>
					<div class="xe-label">
						<strong class="num">{new_version}</strong>
						<span>{L_new_version}</span>
					</div>
					</div>
				</a>[/if {is_new}==1]

				{include templates="MainServerLoad"}
				
				<span id="cache" class="col-md-[if {C_FullMenu}==1]4[/if {C_FullMenu}==1][if {C_FullMenu}!=1]3[/if {C_FullMenu}!=1] col-sm-12">
					
					<div class="xe-widget xe-counter xe-counter-purple" data-count=".num" data-from="0" data-to="{CacheSize}" data-suffix="{CacheSizeS}" data-duration="3" data-easing="false">
						<div class="xe-icon">
							<i class="linecons-inbox"></i>
						</div>
						<div class="xe-label">
							<strong class="num">{Cache}</strong>
							<span>{L_"Cache Data"}</span>
						</div>
					</div>
				
				</span>
				
				<span id="cachephp" class="col-md-[if {C_FullMenu}==1]4[/if {C_FullMenu}==1][if {C_FullMenu}!=1]3[/if {C_FullMenu}!=1] col-sm-12">
					
					<div class="xe-widget xe-counter xe-counter-purple" data-count=".num" data-from="0" data-to="{CachePHPSize}" data-suffix="{CachePHPSizeS}" data-duration="3" data-easing="false">
						<div class="xe-icon">
							<i class="linecons-inbox"></i>
						</div>
						<div class="xe-label">
							<strong class="num">{CachePHP}</strong>
							<span>{L_"Cache Templates"}</span>
						</div>
					</div>
				
				</span>
			
				<div class="col-md-[if {C_FullMenu}==1]4[/if {C_FullMenu}==1][if {C_FullMenu}!=1]3[/if {C_FullMenu}!=1] col-sm-12"[if {uptime_visible}==false] style="display:none;"[/if {uptime_visible}==false]>
					
					<div class="xe-widget xe-counter" data-count=".num" data-from="0" data-to="{uptime_value}" data-suffix="%" data-duration="2">
						<div class="xe-icon">
							<i class="linecons-cloud"></i>
						</div>
						<div class="xe-label">
							<strong class="num">{uptime_value}%</strong>
							<span>{L_"Server uptime"}</span>
						</div>
					</div>
					
				</div>
				
				<a href="{C_default_http_host}admincp.php/?pages=Users" class="col-md-[if {C_FullMenu}==1]4[/if {C_FullMenu}==1][if {C_FullMenu}!=1]3[/if {C_FullMenu}!=1] col-sm-12"[if {isUsers}==1] style="display:none;"[/if {isUsers}==1]>
					
					<div class="xe-widget xe-counter xe-counter-blue" data-count=".num" data-from="0" data-to="{users}" data-duration="3" data-easing="false">
						<div class="xe-icon">
							<i class="linecons-user"></i>
						</div>
						<div class="xe-label">
							<strong class="num">{users}</strong>
							<span>{L_"Users Total"}</span>
						</div>
					</div>
				
				</a>
				
				[if {is_messagesAdmin}==1]
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-title">{L_"Рекомендации от"}&nbsp;<b>Cardinal Engine</b></div>
						<div class="panel-body">
							<div class="scrollable" data-max-height="200">
								{messagesAdmin}
							</div>
						</div>
					</div>
				</div>
				[/if]
				
				[if {is_new}==1]
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-title">{L_list_changelog}</div>
						<div class="panel-body">
							<div class="scrollable" data-max-height="200">
								{changelog}
							</div>
						</div>
					</div>
				</div>
				[/if]
			</div>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("#cache").click(function() {
		jQuery.post("./?pages=Main&clear&cache", function(data) {
			toastr.info(data, "{L_"Clear Cache Data"}");
		});
	});
	jQuery("#cachephp").click(function() {
		jQuery.post("./?pages=Main&clear&tmp", function(data) {
			toastr.info(data, "{L_"Clear Cache Templates"}");
		});
	});
});
</script>