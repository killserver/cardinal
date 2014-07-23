<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/** 
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 **/

define("IS_CORE", true);
include_once($min_documentRoot."/core/media/config.php");

return array(
    // custom source example
    'general' => array(
	$min_documentRoot . '/js/jquery.js',
	$min_documentRoot . '/js/jquery-migrate.js',
	$min_documentRoot . '/js/jquery.form.js',
	$min_documentRoot . '/js/jquery-ui.js',
	$min_documentRoot . '/js/jquery.tagsinput.js',
	$min_documentRoot . '/js/select.min.js',
	$min_documentRoot . "/skins/".$config['skins']['skins']."/js/jquery.jmpopups-0.5.1.js",
	$min_documentRoot . '/js/init.js',
	$min_documentRoot . '/js/ajax_core.js',
	$min_documentRoot . '/core/flash/tagcloud/swfobject.js',
     	//'http://code.jquery.com/jquery-2.1.0.min.js',
    ),

    /*'admin' => array(
     	$min_documentRoot . '/engine/skins/javascripts/application.js', 
    ),*/
);