<link rel="stylesheet" href="{C_default_http_local}skins/core/install.css" />
<center>{L_install} Cardinal Engine v{D_VERSION}</center>
<ol id="checkoutBreadcrumb">
	<li class="basket<!-- IF {% RP[line] %}==1 -->active<!-- ENDIF --><!-- IF {% RP[line] %}==2 --> nextactive<!-- ENDIF -->">1. {L_licence}</li>
	<li class="login<!-- IF {% RP[line] %}==2 -->active<!-- ENDIF --><!-- IF {% RP[line] %}==3 --> nextactive<!-- ENDIF -->">2. {L_chmod}</li>
	<li class="delivery<!-- IF {% RP[line] %}==3 -->active<!-- ENDIF --><!-- IF {% RP[line] %}==4 --> nextactive<!-- ENDIF -->">3. {L_settings}</li>
	<li class="done<!-- IF {% RP[line] %}==4 -->active<!-- ENDIF -->">4. {L_done}</li>
</ol>
[if {page}=="error_server"]
	<div style="border:1px solid #000000;border-radius:10px;box-shadow:0px 4px 10px #000;padding:18px;background:rgb(255,230,196);color:#4C289E;">Установка скрипта заблокированна, так-как была попытка запустить сервер на localhost домене. Пожалуйста, перенесите скрипт на хостинг.</b></div>
[/if]
[if {page}==1]
<form method="post" action="{C_default_http_local}install.php">
<div style="border:1px solid #000000;border-radius:10px;box-shadow:0px 4px 10px #000;padding:18px;background:rgb(255,230,196);color:#4C289E;">
<center><b>Данный программный продукт распростроняется под лицензей GNU.</b></center><br />Все изменения, которые пользователь будет выполнять используя данное ПО - не должно нарушать права человека и права котеек! Будьте более доброжелательны.<br />Приятного использования!<br />
<small>Если Вы испытываете проблемы с установкой - установите флажок: <input type="checkbox" name="rewrite" value="1" /></small>
</div>
<input type="submit" class="next" name="submit" value="Принять" />
</form>
[/if]
[if {page}==2]
<form method="post" action="{C_default_http_local}{R_[install_first][file=install.php;page=install;method=change;is_file=true;line=3]}">
[if {isRewrite}==1]<input type="hidden" name="rewrite" value="1">[/if {isRewrite}==1]
<div style="border:1px solid #000000;border-radius:10px;box-shadow:0px 4px 10px #000;padding:18px;background:rgb(255,230,196);color:#4C289E;">
	[if {is_stop}==0]<div style="text-align:center;font-weight:bold;">Все необходимые права доступа и требуемое программное обезпечение установлено, можно продолжать установку!</div>[/if {is_stop}==0]
	[if {is_stop}==1]<div style="text-align:center;font-weight:bold;">Установите требуемые права доступа к папкам или проверьте наличие необходимого программного обезпечения для продолжения установки!</div>[/if {is_stop}==1]
	<div><div style="display:inline-block;width:200px;">Apache</div><div style="display:inline-block;color:{apache};font-weight:bold;">2.0</div></div>
	<div><div style="display:inline-block;width:200px;">PHP</div><div style="display:inline-block;color:{php};font-weight:bold;">5.3.2</div></div>
	<div><div style="display:inline-block;width:200px;">core/cache/</div><div style="display:inline-block;color:{cache};font-weight:bold;">0777</div></div>
	<div><div style="display:inline-block;width:200px;">core/cache/system/</div><div style="display:inline-block;color:{system_cache};font-weight:bold;">0777</div></div>
	<div><div style="display:inline-block;width:200px;">core/cache/tmp/</div><div style="display:inline-block;color:{template};font-weight:bold;">0777</div></div>
	<div><div style="display:inline-block;width:200px;">core/media/</div><div style="display:inline-block;color:{media};font-weight:bold;">0777</div></div>
	<div><div style="display:inline-block;width:200px;">MbString</div><div style="display:inline-block;color:{mb};font-weight:bold;">[if {mb}==red]Не установлен[else {mb}==red]Установлен[/if {mb}==red]</div></div>
</div>
<input type="hidden" name="cache" />
[if {is_stop}==0]<input type="submit" class="next" name="submit" value="Принять" />[/if {is_stop}==0]
</form>
[/if]
[if {page}==3]
<form method="post" id="Form" action="{C_default_http_local}{R_[install_first][file=install.php;page=install;method=change;is_file=true;line=4]}" onsubmit="return check();">
[if {isRewrite}==1]<input type="hidden" name="rewrite" value="1">[/if {isRewrite}==1]
<div style="border:1px solid #000000;border-radius:10px;box-shadow:0px 4px 10px #000;background:rgb(255,230,196);text-align:center;font-weight:bold;padding:7px;margin:0px auto 10px;width:50%;color:#f00;font-size:18px;">Убедитесь, что все необходимые драйверы баз данных установленны!</div>
<span style="float:left;">
	<span style="width:400px;border:1px solid #000;display:block;padding:10px;">
		<center>База данных</center>
		<hr />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Сервер</div><input type="text" name="db_host" value="localhost" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Порт сервера</div><input type="text" name="db_port" value="3306" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Имя пользователя</div><input type="text" name="db_user" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Пароль</div><input type="password" name="db_pass" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Имя базы данных</div><input type="text" name="db_db" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Префикс базы данных<br>(<small>если не знаете - лучше не трогайте</small>)</div><input type="text" name="db_prefix" value="cardinal_" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Драйвер базы данных</div><select name="db_driver" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;">[foreach block=drivers]<option value="{drivers.name}">{drivers.value}</option>[/foreach]</select><br />
	</span>
	<br />
	<span style="width:400px;border:1px solid #000;display:block;padding:10px;">
		<center>Пользователь</center>
		<hr />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Имя пользователя</div><input type="text" name="user_name" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Пароль</div><input type="password" name="user_pass" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Контактная почта</div><input type="text" name="user_email" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
	</span>
</span>
<br />
<span style="float:right;width:400px;border:1px solid #000;display:block;padding:10px;">
	<center>Система Кеша<span alt="Создание копии данных базы данных для ускорения работы сайта" title="Создание копии данных базы данных для ускорения работы сайта">(?)</span></center>
	<hr />
	<div style="display:inline-block;width:50%;padding:5px;margin:1px;">Система кеширования</div><select name="cache_type" style="width:45%;padding:5px;margin:1px;"><option value="CACHE_NONE">Без кеширования</option><option value="CACHE_FILE">Файловый кеш</option><option value="CACHE_MEMCACHE">Memcache</option><option value="CACHE_MEMCACHED">Memcached</option><option value="CACHE_FTP">FTP</option></select><br />
	<div style="display:inline-block;width:50%;padding:5px;margin:1px;">Сервер кеширования</div><input type="text" name="cache_host" value="localhost" style="width:45%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
	<div style="display:inline-block;width:50%;padding:5px;margin:1px;">Порт кеширования</div><input type="text" name="cache_port" value="11211" style="width:45%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
	<div style="display:inline-block;width:50%;padding:5px;margin:1px;">Пользователь кеширования</div><input type="text" name="cache_user" style="width:45%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
	<div style="display:inline-block;width:50%;padding:5px;margin:1px;">Пароль кеширования</div><input type="password" name="cache_pass" style="width:45%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
	<div style="display:inline-block;width:50%;padding:5px;margin:1px;">Путь кеширования</div><input type="text" name="cache_path" value="/" style="width:45%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
	<center><b>Если Вы не знаете, что из предложенного выбрать, либо не знаете - поддерживает-ли Ваш сервер ту или иную настройку - установите значение выше в "Файловый кеш" и пропустите этот пункт</b></center>
</span>
	<span style="width:95%;border:1px solid #000;display:inline-block;padding:10px;margin:10px auto;">
		<center>Настройки</center>
		<hr />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Название сайта</div><input type="text" name="sitename" maxlength="70" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Описание сайта</div><input type="text" name="description" maxlength="160" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Доменное имя Вашего сайта</div><input type="text" name="SERVER" value="{SERVERS}" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Путь к Вашему сайту</div><input type="text" name="PATH" value="http://{SERNAME}/" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Система записи ошибок</div><select name="error_type" style="width:50%;padding:5px;margin:1px;"><option value="ERROR_FILE">Ошибки будут записываться в файл</option><option value="ERROR_DB">Ошибки будут записываться в базу данных</option></select><br />
	</span>
<input type="submit" name="submit" value="Принять" style="margin:10px auto;width:90%;padding:5pt;font-size:18pt;display:block;" />
</form>
<script type="text/javascript">
function in_array(what, where) {
    for(var i=0; i<where.length; i++)
        if(what == where[i])
            return true;
    return false;
}
function check() {
	var res = true;
	var Form = document.getElementById('Form');
	for(I = 0; I < Form.length; I++) {
		var name = Form[I].name;
		if(in_array(Form[I].name, ['db_pass','cache_host','cache_port','cache_user','cache_pass','cache_path','submit'])) {
			continue;
		}
		if(Form[I].value=="") {
			if(res) {
				res = false;
			}
			Form[I].className = "selected";
		}
	}
	if(!res) {
		alert("Продолжение установки прервано, так как не заполнены обязательные поля!");
	}
	return res;
}
</script>
[/if]
[if {page}=="error"]
<div style="border:1px solid #000000;border-radius:10px;box-shadow:0px 4px 10px #000;padding:18px;background:rgb(255,230,196);color:#4C289E;">Нет соединения с Базой Данных.<br />Пожалуйста, вернитесь назад и проверьте введённые данные, либо обратитесь в службу тех.поддержки.<br /><a href="javascript:history.go(-1)">Назад</a></div>
[/if]
[if {page}==4]
<div style="border:1px solid #000000;border-radius:10px;box-shadow:0px 4px 10px #000;padding:18px;background:rgb(255,230,196);color:#4C289E;">Благодарим за выбор нашей продукции.<br />Теперь Вы можете перейти в админ-панель, либо перейти к работе с сайтом!<br /><b><font color="red">Не забудьте удалить файл install.php с корня Вашего сайта</font></b></div>
<a href="{C_default_http_host}admincp.php/?pages=main" class="done">Админ-панель</a>
<a href="{C_default_http_host}" class="done">Главная</a>
[/if]