<center>Установка Cardinal Engine v{D_VERSION}</center>
[if {page}=="error_server"]
	<div style="border:1px solid #000000;border-radius:10px;box-shadow:0px 4px 10px #000;padding:18px;background:rgb(255,230,196);color:#4C289E;">Установка скрипта заблокированна, так-как была попытка запустить сервер на localhost домене. Пожалуйста, перенесите скрипт на хостинг.</b></div>
[/if]
[if {page}==1]
<form method="post">
<div style="border:1px solid #000000;border-radius:10px;box-shadow:0px 4px 10px #000;padding:18px;background:rgb(255,230,196);color:#4C289E;">
<center><b>Данный программный продукт распростроняется под лицензей GNU.</b></center><br />Все изменения, которые пользователь будет выполнять используя данное ПО - не должно нарушать права человека и права котеек! Будьте более доброжелательны.<br />Приятного использования!
</div>
<style type="text/css">
input {
    float: right;
    margin: 15px;
    border: 1px solid #000;
    padding: 8px;
    background: #00FF2B;
    color: #1C00F5;
    border-radius: 10px;
    font-size: 14pt;
}
input:hover {
    box-shadow: 1px 1px 1px #000;
    cursor: pointer;
}
</style>
<input type="submit" name="submit" value="Принять" />
</form>
[/if]
[if {page}==2]
<form method="post">
<span style="float:left;">
	<span style="width:400px;border:1px solid #000;display:block;padding:10px;">
		<center>База данных</center>
		<hr />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Сервер</div><input type="text" name="db_host" value="localhost" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Порт сервера</div><input type="text" name="db_port" value="3306" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Имя пользователя</div><input type="text" name="db_user" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Пароль</div><input type="password" name="db_pass" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Имя базы данных</div><input type="text" name="db_db" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
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
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Название сайта</div><input type="text" name="sitename" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Ключевые слова сайта</div><input type="text" name="keywords" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Описание сайта</div><input type="text" name="description" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Доменное имя Вашего сайта</div><input type="text" name="SERVER" value="{SERNAME}" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Путь к Вашему сайту</div><input type="text" name="PATH" value="http://{SERNAME}/" style="width:50%;border:1px solid #000;border-radius:10px;padding:5px;margin:1px;"><br />
		<div style="display:inline-block;width:40%;padding:5px;margin:1px;">Система записи ошибок</div><select name="error_type" style="width:50%;padding:5px;margin:1px;"><option value="ERROR_FILE">Ошибки будут записываться в файл</option><option value="ERROR_DB">Ошибки будут записываться в базу данных</option></select><br />
	</span>
<input type="submit" name="submit" value="Принять" style="margin:10px auto;width:90%;padding:5pt;font-size:18pt;display:block;" />
</form>
[/if]
[if {page}=="error"]
<div style="border:1px solid #000000;border-radius:10px;box-shadow:0px 4px 10px #000;padding:18px;background:rgb(255,230,196);color:#4C289E;">Нет соединения с Базой Данных.<br />Пожалуйста, вернитесь назад и проверьте введённые данные, либо обратитесь в службу тех.поддержки.<br /><a href="javascript:history.go(-1)">Назад</a></div>
[/if]
[if {page}==3]
<div style="border:1px solid #000000;border-radius:10px;box-shadow:0px 4px 10px #000;padding:18px;background:rgb(255,230,196);color:#4C289E;">Благодарим за выбор нашей продукции.<br />Теперь Вы можете перейти в админ-панель, либо перейти к работе с сайтом!<br /><b><font color="red">Не забудьте удалить файл install.php с корня Вашего сайта</font></b></div>
<style type="text/css">
a, input {
    float: right;
    margin: 15px;
    border: 1px solid #000;
    padding: 8px;
    background: #00FF2B;
    color: #1C00F5;
    border-radius: 10px;
    font-size: 14pt;
}
a:hover, input:hover {
    box-shadow: 1px 1px 1px #000;
    cursor: pointer;
}
</style>
<a href="admincp.php/?pages=main">Админ-панель</a>
<a href="./">Главная</a>
[/if]