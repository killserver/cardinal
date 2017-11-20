<form role="form" action="./?pages=Settings&save" class="form-horizontal" method="post" autocomplete="off">
<div class="col-md-12">
<center>API-{L_key}: <input type="text" value="{API}" readonly="readonly" disabled="disabled"></center>
<ul class="nav nav-tabs right-aligned"> 
<li class="active">
	<a href="#home" data-toggle="tab">
		<span class="hidden-xs">{L_MainSettings}</span>
	</a>
</li>
[foreach block=sub_nav]
<li>
	<a href="#{sub_nav.subname}" data-toggle="tab">
		<span class="hidden-xs">{sub_nav.name}</span>
	</a>
</li>
[/foreach]
</ul>
<div class="tab-content">
<div class="tab-pane active" id="home">
	<span style="width:95%;border:1px solid #000;display:inline-block;padding:10px;margin:10px auto;">
		<center>{L_systemCache}<span alt="{L_systemCacheHelp}" title="{L_systemCacheHelp}">(?)</span></center>
		<hr />
		<div style="display:inline-block;width:50%;padding:5px;margin:1px;">{L_systemCacheFull}</div><select name="cache_type" style="width:45%;padding:5px;margin:1px;"><option value="CACHE_NONE"[if {C_cache[type]}=={D_CACHE_NONE}] selected="selected"[/if {C_cache[type]}=={D_CACHE_NONE}]>{L_CacheWithout}</option><option value="CACHE_FILE"[if {C_cache[type]}=={D_CACHE_FILE}] selected="selected"[/if {C_cache[type]}=={D_CACHE_FILE}]>{L_CacheFile}</option><option value="CACHE_MEMCACHE"[if {C_cache[type]}=={D_CACHE_MEMCACHE}] selected="selected"[/if {C_cache[type]}=={D_CACHE_MEMCACHE}]>Memcache</option><option value="CACHE_MEMCACHED"[if {C_cache[type]}=={D_CACHE_MEMCACHED}] selected="selected"[/if {C_cache[type]}=={D_CACHE_MEMCACHED}]>Memcached</option><option value="CACHE_FTP"[if {C_cache[type]}=={D_CACHE_FTP}] selected="selected"[/if {C_cache[type]}=={D_CACHE_FTP}]>FTP</option><option value="CACHE_XCACHE"[if {C_cache[type]}=={D_CACHE_XCACHE}] selected="selected"[/if {C_cache[type]}=={D_CACHE_XCACHE}]>XCache</option><option value="CACHE_REDIS"[if {C_cache[type]}=={D_CACHE_REDIS}] selected="selected"[/if {C_cache[type]}=={D_CACHE_REDIS}]>Redis</option></select><br />
		<div style="display:inline-block;width:50%;padding:5px;margin:1px;">{L_CacheServer}</div><input value=" " onclick="if(this.value == ' ') this.value=''" type="text" name="cache_host" value="{C_cache[server]}" style="width:45%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:50%;padding:5px;margin:1px;">{L_CachePort}</div><input value=" " onclick="if(this.value == ' ') this.value=''" type="text" name="cache_port" value="{C_cache[port]}" style="width:45%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:50%;padding:5px;margin:1px;">{L_CacheUser}</div><input value=" " onclick="if(this.value == ' ') this.value=''" type="text" name="cache_user" value="{C_cache[login]}" style="width:45%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:50%;padding:5px;margin:1px;">{L_CachePass}</div><input value=" " onclick="if(this.value == ' ') this.value=''" type="password" name="cache_pass" value="{C_cache[pass]}" style="width:45%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;" autocomplete="off"><br />
		<div style="display:inline-block;width:50%;padding:5px;margin:1px;">{L_CachePath}</div><input value=" " onclick="if(this.value == ' ') this.value=''" type="text" name="cache_path" value="{C_cache[path]}" style="width:45%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<center><b>{L_CacheHelp}</b></center>
		<hr />
		<center>{L_Settings}</center>
		<hr />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">{L_SiteName}</div><input value=" " onclick="if(this.value == ' ') this.value=''" type="text" name="sitename" maxlength="70" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;" value="{sitename}"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">{L_SiteDescr}</div><input value=" " onclick="if(this.value == ' ') this.value=''" type="text" name="description" maxlength="160" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;" value="{description}"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">{L_SiteDomain}</div><input value=" " onclick="if(this.value == ' ') this.value=''" type="text" name="SERVER" value="{SERNAME}" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">{L_SitePath}</div><input value=" " onclick="if(this.value == ' ') this.value=''" type="text" name="PATH" value="{SERPATH}" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Moby site link</div><input value=" " onclick="if(this.value == ' ') this.value=''" type="text" name="mobyhost" value="[if {C_default_http_mobyhost}!="{C_default_http_mobyhost}"]{C_default_http_mobyhost}[/if {C_default_http_mobyhost}!="{C_default_http_mobyhost}"]" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">{L_LogErrors}</div><select name="error_type" style="width:50%;padding:5px;margin:1px;"><option value="ERROR_FILE">{L_LogFile}</option><option value="ERROR_DB">{L_LogDB}</option></select><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">{L_GetNewUpdates}</div><input value=" " onclick="if(this.value == ' ') this.value=''" type="checkbox" name="speed_update" value="1"[if {C_speed_update}==1] checked="checked"[/if {C_speed_update}==1] class="iswitch iswitch-secondary" /><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">{L_CacheTemplates}</div><input value=" " onclick="if(this.value == ' ') this.value=''" type="checkbox" name="ParsePHP" value="1"[if {C_ParsePHP}==1] checked="checked"[/if {C_ParsePHP}==1] class="iswitch iswitch-secondary" /><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;" alt="Full cache page as html" title="Full cache page as html">Full cache [beta]</div><input value=" " onclick="if(this.value == ' ') this.value=''" type="checkbox" name="activeCache" value="1"[if {C_activeCache}==1] checked="checked"[/if {C_activeCache}==1] class="iswitch iswitch-secondary" alt="Full cache page as html" title="Full cache page as html" /><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Viewport</div><input value=" " onclick="if(this.value == ' ') this.value=''" type="text" name="viewport" value="[if {C_viewport}!="{C_viewport}"]{C_viewport}[/if {C_viewport}!="{C_viewport}"]" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Moby Active Redirect to subdomain</div><input value=" " onclick="if(this.value == ' ') this.value=''" type="checkbox" name="mobyActive" value="1"[if {C_mobyActive}==1] checked="checked"[/if {C_mobyActive}==1] class="iswitch iswitch-secondary" /><br />
	</span>
</div>
[foreach block=sub_nav_options]
<div class="tab-pane" id="{sub_nav_options.subname}">
{sub_nav_options.options}
</div>
[/foreach]
	<button type="submit" class="btn btn-purple btn-icon">
		<i class="fa-check"></i>
		<span>{L_save}</span>
	</button>
</div>
<br/>
</div>
</form>