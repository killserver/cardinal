[!ajax]<form method="post" role="form" action="./?pages=Archer&type={ArcherPath}&pageType=Take{ArcherPage}{addition}{ref}" class="form-horizontal formSubmit" enctype="multipart/form-data">
	{E_[KernalArcher::BeforeForm][type={ArcherPath};action={ArcherPage};data={addition}]}
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
	</div>[/!ajax]
	{E_[KernalArcher::AfterForm][type={ArcherPath};action={ArcherPage};data={addition}]}
[!ajax]</form>[/!ajax]
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
	input + .btn {
	    margin: 0;
	}
	input + .btn + a {
	    margin-bottom: 20px;
	    display: table;
	}
</style>
<script type="text/javascript">
	// Dynamically load images while scrolling
	// Source: github.com/ByNathan/jQuery.loadScroll
	// Version: 1.0.1

	(function($) {
	    
	    $.fn.loadScroll = function(duration, elem) {
	    
	        var $window = $(window);
	    	if(elem) {
	    		$window = $(elem);
	    	}
	        var images = this,
	            inview,
	            loaded;

	        images.one('loadScroll', function() {
	            
	            if (this.getAttribute('data-src')) {
	                this.setAttribute('src',
	                this.getAttribute('data-src'));
	                this.removeAttribute('data-src');
	                
	                if (duration) {
	                    
	                    $(this).hide()
	                           .fadeIn(duration)
	                           .add('img');
	                    
	                } else return false;
	            }
	            
	        });
	    
	        $window.scroll(function() {
	        
	            inview = images.filter(function() {
	                
	                var a = $window.scrollTop(),
	                    b = $window.height(),
	                    c = $(this).offset().top,
	                    d = $(this).height();
	                    
	                return c + d >= a && c <= a + b;
	                
	            });
	            
	            loaded = inview.trigger('loadScroll');
	            images = images.not(loaded);
	                    
	        });
	    };
	    
	})(jQuery);
</script>
<script type="text/javascript">
	function ucfirst(text) {
		var textNew = "";
		textNew += text.substr(0, 1).toUpperCase();
		textNew += text.substr(1);
		return textNew;
	}
	var removeImg = {};
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
	var editorReady = function() {
		jQuery("body").trigger("before_editorReady");
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
		jQuery("[data-ass]").each(function(i, elem) {
			var name = $(elem).attr("data-ass");
			var input = $(elem).find("input").attr("name");
			$(elem).find("input").attr("name", input+"["+name+"]");
		});
		jQuery("body").trigger("editorReady");
	}
	function minHeightForAdd() {
		$("[data-for-btn-data] .inputedAccess").each(function(i, elem) {
		    $(elem).on("change", function() {
		        if($(elem).val().length==0) {
		        	$(elem).parents("[data-for-btn-data]").find(".btn-add-image").addClass("minHeight")
		        } else {
		            $(elem).parents("[data-for-btn-data]").find(".btn-add-image").removeClass("minHeight");
		        }
		    });
		    if($(elem).val().length==0) {
		        $(elem).parents("[data-for-btn-data]").find(".btn-add-image").addClass("minHeight")
		    }
		});
	}
	jQuery(document).ready(function() {
		editorReady();
		minHeightForAdd();
		$("body").off("click", ".showPreview.new").on("click", ".showPreview.new", function(e) {
			if($(e.target).is(".showPreview.new") || e.target.closest(".showPreview.new")) {
				var item = $(e.target).is(".showPreview.new") ? e.target : e.target.closest(".showPreview.new");
				if($(item).parent().children("a.btn-success").hasClass("iframe-btn")) {
					e.preventDefault()
					$(item).parent().children("a.btn-success")[0].click()
		        }
		    }
		})
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
		jQuery(th).parents("[data-show]").remove();
	}
	function showPreviewFn() {
		jQuery(".showPreview.new").each(function(i, elem) {
			jQuery(elem).parent().find("img").remove();
			jQuery(elem).html("<img src='https://placehold.it/600x350&text=Loading...' data-src='"+jQuery(elem).attr("href")+"' data-link='"+jQuery(elem).attr("data-link")+"' style='max-width:100%; background:#333; display: table;max-height:400px'>");
		});
		jQuery(".showPreview:not(.new)").each(function(i, elem) {
			jQuery(elem).after("<img src='https://placehold.it/600x350&text=Loading...' data-src='"+jQuery(elem).attr("href")+"' width='200' style='background:#333; display: table;'>");
		});
    	$('img').loadScroll(500);
	}
	function addInputFile(th, name) {
		var elem = jQuery(th).parent().find("div#inputForFile");
		elem.append('<div><div class="col-sm-10"><input class="form-control" type="file" multiple="multiple"'+(elem.attr("data-accept") ? 'accept="'+elem.attr("data-accept")+'"' : "")+' name="'+name+'[]" placeholder="Выберите файл"></div><div class=\'col-sm-2\'><a class=\'btn btn-red btn-block fa-remove\' onclick=\'jQuery(this).parent().parent().remove();\'></a></div></div>');
		jQuery("input[type='file'][accept*='image']").unbind("change").change(function() {
			readURL(this);
		});
		minHeightForAdd();
		i++;
	}
	function rand(min, max) { if(min===undefined) min=-9999999; if(max===undefined) max=9999999; if(max) return Math.floor(Math.random()*(max-min+1))+min; else return Math.floor(Math.random()*(min+1)); }
	function addInputFileAccess(th, name, type, val, multiple) {
		if(typeof(val)==="undefined") {
			val = "";
		}
		if(typeof(multiple)==="undefined") {
			multiple = "";
		} else {
			name = name.split("[");
			name = name[0];
			multiple = "&multiple=1";
		}
		var elem = jQuery(th).parent().find("div#inputForFiles");
		console.warn(elem)
		var field_id = rand();
		var tmp = $(".template_array_access[data-template-id='"+name+"']").last().html();
		if(typeof(tmp)!=="undefined") {
			tmp = tmp.replace(new RegExp("{template_access_uid}", "ig"), field_id);
			tmp = tmp.replace(new RegExp("{template_access_id}", "ig"), arrayAccess[name]);
			tmp = tmp.replace(new RegExp("{template_access_name}", "ig"), name+(multiple.length>0 ? "[]" : ""));
			tmp = tmp.replace(new RegExp("{template_access_value}", "ig"), val);
			tmp = tmp.replace(new RegExp("{template_access_btnWithData}", "ig"), "");
		} else {
			tmp = '<div class="row array" data-show="'+field_id+'"><div class="col-sm-10"><input class="form-control" id="'+field_id+'" type="text"'+(elem.attr("data-accept") ? 'accept="'+elem.attr("data-accept")+'" data-accept="'+elem.attr("data-accept")+'"' : "")+' name="'+name+(multiple.length>0 ? "[]" : "")+'" placeholder="Выберите файл" style="position:fixed;top:-99999px;left:-99999px;z-index:-1000;" value="'+val+'"><a href="{C_default_http_host}{D_ADMINCP_DIRECTORY}/assets/tinymce/filemanager/dialog.php?type='+type+'&field_id='+field_id+'&relative_url=0'+multiple+'" class="btn btn-icon btn-success iframe-btn btn-block"><i class="fa-plus"></i></a></div><div class=\'col-sm-2\'><a class=\'btn btn-red btn-block fa-remove\' onclick=\'jQuery(this).parent().parent().remove();\'></a></div></div>';
		}


		elem.append(tmp);
		jQuery("body").trigger("addInputFileAccess", {
			"element": elem,
			"this": th,
			"name": name,
			"type": type,
			"value": val,
			"multiple": multiple
		});
		i++;
		arrayAccess[name]++;
		minHeightForAdd();
		return field_id;
	}
	function applyAmp(val) {
		return val.indexOf("&")>-1 ? "&" : "?"
	}
	jQuery("body").on("change_filemanager", function(e, data) {
		console.log(e, data);
		var cat = function(data) {
			var link_now = data.http_link+applyAmp(data.http_link)+(new Date().getTime()/1000);
			var tpl = $(".template_btn_access[data-template-id='"+$("#"+data.field_id).attr("name")+"']").last().html();
			if(typeof(tpl)!=="undefined") {
				tpl = tpl.replace(new RegExp("{template_access_uid}", "ig"), data.field_id);
				tpl = tpl.replace(new RegExp("{template_access_class}", "ig"), (data.type.indexOf("image")>-1 || data.type.indexOf("imageAccess")>-1 || data.type.indexOf("imageArrayAccess")>-1 ? " showPreview new" : ""));
				tpl = tpl.replace(new RegExp("{template_access_val}", "ig"), link_now);

				$(data.parent).parents("[data-for-btn-data]").find(".btn-add-image").append(tpl);
			} else {
				data.parent.append('<a data-link="'+data.field_id+'" id="img'+data.field_id+'" href="'+link_now+'"'+(data.type.indexOf("image")>-1 || data.type.indexOf("imageAccess")>-1 || data.type.indexOf("imageArrayAccess")>-1 ? " class=\"showPreview new\"" : "")+' target="_blank">Просмотреть</a>');
			}
			$("#"+data.field_id).val(link_now.replace(default_link, ""))
			minHeightForAdd();
		}
		try {
			data.http_link = JSON.parse(data.http_link)
			if(typeof(data.http_link)==="object") {
				var parent = data.parent.parents(".form-group");
				var name = $("#"+data.field_id).attr("name");
				for(var z=0;z<data.http_link.length;z++) {
					var link_now = data.http_link[z]+applyAmp(data.http_link[z])+(new Date().getTime()/1000);
					var id = addInputFileAccess(parent, name, "1", link_now.replace(default_link, ""), true)
					$("input#"+id).parent().append('<a data-link="'+data.field_id+'" id="img'+data.field_id+'" href="'+link_now+'"'+(data.type.indexOf("image")>-1 || data.type.indexOf("imageAccess")>-1 || data.type.indexOf("imageArrayAccess")>-1 ? " class=\"showPreview new\"" : "")+' target="_blank">Просмотреть</a>');
				}
				$(data.parent).parents("[data-for-btn-data]").remove()
			} else {
				cat(data)
			}
		} catch(e) {
			cat(data)
		}
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
	jQuery("body").on("keydown", function(e) {
	    if(e.ctrlKey && e.keyCode==13) {
	    	$(".formSubmit").submit()
	    }
	})
</script>