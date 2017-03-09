<?php
if(!defined("IS_CORE")) {
	echo "ERROR";
	die();
}

Route::Set("install_done", "install/done")->defaults(array(
          'page' => 'install',
        'method' => 'change',
       'is_file' => true,
          'line' => "4",
          'file' => ROOT_PATH."install.php",
));
Route::Set("install_first", "install(/step<line>)")->defaults(array(
          'page' => 'install',
        'method' => 'change',
       'is_file' => true,
          'line' => "0",
          'file' => ROOT_PATH."install.php",
));

Route::Set("news", "post/<view>.html")->defaults(array(
          'page' => 'news',
          'view' => "",
));

Route::Set("post", "post/<action>(/<sub_link>)", array("action" => "(add|edit|delete)"))->defaults(array(
          'page' => 'post',
        'action' => "",
      "sub_link" => "",
));

Route::Set("related", "post/<view>/related")->defaults(array(
          "page" => "news",
          "view" => "",
    "sub_action" => "related",
));

Route::Set("user_view", "user/<action>")->defaults(array(
	      'page' => 'user',
	    'action' => "",
));

Route::Set("messages", "messages(/<select>)")->defaults(array(
          'page' => 'messages',
        'select' => '-1',
));

Route::Set("css_skin", "skin.css")->defaults(array(
          'page' => 'skin',
));

Route::Set("goToBanner", "banner/<banner>.html")->defaults(array(
        'banner' => 'banner',
));

Route::Set("cat", "cat/<alt_name>(/<page>).html")->defaults(array(
	      'page' => 'search',
	      'type' => 'cat',
      'alt_name' => "",
));

Route::Set("search", "search(/<searchWord>)(/<page>).html")->defaults(array(
	      'page' => 'search',
	      'type' => 'search',
	  'alt_name' => "",
    'searchWord' => "",
));

Route::Set("manifest", "manifest.cache")->defaults(array(
          'page' => 'manifest',
));

Route::Set("main", "page/<pages>.html")->defaults(array(
          'page' => 'main',
));

function routeDefault($uri, $page) {
	if($uri && preg_match("#^(?:page/(?P<page>[^/.,;?\n]++).html)?$#uD", $uri, $all)) {
		$ret = array(
			'page'  => 'main',
			'pages' => $all['page'],
		);
		return $ret;
	}
	if($uri) {
		Route::Delete("default");
		$par = Route::Load($page);
		if(Route::Search("notFound") && (!is_array($par) || !isset($par['params']) || !is_array($par['params']) || !Route::checkEmpty($par['params']))) {
			return Route::Get('notFound')->GetDefaults();
		} else {
			return $par['params'];
		}
	} else {
		return array(
			'page' => $page,
		);
	}
}
Route::Set("default", "routeDefault");