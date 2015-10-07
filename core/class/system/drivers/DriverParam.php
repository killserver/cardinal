<?php
/*
 *
 * @version 2015-10-07 17:50:38 1.25.6-rc3
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc3
 * Version File: 1
 *
 * 1.1
 * add support driver for databases
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

abstract class DriverParam {

	public $connecten;
	public $type_driver;
	public static $subname;

}

?>