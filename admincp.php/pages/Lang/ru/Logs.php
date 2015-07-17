<?php
if(!defined("IS_ADMIN")) {
echo "403 ERROR";
die();
}

$lang = array_merge($lang, array(
	"Logs" => "Журнал ошибок",
));

?>