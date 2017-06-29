<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function get_date($date, $array) {
	$first = $array[0];
	$second = $array[1];
	$third = $array[2];
	if((($date % 10) > 4 && ($date % 10) < 10) || ($date > 10 && $date < 20)) {
		return $second;
	}
	if(($date % 10) > 1 && ($date % 10) < 5) {
		return $third;
	}
	if($date == 1) {
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
	if(is_array($date) && isset($date[4]) && !empty($date[4])) {
		$only_date = true;
	}
	if(is_array($date) && isset($date[1])) {
		$date = $date[1];
	}
	$lang = lang::get_lang("langdate");
	if(!is_array($lang)) {
		return date($temp, $date);
	}
	if(empty($date) || $date==0 || !is_numeric($date)) {
		if($only_date) {
			$local = new DateTime('@'.time());
			$local->setTimeZone(new DateTimeZone(config::Select("date_timezone")));
			$date = strtr($local->format($temp), $lang);
			return $date;
		}
		return "";
	}
	if(date('Ymd', $date) == date('Ymd', time())) {
		$local = new DateTime('@'.$date);
		$local->setTimeZone(new DateTimeZone(config::Select("date_timezone")));
		$date = strtr($local->format($temp), $lang);
		if(!$only_date) {
			return lang::get_lang("time_heute").",".$date;
		} else {
			return $date;
		}
	} elseif(date('Ymd', $date) == date('Ymd', (time()-86400))) {
		$local = new DateTime('@'.$date);
		$local->setTimeZone(new DateTimeZone(config::Select("date_timezone")));
		$date = strtr($local->format($temp), $lang);
		if(!$only_date) {
			return lang::get_lang("time_gestern").",".$date;
		} else {
			return $date;
		}
	} else {
		$local = new DateTime('@'.$date);
		$local->setTimeZone(new DateTimeZone(config::Select("date_timezone")));
		return strtr($local->format($temp), $lang);
	}
}

function timespan($seconds = 1, $time = "") {
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
		$lang = lang::get_lang('times', 'years');
		if(!is_array($lang) || sizeof($lang)<3) {
			throw new Exception("Lang times for years is not implemented");
			die();
		}
		$result[] = $years.' '.get_date($years, $lang);
	}

	$seconds -= $years*31536000;
	$months = floor($seconds/2628000);

	if($years > 0 || $months > 0) {
		if($months > 0) {
			$lang = lang::get_lang('times', 'months');
			if(!is_array($lang) || sizeof($lang)<3) {
				throw new Exception("Lang times for months is not implemented");
				die();
			}
			$result[] = $months.' '.get_date($months, $lang);
		}
		$seconds -= $months * 2628000;
	}

	$weeks = floor($seconds / 604800);
	if($years > 0 || $months > 0 || $weeks > 0) {
		if($weeks > 0) {
			$lang = lang::get_lang('times', 'weeks');
			if(!is_array($lang) || sizeof($lang)<3) {
				throw new Exception("Lang times for weeks is not implemented");
				die();
			}
			$result[] = $weeks.' '.get_date($weeks, $lang);
		}
		$seconds -= $weeks * 604800;
	}

	$days = floor($seconds / 86400);
	if($months > 0 || $weeks > 0 || $days > 0) {
		if($days > 0) {
			$lang = lang::get_lang('times', 'days');
			if(!is_array($lang) || sizeof($lang)<3) {
				throw new Exception("Lang times for days is not implemented");
				die();
			}
			$result[] = $days.' '.get_date($days, $lang);
		}
		$seconds -= $days * 86400;
	}

	$hours = floor($seconds / 3600);
	if($days > 0 || $hours > 0) {
		if($hours > 0) {
			$lang = lang::get_lang('times', 'hours');
			if(!is_array($lang) || sizeof($lang)<3) {
				throw new Exception("Lang times for hours is not implemented");
				die();
			}
			$result[] = $hours.' '.get_date($hours, $lang);
		}
		$seconds -= $hours * 3600;
	}

	$minutes = floor($seconds / 60);
	if($days > 0 || $hours > 0 || $minutes > 0) {
		if($minutes > 0) {
			$lang = lang::get_lang('times', 'minutes');
			if(!is_array($lang) || sizeof($lang)<3) {
				throw new Exception("Lang times for minutes is not implemented");
				die();
			}
			$result[] = $minutes.' '.get_date($minutes, $lang);
		}
		$seconds -= $minutes * 60;
	}

	if(empty($result)) {
		$lang = lang::get_lang('times', 'seconds');
		if(!is_array($lang) || sizeof($lang)<3) {
			throw new Exception("Lang times for seconds is not implemented");
			die();
		}
		$result[] = $seconds.' '.get_date($seconds, $lang);
	}
return implode(" ", $result);
}

?>