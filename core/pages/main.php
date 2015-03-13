<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
exit();
}

class page {

    function __construct() {
		$tmp = templates::complited_assing_vars("index");
		templates::complited($tmp);
		templates::display();
	}

}

?>