<?php

class Router {

	public static function all($uri_callback = "", $name = "", $regex  = array(), $callback = "") {
		return Route::method("GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD", $uri_callback, $name, $regex, $callback);
	}

	public static function get($uri_callback = "", $name = "", $regex  = array(), $callback = "") {
		return Route::method("GET", $uri_callback, $name, $regex, $callback);
	}

	public static function post($uri_callback = "", $name = "", $regex  = array(), $callback = "") {
		return Route::method("POST", $uri_callback, $name, $regex, $callback);
	}

	public static function put($uri_callback = "", $name = "", $regex  = array(), $callback = "") {
		return Route::method("PUT", $uri_callback, $name, $regex, $callback);
	}

	public static function delete($uri_callback = "", $name = "", $regex  = array(), $callback = "") {
		return Route::method("DELETE", $uri_callback, $name, $regex, $callback);
	}

	public static function options($uri_callback = "", $name = "", $regex  = array(), $callback = "") {
		return Route::method("OPTIONS", $uri_callback, $name, $regex, $callback);
	}

	public static function patch($uri_callback = "", $name = "", $regex  = array(), $callback = "") {
		return Route::method("PATCH", $uri_callback, $name, $regex, $callback);
	}

	public static function head($uri_callback = "", $name = "", $regex  = array(), $callback = "") {
		return Route::method("HEAD", $uri_callback, $name, $regex, $callback);
	}

}