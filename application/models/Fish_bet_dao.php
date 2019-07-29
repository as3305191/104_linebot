<?php
class Fish_bet_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('fish_bet');

		$this -> alias_map = array(

		);
	}

		function sum_total_amt($tab_id, $hall_id) {
			$sql = "select sum(pool_amt) as samt from {$this->table_name} where tab_id = {$tab_id} and hall_id = {$hall_id}";
			$list = $this -> db -> query($sql) -> result();
			if(count($list) > 0) {
				return !empty($list[0] -> samt) ? $list[0] -> samt : 0;
			}
			return 0;
		}

		function sum_total_amt_jp($tab_id, $hall_id) {
			$sql = "select sum(pool_amt_jp) as samt from {$this->table_name} where tab_id = {$tab_id} and hall_id = {$hall_id}";
			$list = $this -> db -> query($sql) -> result();
			if(count($list) > 0) {
				return !empty($list[0] -> samt) ? $list[0] -> samt : 0;
			}
			return 0;
		}

		function query_ajax($data) {
			$start = $data['start'];
			$limit = $data['length'];
			$columns = $data['columns'];
			$search = $data['search'];
			$order = $data['order'];

			// select
			$this -> db -> select('_m.*');
			$this -> db -> select('c.corp_name');

			$this -> db -> select('st.tab_name');

			$this -> db -> select('u.user_name');
			$this -> db -> select('u.account as user_account');


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

			if(isset($data['id']) && $data['id'] > -1) {
				$this -> db -> where('_m.id', $data['id']);
			}

			if(!empty($data['tab_id'])) {
				$this -> db -> where('_m.tab_id', $data['tab_id']);
			}

			if(!empty($data['tab_type'])) {
				$this -> db -> where('bt.tab_type', $data['tab_type']);
			}

			if(!empty($data['corp_id'])) {
				$this -> db -> where('_m.corp_id', $data['corp_id']);
			}
			if(!empty($data['create_date'])) {
				$v = $data['create_date'];
				$this -> db -> where("_m.create_time like '{$v}%' ");
			}
			if(!empty($data['s_user_name'])) {
				$v = $data['s_user_name'];
				$this -> db -> where("(u.account like '{$v}%' or u.user_name like '{$v}%' )");
			}
		}

		function ajax_from_join() {
			// join
			$this -> db -> from("$this->table_name as _m");
			$this -> db -> join("corp c", "c.id = _m.corp_id", "left");
			$this -> db -> join("users u", "u.id = _m.user_id", "left");
			$this -> db -> join("slot_cao_tab st", "st.id = _m.tab_id", "left");
		}

		function sum_ajax($data) {
			$start = $data['start'];
			$limit = $data['length'];
			$columns = $data['columns'];
			$search = $data['search'];
			$order = $data['order'];

			// select
			$this -> db -> select('sum(_m.bet_amt) as s_total_bet');

			$this -> db -> select('u.user_name');
			$this -> db -> select('u.account as user_account');

			// join
			$this -> db -> from("$this->table_name as _m");
			$this -> db -> join("corp c", "c.id = _m.corp_id", "left");
			$this -> db -> join("users u", "u.id = _m.user_id", "left");

			// group
			$this -> db -> group_by('_m.user_id');

			// search always
			if(!empty($data['corp_id'])) {
				$this -> db -> where('_m.corp_id', $data['corp_id']);
			}
			if(!empty($data['create_date'])) {
				$v = $data['create_date'];
				$this -> db -> where("_m.create_time like '{$v}%' ");
			}
			if(!empty($data['s_user_name'])) {
				$v = $data['s_user_name'];
				$this -> db -> where("(u.account like '{$v}%' or u.user_name like '{$v}%' )");
			}


			$this -> db -> order_by('s_total_bet', 'desc');

			// limit

			// query results
			$query = $this -> db -> get();
			return $query -> result();
		}

		function sum_win_amt_by_user_and_date($user_id, $date) {
			$this -> db -> select("sum(win_amt) as sum_win_amt");
			$this -> db -> from("{$this->table_name}");
			$this -> db -> where("user_id", $user_id);
			$this -> db -> where("is_win", 1);
			$this -> db -> where("(create_time like '$date%')");
			$list = $this -> db -> get() -> result();
			if(count($list) > 0) {
				$item = $list[0];
				if(!empty($item -> sum_win_amt)) {
					return $item -> sum_win_amt;
				}
 			}
			return 0;
		}

		function count_bet_in_seconds($tab_id, $seconds) {
			$dt = date('Y-m-d H:i:s', strtotime("-{$seconds} second"));
			$this -> db -> distinct();
			$this -> db -> select("user_id");
			$this -> db -> where("tab_id", $tab_id);
			$this -> db -> where("(create_time >= '$dt')");
			$this -> db -> from($this -> table_name);
			$list = $this -> db -> get() -> result();

			// error_log($this -> db -> last_query());
			return count($list);
		}

		function count_bet_users_iseconds($tab_id, $seconds) {
			$dt = date('Y-m-d H:i:s', strtotime("-{$seconds} second"));
			$this -> db -> distinct();
			$this -> db -> select("u.nick_name");
			$this -> db -> select("u.line_picture");

			$this -> db -> join("users u", "u.id = _m.user_id", "left");

			$this -> db -> where("_m.tab_id", $tab_id);
			$this -> db -> where("(_m.create_time >= '$dt')");
			$this -> db -> from("$this->table_name as _m");
			$list = $this -> db -> get() -> result();

			// error_log($this -> db -> last_query());
			return $list;
		}

	function sync() {
		$this -> load -> model('Total_bet_dao', 'tb_dao');

		$this -> db -> where("is_sync", 0);
		$this -> db -> where("parent_id", 0);
		$list = $this  -> find_all();

		foreach($list as $each) {
			$this -> db -> trans_begin();

			$this -> tb_dao -> insert(array(
				'corp_id' => $each -> corp_id,
				'user_id' => $each -> user_id,
				'game_id' => 11,
				'bet_record_id' => $each -> id,
				'amt' => $each -> bet_amt,
				'create_time' => $each -> create_time,
			));

			$this -> update(array('is_sync' => 1), $each -> id);

			if ($this->db->trans_status() === FALSE)
			{
			    $this->db->trans_rollback();
			}
			else
			{
			    $this->db->trans_commit();
			}
		}
	}

	function find_user_fish($data, $is_count = FALSE) {

		$user_id = $data['user_id'];
		$start = $data['start'];
		$limit = $data['length'];

		// select
		$this -> db -> from("$this->table_name as _m");

		$this -> db -> select('_m.hall_id');
		$this -> db -> select('_m.bet_type');
		$this -> db -> select('_m.fish_amt ');
		$this -> db -> select('_m.jp_amt');
		$this -> db -> select('_m.king_amt ');
		$this -> db -> select('_m.accu_box');
		$this -> db -> select('_m.king_name');
		$this -> db -> select('_m.is_fatal');
		$this -> db -> select('p.product_name');
		$this -> db -> select('_m.create_time');


		$this -> db -> join("products p", "p.id = _m.fatal_product_id", "left");

		if(!$is_count) {
			$this -> db -> limit($limit, $start);
		}

		$this -> db -> where('_m.user_id',$user_id);
		$this -> db -> order_by('_m.id','desc');


		// query results
		if(!$is_count) {
			$query = $this -> db -> get();
			return $query -> result();
		} else {
			return $this -> db -> count_all_results();
		}

	}
}
?>
