<?php

class Phpinfo extends Core {
	
	public function __construct() {
		ob_start();
		phpinfo();
		$pinfo = ob_get_contents();
		ob_end_clean();
		$pinfo = preg_replace(array(
			"#body \{.+?\}#",
			"#a\:.+?\{.+?\}#"
		), "", $pinfo);
		$this->Prints($pinfo, true, true);
	}
	
}
ReadPlugins(dirname(__FILE__)."/Plugins/", "Phpinfo");

?>