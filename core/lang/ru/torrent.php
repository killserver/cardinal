<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

$lang = array_merge($lang, array(
	"general_size" => "Общий размер",
	"show_all" => "Показать все",
	"roll_up_all" => "Свернуть все",
	"size" => "Размер",
	"self" => "Взят",
	"torrent" => "Торрент",
	"ones" => "раз",
	"tracker" => "Трекер",
	"seeders" => "Раздают",
	"leechers" => "Качают",
	"see_full" => "Смотреть полностью",
	"reg_torrent" => "Зарегистрирован",
	"downloaded" => "Скачан",

	"download" => "Скачать",
	"download_free" => "Скачать бесплатно",

	"bolvan" => array(
		"t0" => "болванка",
		"t1" => "болванок",
		"t2" => "блованок",
	),
	"descr_size" => "%s (занимает %s %s, для записи нужно: %s %s %sR/RW)",
	"hash" => "Хэш",

	"share" => "Поделиться",
	"list_file" => "Список файлов",
));

?>