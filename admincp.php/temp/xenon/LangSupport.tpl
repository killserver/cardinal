<div class="row">
	<div class="col-xs-12 col-md-6">
		<select name="">
		[foreach block=supportLang]<option value="{supportLang.clearLang}">{supportLang.lang}</option>[/foreach]
		</select>
	</div>
	<div class="col-xs-12 col-md-6">
		<select name="">
		[foreach block=supportTranslate]<option value="{supportTranslate.clearLang}">{supportTranslate.lang}</option>[/foreach]
		</select>
	</div>
</div>