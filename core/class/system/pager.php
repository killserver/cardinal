<?php
/*
*
* Version Engine: 1.25.5b1
* Version File: 1
*
* 1.1
* add paginator in core
*
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

final class pager {
	
	private $pages = array();
	private $limits = array();

	function __construct($rpp, $count_all, $on_page, $url_page, $p_page = "/page/", $max_view = 10) {
		$this->limits = array(ceil($rpp*$on_page), $on_page);
		$c_link = 1;
		if($count_all>$on_page) {
			$rpp = $rpp*$on_page;
			$enpages_count = @ceil($count_all/$on_page);
			$rpp = ($rpp/$on_page) + 1;
			if($enpages_count<=$max_view) {
				for($j=1;$j<=$enpages_count;$j++) {
					if($j!=$rpp) {
							if($j == 1) {
								$this->pages[$c_link]['is_link'] = 1;
								$this->pages[$c_link]['now'] = 0;
								$this->pages[$c_link]['link'] = $url_page;
								$this->pages[$c_link]['title'] = "".$j;
								$c_link++;
							} else {
								$this->pages[$c_link]['is_link'] = 1;
								$this->pages[$c_link]['now'] = 0;
								$this->pages[$c_link]['link'] = $url_page.$p_page.$j;
								$this->pages[$c_link]['title'] = "".$j;
								$c_link++;
							}
					} else {
						$this->pages[$c_link]['is_link'] = 0;
						$this->pages[$c_link]['now'] = 1;
						$this->pages[$c_link]['title'] = "".$j;
						$c_link++;
					}
				}
			} else {
				$start = 1;
				$end = $max_view;
				$nav_prefix = "...";
				if($rpp>0) {
					if($rpp>6) {
						$start = $rpp - 4;
						$end = $start + 8;
						if($end>=$enpages_count) {
							$start = $enpages_count - 9;
							$end = $enpages_count - 1;
							$nav_prefix = "";
						} else
							$nav_prefix = "...";
					}
				}
				if($start>=2) {
					$this->pages[$c_link]['is_link'] = 1;
					$this->pages[$c_link]['now'] = 0;
					$this->pages[$c_link]['link'] = $url_page;
					$this->pages[$c_link]['title'] = "1";
					$c_link++;
					$this->pages[$c_link]['is_link'] = 0;
					$this->pages[$c_link]['now'] = 0;
					$this->pages[$c_link]['title'] = "...";
					$c_link++;
				}
				for($j=$start;$j<=$end;$j++) {
					if($j!=$rpp) {
							if($j==1) {
								$this->pages[$c_link]['is_link'] = 1;
								$this->pages[$c_link]['now'] = 0;
								$this->pages[$c_link]['page'] = $url_page;
								$this->pages[$c_link]['title'] = $j;
								$c_link++;
							} else {
								$this->pages[$c_link]['is_link'] = 1;
								$this->pages[$c_link]['now'] = 0;
								$this->pages[$c_link]['page'] = $url_page.$p_page.$j;
								$this->pages[$c_link]['title'] = $j;
								$c_link++;
							}
					} else {
						$this->pages[$c_link]['is_link'] = 0;
						$this->pages[$c_link]['now'] = 1;
						$this->pages[$c_link]['title'] = "".$j;
						$c_link++;
					}
				}
				if($rpp!=$enpages_count) {
					$this->pages[$c_link]['is_link'] = 0;
					$this->pages[$c_link]['now'] = 0;
					$this->pages[$c_link]['title'] = "".$nav_prefix;
					$c_link++;
					$this->pages[$c_link]['is_link'] = 1;
					$this->pages[$c_link]['link'] = $url_page.$p_page.$enpages_count;
					$this->pages[$c_link]['now'] = 0;
					$this->pages[$c_link]['title'] = $enpages_count;
					$c_link++;
				} else {
					$this->pages[$c_link]['is_link'] = 0;
					$this->pages[$c_link]['title'] = "".$enpages_count;
					$c_link++;
				}
			}
		}
	}
	
	function limit() {
		return "LIMIT ".$this->limits[0].",".$this->limits[1];
	}
	
	function get() {
		return $this->pages;
	}
}

?>