<?php

class Shop extends Core {
	
	function Shop() {
		templates::assign_var("set_domain", cardinal::SaveCardinal(config::Select("default_http_hostname"), true));
		$this->Prints("Shop");
	}
	
}

?>