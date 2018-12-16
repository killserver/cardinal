jQuery(document).ready(function($) {
	$(".multiple-select").each(function(i, elem) {
		$(elem).select2({
			placeholder: $(elem).attr("placeholder") ? $(elem).attr("placeholder") : "",
			allowClear: true,
			dropdownAutoWidth: true
		}).on('select2-open', function() {
			// Adding Custom Scrollbar
			$(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
		});
	});
});