<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die;
}

function get_date($date,$array) {
	$first=$array[0];
	$second=$array[1];
	$third=$array[2];
	if((($date % 10) > 4 && ($date % 10) < 10) || ($date > 10 && $date < 20)){
		return $second;
	}
	if(($date % 10) > 1 && ($date % 10) < 5){
		return $third;
	}
	if(($date) == 1){
		return $first;
	} else {
		return $second;
	}
}

function langdate($date) {
	$only_date = false;
	if(is_array($date) && isset($date[3]) && !empty($date[3])) {
		$temp = $date[3];
	} else {
		$temp = ", H:i";
	}
	if(is_array($date[4]) && isset($date[4]) && !empty($date[4])) {
		$only_date = true;
	}
	if(is_array($date) && isset($date[1])) {
		$date = $date[1];
	}
	if(date('Ymd', $date) == date('Ymd', time())) {
		$local = new DateTime('@'.$date);
		$local->setTimeZone(new DateTimeZone(config::Select("date_timezone")));
		$date = strtr($local->format($temp), lang::get_lang("langdate"));
		if(!$only_date) {
			return lang::get_lang("time_heute").",".$date;
		} else {
			return $date;
		}
	} elseif(date('Ymd', $date) == date('Ymd', (time()-86400))) {
		$local = new DateTime('@'.$date);
		$local->setTimeZone(new DateTimeZone(config::Select("date_timezone")));
		$date = strtr($local->format($temp), lang::get_lang("langdate"));
		if(!$only_date) {
			return lang::get_lang("time_gestern").",".$date;
		} else {
			return $date;
		}
	} else {
		$local = new DateTime('@'.$date);
		$local->setTimeZone(new DateTimeZone(config::Select("date_timezone")));
		return strtr($local->format($temp), lang::get_lang("langdate"));
	}
}

function timespan($seconds = 1, $time = null) {
global $lang;
	if(!is_numeric($seconds)) {
		$seconds = 1;
	}
	if(!is_numeric($time)) {
		$time = time();
	}
	if($time <= $seconds) {
		$seconds = 1;
	} else {
		$seconds = $time - $seconds;
	}

	$result = array();
	$years = floor($seconds / 31536000);

	if($years > 0) {
		$result[] = $years.' '.get_date($years,$lang['times']['years']);
	}

	$seconds -= $years*31536000;
	$months = floor($seconds/2628000);

	if($years > 0 || $months > 0) {
		if($months > 0) {
			$result[] = $months.' '.get_date($months,$lang['times']['months']);
		}
		$seconds -= $months * 2628000;
	}

	$weeks = floor($seconds / 604800);
	if($years > 0 || $months > 0 || $weeks > 0) {
		if($weeks > 0) {
			$result[] = $weeks.' '.get_date($weeks,$lang['times']['weeks']);
		}
		$seconds -= $weeks * 604800;
	}

	$days = floor($seconds / 86400);
	if($months > 0 || $weeks > 0 || $days > 0) {
		if($days > 0) {
			$result[] = $days.' '.get_date($days,$lang['times']['days']);
		}
		$seconds -= $days * 86400;
	}

	$hours = floor($seconds / 3600);
	if($days > 0 || $hours > 0) {
		if($hours > 0) {
			$result[] = $hours.' '.get_date($hours,$lang['times']['hours']);
		}
		$seconds -= $hours * 3600;
	}

	$minutes = floor($seconds / 60);
	if($days > 0 || $hours > 0 || $minutes > 0) {
		if($minutes > 0) {
			$result[] = $minutes.' '.get_date($minutes,$lang['times']['minutes']);
		}
		$seconds -= $minutes * 60;
	}

	if(empty($result)) {
		$result[] = $seconds.' '.get_date($seconds,$lang['times']['seconds']);
	}
return implode(" ", $result);
}

?>