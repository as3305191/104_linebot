<?php
class Mining_machine_buy_records_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('mining_machine_buy_records');

		$this -> alias_map = array(
			'user_account' => 'u.account',
			'corp_name' => 'c.corp_name'
 		);
	}

	function find_all_unprocess() {
		$an_hour_ago = date('Y-m-d H:i:s', strtotime('-1 hour'));
		$sql = "select _m.*, mm.ntd_reward_monthly
		from mining_machine_buy_records as _m
		left join mining_machines mm on mm.id = _m.mining_machine_id
		where last_process_time < '$an_hour_ago' ";
		$list = $this -> db -> query($sql) -> result();
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
		$this -> db -> select('c.corp_name');
		$this -> db -> select('u.account as user_account, u.user_name');
		$this -> db -> select('mm.machine_name, mm.max_days, DATEDIFF(now(),_m.create_time) as day_diff');
		// $this -> db -> select('pt.type_name as pay_type_name');
		// $this -> db -> select('ps.pay_status_name');
		// $this -> db -> select('u.account as user_account');

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
		$this -> db -> join("mining_machines mm", "mm.id = _m.mining_machine_id", "left");
		// $this -> db -> join("pay_types pt", "pt.id = _m.pay_type_id", "left");
		// $this -> db -> join("pay_status ps", "ps.id = _m.status", "left");
		$this -> db -> join("corp c", "c.id = _m.corp_id", "left");
		$this -> db -> join("users u", "u.id = _m.user_id", "left");

	}

	function find_all_mine($user_id) {

		$this -> db -> select('_m.*');
		$this -> db -> select('mm.machine_name, mm.card, mm.max_days, DATEDIFF(now(),_m.create_time) as day_diff');

		$this -> db -> from($this -> table_name . ' as _m');
		$this -> db -> join("mining_machines mm", "mm.id = _m.mining_machine_id", "left");

		$this -> db -> where('user_id', $user_id);
		$this -> db -> order_by('id', 'desc');
		$list = $this -> db -> get() -> result();
		// echo $this -> db -> last_query();
		return $list;
	}

}
?>
