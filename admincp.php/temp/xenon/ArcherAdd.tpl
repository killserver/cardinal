[!ajax]<form method="post" role="form" action="./?pages=Archer&type={ArcherPath}&pageType=Take{ArcherPage}{addition}{ref}" class="form-horizontal formSubmit" enctype="multipart/form-data">
	{E_[KernalArcher::BeforeForm][type={ArcherPath};action={ArcherPage};data={addition}]}
	<div class="row">
		<div class="col-sm-12">
			<div class="panel panel-default">
				///***<div class="panel-heading">
					<h3 class="panel-title">{ArcherMind}</h3>
				</div>***///
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
						<div class="row">
							{ArcherData}
						</div>
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
	.form-group .removeImg {
		opacity: 0;
	}
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
	.form-control[data-upload-type='fileArrayAccess'] + .btn-add-image {
		min-height: 180px;
		background: rgba(0,0,0,0.75);
		margin-bottom: 20px;
		position: relative;
		z-index: 1;
	}
	.form-control[data-upload-type='fileArrayAccess'] + .btn-add-image + .children {
		position: relative;
		z-index: 2;
	}
	.form-control[data-upload-type='fileArrayAccess'] + .btn-add-image a[target='_blank'] {
		position: absolute;
		bottom: -5px;
		left: 0;
		z-index: 2;
	}
	[data-accept=''] [data-show] {
		overflow: initial;
	}
	.tempImageShow {
		position: absolute;
		z-index: 20;
		left: 10px;
		top: 50px;
		width: 30px;
		height: 30px;
		border-radius: 6px;
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 0;
		background: #2c2e2f;
		color: #fff;
	}
	.tempImageShow:hover,
	.tempImageShow:focus {
		color: #fff;
	}
	.children .tempImageShow {
		top: 45px;
		left: 30px;
	}
	.tempImageShow:before, .children .tempImageShow:before {
		font-family: "FontAwesome";
		content: "\f002";
	}
	.tempImageShow.dataFileAccess {
		top: 10px;
		left: 30px;
	}
	@media(max-width: 900px) {
		.tempImageShow {
			top: 10px;
			left: 10px;
			width: 30px;
			height: 30px;
		}
		.children .tempImageShow {
			top: 15px;
			left: 30px;
		}
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
	    	console.warn('scroll', $window)
	        var images = this,
	            images = this,
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
<script type="text/javascript">
	$(".panel .panel-body").each(function() {
		$(this).html().trim().length===0 && $(this).remove();
	});
	$(".panel.panel-default").each(function() {
		$(this).html().trim().length===0 && $(this).remove();
	});
	$("body").on("change", ".form-group input[type='file']", function() {
		if($(this)[0].files.length) {
			$(this).parents(".form-group").addClass("selectedFile");
			$(this).parents(".form-group").find(".removeImg").css({opacity: 1})
		} else {
			$(this).parents(".form-group").removeClass("selectedFile");
			$(this).parents(".form-group").find(".removeImg").css({opacity: 0})
		}
	});
	$("body").on("change", ".form-group input[data-upload-type]", function() {
		if($(this).val()) {
			$(this).parents(".form-group").addClass("selectedFile");
			$(this).parents(".form-group").find(".removeImg").css({opacity: 1})
		} else {
			$(this).parents(".form-group").removeClass("selectedFile");
			$(this).parents(".form-group").find(".removeImg").css({opacity: 0})
		}
		var th = this;
		var time = setTimeout(function() {
			clearTimeout(time);
			console.log('tester', $(th).parents(".form-group").find(".tempImageShow"))
			$(th).parents(".form-group").find(".tempImageShow").remove();
			$(th).parents(".form-group").find(".showPreview.new").each(function() {
				if($(this).attr("completedTemp")) {
					return;
				}
				$(this).attr("completedTemp", "true");
				var link = $(this).attr("href");
				$(this).after('<a href="'+link+'" data-fancybox class="tempImageShow '+($(this).parents(".col-sm-9").find(".containerFiles")[0] ? "dataFileAccess" : "")+' "></a>')
			});
			Fancybox.bind("[data-fancybox]", {
				// Your options go here
				Image: {
					zoom: false,
					click: "close",
					groupAll: false,
					groupAttr: false,
				},
			});
		}, 300);
	});
	jQuery(document).ready(function($) {
		$(".form-group input[data-upload-type]").each(function() {
			if($(this).val()) {
				$(this).parents(".form-group").addClass("selectedFile");
				$(this).parents(".form-group").find(".removeImg").css({opacity: 1})
			} else {
				$(this).parents(".form-group").removeClass("selectedFile");
				$(this).parents(".form-group").find(".removeImg").css({opacity: 0})
				$(this).parents(".form-group").find(".tempImageShow").remove();
			}
		});
		$(".form-group input[type='file']").each(function() {
			$(this).parents(".form-group").addClass("uploadFileInput {ArcherPage}");
			if(!!$(this).parent().find(".showPreview")[0]) {
				$(this).parents(".form-group").addClass("selectedFile");
				$(this).parents(".form-group").find(".removeImg").css({opacity: 1})
			} else {
				$(this).parents(".form-group").removeClass("selectedFile");
				$(this).parents(".form-group").find(".removeImg").css({opacity: 0})
				$(this).parents(".form-group").find(".tempImageShow").remove();
			}
		});
		$(".showPreview.new").each(function() {
			$(this).attr("completed", "true");
			$(this).attr("completedTemp", "true");
			var link = $(this).attr("href");
			$(this).after('<a href="'+link+'" data-fancybox class="tempImageShow '+($(this).parents(".col-sm-9").find(".containerFiles")[0] ? "dataFileAccess" : "")+' "></a>')
		});
		Fancybox.bind("[data-fancybox]", {
			// Your options go here
			Image: {
				zoom: false,
				click: "close",
				groupAll: false,
				groupAttr: false,
			},
		});
	});
	function ucfirst(text) {
		var textNew = "";
		textNew += text.substr(0, 1).toUpperCase();
		textNew += text.substr(1);
		return textNew;
	}
	var removeImg = {};
	var optionSelectLang = ucfirst(selectLang)
	function reinitPartLang() {
		$(".form-horizontal .form-group[data-group][data-lang]").each(function(i, elem) {
			if($(elem).attr("data-lang")===optionSelectLang) {
				$(elem).css("display", "block");
			} else {
				$(elem).css("display", "none");
			}
		});
	}
	function reinitLang() {
		var selectLangForm = ucfirst(selectLang);
		$(".form-horizontal ul li a[data-lang='"+selectLangForm+"']").parent().addClass("active");
		reinitPartLang()
		$(".form-horizontal ul li a").off("click").click(function() {
			var lang = $(this).attr("data-lang");
			optionSelectLang = lang
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
		var timer = setTimeout(function() {
			clearTimeout(timer);
			console.warn('scroller')
			jQuery("html").animate({
				scrollTop: jQuery("html").scrollTop()+1
			}, 100);
		}, 500);
		$("body").off("click").on("click", ".removeImg", function() {
			var id = $(this).attr("id");
			id = id.replace("remove_", "");
			$("input[id='"+id+"']").parent().find(".tmpImage,img,.tempImageShow,.showPreview.new").remove();
			removeImg[id] = true;
			var get = $("#"+id+"").parent().html();
			$("#"+id+"").parent().html(get);
			$(".removeImages").val(Object.keys(removeImg).join(","));
			$("input[id='"+id+"']").change(function() {
				delete removeImg[id];
				$(".removeImages").val(Object.keys(removeImg).join(","));
			});
			if(!$("input[id='"+id+"']")[0].files.length) {
				$("input[id='"+id+"']").parents(".form-group").removeClass("selectedFile");
				$("input[id='"+id+"']").parents(".form-group").find(".removeImg").css({opacity: 0})
			}
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
		$("body").off("click", ".btn-save-success").on("click", ".btn-save-success", function() {
			$(this).attr("disabled", "disabled");
			$(this).parents("form").submit()
			var th = this
			setTimeout(function() {
				$(th).removeAttr("disabled");
			}, 2000);
			return true;
		});
		$("body").off("click", ".showPreview.new").on("click", ".showPreview.new", function(e) {
			if($(this).parents(".cont").find("input[type='file']")) {
				e.preventDefault();
				$(this).parents(".cont").find("input[type='file']")[0].click();
			}
			if($(e.target).is(".showPreview.new") || e.target.closest(".showPreview.new")) {
				var item = $(e.target).is(".showPreview.new") ? e.target : e.target.closest(".showPreview.new");
				if($(item).parent().children("a.btn-success").hasClass("iframe-btn")) {
					e.preventDefault()
					$(item).parent().children("a.btn-success")[0].click()
		        }
		    }
		})
		jQuery("body").on("click", "[data-for-btn-data] .children a.showPreview", function(e) {
			e.preventDefault();
			console.warn('test', $(e.target).parents("[data-for-btn-data]").find(".iframe-btn"))
			$(e.target).parents("[data-for-btn-data]").find(".iframe-btn")[0].click();
		});
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
			jQuery(elem).html("<img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAQMAAADCCAMAAAB6zFdcAAAAM1BMVEXf39+goKDe3t6dnZ3i4uKmpqabm5vS0tLV1dW3t7fLy8vDw8O6urqqqqqioqLKysqwsLA0gLe7AAAE+ElEQVR4nO2di3KDIBBFCaB5iv7/15aLihCNtcmAKd470+Yh4npcloVIIuQGiS2FNlQjwoeP69lcfL6/iLaIvkoR6JeDokRfbDwlX6WQi7sP1YfWPB9PPGmxgtj2uGxcj1yvaConZzXFZoSPT7VP+84rGV9GcKPK3XMd2Y+Xz3ik/++vQ3y1liyeTjM66dDy6XSDFyI2cH5VIk9Z3B7sGNJaLrlUzeuSwzUe/ma7y/jc1ixcrloIsbV8ySKDVXc9jMiADCDGAzKANnemBYsIGBMh+gEF0Q3IAGI8YH4AEYFgQBBEALEtCDqCIAKIDBgPICIAA314DIcHIMgAIgMygMiA+QFEBGQAsS1wPhEigv6uwb1t2FtkQAYQGZABRAZkADE/wHiBEMiADCASEIQgiABi30g/gNgvkAFEBmQAkQEZQGTgGOi9bdhb9AIygDhe4GeuUHIG+nOlNTB9W9CPj3W9pDUxeUzUplZOtZUKFb6uVbzNv+3+nxPbmJxBdYLM6X2pIhgsXeONKoWB6q5vKwuD1DHRMqhv7/cJphAG6vb27jITg6QUdKUsg7e6eGdaUQwuf5cojIG+23Sg/otUlZFBUg0MpL6rv+YFTUkMahsTLYMhTzLB32pq1GD/LAwS17/AwNicyRJQv1BwDPL0C4nr7xnYmOgZ2JO6nqW83Kv11pGPQWrNGChlzhrrCbW81t/BIFdbCPzgrIcv3NTNqicUFw8mP7hOi0ovtVmJCQUzuExJo26+wg9SQxgYSN8vGP9NrvbNh/oGP8jBQIV9YxV8Ae564lRUjhQxOIUMrpEfmP0YZBgvBAyQOPuNZjpvVVV7tYWsDIy/vP22tja+MajrTc0ZlDN/0AYx8aQeuj+kvoQnbROpp8SxLD+IGJi6k26arI1PWcItwqBQMgObLnf3+8PUYQxUnXZlg/dKZmCGyfbQCRTGFE+eUdR4QcXjhQUZpdy1CLsGla9vTFz/NgauKUg4gjksAzQFKfwnc55BtniQVFv9oDcjTJ5z+kFaCPNcOQ4ECJF2Qzckj0gZxnIZx867MnAUbMpwG60JRtPHYWDP8VTXozVhYzgOA1XXXTuZcdmBQWqNcyjzmGhcxqi61t0kOvy8jhCNzxXLnUsLPcABiD+P1Xc/2VxU3/iCwegB/e8ujXZMjSEjg/TxYImBqpwHLBzdT7gXxUBFc6ruQTWvDjs1hqLaAsaNkR8YVb06rJwaQ1lzqtHYGQiatQUDTXYGieuftYWhIbz0AynaukQ/iO7BMK9jwbCLm1w1hcWDWzgi/A2Bm0ZQp7zzykk1tYXxvuVfELjP4FoUrkRJDFo7Mj7fB20ZoegbSuJZKQzceMEvZIiO/no/3adQp0L6BTAIftZ3o/qyuHupCD9oewcIbjwY3GFpkUroLFpn8YMc8eBxe1ulMPhs/YIpIh58KDIogUFnqk/13+OB7QyWlyz68P8F6xtTH2D2s/Phr6wvbX9+L7mBXPMt/sWa7+QW/gMGycXvEiUDiAzIACIDMoDIgPkBRAT0A4jfj0QGELsFMoDIgH0jRAZkALFvdDHx8BDYL0R3Ch9WbAtsCxAZkAFEBpxHgjivTAYQGTAeQBw30g8gMiADiAw4eQCRAdsCRARkAJEB4wHEcSMZQGTAeADRDcgAIgPmSE6EQASCDCAyYI4EEQH9AMqzuv67RQRsCxARkIHVD9yhN0e3gqRsAAAAAElFTkSuQmCC' data-src='"+jQuery(elem).attr("href")+"' data-link='"+jQuery(elem).attr("data-link")+"' style='max-width:100%; background:#333; display: table;max-height:400px'>");
		});
		jQuery(".showPreview:not(.new)").each(function(i, elem) {
			jQuery(elem).after("<img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAQMAAADCCAMAAAB6zFdcAAAAM1BMVEXf39+goKDe3t6dnZ3i4uKmpqabm5vS0tLV1dW3t7fLy8vDw8O6urqqqqqioqLKysqwsLA0gLe7AAAE+ElEQVR4nO2di3KDIBBFCaB5iv7/15aLihCNtcmAKd470+Yh4npcloVIIuQGiS2FNlQjwoeP69lcfL6/iLaIvkoR6JeDokRfbDwlX6WQi7sP1YfWPB9PPGmxgtj2uGxcj1yvaConZzXFZoSPT7VP+84rGV9GcKPK3XMd2Y+Xz3ik/++vQ3y1liyeTjM66dDy6XSDFyI2cH5VIk9Z3B7sGNJaLrlUzeuSwzUe/ma7y/jc1ixcrloIsbV8ySKDVXc9jMiADCDGAzKANnemBYsIGBMh+gEF0Q3IAGI8YH4AEYFgQBBEALEtCDqCIAKIDBgPICIAA314DIcHIMgAIgMygMiA+QFEBGQAsS1wPhEigv6uwb1t2FtkQAYQGZABRAZkADE/wHiBEMiADCASEIQgiABi30g/gNgvkAFEBmQAkQEZQGTgGOi9bdhb9AIygDhe4GeuUHIG+nOlNTB9W9CPj3W9pDUxeUzUplZOtZUKFb6uVbzNv+3+nxPbmJxBdYLM6X2pIhgsXeONKoWB6q5vKwuD1DHRMqhv7/cJphAG6vb27jITg6QUdKUsg7e6eGdaUQwuf5cojIG+23Sg/otUlZFBUg0MpL6rv+YFTUkMahsTLYMhTzLB32pq1GD/LAwS17/AwNicyRJQv1BwDPL0C4nr7xnYmOgZ2JO6nqW83Kv11pGPQWrNGChlzhrrCbW81t/BIFdbCPzgrIcv3NTNqicUFw8mP7hOi0ovtVmJCQUzuExJo26+wg9SQxgYSN8vGP9NrvbNh/oGP8jBQIV9YxV8Ae564lRUjhQxOIUMrpEfmP0YZBgvBAyQOPuNZjpvVVV7tYWsDIy/vP22tja+MajrTc0ZlDN/0AYx8aQeuj+kvoQnbROpp8SxLD+IGJi6k26arI1PWcItwqBQMgObLnf3+8PUYQxUnXZlg/dKZmCGyfbQCRTGFE+eUdR4QcXjhQUZpdy1CLsGla9vTFz/NgauKUg4gjksAzQFKfwnc55BtniQVFv9oDcjTJ5z+kFaCPNcOQ4ECJF2Qzckj0gZxnIZx867MnAUbMpwG60JRtPHYWDP8VTXozVhYzgOA1XXXTuZcdmBQWqNcyjzmGhcxqi61t0kOvy8jhCNzxXLnUsLPcABiD+P1Xc/2VxU3/iCwegB/e8ujXZMjSEjg/TxYImBqpwHLBzdT7gXxUBFc6ruQTWvDjs1hqLaAsaNkR8YVb06rJwaQ1lzqtHYGQiatQUDTXYGieuftYWhIbz0AynaukQ/iO7BMK9jwbCLm1w1hcWDWzgi/A2Bm0ZQp7zzykk1tYXxvuVfELjP4FoUrkRJDFo7Mj7fB20ZoegbSuJZKQzceMEvZIiO/no/3adQp0L6BTAIftZ3o/qyuHupCD9oewcIbjwY3GFpkUroLFpn8YMc8eBxe1ulMPhs/YIpIh58KDIogUFnqk/13+OB7QyWlyz68P8F6xtTH2D2s/Phr6wvbX9+L7mBXPMt/sWa7+QW/gMGycXvEiUDiAzIACIDMoDIgPkBRAT0A4jfj0QGELsFMoDIgH0jRAZkALFvdDHx8BDYL0R3Ch9WbAtsCxAZkAFEBpxHgjivTAYQGTAeQBw30g8gMiADiAw4eQCRAdsCRARkAJEB4wHEcSMZQGTAeADRDcgAIgPmSE6EQASCDCAyYI4EEQH9AMqzuv67RQRsCxARkIHVD9yhN0e3gqRsAAAAAElFTkSuQmCC' data-src='"+jQuery(elem).attr("href")+"' width='100%' style='background:#333; display: table;'>");
		});
    	$('img').loadScroll(500);
    	console.warn('loadScroll')
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
		//console.log(e, data);
		var cat = function(data) {
			var link_now = data.http_link+applyAmp(data.http_link)+(new Date().getTime()/1000);
			var name = $("#"+data.field_id).attr("name");
			if(!name) {
				return;
			}
			name = name.split("[");
			name = name[0];
			var tpl = $(".template_btn_access[data-template-id='"+name+"']").last().html();
			if(typeof(tpl)!=="undefined") {
				tpl = tpl.replace(new RegExp("{template_access_uid}", "ig"), data.field_id);
				tpl = tpl.replace(new RegExp("{template_access_class}", "ig"), (typeof(data.type)!=="undefined" && (data.type.indexOf("image")>-1 || data.type.indexOf("imageAccess")>-1 || data.type.indexOf("imageArrayAccess")>-1) ? " showPreview new" : ""));
				tpl = tpl.replace(new RegExp("{template_access_val}", "ig"), link_now);

				$(data.parent).parents("[data-for-btn-data]").find(".btn-add-image").append(tpl);
			} else {
				data.parent.append('<a data-link="'+data.field_id+'" id="img'+data.field_id+'" href="'+link_now+'"'+(typeof(data.type)!=="undefined" && (data.type.indexOf("image")>-1 || data.type.indexOf("imageAccess")>-1 || data.type.indexOf("imageArrayAccess")>-1) ? " class=\"showPreview new\"" : "")+' target="_blank">Просмотреть</a>');
			}
			$("#"+data.field_id).val(link_now.replace(default_link, ""))
			minHeightForAdd();
		}
		try {
			data.http_link = JSON.parse(data.http_link)
			if(typeof(data.http_link)==="object") {
				var parent = data.parent.parents(".form-group");
				var name = $("#"+data.field_id).attr("name");
				if(!name) {
					return;
				}
				for(var z=0;z<data.http_link.length;z++) {
					var link_now = data.http_link[z]+applyAmp(data.http_link[z])+(new Date().getTime()/1000);
					var id = addInputFileAccess(parent, name, "1", link_now.replace(default_link, ""), true)
					$("input#"+id).parent().append('<a data-link="'+data.field_id+'" id="img'+data.field_id+'" href="'+link_now+'"'+(typeof(data.type)!=="undefined" && (data.type.indexOf("image")>-1 || data.type.indexOf("imageAccess")>-1 || data.type.indexOf("imageArrayAccess")>-1) ? " class=\"showPreview new\"" : "")+' target="_blank">Просмотреть</a>');
				}
				$(data.parent).parents("[data-for-btn-data]").remove()
			} else {
				cat(data)
			}
		} catch(e) {
			cat(data)
		}
		jQuery(".showPreview.new").each(function(i, elem) {
			if($(this).attr("completed")) {
				return;
			}
			$(this).attr("completed", "true");
			jQuery(elem).parent().find("img").remove();
			jQuery(elem).html("<img src='"+jQuery(elem).attr("href")+"' data-link='"+jQuery(elem).attr("data-link")+"' style='max-width:100%; background:#333; display: table;max-height:400px'>");
		});
	});
	function readURL(input) {
		if(input.files && input.files[input.files.length-1]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				jQuery(input).parent().find(".showPreview.new").remove();
				jQuery(input).parent().find("img").remove();
				jQuery(input).parent().append('<a class="showPreview new" href="'+e.target.result+'" style="background: #333; display: table;"><img src="'+e.target.result+'" width="100%" style="opacity: 0.75;"></a>');
				jQuery(input).parents(".col-sm-9").find(".tempImageShow").remove();
				$(".showPreview.new").each(function() {
					if($(this).attr("completedTemp")) {
						return;
					}
					$(this).attr("completedTemp", "true");
					var link = $(this).attr("href");
					$(this).after('<a href="'+link+'" data-fancybox class="tempImageShow '+($(this).parents(".col-sm-9").find(".containerFiles")[0] ? "dataFileAccess" : "")+' "></a>')
				});
				Fancybox.bind("[data-fancybox]", {
					// Your options go here
					Image: {
						zoom: false,
						click: "close",
						groupAll: false,
						groupAttr: false,
					},
				});
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