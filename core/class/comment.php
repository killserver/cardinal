<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
exit();
}

final class comment {

	private static $param = array("name_id" => null, "type" => "movie", "user_row" => array());

	public function comment($name_id, $type="movie", $user_row=array()) {
		if(!userlevel::get("view_comments")) {
			return;
		}
		self::$param['name_id'] = $name_id;
		self::$param['type'] = $type;
		self::$param['user_row'] = $user_row;
		if(sizeof($_POST)>3) {
			self::add();
		}
	//return $this;
	}

	public static function set_default($name_id, $type="movie", $user_row=array()) {
		self::$param['name_id'] = $name_id;
		self::$param['type'] = $type;
		self::$param['user_row'] = $user_row;
		if(sizeof($_POST)>3) {
			self::add();
		}
	}

	private static function add() {
	global $user;
		if(!userlevel::get("add_comments")) {
			templates::error("{L_error_level_full}", "{L_error_level}");
		}
		if(isset($user['id']) && ($user['level']>LEVEL_USER && $user['level']!=LEVEL_GUEST)) {
			$is_guest = false;
		} else {
			$is_guest = true;
		}
		if(!$is_guest) {
			$username = $user['username'];
		} else if(isset($_POST['username'])) {
			$username = saves($_POST['username']);
		} else {
			return;
		}
		if(!$is_guest) {
			$mail = $user['email'];
		} else {
			$mail = saves($_POST['email']);
		}
		$comment = saves($_POST['comment']);
		$ip = HTTP::getip();
		$client = getenv('HTTP_USER_AGENT');
		//$content_id = intval($_POST['content']);
		$parent_id = saves($_POST['parent']);
		if($is_guest) {
			$guest = "true";
		} else {
			$guest = "false";
		}
		$spam = new StopSpam();
		if(!$spam->is_spammer(array('email' => $mail, 'ip' => $ip, 'username' => $username))) {
			db::doquery("INSERT INTO comments SET `added`=\"".$user['username']."\", `email`=\"".$mail."\", `type`=\"".self::$param['type']."\", `ip`=\"".$ip."\", `user_agent`=\"".$client."\", `comment`=\"".$comment."\", name_id=\"".self::$param['name_id']."\", `parent_id`=\"".$parent_id."\", `time` = UNIX_TIMESTAMP(), `guest`=\"".$guest."\"");
		}
		unset($_POST);
	}

	/*delete(
		DELETE FROM comments WHERE parent_id = $id
		DELETE FROM comments WHERE id = $id
	}*/

	public static function get() {
	global $user;
		if(isset($user['id']) && $user['level']!=LEVEL_GUEST && userlevel::get("add_comments")) {
			$add_com = templates::load_templates("addcomments", "cp1251");
		} else {
			$add_com = "";
		}
		$comments = "";
		$file_com = templates::load_templates("comments", "cp1251");
		// выводим комменты
		$msg = array();
		if(self::$param['type']=="user") {
			$where = "type=\"user\" AND name_id = \"".self::$param['user_row']['alt_name']."\"";
		} else {
			$where = "type=\"movie\" AND name_id = \"".self::$param['name_id']."\"";
		}
		$result = db::doquery("SELECT id, name_id, (SELECT last_activ FROM users WHERE username=added) as last_activ, (SELECT username FROM users WHERE username=added) as alt_name, (SELECT avatar FROM users WHERE username=added) as avatar, added, ip, time, comment, parent_id, level FROM comments WHERE ".$where, true);//." ORDER BY `comments`.`id` ASC"

		while($row = db::fetch_assoc($result)){
			$msg[] = $row;
		}


		$count = sizeof($msg);
		$parent = 0;
		if($count) {
			$comments = "<div class='comments-all'><span style='float:left'>Всего комментариев: ".$count."</span>&nbsp;<span class='add-comment'>Написать комментарий</span></div>";
			$msg = self::crazysort($msg);
			$count = sizeof($msg);
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
				$comm = str_replace(array("{foto}", "{author_link}", "{author_name}", "{date}", "{comment}", "{id}", "{margin}", "{par}"), array($avatar, user_link($msg[$i]['alt_name'], $msg[$i]['added']), $msg[$i]['added'], date("d-m-Y H:j:s", $msg[$i]['time']), $msg[$i]['comment'], $msg[$i]['id'], $margin, $parentId), $file_com);
				if($msg[$i]['last_activ']>time()-300) {
					$comm=preg_replace('#\[online\]#is', '', $comm);
					$comm=preg_replace('#\[/online\]#is', '', $comm);
					$comm=preg_replace('#\[offline\](.+?)\[/offline\]#is', '', $comm);
				} else {
					$comm=preg_replace('#\[offline\]#is', '', $comm);
					$comm=preg_replace('#\[/offline\]#is', '', $comm);
					$comm=preg_replace('#\[online\](.+?)\[/online\]#is', '', $comm);
				}
				$comments .= $comm;
			}  
		}
		if(isset($user['id']) && $user['level']!=LEVEL_GUEST) {
			$add_com=preg_replace('#\[not-logged\](.+?)\[/not-logged\]#is', '', $add_com);
		} else {
			$add_com=preg_replace('#\[not-logged\]#is', '', $add_com);
			$add_com=preg_replace('#\[/not-logged\]#is', '', $add_com);
		}
		$add_com = str_replace(array("{name_id}", "{parent}"), array(self::$param['name_id'], $parent), $add_com);
	return $add_com.$comments;
	}

	private static function crazysort(&$comments, $parentComment = 0, $level = 0, $count = null) {
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