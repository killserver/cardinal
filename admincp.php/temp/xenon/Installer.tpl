<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default panel-tabs">
			<div class="panel-heading">
				<h3 class="panel-title">{L_"Модули"}</h3>
				<div class="panel-options">
					<ul class="nav nav-tabs">
						<li class="active">
							<a href="#tab-4" data-toggle="tab">{L_"Установленные"}</a>
						</li>
						<li>
							<a href="#tab-5" data-toggle="tab">{L_"Управление серверами"}</a>
						</li>
						<li>
							<a href="#tab-6" data-toggle="tab">{L_"Список модулей"}</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="panel-body">
				<div class="tab-content">
					<div class="tab-pane active" id="tab-4">
						<table class="table table-hover responsive">
							<thead>
								<tr>
									<th>#</th>
									<th width="200">{L_"Изображение"}</th>
									<th>{L_"Название"}</th>
									<th width="50%">{L_"Описание"}</th>
								</tr>
							</thead>
							<tbody id="accordion">
								[foreach block=installed]<tr>
									<td>{installed.$id}</td>
									<td width="200"><img src="{installed.image}" style="max-width: 100%;"></td>
									<td>
										<b>{installed.name}</b><br>
										<div class="btns" style="display: table-cell; vertical-align: bottom;">
											[foreachif {installed.hasUpdate}==true]<a href="#" class="btn btn-purple btn-icon btn-icon-standalone btn-sm update" data-action="{installed.altName}"><i class="fa fa-refresh"></i><span>{L_"Обновить"}</span></a>[/foreachif {installed.hasUpdate}==true]
											[foreachif {installed.active}=="active"&&{installed.OnlyUse}==false]<a href="#" class="btn btn-blue btn-sm action actived" data-action="{installed.altName}" data-status="{installed.active}" turquoise><span>{L_"Отключить"}</span></a>[/foreachif {installed.active}=="active"&&{installed.OnlyUse}==false]
											[foreachif {installed.active}=="unactive"&&{installed.OnlyUse}==false]<a href="#" class="btn btn-turquoise btn-sm action actived" data-action="{installed.altName}" data-status="{installed.active}"><span>{L_"Включить"}</span></a>[/foreachif {installed.active}=="unactive"&&{installed.OnlyUse}==false]
											<a href="#" class="btn btn-red btn-sm remove" data-action="{installed.altName}"><span>{L_"Удалить"}</span></a>
										</div>
									</td>
									<td width="50%">{installed.description}[foreachif {installed.noChangelog}==false]<br><a class="btn" data-toggle="collapse" data-parent="#accordion" href="#collapseOne-{installed.$id}">{L_"Список изменений"}</a><div id="collapseOne-{installed.$id}" class="collapse">{installed.changelog}</div>[/foreachif {installed.noChangelog}==false]</td>
								</tr>[/foreach]
							</tbody>
						</table>
					</div>
					<div class="tab-pane" id="tab-5">
						<textarea class="form-control" rows="15">{listServer}</textarea><br><a href="#" class="saveListModules btn btn-success pull-right">{L_"Сохранить"}</a>
					</div>
					<div class="tab-pane moduleList" id="tab-6">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/template" id="templateCategory">
	<div class="col-xs-12">
		<input type="search" class="form-control quickSearch" placeholder="{L_"Быстрый поиск"}">
		<br><br>
		<div class="searched"></div>
	</div>
	<div class="col-md-12 allItems">
		<ul class="nav nav-tabs nav-tabs-justified" data-item="true">
			{head}
		</ul>
		<div class="tab-content" data-item="true">
			{body}
		</div>
	</div>
</script>
<script type="text/template" id="templateItem">
	<div class="col-sm-4">
		<a href="#" data-info="{altName}" style="width: 100%;"{installedHead}>
			<div style="height:15em;display:flex;align-items:center;"><div class="img" style="background-image:url('{image}');"></div></div>
			<br>
			<b style="width: 100%; height: 3em; display: table-cell; vertical-align: middle; white-space: normal;">{name}</b>
		</a>
		{installedFoot}
	</div>
</script>
<script type="text/template" id="templateItemInstalledHead1">
	data-install="install" class="btn install "
</script>
<script type="text/template" id="templateItemInstalledHead2">
	data-install="update" class="btn update"
</script>
<script type="text/template" id="templateItemInstalledHead3">
	data-install="installed" class="btn installed"
</script>
<script type="text/template" id="templateItemInstalledHead4">
	data-install="buy" class="btn buy"
</script>
<script type="text/template" id="templateItemInstalledFoot0">
	<a href="#" class="btn btn-red btn-block disabled">{L_"Поддерживается на версии"} {version}</a>
</script>
<script type="text/template" id="templateItemInstalledFoot1">
	<a href="#" class="btn btn-turquoise btn-block action install" data-action="{altName}">{L_"Установить"}</a>
</script>
<script type="text/template" id="templateItemInstalledFoot2">
	<a href="#" class="btn btn-blue btn-block action update" data-action="{altName}">{L_"Обновить"}</a>
</script>
<script type="text/template" id="templateItemInstalledFoot3">
	<a href="#" class="btn btn-success btn-block action installed" style="cursor: not-allowed;" data-action="{altName}">{L_"Установлено"}</a>
</script>
<script type="text/template" id="templateItemInstalledFoot4">
	<a href="#" class="btn btn-purple btn-block action buy" data-action="{altName}">{L_"Купить"}</a>
</script>
<script type="text/template" class="langName">{langName}</script>
<script type="text/javascript">
	var cardinalVersionNow = parseFloat("{D_VERSION}");
	var infoAll = '{infoAll}';
	infoAll = JSON.parse(infoAll);
	var langName = $(".langName").html();
	langName = JSON.parse(langName);
	var test;
	jQuery(document).ready(function($) {
		if(Object.keys(infoAll).length==0) {
			jQuery(".moduleList").html("{L_"Сервера не ответили вовремя либо не доступны"}");
		} else {
			var moduleAll = "";
			var themeAll = "";
			var pluginAll = "";
			var componentsAll = "";
			var allTmp = jQuery("#templateCategory").html();
			var tabs = {};
			Object.keys(infoAll).forEach(function(key) {
				tabs[infoAll[key].type] = infoAll[key].type;
			});
			var data = {};
			Object.keys(infoAll).forEach(function(key) {
				var tmpAll = jQuery("#templateItem").html();
				var installedHead = "";
				var installedFoot = "";
				var version = parseFloat(infoAll[key].cardinalVersion);
				if(cardinalVersionNow < version) {
					installedHead = "class=\"btn\"";
					installedFoot = jQuery("#templateItemInstalledFoot0").html();
					installedFoot = installedFoot.replace(/\{version\}/g, infoAll[key].cardinalVersion);
				} else if(infoAll[key].installed==1) {
					installedHead = jQuery("#templateItemInstalledHead1").html();
					installedFoot = jQuery("#templateItemInstalledFoot1").html();
				} else if(infoAll[key].installed==2) {
					installedHead = jQuery("#templateItemInstalledHead2").html();
					installedFoot = jQuery("#templateItemInstalledFoot2").html();
				} else if(infoAll[key].installed==3) {
					installedHead = jQuery("#templateItemInstalledHead3").html();
					installedFoot = jQuery("#templateItemInstalledFoot3").html();
				} else if(infoAll[key].installed==4) {
					installedHead = jQuery("#templateItemInstalledHead4").html();
					installedFoot = jQuery("#templateItemInstalledFoot4").html();
				}
				tmpAll = tmpAll.replace(/\{installedHead\}/g, installedHead);
				tmpAll = tmpAll.replace(/\{installedFoot\}/g, installedFoot);
				tmpAll = tmpAll.replace(/\{altName\}/g, infoAll[key].altName);
				tmpAll = tmpAll.replace(/\{image\}/g, infoAll[key].image);
				tmpAll = tmpAll.replace(/\{name\}/g, infoAll[key].name);
				var typeData = infoAll[key].type;
				if(typeof(data[typeData])==="undefined") {
					data[typeData] = [];
				}
				data[typeData][data[typeData].length] = tmpAll;
			});
			var head = "", body = "";
			Object.keys(tabs).forEach(function(key) {
				head += '<li><a href="#'+key+'" data-toggle="tab"><span>'+(typeof(langName[key])==="undefined" ? key : langName[key])+'</span></a></li>';
				var dd = '';
				if(typeof(data[key])==="undefined") {
					return;
				}
				for(var i=0;i<data[key].length;i++) {
					dd += data[key][i];
				}
				body += '<div class="tab-pane" id="'+key+'"><div class="row">'+dd+'</div></div>';
			});
			allTmp = allTmp.replace(/\{head\}/g, head);
			allTmp = allTmp.replace(/\{body\}/g, body);
			jQuery(".moduleList").html(allTmp);
			jQuery(".moduleList").find("[data-item]").each(function(i, elem) {
				$(elem).children().eq(0).addClass("active");
			});
		}
		jQuery(".btns").each(function(i, elem) {
			jQuery(elem).css("height", jQuery(elem).parent().outerHeight()-jQuery(elem).parent().find("b").outerHeight()*3);
		});
		jQuery("body").on("click", "div > a[data-action]:not(.disabled)", function() {
			test = this;
			var action = this;
			if(jQuery(this).hasClass("actived")) {
				jQuery.post("./?pages=Installer&active="+jQuery(this).attr("data-action"), function(data) {
					jQuery(action).html(jQuery(action).attr("data-status")=="active" ? "{L_"Включить"}" : "{L_"Отключить"}");
					if(jQuery(action).attr("data-status")=="active") {
						jQuery(action).removeClass("btn-blue").addClass('btn-turquoise');
					} else {
						jQuery(action).removeClass("btn-turquoise").addClass('btn-blue');
					}
					jQuery(action).attr("data-status", (jQuery(action).attr("data-status")=="active" ? "unactive" : "active"));
					toastr.info("{L_"Переключён режим работы модуля"}");
				});
			} else if(jQuery(this).hasClass('remove')) {
				var th = this;
				toastr.info("{L_"Удаление модуля"}");
				jQuery.post("./?pages=Installer&remove="+jQuery(th).attr("data-action"), function(data) {}).fail(function(data) {
					toastr.error("{L_"Модуль не был удален, попробуйте позже"}");
				}).done(function(data) {
					jQuery(th).parent().parent().parent().remove(600);
					toastr.info("{L_"Удален модуль"} \""+jQuery(th).attr("data-action")+"\". {L_"Через 3 секунды произойдёт обновление страницы"}");
					setTimeout(function() {
						window.location.reload();
					}, 3000);
				});
			} else if(jQuery(this).hasClass('install')) {
				var th = this;
				toastr.info("{L_"Скачивание модуля"}");
				jQuery.post("./?pages=Installer&download="+jQuery(th).attr("data-action"), function(data) {}).fail(function(data) {
					toastr.error("{L_"Модуль не был скачан, попробуйте позже"}");
				}).done(function(data) {
					toastr.info("{L_"Установка нового модуля"}");
					jQuery.post("./?pages=Installer&install="+jQuery(th).attr("data-action"), function(data) {}).fail(function(data) {
						toastr.error("{L_"Модуль не был установлен, попробуйте позже"}");
					}).done(function(data) {
						toastr.info("{L_"Установлен новый модуль"}. {L_"Через 3 секунды произойдёт обновление страницы"}");
						setTimeout(function() {
							window.location.reload();
						}, 3000);
					});
				});
			} else if(jQuery(this).hasClass('update')) {
				var th = this;
				toastr.info("{L_"Обновление модуля"}");
				jQuery.post("./?pages=Installer&download="+jQuery(th).attr("data-action"), function(data) {}).fail(function(data) {
					toastr.error("{L_"Модуль не был скачан, попробуйте позже"}");
				}).done(function(data) {
					toastr.info("{L_"Обновление модуля"}");
					jQuery.post("./?pages=Installer&install="+jQuery(th).attr("data-action"), function(data) {}).fail(function(data) {
						toastr.error("{L_"Модуль не был обновлён, попробуйте позже"}");
					}).done(function(data) {
						toastr.info("{L_"Обновлён модуль"}");
					});
				});
			} else if(jQuery(this).hasClass('installed')) {
				toastr.info("{L_"Модуль успешно запущен и работает без нарицаний"}");
			} else if(jQuery(this).hasClass('buy')) {
				jQuery("#modal-4 .modal-title").html("Приобретение "+jQuery(this).attr("data-action"));
				var tmp = '<form class="Paymentform" method="POST" action="https://api.privatbank.ua/p24api/ishop"><input type="hidden" name="amt" value="{price}" /><input type="hidden" name="ccy" value="UAH" /><input type="hidden" name="merchant" value="1234567890" /><input type="hidden" name="order" value="'+jQuery(this).attr("data-action")+'" /><input type="hidden" name="details" value="'+jQuery(this).attr("data-action")+'" /><input type="hidden" name="ext_details" value="'+jQuery(this).attr("data-action")+'" /><input type="hidden" name="pay_way" value="privat24" /><input type="hidden" name="return_url" value="" /><input type="hidden" name="server_url" value="" /><button type="submit" class="Privat24">Приват 24</button></form>';
				tmp = tmp.replace(/\{price\}/g, "1000");
				jQuery("#modal-4 .modal-body").html(tmp);
				jQuery("#modal-3 [data-dismiss]").click();
				setTimeout(function() { jQuery("#modal-4").modal('show'); }, 400);
			}
			return false;
		});
		jQuery("body").on("click", "div > a[data-info]", function() {
			var data = infoAll[jQuery(this).attr("data-info")];
			var installation = jQuery(this).attr("data-install");
			console.log(data);
			console.log(installation);
			jQuery("#modal-3 .modal-body").html(jQuery("#templateModule").html());
			jQuery("#modal-3 .modal-body .title").html(data.name);
			jQuery("#modal-3 .modal-body .description span").html(data.description);
			jQuery("#modal-3 .modal-body .version span").html(data.version);
			if(typeof(data.authorLink)!=="undefined") {
				jQuery("#modal-3 .modal-body .author span").remove();
				jQuery("#modal-3 .modal-body .author a").attr("href", data.authorLink).html(data.author);
			} else {
				jQuery("#modal-3 .modal-body .author a").remove();
				jQuery("#modal-3 .modal-body .author span").html(data.author);
			}
			jQuery("#modal-3 .modal-body .screens").remove();
			jQuery("#modal-3 .modal-body .img").css("backgroundImage", "url('"+data.image+"')");
			var html = "";
			if(typeof(data.changelog)!=="undefined") {
				Object.keys(data.changelog).forEach(function(v) {
					html += "<b>"+v+"</b>&nbsp;"+data.changelog[v]+"<br>";
				});
			}
			if(typeof(data.afterInstall)!=="undefined") {
				jQuery("#modal-3 .modal-body .installation span").html(data.afterInstall);
			} else {
				jQuery("#modal-3 .modal-body .installation").remove();
			}
			if(html.length>0) {
				jQuery("#modal-3 .modal-body .changelog span").html(html);
			} else {
				jQuery("#modal-3 .modal-body .changelog").remove();
			}
			jQuery(".btn-copy").remove();
			jQuery(".modal .modal-dialog .modal-content .modal-footer .btn").after('<a href="#" class="btn action btn-copy">{L_"Обновить"}</a>');
			var version = parseFloat(data.cardinalVersion);
			if(cardinalVersionNow < version) {
				jQuery("#modal-3 .modal-footer a.btn.action").attr("class", "").addClass("btn btn-copy btn-red disabled").css("cursor", "").html("{L_"Поддерживается на версии"} "+data.cardinalVersion);
			} else if(installation=="update") {
				jQuery("#modal-3 .modal-footer a.btn.action").attr("class", "").addClass("btn btn-copy action btn-blue update").css("cursor", "").attr("data-action", data.altName).html("{L_"Обновить"}");
			} else if(installation=="installed") {
				jQuery("#modal-3 .modal-footer a.btn.action").attr("class", "").addClass("btn btn-copy action btn-success installed").css("cursor", "not-allowed").attr("data-action", data.altName).html("{L_"Установлено"}");
			} else if(installation=="install") {
				jQuery("#modal-3 .modal-footer a.btn.action").attr("class", "").addClass("btn btn-copy action btn-turquoise install").attr("data-action", data.altName).html("{L_"Установить"}");
			} else if(installation=="buy") {
				jQuery("#modal-3 .modal-footer a.btn.action").attr("class", "").addClass("btn btn-copy action btn-purple buy").attr("data-action", data.altName).html("{L_"Купить"}");
			}
			jQuery("#title_video").html(data.name);
			jQuery(".modal .modal-dialog .modal-content .modal-body").css("overflow", "auto");
			jQuery('#modal-3').modal('show');
			return false;
		});
		jQuery("body").on("input", ".quickSearch", function() {
			var v = $(this).val();
			if(v.length==0) {
				jQuery(".allItems").removeClass("hide");
				jQuery(".searched").html("");
			} else {
				var ds = [];
				Object.keys(infoAll).forEach(function(key) {
					if(new RegExp(v, "ig").test(key)) {
						ds[ds.length] = infoAll[key];
					} else if(new RegExp(v, "ig").test(infoAll[key].name)) {
						ds[ds.length] = infoAll[key];
					} else if(typeof(infoAll[key].tags)!=="undefined" && new RegExp(v, "ig").test(infoAll[key].tags)) {
						ds[ds.length] = infoAll[key];
					}
				});
				var res = "";
				var data = {};
				for(var i=0;i<ds.length;i++) {
					var tmpAll = jQuery("#templateItem").html();
					var installedHead = "";
					var installedFoot = "";
					var version = parseFloat(ds[i].cardinalVersion);
					if(cardinalVersionNow < version) {
						installedHead = "class=\"btn\"";
						installedFoot = jQuery("#templateItemInstalledFoot0").html();
						installedFoot = installedFoot.replace(/\{version\}/g, ds[i].cardinalVersion);
					} else if(ds[i].installed==1) {
						installedHead = jQuery("#templateItemInstalledHead1").html();
						installedFoot = jQuery("#templateItemInstalledFoot1").html();
					} else if(ds[i].installed==2) {
						installedHead = jQuery("#templateItemInstalledHead2").html();
						installedFoot = jQuery("#templateItemInstalledFoot2").html();
					} else if(ds[i].installed==3) {
						installedHead = jQuery("#templateItemInstalledHead3").html();
						installedFoot = jQuery("#templateItemInstalledFoot3").html();
					} else if(ds[i].installed==4) {
						installedHead = jQuery("#templateItemInstalledHead4").html();
						installedFoot = jQuery("#templateItemInstalledFoot4").html();
					}
					tmpAll = tmpAll.replace(/\{installedHead\}/g, installedHead);
					tmpAll = tmpAll.replace(/\{installedFoot\}/g, installedFoot);
					tmpAll = tmpAll.replace(/\{altName\}/g, ds[i].altName);
					tmpAll = tmpAll.replace(/\{image\}/g, ds[i].image);
					tmpAll = tmpAll.replace(/\{name\}/g, ds[i].name);
					res += tmpAll;
				}
				if(res.length==0) {
					res = "{L_"По Вашему запросу - ничего не найдено"}";
				}
				jQuery(".allItems").addClass("hide");
				jQuery(".searched").html(res);
			}
		});
	});
	var disableAllEditors = true;
</script>
<script type="text/template" id="templateModule">
	<div class="installator">
		<div class="col-sm-12"><div class="img"><div class="title"></div></div></div>
		<div class="col-sm-12">
		</div>
		<div class="col-sm-9">
			<div class="col-md-12">
				<ul class="nav nav-tabs nav-tabs-justified">
					<li class="active description">
						<a href="#home-1" data-toggle="tab">{L_"Описание"}</a>
					</li>
					<li class="screens">
						<a href="#home-2" data-toggle="tab">{L_"Скриншоты"}</a>
					</li>
					<li class="changelog">
						<a href="#home-3" data-toggle="tab">{L_"Список изменений"}</a>
					</li>
					<li class="installation">
						<a href="#home-4" data-toggle="tab">{L_"Установка"}</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active description" id="home-1">
						<span></span>
					</div>
					<div class="tab-pane screens" id="home-2">
						<span></span>
					</div>
					<div class="tab-pane changelog" id="home-3">
						<span></span>
					</div>
					<div class="tab-pane installation" id="home-4">
						<span></span>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<ul class="list-group list-group-minimal">
				<li class="list-group-item version">
					<span class="badge badge-roundless badge-primary" style="font-size: 12px; letter-spacing: 0.04em; font-weight: normal;"></span>{L_"Версия"}:
				</li>
				<li class="list-group-item author">
					<span class="badge badge-roundless badge-info" style="font-size: 12px; letter-spacing: 0.04em; font-weight: normal;"></span>{L_"Автор"}:
				</li>
			</ul>
		</div>
	</div>
</script>
<style type="text/css">
	.modal-body .img {
		width: 100%;
		height: 25em;
		background-size: 90%;
		background-attachment: fixed;
		background-repeat: no-repeat;
		background-position: 50% 0%;
		border: 0.5em solid #ddd;
		margin-bottom: 2em;
		position: relative;
	}
	.modal-footer a.btn {
		border-radius: .25rem;
	}
	.modal-body .img .title {
		position: absolute;
		bottom: 2em;
		left: 2em;
		background: #404040;
		color: #fff;
		padding: 1rem 1.5rem;
		border-radius: 0.25rem;
		letter-spacing: 0.06rem;
		font-size: 1.25rem;
		font-weight: 700;
	}
	.modal .col-sm-12.description {
		margin-bottom: 2em;
	}
	.modal .col-sm-12.changelog {}
	.modal .col-sm-12.changelog b {
		color: #00f;
		margin: 0.5em 0px 0.5em;
		display: table;
	}
	.modal .col-sm-12.changelog b:before {
		content: '- ';
	}
	.modal .col-sm-12 h4 {
		color: #333;
		font-style: italic;
		font-weight: bold;
		margin: 1em 0px;
	}
	.tab-content .tab-content {
		background: whitesmoke !important;
	}
	.tab-content .nav.nav-tabs > li > a {
		background-color: #fff;
	}
	.tab-content .nav.nav-tabs > li.active > a {
		background-color: #f4f4f4;
		border: 0px;
	}
	.tab-content .nav.nav-tabs > li > a:hover {
		background-color: #f4f4f4;
	}
	.tab-content a .img {
		width: 100%;
		height: 15em;
		margin: auto;
		display: table;
		background-repeat: no-repeat;
		background-size: cover;
		background-position: 50%;
	}

	.installator .nav.nav-tabs > li > a,
	.installator .nav-tabs > li.active > a:focus {
		border: 1px solid #ddd;
	}
	.installator .nav.nav-tabs > li > a,
	.installator .nav.nav-tabs.nav-tabs-justified {
		background: #ddd;
	}
	.installator .nav.nav-tabs > li.active > a {
		background: #fff;
	}
	.installator .nav.nav-tabs > li > a:hover {
		border: 1px solid #ddd;
	}
	.modal .modal-dialog .modal-content .modal-footer button.btn {
		display: none;
	}
	.modal .modal-dialog .modal-content .modal-body::-webkit-scrollbar {
		width: 9px;
		background: white;
		border: 1px solid #dddddd;
	}
	.modal .modal-dialog .modal-content .modal-body::-webkit-scrollbar-thumb {
		background: #ddd;
	}
</style>