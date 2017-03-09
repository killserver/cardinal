<?php

class Main extends Core {

	public function __construct() {
		$this->Prints("index");
	}

}
ReadPlugins(dirname(__FILE__)."/Plugins/", "Main");

?>