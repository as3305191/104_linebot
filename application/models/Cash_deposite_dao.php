<?php
class Cash_deposite_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('cash_deposite');

		$this -> alias_map = array(
			'corp_id' => '_m.corp_id'
			// 'pay_type_name' => 'pt.type_name'
 		);
	}

	function query_ajax($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('ws.status_name');
		$this -> db -> select('u.account as user_account, u.bank_id, u.bank_account');
		$this -> db -> select('c.corp_name');

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
		if(isset($data['status']) && strlen($data['status']) > 0) {
			$this -> db -> where('_m.status', $data['status']);
		}
		if(isset($data['user_id']) && $data['user_id'] > -1) {
			$this -> db -> where('_m.user_id', $data['user_id']);
		}

		if(isset($data['id']) && $data['id'] > -1) {
			$this -> db -> where('_m.id', $data['id']);
		}

		if(isset($data['corp_id']) && $data['corp_id'] > -1) {
			$this -> db -> where('_m.corp_id', $data['corp_id']);
		}
	}

	function ajax_column_setup($columns, $search, $alias_map) {
		// search
		if(!empty($columns)) {
			foreach($columns as $col) {
				if(!empty($col['search']['value'])) {
					$col_name = $col['data'];
					if($col_name == 'corp_id') {
						$this -> db -> where($this -> get_alias_val($alias_map, $col_name), $col['search']['value']);
					} else {
						$this -> db -> like($this -> get_alias_val($alias_map, $col_name), $col['search']['value']);
					}
				}
			}
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("cash_deposite_status ws", "ws.id = _m.status", "left");
		$this -> db -> join("users u", "u.id = _m.user_id", "left");
		$this -> db -> join("corp c", "c.id = _m.corp_id", "left");
	}

	function find_all_status() {
		$sql = "select * from cash_deposite_status ";
		$list = $this -> db -> query($sql) -> result();
		return $list;
	}

	function sum_by_date($date) {
		$this -> db -> select("sum(amt) as samt");
		$this -> db -> from("$this->table_name");
		$this -> db -> where("create_time like '{$date}%'");
		$this -> db -> where("status", 2);
		$list = $this -> db -> get() -> result();
		$item = $list[0];
		if(!empty($item -> samt)) {
			return $item -> samt;
		}
		return 0;
	}
}
?>
