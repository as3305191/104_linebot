<?php
class Fish_tab_lottery_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('fish_tab_lottery');

		$this -> alias_map = array(

		);
	}

	public function find_current($tab_id) {
			$this -> db -> where("tab_id", $tab_id);
			$this -> db -> where("is_current", 1);
			$list = $this -> db -> get($this -> table_name) -> result();
			if(count($list) > 0) {
				return $list[0];
			}
			return NULL;
	}
	public function find_current_with_lottery_info($tab_id) {
			$this -> db -> select("_m.*");
			$this -> db -> select("l.lottery_name, l.price, l.ratio, l.total_num, l.image_id");
			$this -> db -> from("{$this -> table_name} as _m");
			$this -> db -> join("lottery l", "l.id = _m.lottery_id", "left");
			$this -> db -> where("tab_id", $tab_id);
			$this -> db -> where("is_current", 1);
			$list = $this -> db -> get() -> result();
			if(count($list) > 0) {
				return $list[0];
			}

			return NULL;
	}

	public function do_open($fish_tab_lottery_id, $lottery_tx_sn) {
		$this -> load -> model('Lottery_tx_dao', 'ltx_dao');
		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Lottery_dao', 'l_dao');
		$this -> load -> model('Award_center_dao', 'ac_dao');

		$error_msg = "";

		$ltx = $this -> ltx_dao -> find_by_fish_tab_lottery_id_and_tx_sn($fish_tab_lottery_id, $lottery_tx_sn);
		if(!empty($ltx)) {
			$item = $this -> find_by_id($fish_tab_lottery_id);
			$this -> update(array(
				'is_open' => 1
	 		), $fish_tab_lottery_id);

			// choose one
			$this -> update(array(
				'open_lottery_tx_id' => $ltx -> id,
				'open_lottery_tx_sn' => $ltx -> sn
	 		), $fish_tab_lottery_id);

			// add open message
			$user = $this -> users_dao -> find_by_id($ltx -> user_id);
			$lottery = $this -> l_dao -> find_by_id($ltx -> lottery_id);
			$p = array();
			$p['to'] = $user -> line_sub;
			$p['messages'][] = array(
				"type" => "text",
				"text" => "恭喜您中頭獎 期數: {$item->lottery_no} 號碼:{$ltx->sn}"
			);
			$ret = call_line_api("POST", "https://api.line.me/v2/bot/message/push", json_encode($p), CHANNEL_ACCESS_TOKEN);

			$tx = array();
			$tx['user_id'] = $user->id;
			$tx['date'] = date("Y/m/d");
			$tx['cate'] = "獎品";
			$tx['detail'] = "抽獎獲得{$lottery->lottery_name}";
			$tx['status'] = 0;
			$tx['tx_type'] = 'lottery_tx';
			$tx['tx_id'] = $ltx->id;

			$this -> ac_dao -> insert($tx);
		} else {
			$error_msg = "查無此彩卷";
		}

		return $error_msg;
	}

	public function find_next($tab_id) {
			$this -> load -> model('Lottery_dao', 'l_dao');
			$this -> load -> model('Lottery_tx_dao', 'ltx_dao');

			$current = $this -> find_current($tab_id);
			$next_item = NULL;
			$next_item_id = 0;
			if(!empty($current)) {
				$this -> db -> where("id > {$current->id}");
				$this -> db -> where("tab_id", $tab_id);
				$this -> db -> order_by("id", 'asc');
				$this -> db -> limit(1);
				$list = $this -> find_all();

				if(count($list) > 0) {
					$next_item = $list[0];
					$this -> update(array(
						'is_current' => 1
					), $next_item -> id);
					$next_item_id = $next_item -> id;
				} else {
					// create basic by basic item
					$basic_list = $this -> l_dao -> find_all_by('is_basic', 1);
					$next_lottery = NULL;
					if(count($basic_list) > 0) {
						$next_lottery = $basic_list[0];
					} else {
						// $next_lottery = $this -> l_dao -> random_one();
					}

					$last_id = $this -> insert(array(
						'lottery_id' => $next_lottery -> id,
						'tab_id' => $tab_id,
						'is_current' => 1
 					));
					$this -> update(array(
						'lottery_no' => "AUTO{$last_id}"
					), $last_id);

					$next_item = $this -> find_by_id($last_id);
					$next_item_id = $next_item -> id;
				}

				// unset current
				$this -> update(array(
					'is_current' => 0,
					'is_end' => 1,
				), $current -> id);
			} else {
				// find first
				$next_item = $this -> find_first($tab_id);
				if(!empty($next_item)) {
					$this -> update(array(
						'is_current' => 1
					), $next_item -> id);
					$next_item_id = $next_item -> id;
				} else {
					// create basic by basic item
					$basic_list = $this -> l_dao -> find_all_by('is_basic', 1);
					$next_lottery = NULL;
					if(count($basic) > 0) {
						$next_lottery = $basic_list[0];
					} else {
						$next_lottery = $this -> l_dao -> random_one();
					}

					$last_id = $this -> insert(array(
						'lottery_id' => $next_lottery -> id,
						'tab_id' => $tab_id,
						'is_current' => 1
					));
					$this -> update(array(
						'lottery_no' => "AUTO{$last_id}"
					), $last_id);
					$next_item = $this -> find_by_id($last_id);
					$next_item_id = $next_item -> id;
				}
			}

			// user basic lottery
			// if(empty($next_item)) {
			// 	$basic_lottery = $this -> l_dao -> find_basic_item();
			// 	$next_item_id = $this -> insert(array(
			// 		'tab_id' => $tab_id,
			// 		'lottery_id' => $basic_lottery -> id,
			// 		'is_current' => 1,
			// 	));
			// }

			$next_item = $this -> find_by_id($next_item_id);
			return $next_item;
	}

	public function find_first($tab_id) {
		$this -> db -> where('tab_id', $tab_id);
		$this -> db -> where('is_end', 0); // no ending
		$this -> db -> order_by("id", "asc");
		$this -> db -> limit(1);
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	public function fish_lottery_list() {
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select("_m.lottery_no as lottery_no");
		$this -> db -> select("f_t.tab_name as tab_name,f_t.pool_100 as pool_100,f_t.pool_100_king as pool_100_king,f_t.pool_2000 as pool_2000,f_t.pool_2000_king as pool_2000_king,f_t.pool_20000 as pool_20000,f_t.pool_20000_king as pool_20000_king,f_t.pool_200000 as pool_200000,f_t.pool_200000_king as pool_200000_king,f_t.pool_1000000 as pool_1000000,f_t.pool_1000000_king as pool_1000000_king");
		$this -> db -> select("l.lottery_name as lottery_name");

		$this -> db -> join("lottery l", "l.id = _m.lottery_id", "left");
		$this -> db -> join("fish_tab f_t", "f_t.id = _m.tab_id", "left");

		$list = $this -> db -> get() -> result();
		return $list;
	}

	function query_ajax($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('l.lottery_name');
		$this -> db -> select('l.total_num');
		$this -> db -> select('l.sn as lottery_sn');
		$this -> db -> select('u.nick_name as user_nick_name');

		// join
		$this -> ajax_from_join();

		// search always
		$this -> search_always($data);

		// search
		$this -> ajax_column_setup($columns, $search, $this -> alias_map);

		// order
		$this -> ajax_order_setup($order, $columns, $this -> alias_map);
		$this -> db -> order_by('id', 'desc');

		// limit
		$this -> db -> limit($limit, $start);

		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}

	function search_always($data) {
		if(isset($data['user_id']) && $data['user_id'] > -1) {
			$this -> db -> where('_m.user_id', $data['user_id']);
		}

		if(isset($data['tab_id']) && $data['tab_id'] > -1) {
			$this -> db -> where('_m.tab_id', $data['tab_id']);
		}

		if(isset($data['id']) && $data['id'] > -1) {
			$this -> db -> where('_m.id', $data['id']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("lottery l", "l.id = _m.lottery_id", "left");
		$this -> db -> join("lottery_tx ltx", "ltx.id = _m.open_lottery_tx_id", "left");
		$this -> db -> join("users u", "u.id = ltx.user_id", "left");
	}

	function find_by_parameter(){
		$this -> db -> from("$this->table_name as _m");

		// select
		$this -> db -> select('_m.id, _m.lottery_no');
		$this -> db -> order_by('id','desc');

		$limit = 30;
		$start = 0;

		$this -> db -> limit($limit, $start);

		$query = $this -> db -> get();
		$list = $query -> result();

		return $list;
	}

	function find_by_parameter_with_user_info($user_id){
		$this -> db -> from("$this->table_name as _m");
		// select
		$this -> db -> select('_m.id, _m.lottery_no');

		$this -> db -> where("( id in (
			select distinct fish_tab_lottery_id from lottery_tx where user_id = $user_id
			)  )");

		$this -> db -> order_by('id','desc');

		$limit = 30;
		$start = 0;

		$this -> db -> limit($limit, $start);

		$query = $this -> db -> get();
		$list = $query -> result();

		return $list;
	}

	function find_by_user($id){
		$this -> db -> from("$this->table_name as _m");

		// select
		$this -> db -> select('_m.id, _m.lottery_no');
		$this -> db -> join("lottery_tx lt", "lt.fish_tab_lottery_id = _m.id", "left");
		$this -> db -> where('lt.user_id', $id);
		$this -> db -> group_by('lt.fish_tab_lottery_id');

		$limit = 30;
		$start = 0;

		$this -> db -> limit($limit, $start);

		$query = $this -> db -> get();
		$list = $query -> result();

		return $list;
	}

}
?>
