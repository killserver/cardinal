<?php
$links['Recyclebin']["cat"][] = array(
'link' => "#",
'title' => "Корзина данных",
'type' => "cat",
'access' => userlevel::get("recyclebin") && file_exists(PATH_CACHE_USERDATA."trashBin.lock"),
'icon' => 'fa-trash',
);
$links['Recyclebin']["item"][] = array(
'link' => "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=Recyclebin",
'title' => "Корзина данных",
'type' => "item",
'access' => userlevel::get("recyclebin") && file_exists(PATH_CACHE_USERDATA."trashBin.lock"),
'icon' => '',
);
?>