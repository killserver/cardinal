<?php

class Phpinfo extends Core {
	
	public function Phpinfo() {
		ob_start();
		phpinfo();
		$pinfo = ob_get_contents();
		ob_end_clean();
		$this->Prints($pinfo, true);
	}
	
}
ReadPlugins(dirname(__FILE__)."/Plugins/", "Phpinfo");

?>