<?php
/*
 *
 * @version 7.8
 * @copyright 2014-2017 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.7-a1
 * Version File: 10
 *
 * 10.0
 * add video light(really?!five version in cp1251 and five version after - in utf-8....finally!)
 * 10.1
 * add alert and error for ajax, add repeat(really?! I do this?!)
 * 10.2
 * add error for routification
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

$lang = array_merge($lang, array(
	"lang_ini" => "ru",
	"site_name" => "Cardinal Engine",
	"sitename" => "Cardinal Engine",
	"s_description" => "Сайт на Cardinal Engine",
	"adminpanel" => "Админ-панель",
	"save" => "Сохранить",
//errors
	"error" => "Ошибка!!!",
	"error_level" => "Ошибка доступа",

//reg new user
	"reg_page" => "Регистрация",
	"username" => "Имя пользователя",
	"pass" => "Пароль",
	"repass" => "Подтвердите пароль",
	"email" => "Электронная почта",

//added/edit video
	"add" => "Добавить",
	"view" => "Просмотр",
	"delete" => "Удалить",
	"descr" => "Описание",
	"cat_list" => "Категория",
	"submit" => "Отправить",
	"edit" => "Редактировать",
	"quickEdit" => "Быстрое редактирование",

//translate
	"translate" => array('а' => "a", 'б' => "b", 'в' => "v", 'г'=>"g", 'д'=>"d", 'е'=>"e", 'ё'=>"e", 'ж'=>"zh", 'з'=>"z", 'и'=>"i", 'й'=>"i", 'к'=>"k", 'л'=>"l", 'м'=>"m", 'н'=>"n", 'о'=>"o", 'п'=>"p", 'р'=>"r", 'с'=>"s", 'т'=>"t", 'у'=>"u", 'ф'=>"f", 'х'=>"h", 'ц'=>"c", 'ч'=>"ch", 'ш'=>"sh", 'щ'=>"sz", 'ъ'=>"", 'ы'=>"y", 'ь'=>"", 'э'=>"e", 'ю'=>"yu", 'я'=>"ya"),
	"translate_en" => array('f' => "а", ',' => "б", '<' => "б", 'd' => "в", 'u'=>"г", 'l'=>"д", 't'=>"е", '`'=>"ё", '~'=>"ё", ';'=>"ж", ':'=>"ж", 'p'=>"з", 'b'=>"и", 'q'=>"й", 'r'=>"к", 'k'=>"л", 'v'=>"м", 'y'=>"н", 'j'=>"о", 'g'=>"п", 'h'=>"р", 'c'=>"с", 'n'=>"т", 'e'=>"у", 'a'=>"ф", '['=>"х", '{'=>"Х", 'w'=>"ц", 'x'=>"ч", 'i'=>"ш", 'o'=>"щ", ']'=>"ъ", '}'=>"Ъ", 's'=>"ы", 'm'=>"ь", "'"=>"э", "\""=>"Э", '.'=>"ю", '>'=>"Ю", 'z'=>"я"),

	"tags" => "Теги",
//search
	"search" => "Поиск",
	"added" => "Добавил",
	"time" => "Время",

//users
	"profile" => "Профиль",
	"recomend" => "Рекомендуется",
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
		LEVEL_CREATOR => "Создатель",
		LEVEL_CUSTOMER => "Владелец",
		LEVEL_ADMIN => "Администрация",
		LEVEL_MODER => "Модератор",
		LEVEL_USER => "Пользователь",
		LEVEL_GUEST => "Гость",
	),

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
		'Sun' => "Вос",
		'Mon' => "Пон",
		'Tue' => "Вт",
		'Wed' => "Ср",
		'Thu' => "Чет",
		'Fri' => "Пят",
		'Sat' => "Суб",
	),
));

?>