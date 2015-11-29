<?php

class Main_Uptime extends Main {

	public function Main_Uptime() {
		$count = 0;
		$value = 0;
		$uptimerobot_id = config::Select("uptime", "uptimerobot_id");
		$uptimerobot_api = config::Select("uptime", "uptimerobot_api");
		$ping_admin_api = config::Select("uptime", "ping_admin_api");
		$syslab_api = config::Select("uptime", "syslab_api");
		$syslab_id = config::Select("uptime", "syslab_id");
		if(!empty($uptimerobot_id) && !empty($uptimerobot_api)) {
			$count += 1;
			$prs = new Parser("https://api.uptimerobot.com/getMonitors?apiKey=".config::Select("uptime", "uptimerobot_api")."&monitors=".$uptimerobot_id."&format=json&noJsonCallback=1");
			$json = $prs->get();
			$json = json_decode($json, true);
			if(is_array($json) && isset($json['monitors']['monitor'])) {
				$val = "";
				for($i=0;$i<sizeof($json['monitors']['monitor']);$i++) {
					if(isset($json['monitors']['monitor'][$i]['alltimeuptimeratio']) && $json['monitors']['monitor'][$i]['url']==substr(config::Select("default_http_host"), 0, -1)) {
						$val = $json['monitors']['monitor'][$i]['alltimeuptimeratio'];
					}
				}
				$value += intval($val);
			}
		}
		if(!empty($ping_admin_api)) {
			$count += 1;
			$prs = new Parser("https://ping-admin.ru/?a=api&sa=tasks&api_key=".$ping_admin_api);
			$json = $prs->get();
			$json = json_decode($json, true);
			if(is_array($json)) {
				$val = "";
				for($i=0;$i<sizeof($json);$i++) {
					if(isset($json) && $json[$i]['name']==config::Select("default_http_hostname")) {
						$val = $json[$i]['uptime_w']/100*10;
					}
				}
				$value += intval($val);
			}
		}
		if(!empty($syslab_id) && !empty($syslab_api)) {
			$count += 1;
			$prs = new Parser("http://api.syslab.ru/?apiKey=".$syslab_api."&monitorID=".$syslab_id."&monitorType=1&format=json");
			$json = $prs->get();
			$json = json_decode($json, true);
			if(is_array($json) && isset($json['Resourses'])) {
				$json = array_values($json['Resourses']);
				$val = "";
				for($i=0;$i<sizeof($json);$i++) {
					if(isset($json[$i]['statistic']['uptime']) && $json[$i]['address']==substr(config::Select("default_http_host"), 0, -1)) {
						$val = $json[$i]['statistic']['uptime'];
						break;
					}
				}
				$value += intval($val);
			}
		}
		templates::assign_vars(array(
			"uptime_visible" => ($count>0 ? "true" : "false"),
			"uptime_value" => ($count>0 ? number_format($value/$count, 1) : ""),
		));
	}

}

?>