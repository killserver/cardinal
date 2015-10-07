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

interface drivers {

	public function check_connect($host, $user, $pass);
	public function exists_db($host, $user, $pass, $db);
	public function connect($host, $user, $pass, $db, $charset, $port);
	public function connected();
	public function get_type();
	public function set_type($int);
	public function affected_rows();
	public function insert_id();
	public function num_fields();
	public function query($query);
	public function fetch_row($query);
	public function fetch_array($query);
	public function fetch_assoc($query);
	public function fetch_object($query, $class_name, $params);
	public function num_rows($query);
	public function free($query);
	public function error($query);
	public function close();

}

?>