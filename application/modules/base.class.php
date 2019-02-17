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
if (!defined("IS_CORE")) {
	die();
}

class base extends modules {

	function __construct() {
		$this->manifest_log('load_modules', array('base', __FILE__));
		$this->regCssJs("https://cdn.polyfill.io/v2/polyfill.min.js?ua=" . urlencode(HTTP::getServer("HTTP_USER_AGENT")) . "&features=es6&notPack", "js", false, "polyfill");
		$this->regCssJs("{C_default_http_local}js/helpers/apng-canvas.min.js?notPack", "js", false, "apng");
		$this->regCssJs("{C_default_http_local}js/helpers/libwebp.min.js?notPack", "js", false, "webp");
		$this->regCssJs("{C_default_http_local}js/helpers/polyCss.min.js?notPack", "js", false, "polyCss");
	}

}