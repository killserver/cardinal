<form role="form" class="form-horizontal" method="post">
<div class="col-md-12">
<center>API-ключ: <input type="text" value="{API}" readonly="readonly" disabled="disabled" /></center>
<ul class="nav nav-tabs right-aligned"> 
<li class="active">
	<a href="#home" data-toggle="tab">
		<span class="hidden-xs">Основные настройка</span>
	</a>
</li>
[foreach block=sub_nav]
<li>
	<a href="#{sub_nav.subname}" data-toggle="tab">
		<span class="hidden-xs">{sub_nav.name}</span>
	</a>
</li>
[/foreach]
</ul>
<div class="tab-content">
<div class="tab-pane active" id="home">
	<span style="width:95%;border:1px solid #000;display:inline-block;padding:10px;margin:10px auto;">
		<center>Система Кеша<span alt="Создание копии данных базы данных для ускорения работы сайта" title="Создание копии данных базы данных для ускорения работы сайта">(?)</span></center>
		<hr />
		<div style="display:inline-block;width:50%;padding:5px;margin:1px;">Система кеширования</div><select name="cache_type" style="width:45%;padding:5px;margin:1px;"><option value="CACHE_NONE">Без кеширования</option><option value="CACHE_FILE">Файловый кеш</option><option value="CACHE_MEMCACHE">Memcache</option><option value="CACHE_MEMCACHED">Memcached</option><option value="CACHE_FTP">FTP</option></select><br />
		<div style="display:inline-block;width:50%;padding:5px;margin:1px;">Сервер кеширования</div><input type="text" name="cache_host" value="{cache_host}" style="width:45%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:50%;padding:5px;margin:1px;">Порт кеширования</div><input type="text" name="cache_port" value="{cache_port}" style="width:45%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:50%;padding:5px;margin:1px;">Пользователь кеширования</div><input type="text" name="cache_user" value="{cache_user}" style="width:45%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:50%;padding:5px;margin:1px;">Пароль кеширования</div><input type="password" name="cache_pass" value="{cache_pass}" style="width:45%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:50%;padding:5px;margin:1px;">Путь кеширования</div><input type="text" name="cache_path" value="/" style="width:45%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<center><b>Если Вы не знаете, что из предложенного выбрать, либо не знаете - поддерживает-ли Ваш сервер ту или иную настройку - установите значение выше в "Файловый кеш" и пропустите этот пункт</b></center>
		<hr />
		<center>Настройки</center>
		<hr />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Название сайта</div><input type="text" name="sitename" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;" value="{sitename}"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Ключевые слова сайта</div><input type="text" name="keywords" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;" value="{keywords}"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Описание сайта</div><input type="text" name="description" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;" value="{description}"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Доменное имя Вашего сайта</div><input type="text" name="SERVER" value="{SERNAME}" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Путь к Вашему сайту</div><input type="text" name="PATH" value="{SERPATH}" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Система записи ошибок</div><select name="error_type" style="width:50%;padding:5px;margin:1px;"><option value="ERROR_FILE">Ошибки будут записываться в файл</option><option value="ERROR_DB">Ошибки будут записываться в базу данных</option></select><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Система записи ошибок</div><input type="checkbox" name="speed_update" value="1"[if {C_speed_update}==1] checked="checked"[/if {C_speed_update}==1] class="iswitch iswitch-secondary" /><br />
	</span>
</div>
[foreach block=sub_nav_options]
<div class="tab-pane" id="{sub_nav_options.subname}">
{sub_nav_options.options}
</div>
[/foreach]
	<button type="submit" class="btn btn-purple btn-icon">
		<i class="fa-check"></i>
		<span>{L_save}</span>
	</button>
</div>
<br/>
</div>
</form>