if(typeof(jQuery)==="undefined") {
	var element = document.createElement("script");element.src = default_link+'skins/core/jquery-3.2.1.min.js';document.body.appendChild(element);
}
var checkedAdmin = setInterval(function() {
	if(typeof(jQuery)!=="undefined") {
		jQuery(document).ready(function(){
			jQuery("body").addClass("adminbarCardinal");
			var nowAdminCardinal = jQuery(".adminCoreCardinal").html();
			function createNormalAdmin() {
				var elemsFirst = [];
				var elems = [];
				var width = 0;
				var userWidth = jQuery(".adminCoreCardinal > .user").width();
				var linkAdmin = jQuery(".adminCoreCardinal > .linkToAdmin").width();
				jQuery(".adminCoreCardinal > .items").each(function(i,k) {
					elemsFirst.push(jQuery(k));
				});
				for(var i=0;i<elemsFirst.length;i++) {
					var widthEl = elemsFirst[i].width();
					if((jQuery(window).width()-userWidth-linkAdmin-270) < width){
						elems.push(elemsFirst[i]);
						jQuery(elemsFirst[i]).remove();
					}
					width += widthEl;
				}
				if(elems.length > 0){
					jQuery(".adminCoreCardinal > .user").before("<div class=\'more\'><div class=\'elems\'></div></div>");
					for(var iz=0;iz<elems.length;iz++){
						jQuery(".adminCoreCardinal > .more > .elems").append(elems[iz]);
					}
				}
				clearInterval(checkedAdmin);
			}
			jQuery(window).resize(function(){
				jQuery(".adminCoreCardinal").html(nowAdminCardinal);
				createNormalAdmin();
			});
			createNormalAdmin();
		});
	}
}, 200);