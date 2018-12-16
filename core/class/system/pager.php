<?php
/*
 *
 * @version 1.25.6-rc4
 * @copyright 2014-2015 KilleR for Cardinal Engine
 *
 * Version Engine: 1.25.6-rc4
 * Version File: 1
 *
 * 1.1
 * add paginator in core
 * 1.2
 * fix error in name pages
 * 1.3
 * add support prev/next marker
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class pager {
	
	private $pages = array();
	private $limits = array(0,0);
	private $links = array("next" => "", "prev" => "");
	
	final private function Route($array) {
		$route = Route::get($array[0]);
		if(!is_bool($route)) {
			$params = array();
			$array = explode(";", $array[1]);
			for($i=0;$i<sizeof($array);$i++) {
				$exp = explode("=", $array[$i]);
				if(isset($exp[1])) {
					$val = $exp[1];
				} else {
					$val = "";
				}
				if(isset($exp[0])) {
					$params[$exp[0]] = $val;
				} else {
					$params[] = $val;
				}
			}
			unset($val, $exp, $array);
			return $route->uri($params);
		} else {
			return false;
		}
	}

	final public function __construct($rpp, $count_all, $on_page, $url_page, $p_page = "/page/", $max_view = 10, $route = false) {
		if(!is_numeric($rpp) || !is_numeric($count_all) || !is_numeric($on_page) || empty($url_page) || $on_page == 0) {
			return false;
		}
		$this->limits = array(ceil($rpp*$on_page), $on_page);
		$c_link = 1;
		if($count_all>$on_page) {
			$rpp = $rpp*$on_page;
			$enpages_count = @ceil($count_all/$on_page);
			$rpp = ($rpp/$on_page) + 1;
			if($enpages_count<=$max_view) {
				for($j=1;$j<=$enpages_count;$j++) {
					if($j!=$rpp) {
						if($j==($rpp+1)) {
							$this->pages[$c_link]['prev'] = 0;
							$this->pages[$c_link]['next'] = 1;
							if(empty($this->links['next'])) {
								if($route) {
									$this->links['next'] = $this->Route(array($url_page, "".$p_page."=".$j.""));
								} else {
									$this->links['next'] = $url_page.$p_page.$j;
								}
							}
						} else if($j==($rpp-1)) {
							$this->pages[$c_link]['prev'] = 1;
							$this->pages[$c_link]['next'] = 0;
							if(empty($this->links['prev'])) {
								if($route) {
									$this->links['prev'] = ($j==1 ? $this->Route(array($url_page, "")) : $this->Route(array($url_page, "".$p_page."=".$j."")));
								} else {
									$this->links['prev'] = ($j==1 ? $url_page : $url_page.$p_page.$j);
								}
							}
						} else {
							$this->pages[$c_link]['prev'] = 0;
							$this->pages[$c_link]['next'] = 0;
						}
						if($j == 1) {
							$this->pages[$c_link]['is_link'] = 1;
							$this->pages[$c_link]['now'] = 0;
							if($route) {
								$this->pages[$c_link]['link'] = $this->Route(array($url_page, ""));
							} else {
								$this->pages[$c_link]['link'] = $url_page;
							}
							$this->pages[$c_link]['title'] = "".round($j);
							if(empty($this->links['start']) && isset($this->pages[$c_link]['link'])) {
								$this->links['start'] = $this->pages[$c_link]['link'];
							}
							$c_link++;
						} else {
							$this->pages[$c_link]['is_link'] = 1;
							$this->pages[$c_link]['now'] = 0;
							if($route) {
								$this->pages[$c_link]['link'] = $this->Route(array($url_page, "".$p_page."=".$j.""));
							} else {
								$this->pages[$c_link]['link'] = $url_page.$p_page.$j;
							}
							$this->pages[$c_link]['title'] = "".round($j);
							if(empty($this->links['start']) && isset($this->pages[$c_link]['link'])) {
								$this->links['start'] = $this->pages[$c_link]['link'];
							}
							$c_link++;
						}
					} else {
						$this->pages[$c_link]['is_link'] = 0;
						$this->pages[$c_link]['now'] = 1;
						$this->pages[$c_link]['title'] = "".round($j);
						if(empty($this->links['start']) && isset($this->pages[$c_link]['link'])) {
							$this->links['start'] = $this->pages[$c_link]['link'];
						}
						$c_link++;
					}
				}
			} else {
				$start = 1;
				$end = $max_view;
				$nav_prefix = "...";
				if($rpp>0) {
					if(($rpp/2)>=$max_view/2) {
						$start = $rpp - $max_view;
						if($start<=0) {
							$start = 1;
						}
						$end = $start + ($max_view*2);
						if($end>=$enpages_count) {
							$start = $enpages_count - 9;
							$end = $enpages_count - 1;
							$nav_prefix = "";
						} else {
							$nav_prefix = "...";
						}
					}
				}
				if($start>=2) {
					$this->pages[$c_link]['is_link'] = 1;
					$this->pages[$c_link]['now'] = 0;
					if($route) {
						$this->pages[$c_link]['link'] = $this->Route(array($url_page, ""));
					} else {
						$this->pages[$c_link]['link'] = $url_page;
					}
					$this->pages[$c_link]['title'] = "1";
					$c_link++;
					$this->pages[$c_link]['is_link'] = 0;
					$this->pages[$c_link]['now'] = 0;
					$this->pages[$c_link]['title'] = "...";
					$c_link++;
				}
				for($j=$start;$j<=$end;$j++) {
					if($j!=$rpp) {
						if($j==($rpp+1)) {
							$this->pages[$c_link]['prev'] = 0;
							$this->pages[$c_link]['next'] = 1;
							if(empty($this->links['next'])) {
								if($route) {
									$this->links['next'] = $this->Route(array($url_page, "".$p_page."=".$j.""));
								} else {
									$this->links['next'] = $url_page.$p_page.$j;
								}
							}
						} else if($j==($rpp-1)) {
							$this->pages[$c_link]['prev'] = 1;
							$this->pages[$c_link]['next'] = 0;
							if(empty($this->links['prev'])) {
								if($route) {
									$this->links['prev'] = ($j==1 ? $this->Route(array($url_page, "")) : $this->Route(array($url_page, "".$p_page."=".$j."")));
								} else {
									$this->links['prev'] = ($j==1 ? $url_page : $url_page.$p_page.$j);
								}
							}
						} else {
							$this->pages[$c_link]['prev'] = 0;
							$this->pages[$c_link]['next'] = 0;
						}
						if($j==1) {
							$this->pages[$c_link]['is_link'] = 1;
							$this->pages[$c_link]['now'] = 0;
							if($route) {
								$this->pages[$c_link]['link'] = $this->Route(array($url_page, ""));
							} else {
								$this->pages[$c_link]['link'] = $url_page;
							}
							$this->pages[$c_link]['title'] = "".round($j);
							$c_link++;
						} else {
							$this->pages[$c_link]['is_link'] = 1;
							$this->pages[$c_link]['now'] = 0;
							if($route) {
								$this->pages[$c_link]['link'] = $this->Route(array($url_page, "".$p_page."=".$j.""));
							} else {
								$this->pages[$c_link]['link'] = $url_page.$p_page.$j;
							}
							$this->pages[$c_link]['title'] = "".round($j);
							$c_link++;
						}
					} else {
						$this->pages[$c_link]['is_link'] = 0;
						$this->pages[$c_link]['now'] = 1;
						$this->pages[$c_link]['title'] = "".round($j);
						$c_link++;
					}
				}
				if($rpp!=$enpages_count) {
					$this->pages[$c_link]['is_link'] = 0;
					$this->pages[$c_link]['now'] = 0;
					$this->pages[$c_link]['title'] = "...";
					$c_link++;
					$this->pages[$c_link]['is_link'] = 1;
					if($route) {
						$this->pages[$c_link]['link'] = $this->Route(array($url_page, "".$p_page."=".$enpages_count.""));
					} else {
						$this->pages[$c_link]['link'] = $url_page.$p_page.$enpages_count;
					}
					$this->pages[$c_link]['now'] = 0;
					$this->pages[$c_link]['title'] = "".round($enpages_count);
					$c_link++;
				} else {
					$this->pages[$c_link]['is_link'] = 0;
					$this->pages[$c_link]['now'] = 1;
					$this->pages[$c_link]['title'] = "".round($enpages_count);
					$c_link++;
				}
			}
		}
	}
	
	final public function limit() {
		return "LIMIT ".$this->limits[0].",".$this->limits[1];
	}
	
	final public function getLimit() {
		return $this->limits;
	}
	
	final public function prevLink() {
		return $this->links['prev'];
	}
	
	final public function nextLink() {
		return $this->links['next'];
	}
	
	final public function get() {
		return $this->pages;
	}
}

?>