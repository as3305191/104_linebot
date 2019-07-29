<?php
class Bonus_tx_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('bonus_tx');

		$this -> alias_map = array(

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
		$this -> db -> select('b.sn');
		$this -> db -> select('p.product_name');
		$this -> db -> select('u.account as user_account');
		$this -> db -> select('bu.account as buy_user_account');

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

		if(!empty($data['c_date'])) {
			$c_date = $data['c_date'];
			$this -> db -> where("_m.create_time like '$c_date%'");
		}

		if(!empty($data['login_user'])) {
			$login_user = $data['login_user'];
			if($login_user -> role_id == 3 || $login_user -> role_id == 2) {
				if(isset($data['user_id']) && $data['user_id'] > -1) {
					$this -> db -> where('_m.user_id', $data['user_id']);
				} else {
					$this -> db -> where('_m.user_id', -1); // find nothing
				}
			}
			if($login_user -> role_id == 1) {

			}
		} else {
			if(isset($data['user_id']) && $data['user_id'] > -1) {
				$this -> db -> where('_m.user_id', $data['user_id']);
			} else {
				$this -> db -> where('_m.user_id', -1); // find nothing
			}
		}

		if(isset($data['id']) && $data['id'] > -1) {
			$this -> db -> where('_m.id', $data['id']);
		}

		if(!empty($data['s_date'])) {
			$dt = $data['s_date'];
			$this -> db -> where("_m.create_time >= '$dt 00:00:00'");
		}

		if(!empty($data['e_date'])) {
			$dt = $data['e_date'];
			$this -> db -> where("_m.create_time <= '$dt 23:59:59'");
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("buy_records b", "b.id = _m.buy_record_id", "left");
		$this -> db -> join("products p", "p.id = b.product_id", "left");
		$this -> db -> join("users bu", "bu.id = b.user_id", "left");
		$this -> db -> join("users u", "u.id = _m.user_id", "left");
	}

	function query_report($data) {

		// select
		$this -> db -> select('u.account as user_account, u.bank_id, u.bank_account,_m.user_id, sum(_m.amt) as amt');

		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("users u", "u.id = _m.user_id", 'inner');

		// search always
		if(!empty($data['s_date'])) {
			$dt = $data['s_date'];
			$this -> db -> where("_m.create_time >= '$dt 00:00:00'");
		}

		if(!empty($data['e_date'])) {
			$dt = $data['e_date'];
			$this -> db -> where("_m.create_time <= '$dt 23:59:59'");
		}

		$this -> db -> group_by('_m.user_id');
		// order
		$this -> db -> order_by('_m.id', 'asc');


		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}

}
?>
