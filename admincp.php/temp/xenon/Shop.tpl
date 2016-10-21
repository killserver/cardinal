<script>document.domain = "{C_default_http_hostname}";</script>
<iframe class="autoHeight" src="{D_SERVER_MODULES}?sites={set_domain}" frameborder="0" width="100%"></iframe>
<script type="text/javascript">
function doIframe() {
    o = document.getElementsByTagName('iframe');
    for(i=0;i<o.length;i++){
        if (/\bautoHeight\b/.test(o[i].className)){
            jQuery(o[i]).load(function() {
				document.querySelector(".autoHeight").contentWindow.postMessage('send', '{D_SERVER_MODULES}');
			});
        }
    }
}
jQuery(document).ready(function() {
	doIframe();
});
function Install(module) {
	jQuery.post("{D_SERVER_MODULES}shop/search/api/"+module+"?name", function(data) {
		if(confirm("{L_installNow} "+data+". {L_confirmInstallLic}")) {
			jQuery.post("{D_SERVER_MODULES}shop/search/api/"+module+"?install&key="+{C_api_key}, function(data) {
				var opts = {
					"closeButton": false,
					"debug": false,
					"positionClass": "toast-bottom-right",
					"onclick": null,
					"showDuration": "300",
					"hideDuration": "1000",
					"timeOut": "5000",
					"extendedTimeOut": "1000",
					"showEasing": "swing",
					"hideEasing": "linear",
					"showMethod": "fadeIn",
					"hideMethod": "fadeOut"
				};
				if(data=="done") {
					toastr.success("{L_doneInstall}", "{L_installModule}", opts);
				} else {
					toastr.error(data, "{L_installModule}", opts);
				}
			});
		}
	});
}
var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
var eventer = window[eventMethod];
var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
eventer(messageEvent, function(e) {
	switch(e.data.name) {
		case "setHeight":
			document.querySelector(".autoHeight").style.height = (parseInt(e.data.param)+10) + "px";
		break;
		case "Install":
			Install(e.data.param);
		break;
	}
}, false);
</script>