<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/** 
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 **/


return array(
    // custom source example
    'general' => array(
		$min_documentRoot . '/js/jquery.js',
		$min_documentRoot . '/js/jquery-migrate-3.0.0.min.js',
    ),
);