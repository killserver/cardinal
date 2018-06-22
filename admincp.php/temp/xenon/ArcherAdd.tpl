[!ajax]<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{ArcherMind}</h3>
			</div>
			<div class="panel-body">
				<form method="post" role="form" action="./?pages=Archer&type={ArcherPath}&pageType=Take{ArcherPage}{addition}{ref}" class="form-horizontal" enctype="multipart/form-data">[/!ajax]
					[if {count[supportedLang]}>=1]<ul class="nav nav-tabs nav-tabs-justified" data-support="lang">
						[foreach block=supportedLang]<li>
							<a href="#home-3" data-toggle="tab" data-lang="{supportedLang.lang}">{supportedLang.lang}</a>
						</li>[/foreach]
					</ul>[/if {count[supportedLang]}>=1]
					<br><br>
					{ArcherData}
					[!ajax]<button class="btn btn-savePage btn-icon btn-icon-standalone btn-icon-standalone-right btn-sm">
						<i class="fa-save"></i>
						<span>{L_save}</span>
					</button>
				</form>
			</div>
		</div>
	</div>
</div>[/!ajax]
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

var selectLangForm = ucfirst(selectLang);
$(".form-horizontal ul li a[data-lang='"+selectLangForm+"']").parent().addClass("active");
$(".form-horizontal .form-group[data-group][data-lang]").each(function(i, elem) {
	if($(elem).attr("data-lang")===selectLangForm) {
		$(elem).css("display", "block");
	} else {
		$(elem).css("display", "none");
	}
});
$(".form-horizontal ul li a").click(function() {
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
$(document).ready(function() {
	setTimeout(function() {
		$("form > div").each(function(i, elem) {
			var block = $(elem).attr("class").replace("form-group block-", "");
			var tt = tinymce.get(block);
			if(tt!=null) {
				tt.on("input", function() {
					var text = this.getContent();
					var name = this.id;
					for(var i=0;i<langSupport.length;i++) {
						name = name.replace(new RegExp(selectedLang, "g"), "");
						name = name.replace(new RegExp(langSupport[i], "g"), "");
						var sup = tinymce.get(name+langSupport[i]);
						if(sup!=null && sup.getContent()==text.substr(0, text.length-1)) {
							tinymce.get(name+langSupport[i]).setContent(text);
						}
					}
				});
			} else {
				var ret = false;
				if(block.indexOf(selectedLang)>-1) {
					ret = true;
				}
				if(ret) {
					$(elem).find("textarea,input").on("input", function() {
						var name = this.id;
						var text = this.value;
						for(var i=0;i<langSupport.length;i++) {
							name = name.replace(new RegExp(selectedLang, "g"), "");
							name = name.replace(new RegExp(langSupport[i], "g"), "");
							var data = $("#"+name+langSupport[i]);
							console.log(data.val(), text);
							if(data!=null && data.val()==text.substr(0, text.length-1)) {
								data.val(text);
							}
						}
					});
				}
			}
		});
	}, 2000);
});
[ajax]var linkForSubmit = "./?pages=Archer&type={ArcherPath}&pageType=Take{ArcherPage}{addition}{ref}";[/ajax]
var i = 1;
function removeInputFile(th, name, val) {
	var bef = jQuery('input[name="deleteArray['+name+']"]').val();
	jQuery('input[name="deleteArray['+name+']"]').val(val+","+bef);
	jQuery(th).parent().parent().remove();
}
jQuery(document).ready(function() {
	jQuery(".showPreview").each(function(i, elem) {
		jQuery(elem).after("<br><img src='"+jQuery(elem).attr("href")+"' width='200'>");
	});
	jQuery("body").on("click", ".accessRemove", function() {
		var count = jQuery(".containerFiles[data-parent='"+jQuery(this).attr("data-parent")+"'] input").length;
		if(count==1) {
			jQuery(this).parent().parent().find("input").val("");
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
function readURL(input) {
	if(input.files && input.files[input.files.length-1]) {
		var reader = new FileReader();
		reader.onload = function(e) {
			jQuery(input).parent().find(".tmpImage").remove();
			jQuery(input).parent().append('<div class="tmpImage" style="background: #333; display: table;"><img src="'+e.target.result+'" width="200" style="opacity: 0.75;"></div>');
		}
		reader.readAsDataURL(input.files[input.files.length-1]);
	}
}
jQuery("input[type='file'][accept*='image']").change(function() {
	readURL(this);
});
</script>