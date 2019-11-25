<?php
function esc_cardinal($text) {
	$text = (string) $text;
	if(strlen($text)===0) {
		$safe_text = '';
	} else {
		static $is_utf8 = null;
		if(!isset($is_utf8)) {
			$is_utf8 = in_array(config::Select("charset"), array('utf8', 'utf-8', 'UTF8', 'UTF-8'));
		}
		if(!$is_utf8) {
			$safe_text = $text;
		} else {
			static $utf8_pcre = null;
			if(!isset($utf8_pcre)) {
				$utf8_pcre = @preg_match('/^./u', 'a');
			}
			if(!$utf8_pcre) {
				$safe_text = $text;
			} else {
				if(@preg_match('/^./us', $text)===1) {
					$safe_text = $text;
				} else if(function_exists('iconv')) {
					$safe_text = iconv('utf-8', 'utf-8', $text);
				} else {
					$safe_text = "";
				}
			}
		}
	}

	$safe_text = (string) $safe_text;
	if(strlen($safe_text)===0) {
		$safe_text = '';
	} else {
		if(preg_match('/[&<>"\']/', $safe_text)) {
			$quote_style = ENT_QUOTES;
			$charset = config::Select("charset");
			if(in_array($charset, array('utf8', 'utf-8', 'UTF8'))) {
				$charset = 'UTF-8';
			}
			$safe_text = str_replace('&', '&amp;', $safe_text);
			$safe_text = preg_replace_callback('/&amp;([A-Za-z]{2,8}[0-9]{0,2});/', function($matches) {
				if(empty($matches[1])) {
					return '';
				}
				$i = $matches[1];
				$allowedentitynames = array(
					'nbsp',    'iexcl',  'cent',    'pound',  'curren', 'yen',
					'brvbar',  'sect',   'uml',     'copy',   'ordf',   'laquo',
					'not',     'shy',    'reg',     'macr',   'deg',    'plusmn',
					'acute',   'micro',  'para',    'middot', 'cedil',  'ordm',
					'raquo',   'iquest', 'Agrave',  'Aacute', 'Acirc',  'Atilde',
					'Auml',    'Aring',  'AElig',   'Ccedil', 'Egrave', 'Eacute',
					'Ecirc',   'Euml',   'Igrave',  'Iacute', 'Icirc',  'Iuml',
					'ETH',     'Ntilde', 'Ograve',  'Oacute', 'Ocirc',  'Otilde',
					'Ouml',    'times',  'Oslash',  'Ugrave', 'Uacute', 'Ucirc',
					'Uuml',    'Yacute', 'THORN',   'szlig',  'agrave', 'aacute',
					'acirc',   'atilde', 'auml',    'aring',  'aelig',  'ccedil',
					'egrave',  'eacute', 'ecirc',   'euml',   'igrave', 'iacute',
					'icirc',   'iuml',   'eth',     'ntilde', 'ograve', 'oacute',
					'ocirc',   'otilde', 'ouml',    'divide', 'oslash', 'ugrave',
					'uacute',  'ucirc',  'uuml',    'yacute', 'thorn',  'yuml',
					'quot',    'amp',    'lt',      'gt',     'apos',   'OElig',
					'oelig',   'Scaron', 'scaron',  'Yuml',   'circ',   'tilde',
					'ensp',    'emsp',   'thinsp',  'zwnj',   'zwj',    'lrm',
					'rlm',     'ndash',  'mdash',   'lsquo',  'rsquo',  'sbquo',
					'ldquo',   'rdquo',  'bdquo',   'dagger', 'Dagger', 'permil',
					'lsaquo',  'rsaquo', 'euro',    'fnof',   'Alpha',  'Beta',
					'Gamma',   'Delta',  'Epsilon', 'Zeta',   'Eta',    'Theta',
					'Iota',    'Kappa',  'Lambda',  'Mu',     'Nu',     'Xi',
					'Omicron', 'Pi',     'Rho',     'Sigma',  'Tau',    'Upsilon',
					'Phi',     'Chi',    'Psi',     'Omega',  'alpha',  'beta',
					'gamma',   'delta',  'epsilon', 'zeta',   'eta',    'theta',
					'iota',    'kappa',  'lambda',  'mu',     'nu',     'xi',
					'omicron', 'pi',     'rho',     'sigmaf', 'sigma',  'tau',
					'upsilon', 'phi',    'chi',     'psi',    'omega',  'thetasym',
					'upsih',   'piv',    'bull',    'hellip', 'prime',  'Prime',
					'oline',   'frasl',  'weierp',  'image',  'real',   'trade',
					'alefsym', 'larr',   'uarr',    'rarr',   'darr',   'harr',
					'crarr',   'lArr',   'uArr',    'rArr',   'dArr',   'hArr',
					'forall',  'part',   'exist',   'empty',  'nabla',  'isin',
					'notin',   'ni',     'prod',    'sum',    'minus',  'lowast',
					'radic',   'prop',   'infin',   'ang',    'and',    'or',
					'cap',     'cup',    'int',     'sim',    'cong',   'asymp',
					'ne',      'equiv',  'le',      'ge',     'sub',    'sup',
					'nsub',    'sube',   'supe',    'oplus',  'otimes', 'perp',
					'sdot',    'lceil',  'rceil',   'lfloor', 'rfloor', 'lang',
					'rang',    'loz',    'spades',  'clubs',  'hearts', 'diams',
					'sup1',    'sup2',   'sup3',    'frac14', 'frac12', 'frac34',
					'there4',
				);
				return (!in_array($i, $allowedentitynames)) ? "&amp;".$i.";" : "&".$i.";";
			}, $safe_text);
			$safe_text = preg_replace_callback('/&amp;#(0*[0-9]{1,7});/', function($matches) {
				if(empty($matches[1])) {
					return '';
				}
				$i = $matches[1];
				if(($i == 0x9 || $i == 0xa || $i == 0xd || ($i >= 0x20 && $i <= 0xd7ff) || ($i >= 0xe000 && $i <= 0xfffd) || ($i >= 0x10000 && $i <= 0x10ffff))) {
					$i = str_pad(ltrim($i,'0'), 3, '0', STR_PAD_LEFT);
					$i = "&#".$i.";";
				} else {
					$i = "&amp;#".$i.";";
				}
				return $i;
			}, $safe_text);
			$safe_text = preg_replace_callback('/&amp;#[Xx](0*[0-9A-Fa-f]{1,6});/', function($matches) {
				if(empty($matches[1])) {
					return '';
				}
				$hexchars = $matches[1];
				$i = hexdec($hexchars);
				return (!($i == 0x9 || $i == 0xa || $i == 0xd || ($i >= 0x20 && $i <= 0xd7ff) || ($i >= 0xe000 && $i <= 0xfffd) || ($i >= 0x10000 && $i <= 0x10ffff))) ? "&amp;#x".$hexchars.";" : '&#x'.ltrim($hexchars,'0').';';
			}, $safe_text);
			$safe_text = @htmlspecialchars($safe_text, $quote_style, $charset);
		}
	}
	return $safe_text;
}
function esc_attr($text) {
	$safe_text = esc_cardinal($text);
	return execEvent('attribute_escape', $safe_text, $text);
}
function esc_html($text) {
	$safe_text = esc_cardinal($text);
	return execEvent('esc_html', $safe_text, $text);
}
function esc_url($url, $protocols = null) {
	$original_url = $url;
	if($url=='') {
		return $url;
	}
	$url = str_replace(' ', '%20', $url);
	$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\[\]\\x80-\\xff]|i', '', $url);
	if($url==='') {
		return $url;
	}
	if(stripos($url, 'mailto:')!==0) {
		$strip = array('%0d', '%0a', '%0D', '%0A');
		$count = 1;
		while($count) {
			$url = str_replace($strip, '', $url, $count);
		}
	}
	$url = str_replace(';//', '://', $url);
	if(strpos($url, ':') === false && !in_array($url[0], array('/', '#', '?')) && !preg_match('/^[a-z0-9-]+?\.php/i', $url)) {
		$url = 'http://' . $url;
	}

	// display
		$url = str_replace('&', '&amp;', $url);
		$url = preg_replace_callback('/&amp;([A-Za-z]{2,8}[0-9]{0,2});/', function($matches) {
			if(empty($matches[1])) {
				return '';
			}
			$i = $matches[1];
			$allowedentitynames = array(
				'nbsp',    'iexcl',  'cent',    'pound',  'curren', 'yen',
				'brvbar',  'sect',   'uml',     'copy',   'ordf',   'laquo',
				'not',     'shy',    'reg',     'macr',   'deg',    'plusmn',
				'acute',   'micro',  'para',    'middot', 'cedil',  'ordm',
				'raquo',   'iquest', 'Agrave',  'Aacute', 'Acirc',  'Atilde',
				'Auml',    'Aring',  'AElig',   'Ccedil', 'Egrave', 'Eacute',
				'Ecirc',   'Euml',   'Igrave',  'Iacute', 'Icirc',  'Iuml',
				'ETH',     'Ntilde', 'Ograve',  'Oacute', 'Ocirc',  'Otilde',
				'Ouml',    'times',  'Oslash',  'Ugrave', 'Uacute', 'Ucirc',
				'Uuml',    'Yacute', 'THORN',   'szlig',  'agrave', 'aacute',
				'acirc',   'atilde', 'auml',    'aring',  'aelig',  'ccedil',
				'egrave',  'eacute', 'ecirc',   'euml',   'igrave', 'iacute',
				'icirc',   'iuml',   'eth',     'ntilde', 'ograve', 'oacute',
				'ocirc',   'otilde', 'ouml',    'divide', 'oslash', 'ugrave',
				'uacute',  'ucirc',  'uuml',    'yacute', 'thorn',  'yuml',
				'quot',    'amp',    'lt',      'gt',     'apos',   'OElig',
				'oelig',   'Scaron', 'scaron',  'Yuml',   'circ',   'tilde',
				'ensp',    'emsp',   'thinsp',  'zwnj',   'zwj',    'lrm',
				'rlm',     'ndash',  'mdash',   'lsquo',  'rsquo',  'sbquo',
				'ldquo',   'rdquo',  'bdquo',   'dagger', 'Dagger', 'permil',
				'lsaquo',  'rsaquo', 'euro',    'fnof',   'Alpha',  'Beta',
				'Gamma',   'Delta',  'Epsilon', 'Zeta',   'Eta',    'Theta',
				'Iota',    'Kappa',  'Lambda',  'Mu',     'Nu',     'Xi',
				'Omicron', 'Pi',     'Rho',     'Sigma',  'Tau',    'Upsilon',
				'Phi',     'Chi',    'Psi',     'Omega',  'alpha',  'beta',
				'gamma',   'delta',  'epsilon', 'zeta',   'eta',    'theta',
				'iota',    'kappa',  'lambda',  'mu',     'nu',     'xi',
				'omicron', 'pi',     'rho',     'sigmaf', 'sigma',  'tau',
				'upsilon', 'phi',    'chi',     'psi',    'omega',  'thetasym',
				'upsih',   'piv',    'bull',    'hellip', 'prime',  'Prime',
				'oline',   'frasl',  'weierp',  'image',  'real',   'trade',
				'alefsym', 'larr',   'uarr',    'rarr',   'darr',   'harr',
				'crarr',   'lArr',   'uArr',    'rArr',   'dArr',   'hArr',
				'forall',  'part',   'exist',   'empty',  'nabla',  'isin',
				'notin',   'ni',     'prod',    'sum',    'minus',  'lowast',
				'radic',   'prop',   'infin',   'ang',    'and',    'or',
				'cap',     'cup',    'int',     'sim',    'cong',   'asymp',
				'ne',      'equiv',  'le',      'ge',     'sub',    'sup',
				'nsub',    'sube',   'supe',    'oplus',  'otimes', 'perp',
				'sdot',    'lceil',  'rceil',   'lfloor', 'rfloor', 'lang',
				'rang',    'loz',    'spades',  'clubs',  'hearts', 'diams',
				'sup1',    'sup2',   'sup3',    'frac14', 'frac12', 'frac34',
				'there4',
			);
			return (!in_array($i, $allowedentitynames)) ? "&amp;".$i.";" : "&".$i.";";
		}, $url);
		$url = preg_replace_callback('/&amp;#(0*[0-9]{1,7});/', function($matches) {
			if(empty($matches[1])) {
				return '';
			}
			$i = $matches[1];
			if(($i == 0x9 || $i == 0xa || $i == 0xd || ($i >= 0x20 && $i <= 0xd7ff) || ($i >= 0xe000 && $i <= 0xfffd) || ($i >= 0x10000 && $i <= 0x10ffff))) {
				$i = str_pad(ltrim($i,'0'), 3, '0', STR_PAD_LEFT);
				$i = "&#".$i.";";
			} else {
				$i = "&amp;#".$i.";";
			}
			return $i;
		}, $url);
		$url = preg_replace_callback('/&amp;#[Xx](0*[0-9A-Fa-f]{1,6});/', function($matches) {
			if(empty($matches[1])) {
				return '';
			}
			$hexchars = $matches[1];
			$i = hexdec($hexchars);
			return (!($i == 0x9 || $i == 0xa || $i == 0xd || ($i >= 0x20 && $i <= 0xd7ff) || ($i >= 0xe000 && $i <= 0xfffd) || ($i >= 0x10000 && $i <= 0x10ffff))) ? "&amp;#x".$hexchars.";" : '&#x'.ltrim($hexchars,'0').';';
		}, $url);
		$url = str_replace('&amp;', '&#038;', $url);
		$url = str_replace("'", '&#039;', $url);

	if(strpos($url, '[')!==false || strpos($url, ']')!==false) {
		$to_unset = array();
		$parsed1 = strval($url);
		if(substr($parsed1, 0, 2)==='//') {
			$to_unset[] = 'scheme';
			$parsed1 = 'placeholder:'.$parsed1;
		} else if(substr($parsed1, 0, 1)==='/') {
			$to_unset[] = 'scheme';
			$to_unset[] = 'host';
			$parsed1 = 'placeholder://placeholder'.$parsed1;
		}
		$parts = @parse_url($parsed1);
		if($parts===false) {
			return $parts;
		}
		foreach($to_unset as $key) {
			unset($parts[$key]);
		}
		$parsed = $parts;
		$front  = '';
		if(isset($parsed['scheme'])) {
			$front .= $parsed['scheme'].'://';
		} else if($url[0]==='/') {
			$front .= '//';
		}
		if(isset($parsed['user'])) {
			$front .= $parsed['user'];
		}
		if(isset($parsed['pass'])) {
			$front .= ':'.$parsed['pass'];
		}
		if(isset($parsed['user']) || isset($parsed['pass'])) {
			$front .= '@';
		}
		if(isset($parsed['host'])) {
			$front .= $parsed['host'];
		}
		if(isset($parsed['port'])) {
			$front .= ':'.$parsed['port'];
		}
		$end_dirty = str_replace($front, '', $url);
		$end_clean = str_replace(array('[', ']'), array('%5B', '%5D'), $end_dirty);
		$url = str_replace($end_dirty, $end_clean, $url);
	}
	if($url[0]==='/') {
		$good_protocol_url = $url;
	} else {
		if(!is_array($protocols)) {
			$protocols = array('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn', 'tel', 'fax', 'xmpp', 'webcal', 'urn');
		}
		$url = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $url);
		$url = preg_replace('/\\\\+0+/', '', $url);
		$iterations = 0;
		do {
			$original_string = $url;
			$url = bad_protocol_url($url, $protocols);
		} while($original_string != $url && ++$iterations < 6);
		if($original_string != $url) {
			$good_protocol_url = '';
		} else {
			$good_protocol_url = $url;
		}
		if(strtolower($good_protocol_url) != strtolower($url)) {
			return '';
		}
	}
	return execEvent('clean_url', $good_protocol_url, $original_url);
}
function esc_js($text) {
	$safe_text = esc_cardinal($text);
    $safe_text = preg_replace('/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes($safe_text));
    $safe_text = str_replace("\r", '', $safe_text);
    $safe_text = str_replace("\n", '\\n', addslashes($safe_text));
    return execEvent('js_escape', $safe_text, $text);
}
function bad_protocol_url($string, $allowed_protocols, $count = 1) {
	$string2 = preg_split('/:|&#0*58;|&#x0*3a;/i', $string, 2);
	if(isset($string2[1]) && !preg_match('%/\?%', $string2[0])) {
		$string = trim($string2[1]);
		$protocol = bad_protocol_url2($string2[0], $allowed_protocols);
		if($protocol=='feed:') {
			if($count>2) {
				return '';
			}
			$string = bad_protocol_url($string, $allowed_protocols, ++$count);
			if(empty($string)) {
				return $string;
			}
		}
		$string = $protocol.$string;
	}
	return $string;
}
function bad_protocol_url2($string, $allowed_protocols) {
	$string2 = preg_replace_callback('/&#([0-9]+);/', function($m) { return chr($m[1]); }, $string);
	$string2 = preg_replace_callback('/&#[Xx]([0-9A-Fa-f]+);/', function($m) { return chr(hexdec($m[1])); }, $string2);
	$string2 = preg_replace('/\s/', '', $string2);
	$string2 = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $string2);
	$string2 = preg_replace('/\\\\+0+/', '', $string2);
	$string2 = strtolower($string2);
	$allowed = false;
	foreach((array) $allowed_protocols as $one_protocol) {
		if(strtolower($one_protocol) == $string2) {
			$allowed = true;
			break;
		}
	}
	if($allowed) {
		return $string2.":";
	} else {
		return '';
	}
}

function location($link, $time = 0, $exit = true, $code = 301){return function_call('location', array($link, $time, $exit, $code));}
function or_location($link, $time = 0, $exit = true, $code = 301) {
	HTTP::Location(templates::view($link), $time, $exit, $code);
}

function is_ssl() {
	if(HTTP::$protocol==="https") {
		return true;
	} else {
		return false;
	}
}

function protocol() {
	return HTTP::$protocol;
}

function callAjax() {
	templates::$gzip = false;
	Debug::activShow(false);
}

function create_pass($pass) { return function_call('create_pass', array($pass)); }
function or_create_pass($pass) {
	return User::create_pass($pass);
}

$tmpHTML = $tmpName = array();
function add_setting_tab($html, $name = "") {
	global $tmpHTML, $tmpName;
	$file = $filename = "";
	$trace = debug_backtrace();
	if(isset($trace[0]) && isset($trace[0]['file'])) {
		$files = pathinfo($trace[0]['file']);
		$file = (isset($files['filename']) ? $files['filename'] : "");
		$filename = (isset($files['filename']) ? $files['filename'] : "");
	}
	if(empty($filename)) {
		$filename = uniqid();
	}
	$filename = str_Replace(".", "-", $filename);
	$tmpName[$filename] = '<li><a href="#'.$filename.'" data-toggle="tab"><span>'.(empty($name) ? "{L_'Настройки'}".(!empty($file) ? "&nbsp;{L_'".$file."'}" : "") : $name).'</span></a></li>';
	$tmpHTML[$filename] = '<div class="tab-pane" id="'.$filename.'">'.$html.'</div>';
	addEventRef("SettingUser_page", function(&$head, &$html) {
		global $tmpHTML, $tmpName;
		foreach($tmpName as $k => $v) {
			$head[$k] = $v;
			$html[$k] = $tmpHTML[$k];
		}
	});
}

$settingUserMainTpl = array();
function add_setting($html, $name = "") {
	global $settingUserMainTpl;
	$settingUserMainTpl[$name] = $html;
	addEventRef("settinguser_main", function(&$t) {
		global $settingUserMainTpl;
		foreach($settingUserMainTpl as $v) {
			$t .= $v;
		}
	});
}

function closetags($html, $singleTagsAdd = array()) { return function_call('closetags', array($html, $singleTagsAdd)); }
function or_closetags($html, $singleTagsAdd = array()) {
	$single_tags = array('meta', 'img', 'br', 'link', 'area', 'input', 'hr', 'col', 'param', 'base');
	if(sizeof($singleTagsAdd)>0) {
		$single_tags = array_merge($single_tags, $singleTagsAdd);
	}
	preg_match_all('~<([a-z0-9]+)(?: .*)?(?<![/|/ ])>~iU', $html, $result);
	$openedtags = $result[1];
	preg_match_all('~</([a-z0-9]+)>~iU', $html, $result);
	$closedtags = $result[1];
	$len_opened = sizeof($openedtags);
	if(sizeof($closedtags) == $len_opened) {
		return $html;
	}
    $openedtags = array_reverse($openedtags);
	for($i=0;$i<$len_opened;$i++) {
		if(!in_array($openedtags[$i], $single_tags)) {
			if(($key = array_search($openedtags[$i], $closedtags)) !== false) {
				unset($closedtags[$key]);
			} else {
				$html .= '</'.$openedtags[$i].'>';
			}
		}
	}
	return $html;
}