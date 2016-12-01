<?php
if(!defined("IS_ADMIN")) {
echo "403 ERROR";
die();
}

$lang = array_merge($lang, array(
	"list_changelog" => "Список изменений в новой версии",
	"download" => "Загрузить",
	"install" => "Установить",
	"new_versions" => "новую версию",
	"fail_update" => "Ошибка обновления",
	"done_update" => "Успешное обновление",
	"done_updates" => "Шаг обновления успешен",
	"install_update_fail_localhost" => "ВНИМАНИЕ!! Была произведена попытка установить обновления на localhost-е. Для обновления - зайдите в папку core/cache/system/ и распакуйте архив с обновлением в корень Вашего сайта.",
	"install_locked" => "Обновление заблокировано! Отсутствуют требуемые разрешения! Установите права на папку core/cache/ и core/cache/system/ права 0777 и повторите попытку обновления",
));

?>