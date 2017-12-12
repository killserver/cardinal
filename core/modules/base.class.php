<?php
/*
 *
 * @version 4.0a
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 4.0a
 * Version File: 2
 *
 * 2.1
 * add support working without connect to database
 * 2.2
 * add support last changes in system
 *
*/
if(!defined("IS_CORE")) {
die();
}

class base extends modules {

	function __construct() {
		$this->manifest_log('load_modules', array('base', __FILE__));
		$this->regCssJs(array("https://fonts.googleapis.com/css?family=Roboto:300,700", "https://killserver.github.io/Fonts/main.min.css"), "css");
	}

}
modules::load_hooks("base");