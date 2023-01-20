<!DOCTYPE html>
<html lang="{langPanel}">
<head>
	<meta charset="{C_charset}">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="Cardinal Engine Admin Panel" />
	<meta name="author" content="KilleR" />
	<!--base href="{C_default_http_host}{D_ADMINCP_DIRECTORY}/" /-->
	
	<title>{head_title}</title>

	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Arimo:400,700,400italic">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap">
	<link rel="stylesheet" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/css/fonts/linecons/css/linecons.css?1">
	<link rel="stylesheet" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/css/fonts/fontawesome/css/font-awesome.min.css?1">
	<link rel="stylesheet" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/css/bootstrap.css?1">
	<link rel="stylesheet" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/css/xenon-core.css?10">
	<link rel="stylesheet" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/css/xenon-forms.css?1">
	<link rel="stylesheet" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/css/xenon-components.css?10">
	<link rel="stylesheet" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/css/xenon-skins.css?1">
	<link rel="stylesheet" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/css/cardinal.css?{S_time}">
	<link rel="stylesheet" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/css/custom.css?{S_time}">

	<link rel="manifest" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/manifest.php" />
	<script type="module">
		if ('serviceWorker' in navigator) {
			window.addEventListener('load', function() {
				navigator.serviceWorker.register('{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/sw.js').then(function(registration) {
				// Registration was successful
					console.log('ServiceWorker registration successful with scope: ', registration.scope);
				}, function(err) {
				// registration failed :(
					console.log('ServiceWorker registration failed: ', err);
				});
			});
		}
	</script>

	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/jquery-1.11.1.min.js?1"></script>
	<script>
		var defaultTime = {S_time};
		var default_link = "{C_default_http_host}";
		var default_admin_link = "{C_default_http_host}{D_ADMINCP_DIRECTORY}/";
		var default_localadmin_link = "{C_default_http_local}{D_ADMINCP_DIRECTORY}/";
		var selectLang = "{langPanel}";
		var langSupport = '{langSupport}';
		try {
			langSupport = JSON.parse(langSupport);
		} catch(Exception) {}
	</script>
	{header}

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
</head>
<body class="page-body {C_defaultAdminSkin} level_{U_level}">

	<div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->
			
		<!-- Add "fixed" class to make the sidebar fixed always to the browser viewport. -->
		<!-- Adding class "toggle-others" will keep only one menu item open at a time. -->
		<!-- Adding class "collapsed" collapse sidebar root elements and show only icons. -->
		<div class="sidebar-menu toggle-others fixed[if {C_FullMenu}!=1&&{M_[mobile]}==false] collapsed[/if {C_FullMenu}!=1&&{M_[mobile]}==false]">
			
			<div class="sidebar-menu-inner">	
				
				<header class="logo-env">
					
					<!-- logo -->
					<div class="logo">
						<a href="{C_default_http_host}{D_ADMINCP_DIRECTORY}/{C_mainPageAdmin}" class="logo-expanded">
							<img src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/{C_logoAdminMain}" width="{C_logoAdminMainWidth}" alt="" />
						</a>
						
						<a href="{C_default_http_host}{D_ADMINCP_DIRECTORY}/{C_mainPageAdmin}" class="logo-collapsed">
							<img src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/{C_logoAdminMobile}" width="{C_logoAdminMobileWidth}" alt="" />
						</a>
					</div>
					
					<!-- This will toggle the mobile menu and will be visible only on mobile devices -->
					<div class="mobile-menu-toggle visible-xs visible-sm">
						<!--a href="#" data-toggle="user-info-menu">
							<i class="fa-bell-o"></i>
							<span class="badge badge-success">7</span>
						</a-->
						[if {C_accessToSite}=="show"]<a href="{C_default_http_local}" class="visible-xs-inline-block visible-sm-inline-block" title="{L_"Перейти на сайт"}" alt="{L_"Перейти на сайт"}">
							<i class="fa fa-paper-plane"></i>
						</a>[/if {C_accessToSite}=="show"]
						
						<a href="#" data-toggle="mobile-menu">
							<i class="fa-bars"></i>
						</a>
					</div>		
				</header>
				<ul id="main-menu" class="main-menu">
					<!-- add class "multiple-expanded" to allow multiple submenus to open -->
					<!-- class "auto-inherit-active-class" will automatically add "active" class for parent elements who are marked already with class "active" -->
						<li style="[if {C_deactiveMainMenu}==1]display:none;[/if {C_deactiveMainMenu}==1]{E_[admin_console_styleLi]}">
							<a href="{C_default_http_host}{D_ADMINCP_DIRECTORY}/{C_consolePageAdmin}" style="{E_[admin_console_styleA]}">
								<i class="icon-cash-console" style="{E_[admin_console_styleI]}"></i>
								<span class="title" style="{E_[admin_console_styleSpan]}">{L_"Консоль"}</span>
							</a>
						</li>
						[foreach block=menu]
						[foreachif {menu.type_st}=="start"&&{menu.existSub}==false]<li style="{menu.styleLi}">
							<a href="{menu.link}" style="{menu.styleA}">
								<i class="{menu.icon}" style="{menu.styleI}"></i>
								<span class="title" style="{menu.styleSpan}">{menu.value}</span>
							</a>
							<ul>[/foreachif {menu.type_st}=="start"&&{menu.existSub}==false]
						[foreachif {menu.type_st}=="start"&&{menu.existSub}==true]<li class="hasSubmenu" style="{menu.styleLi}">
							<a href="{menu.link}" style="{menu.styleA}">
								<i class="{menu.icon}" style="{menu.styleI}"></i>
								<span class="title" style="{menu.styleSpan}">{menu.value}</span>
							</a>
							<ul>[/foreachif {menu.type_st}=="start"&&{menu.existSub}==true]
								<li [foreachif {menu.is_now}==1] class="active"[/foreachif]style="[foreachif {menu.type_st}=="start"]display:none;[/foreachif {menu.type_st}=="start"] {menu.styleLi}">
									<a href="{menu.link}" style="{menu.styleA}">
										<i class="{menu.icon}" style="{menu.styleI}"></i>
										<span class="title" style="{menu.styleSpan}">{menu.value}</span>
									</a>
								</li>
						[foreachif {menu.type_end}=="end"]	</ul>
						</li>[/foreachif {menu.type_end}=="end"]
						[/foreach]
				</ul>
						
			</div>
			
		</div>
		
		<div class="main-content">
					
			<!-- User Info, Notifications and Menu Bar -->
			<nav class="navbar user-info-navbar" role="navigation">
				
				<!-- Left links for user info navbar -->
				<ul class="user-info-menu left-links list-inline list-unstyled">
					
					[if {C_access_collapsed_menu}==true]<li class="hidden-sm hidden-xs">
						<a href="#" data-toggle="sidebar">
							<i class="fa-bars"></i>
						</a>
					</li>[/if {C_access_collapsed_menu}==true]
					
					[if {C_accessToSite}=="show"]<li class="dropdown hover-line">
						<a href="{C_default_http_host}" class="dropdown-toggle" aria-expanded="true" title="{L_"Перейти на сайт"}" alt="{L_"Перейти на сайт"}">
							<i class="fa-paper-plane"></i>
						</a>
					</li>[/if {C_accessToSite}=="show"]
					
					[if {count[langListSupport]}>=2]<li class="dropdown hover-line language-switcher">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><img src="{nowLangImg}"><!-- {nowLangText} --></a>
						<ul class="dropdown-menu languages">
							[foreach block=langListSupport]<li><a href="./?setLanguage={langListSupport.langMenu}"><img src="{langListSupport.img}">{langListSupport.lang}</a></li>[/foreach]
						</ul>
					</li>[/if {count[langListSupport]}>=2]
					
				</ul>
				
				<!-- <div class="versionCardinal">{L_"Версия"}: {D_VERSION}</div> -->
				
				<!-- Right links for user info navbar -->
				<ul class="user-info-menu right-links list-inline list-unstyled">
					
					<li class="dropdown user-profile">
						<a href="#" data-toggle="dropdown">
							<span>
								{U_username}
								<i class="fa-angle-down"></i>
							</span>
						</a>
						
						<ul class="dropdown-menu user-profile-menu list-unstyled">
							[if {UL_settings}==true]<li>
								<a href="{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=SettingUser">
									<i class="fa-wrench"></i>
									{L_"Settings"}
								</a>
							</li>[/if {UL_settings}==true]
							<li class="last">
								<a href="{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Login&out">
									<i class="fa-lock"></i>
									{L_"Logout"}
								</a>
							</li>
						</ul>
					</li>
					
				</ul>

				<!-- <span id="doc_time"></span> -->
				
			</nav>
			<!-- <div class="page-title">
				<div class="title-env">
					<h1 class="title">{title_admin}</h1>
				</div>
			</div> -->
			<span class="content_admin">
				{E_[print_before_admin]}
				{info}
				{main_admin}
				{E_[print_after_admin]}
			</span>
			<!-- Main Footer -->
			<!-- Choose between footer styles: "footer-type-1" or "footer-type-2" -->
			<!-- Add class "sticky" to  always stick the footer to the end of page (if page contents is small) -->
			<!-- Or class "fixed" to  always fix the footer to the end of page -->
			<footer class="main-footer sticky footer-type-1">
				
				<div class="footer-inner">
				
					<!-- Add your copyright text here -->
					<div class="footer-text">
						&copy; 2015 - {S_data="Y"} 
						<strong>Xenon</strong> 
						theme by <a href="http://laborator.co" target="_blank">Laborator</a> for Cardinal Engine
					</div>
					
					
					<!-- Go to Top Link, just add rel="go-top" to any link to add this functionality -->
					<div class="go-up">
					
						<a href="#" rel="go-top">
							<i class="fa-angle-up"></i>
						</a>
						
					</div>
					
					<div class="pull-right col-sm-2 text-right text-muted">rev. {D_INTVERSION}</div>
					
				</div>
				
			</footer>
		</div>
		
		
	</div>
	
	{E_[admin_footer]}
	
	<div class="modal fade custom-width" id="modal-3" data-backdrop="static">
		<div class="modal-dialog" style="width:95%;height:90%;">
			<form class="modal-content" style="height:100%;display:block;">
				<div class="modal-header">
					<button type="button" class="close hide" onclick="show_hide(this);return false"><span class="collapse-icon">-</span></button>
					<button type="button" class="close" id="closeIco" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="title_video"></h4>
				</div>
				<div class="modal-body" id="content_video" style="height:85%;"></div>
				<div class="modal-footer" style="position:absolute;left:0px;width:100%;padding:30px;bottom:0px;margin-bottom:-10px;">
					<button type="button" class="btn btn-white pull-right" id="close" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
	<div id="modalView" class="btn btn-purple btn-lg hidden" style="bottom:0px;left:0px;position:fixed;background:#fff;color:#000;z-index:100;" onclick="shows();return false;">View</div>
	<div class="modal fade" id="modal-4" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Error</h4>
				</div>
				<div class="modal-body" id="error-body" style="height:500px;overflow:auto;">You can close this modal when you click on button only!</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal">Continue</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modal-yui" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header"><h4 class="modal-title">{L_"Панель запуска Yui"}</h4></div>
				<div class="modal-body">
					<button type="button" class="btn btn-info" data-demo="data-demo" data-demo-this="1" data-dismiss="modal">{L_"Запустить обучение для этой страницы"}</button>
					<button type="button" class="btn btn-red" data-demo="data-demo" data-demo-this="0" data-dismiss="modal">{L_"Запустить полный курс обучения"}</button>
				</div>
			</div>
		</div>
	</div>

	<div class="page-loading-overlay">
		<div class="loader-2"></div>
	</div>

	<!-- Import flash -->
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$("[data-skin]").each(function(i, el)
			{
				var $el = $(el),
					skin = $el.data('skin');
				
				$el.find('a').attr('data-set-skin', skin).attr('href', '#setSkin:' + skin);
			});
			$('[data-set-skin]').on('click', function(ev)
			{
				ev.preventDefault();
				
				var skin = $(this).data('set-skin'),
					skin_name = skin ? (' skin-'+skin) : '';
				
				var body_classes = public_vars.$body.attr('class').replace(/skin-[a-z]+/i, '');
				
				public_vars.$body.attr('class', body_classes).addClass(skin_name);
				
				Cookies.set('current-skin', skin);
			});
			jQuery('body').attr('class', jQuery('body').attr('class').replace(/skin-[a-z]+/i, '')).addClass(Cookies.get('current-skin') ? (' skin-'+Cookies.get('current-skin')) : '');
		});
	</script>
	
	<!-- Imported styles on this page -->
	<link rel="stylesheet" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/toastr/toastr.min.css?1">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.7/css/jquery.fancybox.min.css" rel="stylesheet">
	{css_list}
	<!-- Bottom Scripts -->
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/bootstrap.min.js?1"></script>
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/TweenMax.min.js?1"></script>
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/resizeable.js?1"></script>
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/joinable.js?2"></script>
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/xenon-api.js?3"></script>
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/xenon-toggles.js?1"></script>
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/xenon-widgets.js?1"></script>
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/tinymce/tinymce.min.js?{S_time}"></script>

	
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/datepicker/bootstrap-datepicker.js"></script>
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/timepicker/bootstrap-timepicker.min.js"></script>
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/colorpicker/bootstrap-colorpicker.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.7/js/jquery.fancybox.min.js"></script>

	{js_list}
	
	<script>
	var editorTextarea;
	jQuery(".update-nag .dismiss").unbind("click").click(function() {
		var th = this;
		var id = jQuery(th).attr("data-code");
		jQuery.post("{C_default_http_local}{D_ADMINCP_DIRECTORY}/?pages=main&removeCode="+id, function(d) {
			jQuery(th).parent().parent().hide(500, function() {jQuery(th).parent().parent().remove();});
		});
	});
	function mainTemplateEdit() {
		if(typeof(editorTextarea)!=="object") {
			editorTextarea = {configTinymce};
		}
        editorTextarea.init_instance_callback = function(editor) {
            $("body").trigger("ready_tinymce", {id: editor.id, editor: editor});
            $("body").trigger("ready_tinymce_"+editor.id, {editor: editor});
        }
		tinymce.init(editorTextarea);
		function iframeBtn(e) {
			if(!($(e.target).is('.iframe-btn') || e.target.closest('.iframe-btn'))) {
				return false;
			}
			e.preventDefault();
			var elem = ($(e.target).is('.iframe-btn') ? e.target : e.target.closest('.iframe-btn'));
			var width = (jQuery("body").width()/1.5);
			if($(window).width()<=900) {
				width = $("body").width()-40;
			}
			jQuery.fancybox.open({'href': jQuery(elem).attr("href"), 'width': width, 'height': (jQuery("body").height()/1.5), 'type': 'iframe', 'autoScale': false});
		}
		document.body.removeEventListener("click", iframeBtn);
		document.body.addEventListener("click", iframeBtn);
	}
	if(typeof(disableAllEditors)==="undefined") {
		$(document).ready(function() {
			mainTemplateEdit()
		});
	}
	function responsive_filemanager_callback(field_id) {
		var type = jQuery("#"+field_id).attr("data-accept");
		var link = jQuery("#"+field_id).val();
		if(!link) {
			return;
		}
		var http_link = link;
		link = link.replace(default_link, "");
		jQuery("#"+field_id).val(link).change();
		var par = jQuery("#"+field_id)[0].closest("[data-show]");
		var pas = $(par).find("a[href*='file']").parent();
		$(par).find("br").remove();
		$(par).find("a[data-link]").remove();
		$("body").trigger("change_filemanager", {"data-link": field_id, "http_link": http_link, "type": type, "parent": pas, "field_id": field_id});
		/*pas.append('<a data-link="'+field_id+'" id="img'+field_id+'" href="'+http_link+'"'+(type.indexOf("image")>-1 || type.indexOf("imageAccess")>-1 || type.indexOf("imageArrayAccess")>-1 ? " class=\"showPreview new\"" : "")+' target="_blank">Просмотреть</a>');
		jQuery(".showPreview.new").each(function(i, elem) {
			jQuery(elem).parent().find("img").remove();
			jQuery(elem).after("<br><img src='"+jQuery(elem).attr("href")+"' data-link='"+jQuery(elem).attr("data-link")+"' width='200'>");
		});*/
	}
	if(window.matchMedia) {
		if(window.matchMedia('(prefers-color-scheme: dark)').matches) {
			$("html").removeClass("currentSkin-light").addClass("currentSkin-dark");
		} else {
			$("html").removeClass("currentSkin-dark").addClass("currentSkin-light");
		}
		window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
			var newColorScheme = event.matches ? "dark" : "light";
			$("html").removeClass("currentSkin-light currentSkin-dark").addClass("currentSkin-"+newColorScheme);
		});
	} else {
		$("html").removeClass("currentSkin-dark").addClass("currentSkin-default");
	}
	jQuery(document).ready(function($) {
		SmoothScroll({
			stepSize: 80,
			animationTime: 600,
			frameRate: 120,
			touchpadSupport: true,
			fixedBackground: false
		});
		$("body").on("click", ".page-container .sidebar-menu .sidebar-menu-inner .logo-env .mobile-menu-toggle a", function() {
			$("html").toggleClass("showMenuFull");
		});
	});
	</script>

	<style type="text/css">.mce-branding-powered-by { display: none !important; }</style>

	<!-- JavaScripts initializations and stuff -->
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/xenon-custom.js?2"></script>
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/smoothScroll.js"></script>
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/toastr/toastr.min.js?1"></script>
</body>
</html>