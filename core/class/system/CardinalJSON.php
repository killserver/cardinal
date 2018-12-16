<?php

class CardinalJSON {

    final public static function save($data, $normal = false) {
    	$data = self::json_encode_unicode($data, JSON_HEX_QUOT);
    	if($normal) {
    		$data = self::normalizer($data);
    	}
		return $data;
	}

	final public static function json_encode_unicode($arr, $params) {
		if(defined('JSON_UNESCAPED_UNICODE')) {
			return json_encode($arr, $params | JSON_UNESCAPED_UNICODE);
		} else {
			return preg_replace_callback('/(?<!\\\\)\\\\u([0-9a-f]{4})/i', "CardinalJSON::json_encode_unicode_fn", json_encode($arr, $params));
		}
	}

	final private static function json_encode_unicode_fn($m) {
		$d = pack("H*", $m[1]);
		$r = mb_convert_encoding($d, "UTF8", "UTF-16BE");
		return $r!=="?" && $r!=="" ? $r : $m[0];
	}
	
	final public static function normalizer($data) {
		$arr = array();
		$tab = 1;
		$d = false;
		for($f=0;$f<strlen($data);$f++) {
			$bytes = $data[$f];
			if($d && $bytes === $d) {
				$data[$f - 1] !== "\\" && ($d = !1);
			} else if(!$d && ($bytes === '"' || $bytes === "'")) {
				$d = $bytes;
			} else if(!$d && ($bytes === " " || $bytes === "\t")) {
				$bytes = "";
			} else if(!$d && $bytes === ":") {
				$bytes = $bytes." ";
			} else if(!$d && $bytes === ",") {
				$bytes = $bytes."\n";
				$bytes = str_pad($bytes, ($tab * 2), " ");
			} else if(!$d && ($bytes === "[" || $bytes === "{")) {
				$tab++;
				$bytes .= "\n";
				$bytes = str_pad($bytes, ($tab * 2), " ");
			} else if(!$d && ($bytes === "]" || $bytes === "}")) {
				$tab--;
				$bytes = str_pad("\n", ($tab * 2), " ").$bytes;
			}
			array_push($arr, $bytes);
		}
		return implode("", $arr);
	}
	
}

?>