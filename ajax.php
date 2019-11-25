<?php
define("IS_CORE", true);
define("IS_AJAX", true);
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."core.php");
$method = (isset($_GET['type']) ? $_GET['type'] : "");
$method = explode(",", $method);
HTTP::ajax(call_user_func_array("execEvent", array_merge(array("ajax"), $method)));