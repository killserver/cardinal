<?php
if(!defined("IS_ADMIN")) {
echo "403 ERROR";
die();
}

$lang = array_merge($lang, array(
	"logInAdmin" => "Список действий в админ-панели",
));

?>