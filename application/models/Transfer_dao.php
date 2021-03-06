<?php
class Transfer_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('transfer');

		$this -> alias_map = array(
			// 'user_account' => 'u.account',
			// 'pay_type_name' => 'pt.type_name'
 		);
	}

	function find_me($id) {
		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('ts.status_name');
		$this -> db -> select('iu.account as in_account');
		$this -> db -> select('ou.account as out_account');

		// join
		$this -> ajax_from_join();
		$this -> db -> where('_m.id', $id);

		// query results
		$query = $this -> db -> get();
		$list = $query -> result();
		return $list[0];
	}

	function query_ajax($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('ts.status_name');
		$this -> db -> select('iu.account as in_account');
		$this -> db -> select('ou.account as out_account');

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

		if(isset($data['out_user_id']) && $data['out_user_id'] > -1) {
			$this -> db -> where('_m.out_user_id', $data['out_user_id']);
		}

		if(isset($data['in_user_id']) && $data['in_user_id'] > -1) {
			$this -> db -> where('_m.in_user_id', $data['in_user_id']);
		}

		if(isset($data['id']) && $data['id'] > -1) {
			$this -> db -> where('_m.id', $data['id']);
		}

		if(isset($data['corp_id']) && $data['corp_id'] > -1) {
			$this -> db -> where('_m.corp_id', $data['corp_id']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("transfer_status ts", "ts.id = _m.status", "left");
		$this -> db -> join("users iu", "iu.id = _m.in_user_id", "left");
		$this -> db -> join("users ou", "ou.id = _m.out_user_id", "left");
	}

	function find_all_status() {
		$sql = "select * from transfer_status ";
		$list = $this -> db -> query($sql) -> result();
		return $list;
	}
}
?>
