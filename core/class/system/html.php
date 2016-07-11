<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class html {

	protected $html = "";
	private $tag = "";
	private $type = 1;
	private $sc = "\"";
	
	function __construct() {
		$this->html = "";
	}

	function open($tag, $type=1, $sc="\"") {
		$this->tag = $tag;
		$this->type = $type;
		$this->sc = $sc;
		$this->html = "<".$this->tag;
	return $this;
	}

	function nam($name) {
		$this->html .= " name=".$this->sc.$name.$this->sc;
	return $this;
	}

	function width($width) {
		$this->html .= " width=".$this->sc.$width.$this->sc;
	return $this;
	}

	function height($height) {
		$this->html .= " height=".$this->sc.$height.$this->sc;
	return $this;
	}

	function border($border) {
		$this->html .= " border=".$this->sc.$border.$this->sc;
	return $this;
	}

	function id($id) {
		$this->html .= " id=".$this->sc.$id.$this->sc;
	return $this;
	}

	function colspan($colspan) {
		$this->html .= " colspan=".$this->sc.$colspan.$this->sc;
	return $this;
	}

	function clas($class) {
		$this->html .= " class=".$this->sc.$class.$this->sc;
	return $this;
	}

	function color($color) {
		$this->html .= " color=".$this->sc.$color.$this->sc;
	return $this;
	}

	function style($style) {
		$this->html .= " style=".$this->sc.$style.$this->sc;
	return $this;
	}

	function rel($rel) {
		$this->html .= " rel=".$this->sc.$rel.$this->sc;
	return $this;
	}

	function type($type) {
		$this->html .= " type=".$this->sc.$type.$this->sc;
	return $this;
	}

	function href($href) {
		$this->html .= " href=".$this->sc.$href.$this->sc;
	return $this;
	}

	function title($title) {
		$this->html .= " title=".$this->sc.$title.$this->sc;
	return $this;
	}

	function alt($alt) {
		$this->html .= " alt=".$this->sc.$alt.$this->sc;
	return $this;
	}

	function label($label) {
		$this->html .= " label=".$this->sc.$label.$this->sc;
	return $this;
	}

	function selected($selected = "selected") {
		$this->html .= " selected=".$this->sc.$selected.$this->sc;
	return $this;
	}

	function onclick($onclick) {
		$this->html .= " onclick=".$this->sc.$onclick.$this->sc;
	return $this;
	}

	function val($val) {
		$this->html .= " value=".$this->sc.$val.$this->sc;
	return $this;
	}

	function placeholder($placeholder) {
		$this->html .= " placeholder=".$this->sc.$placeholder.$this->sc;
	return $this;
	}

	function fors($for) {
		$this->html .= " for=".$this->sc.$for.$this->sc;
	return $this;
	}
	
	function checked($checked) {
		$this->html .= " checked=".$this->sc.$checked.$this->sc;
	return $this;
	}
	
	function method($method) {
		$this->html .= " method=".$this->sc.$method.$this->sc;
	return $this;
	}
	
	function attr($attr, $val) {
		$this->html .= " ".$attr."=".$this->sc.$val.$this->sc;
	return $this;
	}

	function src($src) {
		$this->html .= " src=".$this->sc.$src.$this->sc;
	return $this;
	}

	function frameborder($bor) {
		$this->html .= " frameborder=".$this->sc.$bor.$this->sc;
	return $this;
	}

	function allowscriptaccess($allow) {
		$this->html .= " allowscriptaccess=".$this->sc.$allow.$this->sc;
	return $this;
	}

	function allowfullscreen($allow) {
		$this->html .= " allowfullscreen=".$this->sc.$allow.$this->sc;
	return $this;
	}

	function cont($htmls) {
		$html = "";
		if(is_array($htmls)) {
			foreach($htmls as $ht) {
				$html .= $ht;
			}
		} else {
			$html = $htmls;
		}
		if($this->type==1) {
			$this->html .= ">".$html;
		} elseif($this->type==2) {
			$this->html .= " content=".$this->sc.$html.$this->sc;
		}
	return $this;
	}

	function eof() {
		$this->html .= ">";
	return $this;
	}

	function close($tag=null) {
		if(!isset($this->html)) {
			$this->html = "";
		}
		if(!empty($tag)) {
			$this->html .= "</".$tag.">";
			return $this;
		}
		if($this->type==1) {
			$this->html .= "</".$this->tag.">";
		} elseif($this->type==2) {
			$this->html .= " />";
		}
	return $this;
	}

	function eol() {
		$this->html .= "\n";
	return $this;
	}

	function get_html() {
		$html = $this->html;
		unset($this->html);
	return $html;
	}

	function __destruct() {
		$this->html = "";
	}

}

?>