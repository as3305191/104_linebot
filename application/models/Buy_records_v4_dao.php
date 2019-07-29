<?php
class Buy_records_v4_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('buy_records_v4');

		$this -> alias_map = array(
			'user_account' => 'u.account',
			'pay_type_name' => 'pt.type_name'
 		);
	}

	function sum_actual_amt($bet_record_id) {
		$this -> db -> select('sum(actual_amt) as samt');
		$this -> db -> from($this -> table_name . ' as _m');
		$this -> db -> where('bet_record_id', $bet_record_id);
		$list = $this -> db -> get() -> result();
		if(count($list) > 0) {
			return $list[0] -> samt;
		}
		return 0;
	}

	function find_all_by_bet_record($bet_record_id) {
		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('br.sn');
		$this -> db -> select('u.account as user_account');

		// join
		$this -> ajax_from_join();

		$this -> db -> where('bet_record_id', $bet_record_id);
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
		$this -> db -> select('br.sn');
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
		if(isset($data['status']) && $data['status'] > -1) {
			$this -> db -> where('_m.status', $data['status']);
		}

		if(isset($data['user_id']) && $data['user_id'] > -1) {
			$this -> db -> where('_m.user_id', $data['user_id']);
		}

		if(isset($data['corp_id']) && $data['corp_id'] > -1) {
			$this -> db -> where('_m.corp_id', $data['corp_id']);
		}

		if(isset($data['id']) && $data['id'] > -1) {
			$this -> db -> where('_m.id', $data['id']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("bet_records br", "br.id = _m.bet_record_id", "left");
		$this -> db -> join("users u", "u.id = _m.user_id", "left");
	}

}
?>
