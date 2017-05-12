<?php

class SEOBlock extends modules {
	
	function __construct() {
		$this->seoFinder();
	}
	
	function addSeo($name, $val, $type = "main") {
	global $seoBlock;
		if(!isset($seoBlock[$type]) || !is_array($seoBlock[$type])) {
			$seoBlock[$type] = array();
		}
		$seoBlock[$type][$name] = $val;
	}

	function releaseSeo($meta = array(), $return = false, $clear = true) {
	global $seoBlock;
		$title = (isset($meta['title']) ? $meta['title'] : (isset($seoBlock['ogp']['title']) ? $seoBlock['ogp']['title'] : (isset($seoBlock['og']['title']) ? $seoBlock['og']['title'] : (isset($seoBlock['main']['title']) ? $seoBlock['main']['title'] : "{L_sitename}"))));
		$titleHead = (isset($meta['title']) ? $meta['title'] : (isset($seoBlock['ogp']['title']) ? $seoBlock['ogp']['title'] : (isset($seoBlock['og']['title']) ? $seoBlock['og']['title'] : (isset($seoBlock['main']['title']) ? $seoBlock['main']['title'] : ""))));
		$description = (isset($meta['description']) ? $meta['description'] : (isset($seoBlock['ogp']['description']) ? $seoBlock['ogp']['description'] : (isset($seoBlock['og']['description']) ? $seoBlock['og']['description'] : (isset($seoBlock['main']['description']) ? $seoBlock['main']['description'] : "{L_s_description}"))));
		$imageCheck = (isset($seoBlock['ogp']['image']) && (file_exists(ROOT_PATH.$seoBlock['ogp']['image']) || file_exists($seoBlock['ogp']['image']) || file_exists(config::Select("default_http_host").$seoBlock['ogp']['image']))) || (isset($seoBlock['main']['image_src']) && (file_exists(ROOT_PATH.$seoBlock['main']['image_src']) || file_exists($seoBlock['main']['image_src']) || file_exists(config::Select("default_http_host").$seoBlock['main']['image_src']))) || file_exists(ROOT_PATH."logo.jpg");
		$type = (isset($seoBlock['ogp']['type']) ? $seoBlock['ogp']['type'] : (isset($seoBlock['og']['type']) ? $seoBlock['og']['type'] : "website"));
		$link = (isset($meta['canonicalLink']) ? $meta['canonicalLink'] : (isset($meta['link']) ? $meta['link'] : (isset($seoBlock['og']['link']) ? $seoBlock['og']['link'] : (isset($seoBlock['ogp']['link']) ? $seoBlock['ogp']['link'] : (isset($seoBlock['main']['canonical']) ? $seoBlock['main']['canonical'] : (isset($seoBlock['main']['link']) ? $seoBlock['main']['link'] : (isset($seoBlock['main']['url']) ? $seoBlock['main']['url'] : "")))))));
		$keywords = (isset($meta['keywords']) ? $meta['keywords'] : (isset($seoBlock['ogp']['keywords']) ? $seoBlock['ogp']['keywords'] : (isset($seoBlock['og']['keywords']) ? $seoBlock['og']['keywords'] : (isset($seoBlock['main']['keywords']) ? $seoBlock['main']['keywords'] : ""))));
		$robots = (isset($meta['robots']) ? $meta['robots'] : (isset($seoBlock['ogp']['robots']) ? $seoBlock['ogp']['robots'] : (isset($seoBlock['og']['robots']) ? $seoBlock['og']['robots'] : (isset($seoBlock['main']['robots']) ? $seoBlock['main']['robots'] : "all"))));
		if($imageCheck) {
			if(isset($seoBlock['ogp']['image'])) {
				$cLink = substr($seoBlock['ogp']['image'], 0, 1);
				$imageLink = (strpos($cLink, "/")!==false ? "{C_default_http_host}".$seoBlock['ogp']['image'] : (strpos($seoBlock['ogp']['image'], "http")!==false ? $seoBlock['ogp']['image'] : ""));
			} else if(isset($seoBlock['og']['image'])) {
				$cLink = substr($seoBlock['og']['image'], 0, 1);
				$imageLink = (strpos($cLink, "/")!==false ? "{C_default_http_host}".$seoBlock['og']['image'] : (strpos($seoBlock['og']['image'], "http")!==false ? $seoBlock['og']['image'] : ""));
			} else if(isset($seoBlock['main']['image'])) {
				$cLink = substr($seoBlock['main']['image'], 0, 1);
				$imageLink = (strpos($cLink, "/")!==false ? "{C_default_http_host}".$seoBlock['main']['image'] : (strpos($seoBlock['main']['image'], "http")!==false ? $seoBlock['main']['image'] : ""));
			} else if(isset($seoBlock['main']['image_src'])) {
				$cLink = substr($seoBlock['main']['image_src'], 0, 1);
				$imageLink = (strpos($cLink, "/")!==false ? "{C_default_http_host}".$seoBlock['main']['image_src'] : (strpos($seoBlock['main']['image_src'], "http")!==false ? $seoBlock['main']['image_src'] : ""));
			} else if(file_exists(ROOT_PATH."logo.jpg")) {
				$imageLink = "{C_default_http_host}logo.jpg";
			} else {
				$imageCheck = false;
			}
		}
		$ogpr = array(
			"og:site_name" => "{L_sitename}",
			"og:url" => "{C_default_http_host}".$link,
			"og:title" => $title,
			"og:description" => $description,
			"og:type" => $type,
		);
		if($imageCheck && !empty($imageLink)) {
			$ogpr = array_merge($ogpr, array(
				"og:image" => $imageLink."?".time(),
			));
		}
		$og = array(
			"title" => $title,
			"description" => $description,
		);
		if($imageCheck && !empty($imageLink)) {
			$og = array_merge($og, array(
				"image" => $imageLink."?".time(),
			));
		}
		$meta = array(
			"og" => $og,
			"ogpr" => $ogpr,
			"title" => $title,
			"robots" => $robots,
			"description" => $description,
		);
		if(!empty($keywords)) {
			$meta = array_merge($meta, array(
				"keywords" => $keywords,
			));
		}
		$meta = array_merge($meta, array(
			"link" => array(
				"canonical" => "{C_default_http_host}".$link,
			),
		));
		if($imageCheck && !empty($imageLink)) {
			$meta = array_merge($meta, array(
				"link" => array(
					"image_src" => $imageLink."?".time(),
				),
			));
		}
		if($clear) {
			unset($seoBlock);
		}
		if(!empty($titleHead)) {
			$meta = array("title" => $titleHead, "meta" => $meta);
		} else {
			$meta = array("meta" => $meta);
		}
		if($return) {
			return $meta;
		} else {
			templates::change_head($meta);
		}
	}
	
	function seoFinder() {
		if(!file_exists(ROOT_PATH."uploads".DS."robots.txt") && is_writable(ROOT_PATH."uploads".DS)) {
			$host = (class_exists("cardinal") && method_exists("cardinal", "getServer") ? cardinal::getServer('SERVER_NAME') : $_SERVER['SERVER_NAME']);
			$path = (class_exists("cardinal") && method_exists("cardinal", "getServer") ? cardinal::getServer('PHP_SELF') : $_SERVER['PHP_SELF']);
			$path = str_replace(array("uploads".DS."robots.txt", "uploads".(defined("DS_DB") ? DS_DB : "/")."robots.txt", "index.php"), "", $path);
			if(substr($path, 0, 1)=="/") {
				$path = substr($path, 1);
			}
			$robots = "User-agent: *\n".
					"Disallow: ".$path.(!defined("ADMINCP_DIRECTORY") ? "admincp.php" : ADMINCP_DIRECTORY)."/\n".
					"Disallow: ".$path."cdn-cgi/\n".
					"Disallow: ".$path."core/\n".
					"Disallow: ".$path."changelog/\n".
					"Disallow: ".$path."examples/\n".
					"Disallow: ".$path."js/\n".
					"Disallow: ".$path."skins/\n".
					"Disallow: ".$path."version/\n".
					"Disallow: ".$path."uploads/\n".
					"\n".
					"\n".
					"Host: ".$host."\n".
					"Sitemap: http://".$host.$path."sitemap.xml";
			file_put_contents(ROOT_PATH."uploads".DS."robots.txt", $robots);
		}
		$dir = ROOT_PATH."core".DS."cache".DS."system".DS;
		$file = $dir."seoBlock.lock";
		$db = $this->init_db();
		if(!is_writable($dir) || !$db->connected()) {
			return false;
		}
		if(!file_exists($file)) {
			db::query("CREATE TABLE `seoBlock` ( `sId` int(11) not null auto_increment, `sPage` varchar(255) not null, `sLang` varchar(255) not null, `sTitle` varchar(255) not null, `sMetaDescr` varchar(255) not null, `sMetaKeywords` varchar(255) not null, `sMetaRobots` varchar(255) not null, `sRedirect` varchar(255) not null, `sImage` varchar(255) not null, primary key `id`(`sId`), fulltext `lang`(`sLang`), fulltext `page`(`sPage`), fulltext `title`(`sTitle`), fulltext `metaDescr`(`sMetaDescr`), fulltext `metaKeywords`(`sMetaKeywords`) ) ENGINE=MyISAM;");
			file_put_contents($file, "");
		}
		$uri = (class_exists("cardinal") && method_exists("cardinal", "getServer") ? cardinal::getServer('REQUEST_URI') : $_SERVER['REQUEST_URI']);
		if(preg_match("#^(/?)(([a-zA-Z]{2})/|)(.*?)$#", $uri, $match)) {
			if(isset($match[4]) && strpos($match[4], "?")!==false) {
				$page = explode("?", $match[4]);
				if(strlen($page[0])>0) {
					if(isset($page[1])) {
						unset($page[1]);
					}
					$match[4] = implode("", $page);
				}
			}
			$tmp = $this->init_templates();
			$db->doquery("SELECT * FROM `seoBlock` WHERE `sLang` LIKE \"".$match[3]."\" AND `sPage` LIKE \"/".(isset($match[4]) && !empty($match[4]) ? $match[4] : "")."\"", true);
			if($db->num_rows()==0) {
				return false;
			}
			$row = $db->fetch_assoc();
			if(!empty($row['sRedirect'])) {
				header("Location: ".$row['sRedirect'], true, 301);
				die();
			}
			if(!empty($row['sTitle'])) {
				$this->addSeo('title', $row['sTitle']);
			}
			if(!empty($row['sMetaDescr'])) {
				$this->addSeo('description', $row['sMetaDescr']);
			}
			if(!empty($row['sMetaKeywords'])) {
				$this->addSeo('keywords', $row['sMetaKeywords']);
			}
			if(!empty($row['sMetaRobots'])) {
				$this->addSeo('robots', $row['sMetaRobots']);
			}
			if(!empty($row['sImage'])) {
				$this->addSeo('image', $row['sImage']);
			}
			$this->addSeo('link', substr($uri, 1));
			$tmp->change_head($this->releaseSeo(array(), true));
		}
	}
	
}

?>