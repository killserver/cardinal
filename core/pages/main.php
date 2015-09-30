<?php
/*
*
* @version 2015-09-30 13:30:44 1.25.6-rc1
* @copyright 2014-2015 KilleR for Cardinal Engine
*
* Version Engine: 1.25.6-rc1
* Version File: 1
*
* 1.1
* add seo optimization
*
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
exit();
}

class page {

    function __construct() {
		$tmp = templates::complited_assing_vars("index");
		templates::complited($tmp, array("title" => lang::get_lang('sitename'), "meta" => array(
					"ogpr" => array(
						"og:image" => "{C_default_http_host}logo.jpg?1",
						"og:site_name" => "{L_sitename}",
						"og:url" => "{C_default_http_host}",
						"og:title" => "{L_sitename}",
						"og:description" => "{L_s_description}",
						"og:type" => "website",
					),
					"link" => array(
						"image_src" => "{C_default_http_host}logo.jpg?1",
					),
					"keywords" => "{L_s_keywords}",
					"description" => "{L_s_description}",
				)));
		templates::display();
	}

}

?>