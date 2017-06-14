<?php
/*
 *
 * @version 5.3
 * @copyright 2014-2016 KilleR for Cardinal Engine
 * @author KilleR
 *
 * Version Engine: 5.3
 * Version File: 8
 *
 * 6.1
 * rebuild logic for comment
 * 6.2
 * rebuild logic for comment v.2
 * 7.1
 * add support info on level user and rebuild logic parent comment
 * 8.1
 * rebuild full code. first step
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
exit();
}

/**
 * Class comment
 */
class comment {

	/**
	 * @var array Array all parameters for comments
     */
	private static $param = array("u_id" => "", "type" => "news", "user_row" => array(), "levels" => 0);
	/**
	 * @var int Count comments in table
     */
	private static $counts = 0;
	
	private static $checkedSpam = true;
	private static $switcherCrazy = true;
	private static $switcherAddComm = true;
	private static $switcherAvatar = "/images/no_avatar.png";
	private static $switcherLink = false;

	/**
	 * comment constructor.
	 * @access public
	 * @param $u_id Unique id all comments in table
	 * @param string $type Type comments
	 * @param array $user_row Array sub parameters user
	 * @param int $levels Level viewing comments
	 * @return bool Return done setting default config comment
     */
	final public function __construct($u_id, $type = "news", $user_row = array(), $levels = 0) {
		if(!defined("WITHOUT_DB") && !userlevel::get("view_comments")) {
			return false;
		}
		self::$param['u_id'] = $u_id;
		self::$param['type'] = $type;
		self::$param['user_row'] = $user_row;
		self::$param['levels'] = $levels;
		if(sizeof($_POST)>3) {
			self::add();
		}
		return true;
	}

	/**
	 * Reset default parameters
	 * @access public
	 * @param $u_id Unique id all comments in table
	 * @param string $type Type comments
	 * @param array $user_row Array sub parameters user
	 * @param int $levels Level viewing comments
	 * @return bool Return done setting default config comment
     */
	final public static function set_default($u_id, $type = "news", $user_row = array(), $levels = 0) {
		self::$param['u_id'] = $u_id;
		self::$param['type'] = $type;
		self::$param['user_row'] = $user_row;
		self::$param['levels'] = $levels;
		if(sizeof($_POST)>3) {
			self::add();
		}
		return true;
	}
	
	final public static function checkSpam($check = true) {
		self::$checkedSpam = $check;
	}
	
	final public static function switchCrazy($switch = true) {
		self::$switcherCrazy = $switch;
	}
	
	final public static function switchAdded($switch = true) {
		self::$switcherAddComm = $switch;
	}
	
	final public static function switchAvatar($avatar = "") {
		self::$switcherAvatar = $avatar;
	}
	
	final public static function switchLink($link = "") {
		self::$switcherLink = $link;
	}

	/**
	 * Add comments in DB
     */
	final private static function add($check = true) {
		if($check && !userlevel::get("add_comments")) {
			templates::error("{L_error_level_full}", "{L_error_level}");
			return false;
		}
		$postName = Arr::get($_POST, 'username', false);
		$postMail = Arr::get($_POST, 'email', false);
		$postParent = Arr::get($_POST, 'parent', 0);
		$postComment = Arr::get($_POST, 'comment', false);
		if(!$postName || !$postMail || !$postComment) {
			return false;
		}
		$userId = modules::get_user('id');
		$userName = modules::get_user('username');
		$userAltName = modules::get_user('alt_name');
		$userMail = modules::get_user('email');
		$userLevel = modules::get_user('level');
		if($userId && ($userLevel >= LEVEL_USER && $userLevel != LEVEL_GUEST)) {
			$is_guest = false;
			$guest = "false";
			$mod = "yes";
			$username = $userName;
			$mail = $userMail;
		} else {
			$is_guest = true;
			$guest = "true";
			$mod = "no";
			$username = Saves::SaveOld($postName);
			$mail = Saves::SaveOld($postMail);
		}
		$ip = HTTP::getip();
		$client = getenv('HTTP_USER_AGENT');
		$parent_id = Saves::SaveOld($postParent);
		$comment = Saves::SaveOld($postComment);
		if(self::$checkedSpam) {
			$spam = new StopSpam();
			if(!$spam->is_spammer(array('email' => $mail, 'ip' => $ip, 'username' => $username))) {
				$spam = "no";
			} else {
				$spam = "yes";
			}
		} else {
			$spam = "no";
		}
		db::doquery("INSERT INTO `".PREFIX_DB."comments` SET `added` = \"".$userAltName."\", `email` = \"".$mail."\", `type` = \"".self::$param['type']."\", `ip` = \"".$ip."\", `user_agent` = \"".$client."\", `comment` = \"".$comment."\", `u_id` = \"".self::$param['u_id']."\", `parent_id` = \"".$parent_id."\", `time` = UNIX_TIMESTAMP(), `guest` = \"".$guest."\", `mod` = \"".$mod."\", `spam` = \"".$spam."\"");
		unset($_POST);
		location(getenv("REQUEST_URI"));
	}

	/*delete(
		DELETE FROM ".PREFIX_DB."comments WHERE parent_id = $id
		DELETE FROM ".PREFIX_DB."comments WHERE id = $id
	}*/

	/**
	 * Preparing template for including anyway
	 * @access public
	 * @param int $parent Parent comment for added
	 * @return string Completed template for including anyway
     */
	final public static function addcomments($parent = 0) {
		$userId = modules::get_user('id');
		$userLevel = modules::get_user('level');
		if($userId && $userLevel != LEVEL_GUEST && userlevel::get("add_comments")) {
			$add_com = templates::load_templates("addcomments", "cp1251");
		} else {
			return "";
		}
		if($userId && $userLevel != LEVEL_GUEST) {
			$add_com = preg_replace('#\[not-logged\](.+?)\[/not-logged\]#is', '', $add_com);
		} else {
			$add_com = preg_replace('#\[not-logged\]#is',  '', $add_com);
			$add_com = preg_replace('#\[/not-logged\]#is', '', $add_com);
		}
		$add_com = str_replace(array("{u_id}", "{parent}"), array(self::$param['u_id'], $parent), $add_com);
		return $add_com;
	}

	/**
	 * Preparing template for including anyway without checking level for viewing
	 * @access public
	 * @param int $parent Parent comment for added
	 * @return string Completed template for including anyway
     */
	final public static function ViewAdd($parent = 0) {
		$userId = modules::get_user('id');
		$userLevel = modules::get_user('level');
		$add_com = templates::load_templates("addcomments", "cp1251");
		if($userId && $userLevel != LEVEL_GUEST) {
			$add_com = preg_replace('#\[not-logged\](.+?)\[/not-logged\]#is', '', $add_com);
		} else {
			$add_com = preg_replace('#\[not-logged\]#is',  '', $add_com);
			$add_com = preg_replace('#\[/not-logged\]#is', '', $add_com);
		}
		$add_com = str_replace(array("{u_id}", "{parent}"), array(self::$param['u_id'], $parent), $add_com);
		return $add_com;
	}

	/**
	 * Get all comments
	 * @access public
	 * @param bool|true $add Add added comment in end
	 * @param string $tpl Template for prepare viewing comments
	 * @return string Done viewing list comments
     */
	final public static function get($add = true, $tpl = "comments") {
		$userId = modules::get_user('id');
		$userLevel = modules::get_user('level');
		$comments = "";
		$file_com = templates::load_templates($tpl, "cp1251");
		// выводим комменты
		$msg = array();
		if(self::$param['type'] == "user") {
			$type = "user";
			$u_id = self::$param['user_row']['alt_name'];
		} else {
			$type = self::$param['type'];
			$u_id = self::$param['u_id'];
		}
		if(!cache::Exists("comment_".$type."-".$u_id)) {
			$result = db::doquery("SELECT `id`, `u_id`, (SELECT `last_activ` FROM `".PREFIX_DB."users` WHERE `username` = `comments`.`added`) as `last_activ`, (SELECT `username` FROM `".PREFIX_DB."users` WHERE `username` = `comments`.`added`) as `alt_name`, (SELECT `avatar` FROM `".PREFIX_DB."users` WHERE `username` = `comments`.`added`) as `avatar`, `added`, `ip`, `time`, `comment`, `parent_id`, `level` FROM `".PREFIX_DB."comments` WHERE `type` LIKE \"".$type."\" AND `u_id` LIKE \"".$u_id."\" AND `mod` LIKE \"yes\"", true);
			while($row = db::fetch_assoc($result)) {
				$msg[] = $row;
			}
			cache::Set("comment_".$type."-".$u_id, $msg);
		} else {
			$msg = cache::Get("comment_".$type."-".$u_id);
		}

		$parent = 0;
		self::$counts = $count = sizeof($msg);
		if($count>0) {
			if(self::$switcherCrazy) {
				$msg = self::crazysort($msg);
			}
			self::$counts = $count = sizeof($msg);
			for($i=0;$i<$count;$i++) {
				if(!empty($msg[$i]['avatar'])) {
					$avatar = $msg[$i]['avatar'];
				} else {
					$avatar = self::$switcherAvatar;
				}
				$margin = (isset($msg[$i]['level']) ? $msg[$i]['level']*20 : 0);
				if(strpos($msg[$i]['parent_id'], ".") !== false) {
					$aParent = explode(".", $msg[$i]['parent_id']);
					$parentId = end($aParent);
				} else {
					$parentId = $msg[$i]['parent_id'];
				}
				$replace = array(
					"{foto}" =>        $avatar,
					"{author_link}" => (!self::$switcherLink ? user_link($msg[$i]['alt_name'], $msg[$i]['added']) : $msg[$i]['alt_name']),
					"{author_name}" => $msg[$i]['added'],
					"{date}" =>        date(S_TIME_VIEW, $msg[$i]['time']),
					"{comment}" =>     bbcodes::colorit($msg[$i]['comment']),
					"{id}" =>          $msg[$i]['id'],
					"{margin}" =>      $margin,
					"{par}" =>         $parentId,
				);
				$comm = str_replace(array_keys($replace), array_values($replace), $file_com);
				if($userId && ($userLevel > LEVEL_USER && $userLevel != LEVEL_GUEST) && self::$param['levels'] > $msg[$i]['level']) {
					$comm = preg_replace('#\[requoted\]#is',  '', $comm);
					$comm = preg_replace('#\[/requoted\]#is', '', $comm);
				} else {
					$comm = preg_replace('#\[requoted\](.+?)\[/requoted\]#is', '', $comm);
				}
				if($msg[$i]['last_activ'] > time()-300) {
					$comm = preg_replace('#\[online\]#is', '', $comm);
					$comm = preg_replace('#\[/online\]#is', '', $comm);
					$comm = preg_replace('#\[offline\](.+?)\[/offline\]#is', '', $comm);
				} else {
					$comm = preg_replace('#\[offline\]#is', '', $comm);
					$comm = preg_replace('#\[/offline\]#is', '', $comm);
					$comm = preg_replace('#\[online\](.+?)\[/online\]#is', '', $comm);
				}
				$comments .= $comm;
			}  
		}
		if($add || self::$switcherAddComm) {
			$comments = self::addcomments().$comments;
		}
		$comments = str_replace(array("{u_id}", "{parent}"), array(self::$param['u_id'], $parent), $comments);
	return $comments;
	}

	/**
	 * Count comments on targeted criterion in DB
	 * @access public
	 * @return int Count comments on targeted criterion in DB
     */
	final public static function getCount() {
		return self::$counts;
	}

	/**
	 * Compiled comments in list parent-children
	 * @access private
	 * @param array $comments Array comments
	 * @param int $parentComment Parent comment
	 * @param int $level Start level for sort
	 * @param string $count Count comments
	 * @return array|bool Result array parent-children
     */
	final private static function crazysort(&$comments, $parentComment = 0, $level = 0, $count = "") {
		if(is_array($comments) && sizeof($comments)>0) {
			$return = array();
			if(empty($count)){
				$c = sizeof($comments);
			} else {
				$c = $count;
			}
			for($i=0;$i<$c;$i++) {
				if(!isset($comments[$i])) {
					continue;
				}
				$comment = $comments[$i];
				if(strpos($comment['parent_id'], ".") !== false) {
					$aParent = explode(".", $comment['parent_id']);
					$parentId = end($aParent);
				} else {
					$parentId = $comment['parent_id'];
				}
				if($parentId == $parentComment) {
					$comment['level'] = $level;
					$commentId = $comment['id'];
					$return[] = $comment;
					unset($comments[$i]);
					while($nextReturn = self::crazysort($comments, $commentId, $level+1, $c)){
						$return = array_merge($return, $nextReturn);
					}
				}
			}
			return $return;
		} else {
			return false;
		}
	}

}

?>