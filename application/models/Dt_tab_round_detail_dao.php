<?php
class Dt_tab_round_detail_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('dt_tab_round_detail');

		$this -> alias_map = array(
			// 'corp_id' => '_m.corp_id'
			// 'pay_type_name' => 'pt.type_name'
 		);
	}

	function find_current_round_detail($round_id) {
		$this -> db -> select('_m.*');
		$this -> db -> select('btrds.status_name');

		$this -> db -> where('_m.round_id', $round_id); // not finished yet
		$this -> db -> where('_m.status < 5'); // not finished yet
		$this -> db -> order_by('_m.pos', 'asc');
		$this -> db -> limit(1);
		$this -> db -> from($this -> table_name . ' as _m');
		$this -> db -> join('baccarat_tab_round_detail_status btrds', 'btrds.id = _m.status', 'left');

		$list = $this -> db -> get() -> result();
		if(count($list) > 0) {
			$rd = $list[0];
			return $rd;
		} else {
			return NULL;
		}
	}

	function count_winner($round_id) {
		$query = $this -> db -> query("
						select winner as winner_type, count(*) as cnt
						from dt_tab_round_detail
						where round_id = $round_id
						and status >= 5
						group by  winner
						order by winner
		");
		$list = $query -> result();
		return $list;
	}

	function count_winner_type($round_id) {
		$query = $this -> db -> query("
						select winner_type as winner_type, count(*) as cnt
						from dt_tab_round_detail
						where round_id = $round_id
						and status >= 5
						group by  winner_type
						order by winner_type
		");
		$list = $query -> result();
		return $list;
	}

	function list_road($round_id) {
		$this -> db -> select("winner, winner_type");
		$this -> db -> where('round_id', $round_id);
		$this -> db -> where('status >= 5');
		$this -> db -> order_by('pos', 'asc');

		$list = $this -> db -> get($this -> table_name) -> result();
		return $list;
	}

	function find_current_round($tab_id) {
		$this -> db -> select('_m.*');
		$this -> db -> select('btrs.status_name');

		$this -> db -> where('_m.tab_id', $tab_id);
		$this -> db -> where('_m.status < 2'); // not finished
		$this -> db -> order_by('_m.id', 'asc');
		$this -> db -> limit(1);
		$this -> db -> from($this -> table_name . ' as _m');
		$this -> db -> join('baccarat_tab_round_status btrs', 'btrs.id = _m.status', 'left');
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function query_ajax($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('btrd_s.status_name');
		// $this -> db -> select('u.account as user_account, u.bank_id, u.bank_account');
		// $this -> db -> select('bk.bank_name');
		// $this -> db -> select('c.corp_name');

		// join
		$this -> ajax_from_join();

		// search always
		$this -> search_always($data);

		// search
		$this -> ajax_column_setup($columns, $search, $this -> alias_map);

		// order
		$this -> ajax_order_setup($order, $columns, $this -> alias_map);
		$this -> db -> order_by('id', 'asc');

		// limit
		$this -> db -> limit($limit, $start);

		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}

	function search_always($data) {
		// if(isset($data['status']) && strlen($data['status']) > 0) {
		// 	$this -> db -> where('_m.status', $data['status']);
		// }
		if(!empty($data['round_id'])) {
			$this -> db -> where('_m.round_id', $data['round_id']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("baccarat_tab_round_detail_status btrd_s", "btrd_s.id = _m.status", "left");
		// $this -> db -> join("users u", "u.id = _m.user_id", "left");
		// $this -> db -> join("banks bk", "bk.bank_id = u.bank_id", "left");
		// $this -> db -> join("corp c", "c.id = _m.corp_id", "left");
	}
}
?>
