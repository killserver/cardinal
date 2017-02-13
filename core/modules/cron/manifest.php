<?php
class Manifest {
	
	function __construct() {
		$list = read_dir(ROOT_PATH."uploads".DS."manifest".DS);
		for($i=0;$i<sizeof($list);$i++) {
			unlink($list[$i]);
		}
	}
	
}
?>