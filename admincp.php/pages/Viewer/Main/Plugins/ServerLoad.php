<?php

class Main_ServerLoad extends Main {

  private function get_server_load() {
    if(stristr(PHP_OS, 'win')) {
      $wmi = new COM("Winmgmts://");
      $server = $wmi->execquery("SELECT LoadPercentage FROM Win32_Processor");
      $cpu_num = 0;
      $load_total = 0;
      foreach($server as $cpu) {
        $cpu_num++;
        $load_total += $cpu->loadpercentage;
      }
      $load = (int) round($load_total/$cpu_num);
    } else {
      $sys_load = sys_getloadavg();
      $load = $sys_load[0]*100;
    }
    return ($load>100 ? 100 : $load);
  }

  private function get_server_memory_usage() {
  	$free = shell_exec('free');
  	$free = (string)trim($free);
  	$free_arr = explode("\n", $free);
  	$mem = explode(" ", $free_arr[1]);
  	$mem = array_filter($mem);
  	$mem = array_merge($mem);
    $memory_usage = ($mem[3]+$mem[5])/$mem[1]*100;
    $memory_usage = round($memory_usage, 2);
  	return ($memory_usage>100 ? 100 : $memory_usage);
  }

  function __construct() {
    if(isset($_GET['getServerLoad'])) {
      HTTP::echos(json_encode(array($this->get_server_load(), $this->get_server_memory_usage())));
      die();
    }
    templates::assign_var("cpuUse", $this->get_server_load());
    templates::assign_var("memUse", $this->get_server_memory_usage());
  }

}
