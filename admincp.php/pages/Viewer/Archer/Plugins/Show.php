<?php

class Archer_Show {
	
	public function Archer_Show() {
		KernelArcher::callback("Show", array(&$this, "Shows"));
	}
	
	public function Shows($table, $page, $model) {
		$getExclude = KernelArcher::excludeField("get", "Show");
		$list = $model->getArray();
		$first = $model->getFirst();
		$isId = $list[$first];
		unset($list[$first]);
		if(isset(KernelArcher::$editModel["Show"])) {
			for($i=0;$i<sizeof(KernelArcher::$editModel["Show"]);$i++) {
				$list = call_user_func_array(KernelArcher::$editModel["Show"][$i], array($list, $getExclude));
			}
		}
		$data = current($list);
		$body = "";
		foreach($list as $k => $v) {
			if(in_array($k, $getExclude)) {
				continue;
			}
			$l = $model->getAttribute($k, 'type');
			$default = "";
			if(is_array($v) && isset($v['default'])) {
				$default = $v['default'];
				unset($v['default']);
				$v = array_values($v);
			}
			$body .= KernelArcher::Viewing($l, $k, $v, $default, true);
		}
		templates::assign_var("ArcherShow", $body);
		return array("table" => $table, "objTemplate" => $page, "list" => $model);
	}
}

?>