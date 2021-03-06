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
 * add support return array all property
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class DBObject {

    final public function getArray() {
        return get_object_vars($this);
    }

    final public function Time() {
        $r = db::doquery("SELECT UNIX_TIMESTAMP() AS `time`");
        return $r['time'];
    }

    final public function Insert($table) {
        $arr = get_object_vars($this);
        $key = array_keys($arr);
        $val = array_values($arr);
        return db::doquery("INSERT INTO `".$table."` (".implode(", ", array_map(function($d) { return "`".$d."`";}, $key)).") VALUES(".implode(", ", array_map(function($d) {return (strpos($d, "(")!==false&&strpos($d, ")")!==false ? $d : "'".db::escape($d)."'");}, $val)).")");
    }

    final public function Update($table, $where) {
        $arr = get_object_vars($this);
        $key = array_keys($arr);
        $val = array_values($arr);
        return db::doquery("UPDATE `".$table."` SET ".implode(", ", array_map(function($k, $v) { return "`".$k."` = ".(strpos($v, "(")!==false&&strpos($v, ")")!==false ? $v : "'".db::escape($v)."'");}, $key, $val))." WHERE ".$where);
    }

}

?>