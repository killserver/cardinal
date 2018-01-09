<div class="row">
	<div class="col-xs-12 col">
		<div class="panel panel-color panel-black collapsed">
			<div class="panel-heading">
				<h3 class="panel-title">{L_"Ключ доступа к API Yandex"}</h3>
				<div class="panel-options">
					<a href="#" data-toggle="panel">
						<span class="collapse-icon">–</span>
						<span class="expand-icon">+</span>
					</a>
				</div>
			</div>
			<div class="panel-body">
				<input type="text" class="form-control input-sm" name="apiKeyTranslate" value="{C_apiKeyTranslate}">
			</div>
			<div class="panel-disabled"></div>
		</div>
	</div>
	<div class="col-xs-12 col-md-6">
		<div class="panel panel-default panel-border">
			<div class="panel-heading">
				<h3 class="panel-title">{L_"Поддерживаемые языки"}</h3>
				<div class="panel-options">
					<a href="#" data-toggle="panel">
						<span class="collapse-icon">–</span>
						<span class="expand-icon">+</span>
					</a>
				</div>
			</div>
			<div class="panel-body">
				<table class="table table-hover">
					<thead>
						<tr><th>{L_"Язык"}</th><th width="10%">{L_"Операции"}</th></tr>
					</thead>
					<tbody>
						[foreach block=supportLang]<tr><td>{supportLang.lang}</td><td><a href="./?pages=Languages&lang={supportLang.clearLang}" class="btn btn-block btn-success">{L_edit}</a><a href="#" class="btn btn-block btn-red">{L_delete}</a></td></tr>[/foreach]
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-md-6">
		<div class="panel panel-color panel-blue collapsed">
			<div class="panel-heading">
				<h3 class="panel-title">{L_"Создать язык"}</h3>
				<div class="panel-options">
					<a href="#" data-toggle="panel">
						<span class="collapse-icon">–</span>
						<span class="expand-icon">+</span>
					</a>
				</div>
			</div>
			<div class="panel-body">
				<form role="form" class="form-horizontal">
					<div class="form-group">
						<select name="nameCreated" class="form-control">
							[foreach block=supportTranslate]<option value="{supportTranslate.clearLang}">{supportTranslate.lang}</option>[/foreach]
						</select>
					</div>
					<table class="table table-hover">
						<tbody>
							<tr>
								<td>{L_"Поддержка шаблонов"}</td>
								<td width="10%"><input type="checkbox" class="cbr cbr-turquoise"></td>
							</tr>
							<tr>
								<td>{L_"Использовать действующую языковую панель"}</td>
								<td width="10%"><input type="radio" name="useLang" class="cbr cbr-blue" checked="checked"></td>
							</tr>
							<tr>
								<td>{L_"Создать болванку"}</td>
								<td width="10%"><input type="radio" name="useLang" class="cbr cbr-primary"></td>
							</tr>
						</tbody>
					</table>
					<div class="col-sm-4 pull-right">
						<input type="submit" class="btn btn-success btn-block" value="{L_save}">
					</div>
				</form>
			</div>
			<div class="panel-disabled"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
$("input[name='apiKeyTranslate']").change(function(event) {
	$.post("./?pages=Languages&saveAPI=true", { "api": $(this).val() }, function(data) { }).success(function() {
		toastr.success("{L_"API-ключ успешно установлен"}", "Done");
	}).fail(function() {
		toastr.error("{L_"Произошла ошибка, попробуйте позже"}", "{L_error}");
	});
	return false;
});
</script>