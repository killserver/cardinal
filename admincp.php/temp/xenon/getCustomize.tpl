<div class="row">
	<div class="col-sm-12 iframe">
		<div class="row">
			<input type="text" class="url col-sm-12">
			<div class="desktop">
				<iframe></iframe>
			</div>
		</div>
	</div>
</div>
<style>
.iframe input {
	height: 25px;
	padding: 0px;
	position: relative;
	z-index: 50;
}
.iframe {
	right: auto;
	width: 100%;
	position: absolute;
	top: 0px;
	left: 0px;
	width: 100%;
	height: 100%;
	z-index: 20;
	background: #191e23;
}
.iframe iframe {
	width: 100%;
	height: 100%;
	height: calc(100% - 25px);
	position: absolute;
	top: 25px;
	left: 0px;
	border: 0px;
	background: #fff;
	z-index: 40;
}
.iframe > div > div {
	position: absolute;
	left: 0;
	right: 0;
	top: 0;
	bottom: 0;
	height: 100%;
	-webkit-transition: all .2s;
	transition: all .2s;
}
.iframe > div > div.tablet {
	margin: auto 0 auto -360px;
	width: 720px;
	height: 1080px;
	max-height: 100%;
	max-width: 100%;
	left: 50%;
}
.iframe > div > div.mobile {
	margin: auto 0 auto -160px;
	width: 320px;
	height: 480px;
	max-height: 100%;
	max-width: 100%;
	left: 50%;
}
.sidebar-menu-inner .devices {
	margin: 3px 0;
	padding: 4px 8px;
}
.sidebar-menu-inner .devices .resize-desktop {
	font-size: 20px;
	line-height: 30px;
	font-weight: 400;
	color: #fff;
	margin: 3px 0;
	padding: 4px 8px;
	cursor: pointer;
}
.sidebar-menu-inner .devices .resize-tablet {
	font-size: 23px;
	line-height: 30px;
	font-weight: 400;
	color: #fff;
	margin: 3px 0;
	padding: 4px 8px;
	cursor: pointer;
}
.sidebar-menu-inner .devices .resize-mobile {
	font-size: 25px;
	line-height: 30px;
	font-weight: 400;
	color: #fff;
	margin: 3px 0;
	padding: 4px 8px;
	cursor: pointer;
}
.sidebar-menu-inner .devices .reload {
	font-size: 25px;
	line-height: 30px;
	font-weight: 400;
	color: #fff;
	margin: 3px 0;
	padding: 4px 8px;
	cursor: pointer;
}
.sidebar-menu-inner .devices .active {
	color: #aaa;
}
.sidebar-menu-inner .devices .hideDev {
	color: #fff;
	display: block;
	margin: 3px 0;
	padding: 11px;
	float: left;
	cursor: pointer;
}
.sidebar-menu-inner .devices {
	margin: 0px 0px 0px auto;
	position: fixed;
	bottom: 0px;
	left: 0px;
	width: 340px;
	padding-left: 4.05%;
	background: #2c2e2f;
	border-top: 2px solid #aaa;
	z-index: 5;
}
.sidebar-menu.collapsed .devices {
	width: 80px;
	padding-left: 0px;
	height: 54px;
}
.sidebar-menu .main-menu {
	padding-bottom: 60px;
}
.sidebar-menu.collapsed [class*='resize-'] {
	-webkit-transform: scale(0);
	transform: scale(0);
}
.sidebar-menu.collapsed .devices .hideDev {
	text-align: center;
	display: table;
	margin: 0px auto;
	padding-top: 20px;
	display: block;
	margin: -4px -8px;
}
.sidebar-menu.collapsed .devices .hideDev span {
	display: inline-block;
}
.sidebar-menu.collapsed .devices .hideDev span {
	-webkit-transform: scale(0);
	transform: scale(0);
}
.page-container .main-content .page-title, nav.navbar {
	display: none;
}
</style>
<script>
$(document).ready(function() {
	setTimeout(function() {
		$('.iframe > div > div iframe').attr("src", "{C_default_http_local}?noShowAdmin");
		$('.iframe > div > div iframe').load(function() {
			var linked = $('.iframe > div > div iframe').contents()[0].location.href;
			var noAdmin = linked.substr(-("?noShowAdmin".length)).length;
			linked = linked.substr(0, linked.length-noAdmin);
			$(".iframe input").val(linked);
			$('.iframe > div > div iframe').contents().find("a[href*='/']").each(function(i, k) {
				var elem = k;
				var tester = new RegExp("{C_default_http_host}#", "g");
				if(!tester.test(elem.href)) {
					$(elem).attr("href", elem.href+(elem.href.match(/\?/) ? "&noShowAdmin" : "?noShowAdmin"));
				}
			});
		});
	}, 1500);
	$('.colorpicker').on('changeColor.colorpicker', function(event) {
		console.log(event.color.toRGB());
		if($(this).attr("data-colorId")) {
			$('.iframe > div > div iframe').contents().find("#styleId-"+$(this).attr("data-colorId")).remove();
			var col = event.color.toRGB();
			$('.iframe > div > div iframe').contents().find("body").append("<style id='styleId-"+$(this).attr("data-colorId")+"'> .colorSize-"+$(this).attr("data-colorId")+" { background-color: rgba("+col.r+","+col.g+","+col.b+","+col.a+") !important; } </style>");
		}
	});
	$(".saveBackground").click(function() {
		var backgrounds = {};
		$(".backgrounds").each(function(i, elem) {
			backgrounds[$(elem).attr("data-colorId")] = $(elem).val();
		});
		jQuery.post("./?pages=Customize&saveCss=true", {"backgrounds": backgrounds}, function(data) {}).done(function(data) {
			toastr.success("Успешно сохранили", "{L_done}");
		}).fail(function(data) {
			toastr.error("Ошибка при сохранении", "{L_error}");
		});
		return false;
	});
});
$(".iframe input").change(function() {
	if($(this).val().match(/{D_ADMINCP_DIRECTORY}/)) {
		alert("You are stuped idiot!");
		return false;
	}
	$('.iframe > div > div iframe').attr("src", ($(this).val().match(/\?/) ? $(this).val()+"&noShowAdmin" : $(this).val()+"?noShowAdmin"));
});
$(".sidebar-menu-inner").append('<div class="devices"><div class="hideDev fa-minus-square"><span>&nbsp;Скрыть панель</span></div><div class="resize-desktop fa-desktop active" data-resize="desktop"></div><div class="resize-tablet fa-tablet" data-resize="tablet"></div><div class="resize-mobile fa-mobile" data-resize="mobile"></div><div class="reload fa-retweet"></div></div>');
$(".resize-desktop, .resize-tablet, .resize-mobile").click(function() {
	$(".sidebar-menu-inner").find("[class*='resize-']").removeClass("active");
	$(this).addClass("active");
	$(".iframe > div > div").removeClass("desktop tablet mobile").addClass($(this).attr("data-resize"));
	$('.iframe > div > div iframe').contents().find("body").removeClass("desktop tablet mobile").addClass($(this).attr("data-resize"));
});
$(".hideDev").click(function() {
	if($(".sidebar-menu").hasClass("collapsed")) {
		$(this).removeClass("fa-plus-square").addClass("fa-minus-square");
	} else {
		$(this).removeClass("fa-minus-square").addClass("fa-plus-square");
	}
	$(".sidebar-menu").toggleClass("collapsed");
});
$(".reload").click(function() {
	$('.iframe > div > div iframe').contents()[0].location.reload();
	toastr.success("Reloaded");
});
</script>