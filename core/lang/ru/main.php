<?php
/*
*
* Version Engine: 1.25.3
* Version File: 10
*
* 10
* add video light(really?!five version in cp1251 and five version after - in utf-8....finally!)
*
* 10.1
* add alert and error for ajax, add repeat(really?! I do this?!)
*
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

$lang = array_merge($lang, array(
	"lang_ini" => "ru",
	"site_name" => "Онлайн-видео",
	"sitename" => "Онлайн-видео",
	"adminpanel" => "Админ-панель",
	"alert_parser" => "<b><font color=\"red\">!!!ВНИМАНИЕ!!!</font></b><br />Данным парсером поддерживаются только разрешенные сервисы, список которых Вы можете найти нажав на кнопку \"Поддерживаемые сервисы\"",
//errors
	"error" => "Ошибка!!!",
	"error_video_exists" => "Ошибка!!!<br />Видео с таким названием уже есть в системе!!!",
	"error_level_full" => "!!!Ошибка доступа!!!<br />У вас нет права находиться в данном разделе сайта!",
	"error_level" => "Ошибка доступа",

	"error_parse_service" => "Ошибка, введённый сервис не поддерживается нашим парсером информации. Пожалуйста, введите информацию вручную, либо используйте поддерживаемый сервис.<br />Информацию о поддерживаемых сервисах Вы можете найти нажав на кнопку \"Поддерживаемые сервисы\"",

	"error_isset_reg" => "Вы уже зарегистрированны!",
	"error_isset_reg_full" => "Вы уже зарегистрированны в системе и потому Вам нет смысла находиться в данном разделе сайта!",

	"error_album_user" => "<h1>Ошибка!</h1><br />Вы не имеете права находиться в данном разделе сайта!<br />Возможные причины:<ul><li>Вы не авторизированны<li>То, что вы искали - отсутствует у нас в базе данных</ul><br />Если Вы уверенны, что Вашей ошибки нет в данном списке, пожалуйста, сообщите об этом администрацию.",
	"error_album_name" => "<h3>Ошибка!</h3><br />Вы не ввели название альбома",


	"error_reg_username" => "Ошибка имени пользователя!",
	"error_reg_username_full" => "Ошибка при вводе имени пользователя.<br />Вероятно - оно пусто!",
	"error_reg_user" => "Ошибка пользователя!",
	"error_reg_user_exists" => "Ошибка! Пользователь с таким ником уже есть!",
	"error_reg_user_exists_full" => "Ошибка! Пользователь уже найден в нашей системе!",
	"error_reg_pass_incorrect" => "Пароли не совпадают",
	"error_reg_pass_correct" => "Пароли совпадают",
	"error_reg_pass" => "Ошибка пароля пользователя!",
	"error_reg_pass_null" => "Ошибка при вводе пароля пользователя.<br />Вероятно - он пуст!",
	"error_reg_pass_full" => "Ошибка при вводе пароля пользователя.<br />Пароль и проверочный пароль не совпадают!",
	"error_reg_email" => "Ошибка почтового ящика пользователя!",
	"error_reg_email_full" => "Ошибка при вводе почтового ящика пользователя.<br />Вероятно - он пуст!",
	"good_reg" => "Регистрация успешна",


	"error_added" => "!!!Ошибка добавления!!!",
	"error_added_name_full" => "Ошибка добавления! Не добавленно или пусто название видео!",
	"error_added_descr_full" => "Ошибка добавления! Не добавленно или пусто описание видео!",
	"error_added_cat_full" => "Ошибка добавления! Не добавленна или пуста категория видео!",
	"error_added_album_full" => "Ошибка добавления! Не добавлен или пуст альбом видео!",
	"error_added_video_full" => "Ошибка добавления! Не добавлена или пуста ссылка на видео!",
	"error_added_tags_link" => "Ошибка добавления! Не допустимы ссылки в тегах!",


	"error_edit" => "!!!Ошибка редактирование!!!",
	"error_edit_name_full" => "Ошибка редактирование! Не добавленно или пусто название видео!",
	"error_edit_descr_full" => "Ошибка редактирование! Не добавленно или пусто описание видео!",
	"error_edit_cat_full" => "Ошибка редактирование! Не добавленна или пуста категория видео!",
	"error_edit_album_full" => "Ошибка редактирование! Не добавлен или пуст альбом видео!",
	"error_edit_video_full" => "Ошибка редактирование! Не добавлена или пуста ссылка на видео!",
	"error_edit_tags_link" => "Ошибка редактирование! Не допустимы ссылки в тегах!",


	"error_user_not_found" => "Пользователь не найден!",


	"error_video_link" => "Видео не найденно!<hr />Возможные причины:<Br /><li> Видео удаленно<br /><li> Вы ошиблись при вводе идентификатора(id) видео<hr />Убедитесь, что ссылка верная и/или видео есть на нашем сайте, в противном случае - свяжитесь с администрацией",
	"error_search" => "Видео по-вашему запросу не найденно. Поищите по-пожже!",

	"error_feedback_remail" => "Ошибка!!!<br />Не указан или пуст обратный-контактный email-адрес!",
	"error_feedback_theme" => "Ошибка!!!<br />Не указана или пуста тема сообщения!",
	"error_feedback_body" => "Ошибка!!!<br />Не указано или пусто сообщение!",
	"error_feedback_remail" => "Ошибка!!!<br />Не указан обратный-контактный email-адрес!",

	"error_undefined_spoiler" => "Спойлер",
	"error_undefined_quote" => "Цитата",
	
// XXX category
	"error_alert" => "!!!ВНИМАНИЕ!!!<br />Вы входите в раздел +18.<br />Пожалуйста - подтвердите Ваше намерение просматривать материал +18 нажав на эту ссылку: <a href=\"{C_default_http_host}?alert&send\">ССЫЛКА</a>.",
	"alert" => "!!!ВНИМАНИЕ!!!",
	"alert_up_eighteen" => "Вы входите в раздел +18.<br />Пожалуйста - подтвердите Ваше намерение просматривать материал +18 нажав на эту ссылку: <a href=\"{C_default_http_host}?alert&send\">ССЫЛКА</a>.",
	"on_xxx" => "Включить ХХХ",
	"off_xxx" => "Выключить ХХХ",

//reg new user
	"reg_page" => "Регистрация",
	"username" => "Имя пользователя",
	"pass" => "Пароль",
	"repass" => "Подтвердите пароль",
	"email" => "Электронная почта",

//added/edit video
	"add" => "Добавить",
	"saved" => "Сохраненно",
	"delete" => "Удалить",
	"done" => "<b>Успешно!</b>",
	"add_page" => "Добавить видео",
	"add_search_repeat" => "Найти повторы",
	"add_search_null" => "<b>Повторов не найденно</b>",
	"edit_page" => "Редактировать видео",
	"name" => "Название",
	"video" => "Видео",

	"genre" => "Жанр",
	"year" => "Год",
	"year_view" => "Год выхода",
	"year_start" => "Год выпуска",

	"check_name" => "Проверить на повторы",
	"check_prav" => "Проверить правописание",
	"check_prav_opt" => "Параметры...",

	"descr" => "Описание",
	"cat_list" => "Категория",
	"submit" => "Отправить",

	"server_list" => "Поддерживаемые серверы",

	"parser" => "Парсер",
	"error_parser_url" => "Ошибка ввода ссылки на видео",
	"edit" => "Редактировать",
	"edit_all" => "<div title=\"Редактировать все данные согласно парсеру\" alt=\"Редактировать все данные согласно парсеру\">Все данные</div>",

//translate
	"translate" => array('а' => "a", 'б' => "b", 'в' => "v", 'г'=>"g", 'д'=>"d", 'е'=>"e", 'ё'=>"e", 'ж'=>"zh", 'з'=>"z", 'и'=>"i", 'й'=>"i", 'к'=>"k", 'л'=>"l", 'м'=>"m", 'н'=>"n", 'о'=>"o", 'п'=>"p", 'р'=>"r", 'с'=>"s", 'т'=>"t", 'у'=>"u", 'ф'=>"f", 'х'=>"h", 'ц'=>"c", 'ч'=>"ch", 'ш'=>"sh", 'щ'=>"sz", 'ъ'=>"", 'ы'=>"y", 'ь'=>"", 'э'=>"e", 'ю'=>"yu", 'я'=>"ya"),
	"translate_en" => array('f' => "а", ',' => "б", '<' => "б", 'd' => "в", 'u'=>"г", 'l'=>"д", 't'=>"е", '`'=>"ё", '~'=>"ё", ';'=>"ж", ':'=>"ж", 'p'=>"з", 'b'=>"и", 'q'=>"й", 'r'=>"к", 'k'=>"л", 'v'=>"м", 'y'=>"н", 'j'=>"о", 'g'=>"п", 'h'=>"р", 'c'=>"с", 'n'=>"т", 'e'=>"у", 'a'=>"ф", '['=>"х", '{'=>"Х", 'w'=>"ц", 'x'=>"ч", 'i'=>"ш", 'o'=>"щ", ']'=>"ъ", '}'=>"Ъ", 's'=>"ы", 'm'=>"ь", "'"=>"э", "\""=>"Э", '.'=>"ю", '>'=>"Ю", 'z'=>"я"),

//index.php/main page
	"duration_null" => "Неизвестно",
	"duration" => "Продолжительность",
	"next" => "Далее",

//others series
	"season_b" => "Сезон",
	"season" => "сезон",
	"seriya_b" => "Серия",
	"seriya" => "серия",
	"seriya_b_ua" => "Серiя",
	"seriya_ua" => "серiя",
	"part_b" => "Часть",
	"part" => "часть",
	"others_search" => "Сезон|Серия|Часть|сезон|серия|часть|#|№|серiя|Серiя",
	"num_sim" => "№",

//video
	"lightoff" => "Выключить свет",
	"others_seriys" => "Остальные серии",
	"tags" => "Теги",

	"error_workvideo" => "Не работает видео?",
	"error_done" => "Успешно сообщили о неработающем видео!",
	"error_error" => "При попытке сообщить о не работающем видео - произошла ошибка, попробуйте пожже, либо оповестите администратора по-електронной почте <a href=\"mailto:killer-server@mail.ru\">killer-server@mail.ru</a>",

//search
	"search" => "Поиск",
	"maybe_search" => "Возможно Вы искали: \"<a href=\"{C_default_http_host}search/%s[1]\"><b>%s[2]</b></a>\"",
	"search_in_genre" => "Поиск по жанру: %s",
	"cat_in" => "Категория: %s",
	"search_in" => "Поиск: %s",
	"search_tag" => "Поиск по-тегу: \"%s\"",
	"added" => "Добавил",
	"time" => "Время",
	"null" => "Пусто",
	"level_simular" => "Степень схожести",
	"simular_null" => "Поиск неудачен, слишком короткая строка для поиска.",

//album
	"add_album" => "Добавить альбом",
	"album_list" => "Список альбомов",

//users
	"profile" => "Профиль",
	"recomend" => "Рекомендуется",
	"last_add" => "Действия",
	"big_all" => "Последнее добавленное",
	"playlist" => "Плейлисты",
	"playlist0" => "Плейлист",
	"popular" => "Популярные видео",
	"more" => "Больше",
	"views" => "Просмотров",
	"view" => "Просмотр",

//messages
	"messages" => "Сообщения",

//emailer
	"feedback_complited" => "Собщение успешно отправленно",
	"theme" => "Тема сообщения",
	"body" => "Содержание письма<br /><small>\"Тело\" сообщения</small>",
	"attach" => "Вложенные файлы",
	"image" => "Картинка",
	"images" => "Картинки",

	"times" => array(
		"years" => array(
			'год',
			'лет',
			'года',
		),
		"months" => array(
			'месяц',
			'месяцев',
			'месяца',
		),
		"weeks" => array(
			'неделю',
			'недель',
			'недели',
		),
		"days" => array(
			'день',
			'дней',
			'дня',
		),
		"hours" => array(
			'час',
			'часов',
			'часа',
		),
		"minutes" => array(
			'минута',
			'минут',
			'минуты',
		),
		"seconds" => array(
			'секунда',
			'секунд',
			'секунды',
		),
		"back" => "назад",
	),
	"aDay" => array(
		"Понедельник",
		"Вторник",
		"Среда",
		"Четверг",
		"Пятница",
		"Суббота",
		"Воскресенье",
	),
	"avDay" => array(
		"1" => "Бездельник",
		"2" => "Повторник",
		"3" => "Бреда",
		"4" => "Бухверг",
		"5" => "Питница",
		"6" => "Клуббота",
		"7" => "Похмельсенье",
	),

	"level" => array(
		LEVEL_MODER => "Администрация",
		LEVEL_USER => "Пользователь",
		LEVEL_GUEST => "Гость",
	),
	"parser_title" => array(
		"animespirit" => " &raquo; Смотреть аниме онлайн и многое другое - бесплатно и без регистрации",
	),

//most year
	"most_year" => "Вам должно быть",
	"most_year2" => "или более лет для просмотра!",
	"s_years" => "лет",

//albums
	"album" => "Альбом",
	"albums" => "Альбомы",
	"albums_user" => "Альбомы пользователя",

//admin pogoda
	"pogoda" => "Погода",
	"temperature" => "Температура",
	"wind" => "Ветер",
	"pressure" => "Давление",
	"mmrs" => "мм рт. ст.",
	"wind_direction" => "Направление ветра",
	"wind_spped" => "Скорость ветра",
	"humidity" => "Влажность",

//admin main
	"control_cardinal" => "Управление Кардиналом",
	"moder" => "Проверенно",
	"users" => "Пользователи",
	"styles" => "Стили",


	"view_all" => "Смотреть подробнее",
	"add_comments" => "Добавить комментарий",
	"comments" => "Комментарии",
	
	"s_like" => "Понравилось",
	"s_unlike" => "Не понравилось",
	"like" => "понравилось",
	"unlike" => "не понравилось",
	
	//screens
	"screens" => "Скриншоты",
	
	//video
	"video_light" => "Выключить свет",
	"repeat" => "Повтор выключен",
	"repeat_off" => "Повтор выключен",
	"repeat_on" => "Повтор включен",
	
	//recover
	"recover_template_html" => "<h2>Для восстановления пароля, Ваш проверочный код:</h2> <h1><font color=\"\"><b>%s</b></font></h1><br />IP-адрес с которого был запрошен доступ: %s<br /><b>!!!Если Вы не запрашивали восстановление доступа - проигнорируйте это сообщение!!!</b>",
	"recover_template_text" => "Для восстановления пароля, Ваш проверочный код: %s\nIP-адрес с которого был запрошен доступ: %s\n!!!Если Вы не запрашивали восстановление доступа - проигнорируйте это сообщение!!!",
	"recover_access" => "Введите пароль",
	"recover_mail" => "<center>На электронный адрес %s пользователя было отправленно сообщение.</center><br />Для продолжения - введите полученный ключ:",
	"recover_username" => "Введите имя пользователя",
	"recover_or" => "или",
	"recover_done" => "Ваш новый пароль для входа в систему",
	"recover_email" => "Введите электронную почту пользователя",
	"recover_key" => "Введите, полученный на почту, ключ подтверждения",
	"recover_not_key" => "Введённый ключ подтверждения не верный",
	"recover_not_exists_user" => "Введённый пользователь не найден в базе данных",
	"recover_not_exists_email" => "Введённый электронный адрес не найден в базе данных",

	"linet" => "LiveInternet: показано число просмотров за 24",
	"yam" => "яндекс.метрика",
	"yam_full" => "яндекс.метрика: данные за сегодня (просмотры, визиты и уникальные посетители)",
	"mail_rating" => "рейтинг@Mail.ru",
	"linet_full" => "часа, посетителей за 24 часа и за сегодня",
	
	'time_heute' =>	"Сегодня",
	'time_gestern' => "Вчера",
	
	"langdate" => array(
		'January' => "января",
		'February' => "февраля",
		'March' => "марта",
		'April' => "апреля",
		'May' => "мая",
		'June' => "июня",
		'July' => "июля",
		'August' =>	"августа",
		'September' => "сентября",
		'October' => "октября",
		'November' => "ноября",
		'December' => "декабря",
		'Jan' => "янв",
		'Feb' => "фев",
		'Mar' => "мар",
		'Apr' => "апр",
		'Jun' => "июн",
		'Jul' => "июл",
		'Aug' => "авг",
		'Sep' => "сен",
		'Oct' => "окт",
		'Nov' => "ноя",
		'Dec' => "дек",
		'Sunday' => "Воскресенье",
		'Monday' => "Понедельник",
		'Tuesday' => "Вторник",
		'Wednesday'	=> "Среда",
		'Thursday' => "Четверг",
		'Friday' => "Пятница",
		'Saturday' => "Суббота",
		'Sun' => "Вс",
		'Mon' => "Пн",
		'Tue' => "Вт",
		'Wed' => "Ср",
		'Thu' => "Чт",
		'Fri' => "Пт",
		'Sat' => "Сб",
	),
));

?>