<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{settingTitle}</h3>
			</div>
			<div class="panel-body">
				<form method="post" role="form" action="./?pages=SettingUser" class="form-horizontal" enctype="multipart/form-data" autocomplete="off">
					<div class="row">
						<div class="col-sm-12">
							<ul class="nav nav-tabs nav-tabs-justified">
								{head}
							</ul>
							<div class="tab-content">
								{data}
							</div>
						</div>
					</div>
					<button class="btn btn-savePage btn-icon btn-icon-standalone btn-icon-standalone-right btn-sm">
						<i class="fa-save"></i>
						<span>{L_save}</span>
					</button>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var tabAll = jQuery(".tab-content").children("div");
	if(typeof(tabAll[0])!=="undefined") {
		jQuery(tabAll[0]).addClass("active");
	}
	var tabAll = jQuery(".nav.nav-tabs.nav-tabs-justified").children("li");
	if(typeof(tabAll[0])!=="undefined") {
		jQuery(tabAll[0]).addClass("active");
	}
	jQuery(document).ready(function($) {
		var co = document.cookie;
		if(co.indexOf("SaveDone=")>-1) {
			toastr.success("{L_"Настройки успешно сохранены"}", "{L_"Настройки"}");
		}
	});
</script>
<style type="text/css"> .content_admin .nav.nav-tabs > li > a { position: relative; } .content_admin .nav.nav-tabs > li:not(.active) > a:hover {  background-color: #eee; } .content_admin .nav-tabs > li.active > a, .content_admin .nav-tabs > li.active > a:hover, .content_admin .nav-tabs > li.active > a:focus { border: 0px; } </style>