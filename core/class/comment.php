<?php
if(!defined("IS_CORE")) {
echo "403 ERROR";
exit();
}

final class comment {

	private $param = array("name_id" => null, "type" => "movie", "user_row" => array());

	function __construct($name_id, $type="movie", $user_row=array()) {
		if(!userlevel::get("view_comments")) {
			return;
		}
		$this->param['name_id'] = $name_id;
		$this->param['type'] = $type;
		$this->param['user_row'] = $user_row;
		if(sizeof($_POST)>3) {
			$this->add();
		}
	//return $this;
	}

	private function add() {
	global $user, $db;
		if(!userlevel::get("add_comments")) {
			$templates->error("{L_error_level_full}", "{L_error_level}");
		}
		if(isset($user['id']) && ($user['level']>LEVEL_USER && $user['level']!=LEVEL_GUEST)) {
			$comment = saves($_POST['comment']);
			$ip = getenv('REMOTE_ADDR');
			$client = getenv('HTTP_USER_AGENT');
			$content_id = intval($_POST['content']);
			$parent_id = saves($_POST['parent']);
			$db->doquery("INSERT INTO comments SET `added`=\"".$user['username']."\", `type`=\"".$this->param['type']."\", `ip`=\"".$ip."\", `user_agent`=\"".$client."\", `comment`=\"".$comment."\", name_id=\"".$this->param['name_id']."\", `parent_id`=\"".$parent_id."\", `time` = UNIX_TIMESTAMP()");
		}
	}

	/*delete(
		DELETE FROM comments WHERE parent_id = $id
		DELETE FROM comments WHERE id = $id
	}*/

	function get() {
	global $db, $templates, $config, $user;
		if(isset($user['id']) && ($user['level']>LEVEL_USER && $user['level']!=LEVEL_GUEST) && userlevel::get("add_comments")) {
			$add_com = file_get_contents(ROOT_PATH."skins/".$templates->get_skins()."/addcomments.tpl");
		} else {
			$add_com = "";
		}
		$comments = "";
		$file_com = file_get_contents(ROOT_PATH."skins/".$templates->get_skins()."/comments.tpl");
		// выводим комменты
		$msg = array();
		if($this->param['type']=="user") {
			$where = "type=\"user\" AND name_id = \"".$this->param['user_row']['alt_name']."\"";
		} else {
			$where = "type=\"movie\" AND name_id = \"".$this->param['name_id']."\"";
		}
		$result = $db->doquery("SELECT id, name_id, (SELECT username FROM users WHERE username=added) as alt_name, (SELECT avatar FROM users WHERE username=added) as avatar, added, ip, time, comment, parent_id, level FROM comments WHERE ".$where, true);//." ORDER BY `comments`.`id` ASC"

		while($row = $db->fetch_assoc($result)){
			$msg[] = $row;
		}


		$count = sizeof($msg);
		$parent = 0;
		if($count) {
			$comments = "<div class='comments-all'><span style='float:left'>Всего комментариев: {$count}</span>&nbsp;<span class='add-comment'>Написать комментарий</span></div>";
			$msg = $this->crazysort($msg);
			$count = sizeof($msg);
			for($i=0;$i<$count;$i++) {
				if(!empty($msg[$i]['avatar'])) {
					$avatar = $msg[$i]['avatar'];
				} else {
					$avatar = "/images/no_avatar.png";
				}
				$margin = $msg[$i]['level']*20;
				//$comments .= "<div id='msg{$msg[$i]['id']}' style='margin-left: {$margin}px'><div class='comment-title'><span style='float:left'><b>{$msg[$i]['name']}</b> <small>({$date})</small></span><span class='comment-ans' id={$msg[$i]['id']}>ответить</span></div><div class='comment-message'>{$msg[$i]['comment']}</div></div>";
				if(strpos($msg[$i]['parent_id'], ".") !== false) {
					$aParent = explode(".", $msg[$i]['parent_id']);
					$parentId = end($aParent);
				} else {
					$parentId = $msg[$i]['parent_id'];
				}
				$comments .= str_replace(array("{foto}", "{author}", "{date}", "{comment}", "{id}", "{margin}", "{par}"), array($avatar, user_link($msg[$i]['alt_name'], $msg[$i]['added'], "href"), date("d-m-Y H:j:s", $msg[$i]['time']), $msg[$i]['comment'], $msg[$i]['id'], $margin, $parentId), $file_com);
			}  
		}
		$add_com = str_replace(array("{name_id}", "{parent}"), array($this->param['name_id'], $parent), $add_com);
	return iconv("cp1251", $config['charset'], $add_com.$comments);
	}

	function crazysort(&$comments, $parentComment = 0, $level = 0, $count = null) {
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
					while($nextReturn = $this->crazysort($comments, $commentId, $level+1, $c)){
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