<?php
/*
 *
 * @version 3.0
 * @copyright 2014-2016 KilleR for Cardinal Engine
 * @author KilleR
 *
 * Version Engine: 3.0
 * Version File: 6
 *
 * 6.1
 * rebuild logic for comment
 * 6.2
 * rebuild logic for comment v.2
 * 7.1
 * add support info on level user and rebuild logic parent comment
 *
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
exit();
}

final class comment {

	private static $param = array("u_id" => null, "type" => "news", "user_row" => array(), "levels" => 0);
	private static $counts = 0;

	public function comment($u_id, $type="news", $user_row = array(), $levels = 0) {
		if(!userlevel::get("view_comments")) {
			return;
		}
		self::$param['u_id'] = $u_id;
		self::$param['type'] = $type;
		self::$param['user_row'] = $user_row;
		self::$param['levels'] = $levels;
		if(sizeof($_POST)>3) {
			self::add();
		}
	}

	public static function set_default($u_id, $type="news", $user_row=array(), $levels = 0) {
		self::$param['u_id'] = $u_id;
		self::$param['type'] = $type;
		self::$param['user_row'] = $user_row;
		self::$param['levels'] = $levels;
		if(sizeof($_POST)>3) {
			self::add();
		}
	}

	private static function add() {
		if(!userlevel::get("add_comments")) {
			templates::error("{L_error_level_full}", "{L_error_level}");
		}
		if(modules::get_user('id') && (modules::get_user('level')>=LEVEL_USER && modules::get_user('level')!=LEVEL_GUEST)) {
			$is_guest = false;
		} else {
			$is_guest = true;
		}
		if(!$is_guest) {
			$username = modules::get_user('username');
		} else if(isset($_POST['username'])) {
			$username = saves($_POST['username']);
		} else {
			return;
		}
		if(!$is_guest) {
			$mail = modules::get_user('email');
		} else if(isset($_POST['email'])) {
			$mail = saves($_POST['email']);
		}
		$comment = saves($_POST['comment']);
		$ip = HTTP::getip();
		$client = getenv('HTTP_USER_AGENT');
		if(isset($_POST['parent'])) {
			$parent_id = saves($_POST['parent']);
		} else {
			$parent_id = 0;
		}
		if($is_guest) {
			$guest = "true";
		} else {
			$guest = "false";
		}
		if(modules::get_user('id') && (modules::get_user('level')>LEVEL_USER && modules::get_user('level')!=LEVEL_GUEST)) {
			$mod = "yes";
		} else if(modules::get_user('level')==LEVEL_GUEST) {
			$mod = "yes";
		} else {
			$mod = "no";
		}
		$spam = new StopSpam();
		if(!$spam->is_spammer(array('email' => $mail, 'ip' => $ip, 'username' => $username))) {
			$spam = "no";
		} else {
			$spam = "yes";
		}
		db::doquery("INSERT INTO comments SET `added`=\"".modules::get_user('alt_name')."\", `email`=\"".$mail."\", `type`=\"".self::$param['type']."\", `ip`=\"".$ip."\", `user_agent`=\"".$client."\", `comment`=\"".$comment."\", `u_id`=\"".self::$param['u_id']."\", `parent_id`=\"".$parent_id."\", `time` = UNIX_TIMESTAMP(), `guest`=\"".$guest."\", `mod` = \"".$mod."\", `spam` = \"".$spam."\"");
		unset($_POST);
		location(getenv("REQUEST_URI"));
	}

	/*delete(
		DELETE FROM comments WHERE parent_id = $id
		DELETE FROM comments WHERE id = $id
	}*/
	
	public static function addcomments($parent = 0) {
		if(modules::get_user('id') && modules::get_user('level')!=LEVEL_GUEST && userlevel::get("add_comments")) {
			$add_com = templates::load_templates("addcomments", "cp1251");
		} else {
			$add_com = "";
		}
		if(modules::get_user('id') && modules::get_user('level')!=LEVEL_GUEST) {
			$add_com=preg_replace('#\[not-logged\](.+?)\[/not-logged\]#is', '', $add_com);
		} else {
			$add_com=preg_replace('#\[not-logged\]#is', '', $add_com);
			$add_com=preg_replace('#\[/not-logged\]#is', '', $add_com);
		}
		$add_com = str_replace(array("{u_id}", "{parent}"), array(self::$param['u_id'], $parent), $add_com);
		return $add_com;
	}
	
	public static function ViewAdd($parent = 0) {
		$add_com = templates::load_templates("addcomments", "cp1251");
		if(modules::get_user('id') && modules::get_user('level')!=LEVEL_GUEST) {
			$add_com=preg_replace('#\[not-logged\](.+?)\[/not-logged\]#is', '', $add_com);
		} else {
			$add_com=preg_replace('#\[not-logged\]#is', '', $add_com);
			$add_com=preg_replace('#\[/not-logged\]#is', '', $add_com);
		}
		$add_com = str_replace(array("{u_id}", "{parent}"), array(self::$param['u_id'], $parent), $add_com);
		return $add_com;
	}

	public static function get($add = true, $tpl = "comments") {
		$comments = "";
		$file_com = templates::load_templates($tpl, "cp1251");
		// выводим комменты
		$msg = array();
		if(self::$param['type']=="user") {
			$type = "user";
			$u_id = self::$param['user_row']['alt_name'];
		} else {
			$type = self::$param['type'];
			$u_id = self::$param['u_id'];
		}
		if(!cache::Exists("comment_".$type."-".$u_id)) {
			$result = db::doquery("SELECT `id`, `u_id`, (SELECT `last_activ` FROM `users` WHERE `username` = `comments`.`added`) as `last_activ`, (SELECT `username` FROM `users` WHERE `username` = `comments`.`added`) as `alt_name`, (SELECT `avatar` FROM `users` WHERE `username` = `comments`.`added`) as `avatar`, `added`, `ip`, `time`, `comment`, `parent_id`, `level` FROM `comments` WHERE `type` = \"".$type."\" AND `u_id` = \"".$u_id."\" AND `mod` = \"yes\"", true);
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
			$msg = self::crazysort($msg);
			self::$counts = $count = sizeof($msg);
			for($i=0;$i<$count;$i++) {
				if(!empty($msg[$i]['avatar'])) {
					$avatar = $msg[$i]['avatar'];
				} else {
					$avatar = "/images/no_avatar.png";
				}
				$margin = $msg[$i]['level']*20;
				if(strpos($msg[$i]['parent_id'], ".") !== false) {
					$aParent = explode(".", $msg[$i]['parent_id']);
					$parentId = end($aParent);
				} else {
					$parentId = $msg[$i]['parent_id'];
				}
				$comm = str_replace(array("{foto}", "{author_link}", "{author_name}", "{date}", "{comment}", "{id}", "{margin}", "{par}"), array($avatar, user_link($msg[$i]['alt_name'], $msg[$i]['added']), $msg[$i]['added'], date(S_TIME_VIEW, $msg[$i]['time']), bbcodes::colorit($msg[$i]['comment']), $msg[$i]['id'], $margin, $parentId), $file_com);
				if(modules::get_user('id') && (modules::get_user('level')>LEVEL_USER && modules::get_user('level')!=LEVEL_GUEST) && self::$param['levels']>$msg[$i]['level']) {
					$comm = preg_replace('#\[requoted\]#is', '', $comm);
					$comm = preg_replace('#\[/requoted\]#is', '', $comm);
				} else {
					$comm = preg_replace('#\[requoted\](.+?)\[/requoted\]#is', '', $comm);
				}
				if($msg[$i]['last_activ']>time()-300) {
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
		if($add) {
			$comments = self::addcomments().$comments;
		}
		$comments = str_replace(array("{u_id}", "{parent}"), array(self::$param['u_id'], $parent), $comments);
	return $comments;
	}
	
	public static function getCount() {
		return self::$counts;
	}

	private static function crazysort(&$comments, $parentComment = 0, $level = 0, $count = "") {
		if(is_array($comments) && sizeof($comments)) {
			$return = array();
			if(empty($count)){
				$c = sizeof($comments);
			} else {
				$c = $count;
			}
			for($i=0;$i<$c;$i++){
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
		}
	return false;
	}

}

?>