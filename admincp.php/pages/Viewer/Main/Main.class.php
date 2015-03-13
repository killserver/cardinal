<?php

class Main extends Core {

	public function Main() {
		$this->Prints("index");
	}

}
ReadPlugins(dirname(__FILE__)."/Plugins/", "Main");

?>