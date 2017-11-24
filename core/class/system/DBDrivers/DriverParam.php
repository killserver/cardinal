<?php
/*
 *
 * @version 4.1
 * @copyright 2014-2016 KilleR for Cardinal Engine
 *
 * Version Engine: 4.1
 * Version File: 1
 *
 * 1.1
 * add support driver for databases
 * 1.2
 * add support all need columns
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

abstract class DriverParam {

	//private $mc;
	public $connecten;
	public $type_driver;
	public static $subname;
	public $type_error;

}

?>