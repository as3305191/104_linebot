<?php
class Slot_sun_data_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('slot_sun_data');

		$this -> alias_map = array(

		);
	}

	function random_one($p = array()) {
		$this -> db -> order_by('id', 'RANDOM');
		$this -> db -> limit(1);

		if(!empty($p['is_sp'])) {
			$val = $p['is_sp'];
			if($val == 'Y') {
				$this -> db -> where('is_bonus_game', 1);
			}
			if($val == 'N') {
				$this -> db -> where('is_bonus_game', 0);
			}
		}

		if(!empty($p['is_win'])) {
			$val = $p['is_win'];
			$max_times = $p['max_times'];
			$min_times = $p['min_times'];
			if($val == 'Y' && !empty($max_times)) { // 要贏需要設定最大的倍數（根據彩池）
				$this -> db -> where("(total_times >= {$min_times} and total_times <= {$max_times})");
			} else {
				$this -> db -> where('total_times <= 0');
			}
		}

		if(!empty($p['bonus_times'])) {
			$this -> db -> where('bonus_times', $p['bonus_times']);
		}

		$list = $this -> find_all();
		// echo $this -> db -> last_query();
		if(count($list) > 0) {
			return $list[0];
		} else {
			// ˋ找不到就找一個輸的局填補
			$p['is_win'] = "N";
			$item = $this -> random_loose_one($p);
			return $item;
		}
		return NULL;
	}

	function random_loose_one($p = array()) {
		$this -> db -> order_by('id', 'RANDOM');
		$this -> db -> limit(1);

		if(!empty($p['is_sp'])) {
			$val = $p['is_sp'];
			if($val == 'Y') {
				$this -> db -> where('is_bonus_game', 1);
			}
			if($val == 'N') {
				$this -> db -> where('is_bonus_game', 0);
			}
		}

		if(!empty($p['is_win'])) {
			$val = $p['is_win'];
			$max_times = $p['max_times'];
			$min_times = $p['min_times'];
			if($val == 'Y' && !empty($max_times)) { // 要贏需要設定最大的倍數（根據彩池）
				$this -> db -> where("(total_times >= {$min_times} and total_times <= {$max_times})");
			} else {
				$this -> db -> where('total_times <= 0');
			}
		}
		$list = $this -> find_all();
		return $list[0];
	}
}
?>
