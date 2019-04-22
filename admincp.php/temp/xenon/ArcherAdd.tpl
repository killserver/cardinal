[!ajax]<form method="post" role="form" action="./?pages=Archer&type={ArcherPath}&pageType=Take{ArcherPage}{addition}{ref}" class="form-horizontal" enctype="multipart/form-data">
	<div class="row">
		<div class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">{ArcherMind}</h3>
				</div>
				<div class="panel-body">[/!ajax]
						[if {count[supportedLang]}>=1]
							</div></div>
							<div class="panel panel-default panel-tabs" data-panel-lang="true">
								<div class="panel-body">
									<ul class="nav nav-tabs nav-tabs-justified" data-support="lang">
										[foreach block=supportedLang]<li>
											<a href="#home-3" data-toggle="tab" data-lang="{supportedLang.lang}">{supportedLang.lang}</a>
										</li>[/foreach]
									</ul>
								</div>
							</div>
							<div class="panel panel-default">
								<div class="panel-body">
						[/if {count[supportedLang]}>=1]
						<br><br>
						<input type="hidden" name="removeImg" class="removeImages" value="">
						{ArcherData}
						[!ajax]
				</div>
			</div>
			<div class="panel panel-default panel-tabs" data-panel-submit="true">
				<div class="panel-body">
					<button class="btn btn-single btn-savePage btn-icon btn-icon-standalone btn-icon-standalone-right btn-sm">
						<i class="fa-save"></i>
						<span>{L_save}</span>
					</button>
				</div>
			</div>
		</div>
	</div>
	{E_[KernalArcher::AfterForm][type={ArcherPath};data={addition}]}
</form>[/!ajax]
<style>
	#inputForFile .array {
		padding-bottom: 2.5em;
		border-bottom: 0.1em solid #e4e4e4;
		margin-bottom: 2em;
		display: inline-block;
		width: 100%;
	}
	#inputForFile .array:last-of-type {
	    border-bottom: 0px
	}
	.panel-body .nav.nav-tabs > li > a {
		border: 1px solid #ddd;
	}
</style>
<script type="text/javascript">
	function ucfirst(text) {
		var textNew = "";
		textNew += text.substr(0, 1).toUpperCase();
		textNew += text.substr(1);
		return textNew;
	}
	var removeImg = {};
	$("body").off("click").on("click", ".removeImg", function() {
		var id = $(this).attr("id");
		id = id.replace("remove_", "");
		$("input[id='"+id+"']").parent().find(".tmpImage,img").remove();
		removeImg[id] = true;
		var get = $("#"+id+"").parent().html();
		$("#"+id+"").parent().html(get);
		$(".removeImages").val(Object.keys(removeImg).join(","));
		$("input[id='"+id+"']").change(function() {
			delete removeImg[id];
			$(".removeImages").val(Object.keys(removeImg).join(","));
		});
		return false;
	});
	function reinitLang() {
		var selectLangForm = ucfirst(selectLang);
		$(".form-horizontal ul li a[data-lang='"+selectLangForm+"']").parent().addClass("active");
		$(".form-horizontal .form-group[data-group][data-lang]").each(function(i, elem) {
			if($(elem).attr("data-lang")===selectLangForm) {
				$(elem).css("display", "block");
			} else {
				$(elem).css("display", "none");
			}
		});
		$(".form-horizontal ul li a").off("click").click(function() {
			var lang = $(this).attr("data-lang");
			$(".form-horizontal .form-group[data-group][data-lang]").each(function(i, elem) {
				if($(elem).attr("data-lang")===lang) {
					$(elem).css("display", "block");
				} else {
					$(elem).css("display", "none");
				}
			});
		});
		function ucfirst(text) { return text.substr(0, 1).toUpperCase()+text.substr(1); }
		selectedLang = ucfirst(selectLang);
		var arrLang = [];
		for(var i=0;i<langSupport.length;i++) {
			if(langSupport[i]!=selectedLang) { arrLang[arrLang.length] = langSupport[i]; }
		}
		langSupport = arrLang;
	}
	reinitLang();
	var onInteractive = {};
	$(document).ready(function() {
		setTimeout(function() {
			$("form > div").each(function(i, elem) {
				var block = $(elem).attr("class").replace("form-group block-", "");
				var tt = tinymce.get(block);
				if(tt!=null) {
					tt.on("focus", function() {
						onInteractive[this.id] = true;
					});
					tt.on("keydown paste cut change", function() {
						var text = this.getContent();
						var name = this.id;
						for(var i=0;i<langSupport.length;i++) {
							name1 = name.replace(new RegExp(selectedLang, "g"), "");
							name1 = name1.replace(new RegExp(langSupport[i], "g"), "");
							var sup = tinymce.get(name1+langSupport[i]);
							if(sup!=null && typeof(onInteractive[name1+langSupport[i]])==="undefined") {
								sup.setContent(text);
								sup.undoManager.add({content: text});
							}
						}
					});
				} else {
					var ret = false;
					if(block.indexOf(selectedLang)>-1) {
						ret = true;
					}
					if(ret) {
						$(elem).find("textarea,input").on("keydown paste cut", function() {
							var name = this.id;
							var text = this.value;
							for(var i=0;i<langSupport.length;i++) {
								name = name.replace(new RegExp(selectedLang, "g"), "");
								name = name.replace(new RegExp(langSupport[i], "g"), "");
								var data = $("#"+name+langSupport[i]);
								if(data!=null && (data.val()=="" || data.val()==text.substr(0, text.length-1))) {
									data.val(text);
								}
							}
						});
					}
				}
			});
			[if {ArcherPageNow}==Edit]$("input,textarea").each(function(i, elem) {
				for(var i=0;i<langSupport.length;i++) {
					name = $(elem).attr("id");
					name1 = name.replace(new RegExp(selectedLang, "g"), "");
					name1 = name1.replace(new RegExp(langSupport[i], "g"), "");
					onInteractive[name1+langSupport[i]] = true;
				}
			});[/if {ArcherPageNow}==Edit]
		}, 2000);
	});
	jQuery(window).load(function() {
		console.log("test");
		setTimeout(function() {
			for(var i=0;i<tinyMCE.editors.length;i++){tinyMCE.editors[i].notificationManager.close();}
		}, 200);
	});
	[ajax]var linkForSubmit = "./?pages=Archer&type={ArcherPath}&pageType=Take{ArcherPage}{addition}{ref}";[/ajax]
	var i = 1;
	function removeInputFile(th, name, val) {
		var bef = jQuery('input[name="deleteArray['+name+']"]').val();
		jQuery('input[name="deleteArray['+name+']"]').val(val+","+bef);
		jQuery(th).parent().parent().remove();
	}
	function showPreviewFn() {
		jQuery(".showPreview.new").each(function(i, elem) {
			jQuery(elem).parent().find("img").remove();
			jQuery(elem).html("<img src='"+jQuery(elem).attr("href")+"' data-link='"+jQuery(elem).attr("data-link")+"' style='max-width:100%; background:#333; display: table;max-height:400px'>");
		});
		jQuery(".showPreview:not(.new)").each(function(i, elem) {
			jQuery(elem).after("<br><img src='"+jQuery(elem).attr("href")+"' width='200' style='background:#333; display: table;'>");
		});
	}
	jQuery(document).ready(function() {
		showPreviewFn();
		jQuery("body").on("click", ".accessRemove", function() {
			var count = jQuery(".containerFiles[data-parent='"+jQuery(this).attr("data-parent")+"'] input").length;
			if(count==1) {
				jQuery(this).parent().parent().find("input").val("").change();
				jQuery(this).parent().parent().find("img").remove();
				jQuery(this).parent().parent().find("a[data-link]").remove();
			} else {
				jQuery(this).parent().parent().remove();
			}
			return false;
		});
	});
	function addInputFile(th, name) {
		var elem = jQuery(th).parent().find("div#inputForFile");
		elem.append('<div><div class="col-sm-10"><input class="form-control" type="file" multiple="multiple"'+(elem.attr("data-accept") ? 'accept="'+elem.attr("data-accept")+'"' : "")+' name="'+name+'[]" placeholder="Выберите файл"></div><div class=\'col-sm-2\'><a class=\'btn btn-red btn-block fa-remove\' onclick=\'jQuery(this).parent().parent().remove();\'></a></div></div>');
		jQuery("input[type='file'][accept*='image']").unbind("change").change(function() {
			readURL(this);
		});
		i++;
	}
	function rand(min, max) { if(min===undefined) min=-9999999; if(max===undefined) max=9999999; if(max) return Math.floor(Math.random()*(max-min+1))+min; else return Math.floor(Math.random()*(min+1)); }
	function addInputFileAccess(th, name, type) {
		var elem = jQuery(th).parent().find("div#inputForFiles");
		var field_id = rand();
		elem.append('<div class="row array" data-show="'+field_id+'"><div class="col-sm-10"><input class="form-control" id="'+field_id+'" type="text"'+(elem.attr("data-accept") ? 'accept="'+elem.attr("data-accept")+'" data-accept="'+elem.attr("data-accept")+'"' : "")+' name="'+name+'[]" placeholder="Выберите файл" style="position:fixed;top:-99999px;left:-99999px;z-index:-1000;"><a href="{C_default_http_host}{D_ADMINCP_DIRECTORY}/assets/tinymce/filemanager/dialog.php?type='+type+'&field_id='+field_id+'&relative_url=0" class="btn btn-icon btn-success iframe-btn btn-block"><i class="fa-plus"></i></a></div><div class=\'col-sm-2\'><a class=\'btn btn-red btn-block fa-remove\' onclick=\'jQuery(this).parent().parent().remove();\'></a></div></div>');
		i++;
	}
	jQuery("body").on("change_filemanager", function(e, data) {
		console.log(data);
		data.parent.append('<a data-link="'+data.field_id+'" id="img'+data.field_id+'" href="'+data.http_link+'"'+(data.type.indexOf("image")>-1 || data.type.indexOf("imageAccess")>-1 || data.type.indexOf("imageArrayAccess")>-1 ? " class=\"showPreview new\"" : "")+' target="_blank">Просмотреть</a>');
		jQuery(".showPreview.new").each(function(i, elem) {
			jQuery(elem).parent().find("img").remove();
			jQuery(elem).html("<img src='"+jQuery(elem).attr("href")+"' data-link='"+jQuery(elem).attr("data-link")+"' style='max-width:100%; background:#333; display: table;max-height:400px'>");
		});
	});
	function readURL(input) {
		if(input.files && input.files[input.files.length-1]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				jQuery(input).parent().find(".tmpImage").remove();
				jQuery(input).parent().find("img").remove();
				jQuery(input).parent().append('<div class="tmpImage" style="background: #333; display: table;"><img src="'+e.target.result+'" width="200" style="opacity: 0.75;"></div>');
			}
			reader.readAsDataURL(input.files[input.files.length-1]);
		}
	}
	jQuery("body").on("change", "input[type='file'][accept*='image']", function() {
		readURL(this);
	});
</script>