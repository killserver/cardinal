<style type="text/css">
td > img {
	background: #fff;
    padding: 5px;
	animation-timing-function: ease;
	transition: 0.3s;
}
td > img:hover {
	transform: scale(2);
}
.full_descr {
	display: none;
}
</style>
<table width="100%">
	<tr><td colspan="3"><div style="width:100%;height:18px;background:#ccc;"><div id="proccess" style="background:#00f;height:19px;width:0%;"></div></div></td></tr>
[foreach block=ListModules]
	<tr style="border: 1px dashed #000;" class="{ListModules.module}"><td width="100" align="center"><img src="{ListModules.image}" style="max-width:80px;max-height:80px;" /></td><td valign="top"><span style="font-style:italic;font-size:24px;">{ListModules.name}</span><br />{ListModules.file}[foreachif {ListModules.is_descr}==1]<br /><a href="#" class="descr">{L_descr}</a><span class="full_descr">{ListModules.description}</span>[/foreachif {ListModules.is_descr}==1]</td><td width="150" align="center"><a class="btn btn-purple btn-icon"[foreachif {ListModules.active}=="no"] href="./?pages=ModuleList&active={ListModules.module}">{L_ActiveModule}[/foreachif {ListModules.active}=="no"][foreachif {ListModules.active}=="yes"] href="./?pages=ModuleList&unactive={ListModules.module}">{L_DeActiveModule}[/foreachif {ListModules.active}=="yes"]</span><i class="fa-money"></i></a><a class="btn btn-red btn-icon" href="./?pages=ModuleList&uninstall={ListModules.module}" onclick="return Delete(this, '{ListModules.module}');"><span>{L_ModuleDelete}</span><i class="fa-money"></i></a></td></tr>
[/foreach]
</table>
<script type="text/javascript">
function Delete(th, module) {
	document.getElementById("proccess").style.width = "20%";
	if(confirm('{L_ModuleAlert}')) {
		document.getElementById("proccess").style.width = "45%";
		jQuery.post(th.href, function(data) {
			var options = {
				"closeButton": false,
				"debug": false,
				"newestOnTop": false,
				"progressBar": false,
				"positionClass": "toast-top-right",
				"preventDuplicates": false,
				"onclick": null,
				"showDuration": "300",
				"hideDuration": "1000",
				"timeOut": "1500",
				"extendedTimeOut": "1000",
				"showEasing": "swing",
				"hideEasing": "linear",
				"showMethod": "fadeIn",
				"hideMethod": "fadeOut"
			}
			if(data=="Done") {
				toastr.success(data, options);
				jQuery("."+module).remove();
			} else {
				toastr.error(data, options);
			}
			document.getElementById("proccess").style.width = "100%";
			setTimeout(function() {
				document.getElementById("proccess").style.width = "0%";
			}, 1500);
		});
	}
	return false;
}
jQuery(".descr").click(function() {
	jQuery('#modal-4').modal('show', {backdrop: 'static'});
	var descr = jQuery(this).parent().children(".full_descr").html();
	jQuery('#error-body').html(descr);
	return false;
});
</script>