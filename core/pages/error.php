<?php
/*
 *
 * @version 1.25.7-a1
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.7-a1
 * Version File: 1
 *
 * 1.1
 * add error for routification
 *
*/
class page {
	
	function __construct() {
		templates::error("{L_error_page}", "{L_error}");
	}
	
}

?>