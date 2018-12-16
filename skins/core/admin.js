var nowAdminCardinal = "";
var checkedAdmin = setInterval(function() {
	if(typeof(jQuery)!=="undefined") {
		clearInterval(checkedAdmin);
		jQuery(document).ready(function(){
			function createNormalAdmin() {
				jQuery(".adminCoreCardinal").addClass("test");
				var elems = [];
				var userWidth = jQuery("html").hasClass("hideUserCardinal") ? 0 : jQuery(".adminCoreCardinal > .elems > .user").outerWidth();
				var linkAdmin = jQuery(".adminCoreCardinal > .linkToAdmin").outerWidth();
				var logoWidth = jQuery(".adminCoreCardinal > .logo").outerWidth();
				var windowWidth = jQuery(window).width();
				var elemsFirst = [];
				jQuery(".adminCoreCardinal > .elems > .items").each(function(i,k) {
					elemsFirst.push(jQuery(k));
				});
				var width = 0;
				width += userWidth+linkAdmin+logoWidth;
				width += (jQuery("html").hasClass("hideUserCardinal") ? 250 : 350);
				var widthCurrent = windowWidth;
				for(var i=0;i<elemsFirst.length;i++) {
					var widthEl = elemsFirst[i].outerWidth();
					if(width > widthCurrent){
						elems.push(elemsFirst[i]);
						jQuery(elemsFirst[i]).remove();
					}
					width += widthEl;
				}
				if(elems.length > 0){
					jQuery(".adminCoreCardinal > .elems > .user").before("<div class=\'more\'><div class=\'elems\'></div></div>");
					for(var iz=0;iz<elems.length;iz++){
						jQuery(".adminCoreCardinal > .elems > .more > .elems").append(elems[iz]);
					}
				}
				jQuery(".adminCoreCardinal").removeClass("test");
			}
			setTimeout(function() {
				jQuery("body").addClass("adminbarCardinal");
				if(nowAdminCardinal.length==0) {
					nowAdminCardinal = jQuery(".adminCoreCardinal").html();
					createNormalAdmin();
				}
				jQuery(window).resize(function(){
					jQuery(".adminCoreCardinal").html(nowAdminCardinal);
					createNormalAdmin();
				});
			}, 1000);
		});
	}
}, 200);