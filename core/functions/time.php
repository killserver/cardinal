<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

function get_date($date, $array){return function_call('get_date', array($date, $array));}
function or_get_date($date, $array) {
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

function langdate($date, $temp, $only_date){return function_call('langdate', array($date, $temp, $only_date));}
function or_langdate($date, $temp = "d F Y H:i:s", $only_date = false) {
	if(class_exists("lang")) {
		$lang = lang::get_lang("langdate");
	} else {
		$lang = array();
	}
	$temp = str_replace(array("<br>", "<br/>", "<br />"), "\n", $temp);
	if(!is_array($lang)) {
		return date($temp, $date);
	}
	if(empty($date) || $date==0 || !is_numeric($date)) {
		if($only_date) {
			$local = new DateTime('@'.time());
			$local->setTimeZone(new DateTimeZone(config::Select("date_timezone")));
			if(sizeof($lang)>0) {
				$date = strtr($local->format($temp), $lang);
			} else {
				$date = $local->format($temp);
			}
			return nl2br($date);
		}
		return "";
	}
	if(date('Ymd', $date) == date('Ymd', time())) {
		$local = new DateTime('@'.$date);
		$local->setTimeZone(new DateTimeZone(config::Select("date_timezone")));
		if(sizeof($lang)>0) {
			$date = strtr($local->format($temp), $lang);
		} else {
			$date = $local->format($temp);
		}
		if(!$only_date) {
			return nl2br((class_exists("lang") ? lang::get_lang("time_heute") : "Today").",".$date);
		} else {
			return nl2br($date);
		}
	} elseif(date('Ymd', $date) == date('Ymd', (time()-86400))) {
		$local = new DateTime('@'.$date);
		$local->setTimeZone(new DateTimeZone(config::Select("date_timezone")));
		if(sizeof($lang)>0) {
			$date = strtr($local->format($temp), $lang);
		} else {
			$date = $local->format($temp);
		}
		if(!$only_date) {
			return nl2br((class_exists("lang") ? lang::get_lang("time_gestern") : "Yesterday").",".$date);
		} else {
			return nl2br($date);
		}
	} else {
		$local = new DateTime('@'.$date);
		$local->setTimeZone(new DateTimeZone(config::Select("date_timezone")));
		if(sizeof($lang)>0) {
			$date = strtr($local->format($temp), $lang);
		} else {
			$date = $local->format($temp);
		}
		return nl2br($date);
	}
}

function timespan($seconds = 1, $time = 0){return function_call('timespan', array($seconds, $time));}
function or_timespan($seconds = 1, $time = 0) {
	if(!is_numeric($seconds)) {
		$seconds = 1;
	}
	if(is_numeric($time) && $time<=0) {
		$time = time();
	}
	if($time >= $seconds) {
		$seconds = $time - $seconds;
	}

	$result = array();
	$years = floor($seconds / 31536000);

	if($years > 0) {
		$lang = (class_exists("lang") ? lang::get_lang('times', 'years') : "years");
		$result[] = $years.' '.get_date($years, $lang);
	}

	$seconds -= $years*31536000;
	$months = floor($seconds/2628000);

	if($years > 0 || $months > 0) {
		if($months > 0) {
			$lang = (class_exists("lang") ? lang::get_lang('times', 'months') : "months");
			$result[] = $months.' '.get_date($months, $lang);
		}
		$seconds -= $months * 2628000;
	}

	$weeks = floor($seconds / 604800);
	if($years > 0 || $months > 0 || $weeks > 0) {
		if($weeks > 0) {
			$lang = (class_exists("lang") ? lang::get_lang('times', 'weeks') : "weeks");
			$result[] = $weeks.' '.get_date($weeks, $lang);
		}
		$seconds -= $weeks * 604800;
	}

	$days = floor($seconds / 86400);
	if($months > 0 || $weeks > 0 || $days > 0) {
		if($days > 0) {
			$lang = (class_exists("lang") ? lang::get_lang('times', 'days') : "days");
			$result[] = $days.' '.get_date($days, $lang);
		}
		$seconds -= $days * 86400;
	}

	$hours = floor($seconds / 3600);
	if($days > 0 || $hours > 0) {
		if($hours > 0) {
			$lang = (class_exists("lang") ? lang::get_lang('times', 'hours') : "hours");
			$result[] = $hours.' '.get_date($hours, $lang);
		}
		$seconds -= $hours * 3600;
	}

	$minutes = floor($seconds / 60);
	if($days > 0 || $hours > 0 || $minutes > 0) {
		if($minutes > 0) {
			$lang = (class_exists("lang") ? lang::get_lang('times', 'minutes') : "minutes");
			$result[] = $minutes.' '.get_date($minutes, $lang);
		}
		$seconds -= $minutes * 60;
	}

	if(empty($result)) {
		$lang = (class_exists("lang") ? lang::get_lang('times', 'seconds') : "seconds");
		$result[] = $seconds.' '.get_date($seconds, $lang);
	}
	return implode(" ", $result);
}
if(!function_exists("hrtime")) {
	$startAt = 1533462603;
	function hrtime($asNum = false) {
		global $startAt;
		$ns = microtime(false);
		$s = substr($ns, 11) - $startAt;
		$ns = 1E9 * (float) $ns;
		if($asNum) {
			$ns += $s * 1E9;
			return \PHP_INT_SIZE === 4 ? $ns : (int) $ns;
		}
		return array($s, (int) $ns);
	}
}

?>