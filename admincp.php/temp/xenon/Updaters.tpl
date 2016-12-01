<div class="col-md-12">
	<a id="core-cardinal" class="btn btn-[if {is_download}==0]red download[/if {is_download}==0][if {is_download}==1]success install[/if {is_download}==1] btn-icon btn-icon-standalone btn-lg [if {is_locked}==1]locked[/if {is_locked}==1]">
		<i class="fa-download"></i>
		<span>[if {is_download}==0]{L_download}[/if {is_download}==0][if {is_download}==1]{L_install}[/if {is_download}==1] {L_new_versions} [{new_version}]</span>
	</a>
	<div class="progress progress-striped active">
		<div class="progress-bar progress-bar-success" style="width:0%;"></div>
	</div>
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
</div>
<script type="text/javascript">
function rebuild() {
	jQuery(".download").click(function() {
		NProgress.start();
		NProgress.inc();
		setTimeout(function() {
			jQuery.ajax({
				url: "{C_default_http_host}admincp.php/?pages=Updaters&download",
			}).done(function(data) {
				var opts = {
					"closeButton": true,
					"debug": false,
					"positionClass": "toast-top-full-width",
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
				toastr.success("{L_done_updates}", "{L_done_update}", opts);
				jQuery(".download").unbind("click");
				jQuery("a#core-cardinal").removeClass("btn-red").removeClass("download").addClass("btn-success").addClass("install");
				jQuery("a#core-cardinal span").html("{L_install} {L_new_versions} [{new_version}]");
				rebuild();
			}).fail(function(data) {
				var opts = {
					"closeButton": true,
					"debug": false,
					"positionClass": "toast-top-full-width",
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
				toastr.error(data, "{L_fail_update}", opts);
			}).always(function() {
				NProgress.done();
			});
		}, 1000);
	});
	jQuery(".install").click(function() {
		NProgress.start();
		NProgress.inc();
		setTimeout(function() {
			jQuery.ajax({
				url: "{C_default_http_host}admincp.php/?pages=Updaters&install",
			}).done(function(data) {
				var opts = {
					"closeButton": true,
					"debug": false,
					"positionClass": "toast-top-full-width",
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
				toastr.success("{L_done_updates}", "{L_done_update}", opts);
			}).fail(function(data) {
				var opts = {
					"closeButton": true,
					"debug": false,
					"positionClass": "toast-top-full-width",
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
				toastr.error(data, "{L_fail_update}", opts);
			}).always(function() {
				NProgress.done();
			});
		}, 1000);
	});
	jQuery(".locked").off("click").click(function() {
		alert("{L_install_locked}");
	});
}
jQuery(document).ready(function() {
	rebuild();
});
</script>
<style type="text/css">
/* Make clicks pass-through */
#nprogress {
  pointer-events: none;
}

#nprogress .bar {
  background: #29d;

  position: fixed;
  z-index: 1031;
  top: 0;
  left: 0;

  width: 100%;
  height: 2px;
}

/* Fancy blur effect */
#nprogress .peg {
  display: block;
  position: absolute;
  right: 0px;
  width: 100px;
  height: 100%;
  box-shadow: 0 0 10px #29d, 0 0 5px #29d;
  opacity: 1.0;

  -webkit-transform: rotate(3deg) translate(0px, -4px);
      -ms-transform: rotate(3deg) translate(0px, -4px);
          transform: rotate(3deg) translate(0px, -4px);
}

/* Remove these to get rid of the spinner */
#nprogress .spinner {
  display: block;
  position: fixed;
  z-index: 1031;
  top: 15px;
  right: 15px;
}

#nprogress .spinner-icon {
  width: 18px;
  height: 18px;
  box-sizing: border-box;

  border: solid 2px transparent;
  border-top-color: #29d;
  border-left-color: #29d;
  border-radius: 50%;

  -webkit-animation: nprogress-spinner 400ms linear infinite;
          animation: nprogress-spinner 400ms linear infinite;
}

.nprogress-custom-parent {
  overflow: hidden;
  position: relative;
}

.nprogress-custom-parent #nprogress .spinner,
.nprogress-custom-parent #nprogress .bar {
  position: absolute;
}

@-webkit-keyframes nprogress-spinner {
  0%   { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}
@keyframes nprogress-spinner {
  0%   { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
<script type="text/javascript" src="assets/xenon/js/nprogress.js"></script>