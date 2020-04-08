var select2Init = function() {
	$(":input.multiple-select").each(function(i, elem) {
		var el = $(elem).attr("data-options");
		el = $("script#"+el).html();
		el = JSON.parse(el);
		console.warn(el)
		$(elem).select2({
			minimumInputLength: 0,
			createSearchChoice: function(term, data) {
				if($(data).filter(function() {
					return this.text.localeCompare(term) === 0;
				}).length === 0) {
					return {
						id: term,
						text: term
					};
				}
			},
			tags: el,
			placeholder: $(elem).attr("placeholder") ? $(elem).attr("placeholder") : "",
			dropdownAutoWidth: true
		}).on('select2-open', function() {
			// Adding Custom Scrollbar
			$(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
		});
		setTimeout(function(elem, el) {
			var def = $(elem).val().split(",");
			var selectedBy = $(elem).attr("data-selectedBy");
			console.log("def: "+def);
			console.log("elem: "+elem);
			var newVal = [];
			for(var i=0;i<el.length;i++) {
				for(var z=0;z<def.length;z++) {
					if((typeof(selectedBy)==="undefined" || selectedBy.length==0 || selectedBy=="key") && (def[z]==el[i].id || def[z]==el[i].text)) {
						newVal[newVal.length] = ""+el[i].id;
						break;
					} else if(selectedBy=="val" && def[z]==el[i].text) {
						newVal[newVal.length] = ""+el[i].text;
						break;
					}
				}
			}
			newVal = newVal.join(",");
			console.log("newVal: "+newVal);
			if(newVal.length>0) {
				$(elem).val(newVal).trigger('change.select2');
			}
		}, 300, elem, el);
	});
}
jQuery(document).ready(function($) {
	select2Init();
});