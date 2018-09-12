
			<div class="row">
				
				[if {is_new}==new]<a href="{C_default_http_host}admincp.php/?pages=Updaters" class="col-md-[if {C_FullMenu}==1]4[/if {C_FullMenu}==1][if {C_FullMenu}!=1]3[/if {C_FullMenu}!=1] col-sm-12">
					<div class="xe-widget xe-counter xe-counter-red">
					<div class="xe-icon"><i class="linecons-params"></i></div>
					<div class="xe-label">
						<strong class="num">{new_version}</strong>
						<span>{L_"новая версия"}</span>
					</div>
					</div>
				</a>[/if {is_new}==new]
				
				<span id="cache" class="col-md-[if {C_FullMenu}==1]4[/if {C_FullMenu}==1][if {C_FullMenu}!=1]3[/if {C_FullMenu}!=1] col-sm-12" style="[if {clearCacheAll}==0]display:none;[/if {clearCacheAll}==0][if {clearCacheData}==1]display:block;[/if {clearCacheData}==1][if {clearCacheData}==0]display:none;[/if {clearCacheData}==0]">
					
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
				
				<span id="CachePages" class="col-md-[if {C_FullMenu}==1]4[/if {C_FullMenu}==1][if {C_FullMenu}!=1]3[/if {C_FullMenu}!=1] col-sm-12" style="[if {clearCacheAll}==0]display:none;[/if {clearCacheAll}==0][if {clearCacheTmp}==1]display:block;[/if {clearCacheTmp}==1][if {clearCacheTmp}==0]display:none;[/if {clearCacheTmp}==0]">
					
					<div class="xe-widget xe-counter xe-counter-purple" data-count=".num" data-from="0" data-to="{CachePagesSize}" data-suffix="{CachePagesSizeS}" data-duration="3" data-easing="false">
						<div class="xe-icon">
							<i class="linecons-inbox"></i>
						</div>
						<div class="xe-label">
							<strong class="num">{CachePages}</strong>
							<span>{L_"Cache Pages"}</span>
						</div>
					</div>
				
				</span>
				
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
				
				<span class="col-md-[if {C_FullMenu}==1]4[/if {C_FullMenu}==1][if {C_FullMenu}!=1]3[/if {C_FullMenu}!=1] col-sm-12"[if {IsUserOnline}==0] style="display:none;"[/if {IsUserOnline}==0]>
					
					<div class="xe-widget xe-counter xe-counter-blue" data-count=".num" data-from="0" data-to="{UserOnline}" data-duration="3" data-easing="false">
						<div class="xe-icon">
							<i class="linecons-user"></i>
						</div>
						<div class="xe-label">
							<strong class="num">{UserOnline}</strong>
							<span>{L_"Users Online"}</span>
						</div>
					</div>
				
				</span>
				
				<span id="debug" class="col-md-[if {C_FullMenu}==1]5[/if {C_FullMenu}==1][if {C_FullMenu}!=1]4[/if {C_FullMenu}!=1] col-sm-12"[if {debugpanelshow}==0] style="display:none;"[/if {debugpanelshow}==0]>
					
					<div class="xe-widget xe-counter xe-counter-orange">
						<div class="xe-icon">
							<i class="fa-cogs"></i>
						</div>
						<div class="xe-label">
							<strong class="num">{L_"Активировать"}</strong>
							<span>{L_"Debug Panel"}</span>
						</div>
					</div>
				
				</span>

				{contentForAdmin}
				
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
				[/if {is_messagesAdmin}==1]
				
				[if {is_new}==new]
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-title">{L_"Список изменений"}</div>
						<div class="panel-body">
							<div class="scrollable" data-max-height="200">
								{changelog}
							</div>
						</div>
					</div>
				</div>
				[/if {is_new}==new]
			</div>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("#cache").click(function() {
		jQuery.post("./?pages=Main&clear&cache", function(data) {
			toastr.info(data, "{L_"Clear Cache Data"}");
		});
	});
	jQuery("#CachePages").click(function() {
		jQuery.post("./?pages=Main&clear&tmp", function(data) {
			toastr.info(data, "{L_"Clear Cache Templates"}");
		});
	});
	if({debugPanel}==1) {
		jQuery("#debug .num").html("{L_"Деактивировать"}");
	}
	jQuery("#debug").click(function() {
		jQuery.post("./?pages=Main&debugPanel=true", function(data) {
			var states;
			if(jQuery("#debug .num").html()=="{L_"Активировать"}") {
				jQuery("#debug .num").html("{L_"Деактивировать"}");
				states = "{L_"деактивирована"}";
			} else {
				jQuery("#debug .num").html("{L_"Активировать"}");
				states = "{L_"активирована"}";
			}
			toastr.info(data, "{L_"Debug Panel"} "+states);
		});
	});
    var data = localStorage.getItem("mainAdminCollapsed");
    if(data!==null) {
        data = JSON.parse(data);
    } else {
        data = {};
    }
    Object.keys(data).forEach(function(elem) {
        var e = jQuery("[data-module='"+elem+"']");
        if(data[elem]===true) {
            e.removeClass('collapsed');
        } else if(data[elem]===false) {
            e.addClass('collapsed');
        }
    });
});
jQuery(".content_admin [data-toggle]").click(function() {
    var elem = jQuery(this).parent().parent().parent();
    var module = elem.attr("data-module");
    var hidded = elem.hasClass("collapsed");
    var data = localStorage.getItem("mainAdminCollapsed");
    if(data!==null) {
        data = JSON.parse(data);
    } else {
        data = {};
    }
    data[module] = hidded;
    data = JSON.stringify(data);
    localStorage.setItem("mainAdminCollapsed", data);
});
</script>