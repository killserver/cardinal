<table id="example-1" class="table table-striped table-bordered" cellspacing="0" width="100%">
<thead>
	<tr>
		<th>{L_original}</th>
		<th>{L_translates}</th>
		<th>{L_options}</th>
	</tr>
</thead>
<tfoot>
	<tr>
		<th>{L_original}</th>
		<th>{L_translates}</th>
		<th>{L_options}</th>
	</tr>
</tfoot>
<tbody>
[foreach block=langList]<tr>
	<td class="col-md-5">{langList.or}</td>
	<td class="col-md-5"><textarea class="changed col-md-8" name="{langList.or}" style="height:100px;">{langList.lang}</textarea></td>
	<td class="col-md-2">
		<a href="./?pages=Languages&" onclick="return confirmClear(this);" class="btn btn-red btn-sm">{L_"Сбросить"}</a>
	</td>
</tr>[/foreach]
</tbody>
</table>
<div class="btn btn-secondary add" onclick="AddToLang()">{L_"Добавить перевод"}</div>
<script type="text/javascript">
function confirmClear(th) {
	if (confirm("{L_"Вы подтверждаете сброс?(Данную операцию невозможно будет обратить)"}")) {
		jQuery.post("./?pages=Languages&lang={initLang}&resetLang", "orLang="+jQuery(th).parent().parent().find("td").first().html(), function(data) {
			jQuery(th).parent().parent().remove();
		});
	}
	return false;
}
function saveLang(th) {
	var elem = jQuery(th).parent().parent();
	var ret = [];
	$.each($(elem).find(':input'), function() {
		ret.push(encodeURIComponent(this.name) + "=" + encodeURIComponent($(this).val()));
	});
	ret = ret.join("&").replace(/%20/g, "+");
	jQuery.post("./?pages=Languages&lang={initLang}&saveLang", ret, function(data) {
		jQuery(elem).find("input").each(function() {
			jQuery(this).parent().html(jQuery(this).val());
		});
		jQuery(elem).find("textarea").each(function() {
			jQuery(this).addClass("changed");
		});
		jQuery(elem).find(".save").after('<a href="./?pages=Languages&" onclick="confirmClear();" class="btn btn-red btn-sm">{L_"Сбросить"}</a>').remove();
		toastr.options = {
			"closeButton": false,
			"debug": false,
			"newestOnTop": false,
			"progressBar": false,
			"positionClass": "toast-top-right",
			"preventDuplicates": false,
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "1000",
			"timeOut": "5000",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		}
		if(data=="1") {
			toastr.success("{L_"Успешно добавили элемент в языковую панель"}");
		} else {
			toastr.error("{L_"Произошла ошибка при попытке добавить элемент в языковую панель!"}");
		}
	});
	console.log(ret);
}
function AddToLang() {
	console.log(jQuery("#example-1").find("tr").last().after("<tr><td class=\"col-md-5\"><input type=\"text\" name=\"orLang\" class=\"col-md-12\"></td><td class=\"col-md-5\"><textarea class=\"col-md-8\" name=\"translate\" style=\"height:100px;\"></textarea></td><td class=\"col-md-2\"><a href=\"#\" onclick=\"saveLang(this);return false;\" class=\"btn btn-secondary btn-sm save\">{L_"Сохранить"}</a></td></tr>"));
}
jQuery(".changed").change(function() {
	var orig = jQuery(this).parent().parent().children("td").first().html();
	var translate = jQuery(this).val();
	jQuery.post("./?pages=Languages&lang={initLang}&saveLang", "orLang="+orig+"&translate="+translate, function(data) {
		toastr.options = {
			"closeButton": false,
			"debug": false,
			"newestOnTop": false,
			"progressBar": false,
			"positionClass": "toast-top-right",
			"preventDuplicates": false,
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "1000",
			"timeOut": "5000",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		}
		if(data=="1") {
			toastr.success("{L_"Успешно отредактировали элемент в языковой панели"}");
		} else {
			toastr.error("{L_"Произошла ошибка при попытке отредактировать элемент в языковой панели!"}");
		}
	});
});
</script>