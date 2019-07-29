<?php
class Transfer_coin_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('transfer_coin');

		$this -> alias_map = array(
			'in_account' => 'iu.account',
			'out_account' => 'ou.account'
 		);
	}

	function check_expired_transfer_in() {
		$sql = "update transfer_coin set status = -2 where type = 1 and status = 0 and create_time < now()";
		$this -> db -> query($sql);
	}

	function find_me($id) {
		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('ts.status_name');
		$this -> db -> select('iu.account as in_account');
		$this -> db -> select('ou.account as out_account');
		$this -> db -> select('co.currency_name');
		$this -> db -> select('tt.type_name');;

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
		$this -> db -> select('co.currency_name');
		$this -> db -> select('tt.type_name');

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
			$user_id = $data['user_id'];
			$this -> db -> where("( _m.in_user_id = $user_id or _m.out_user_id = $user_id )");
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

		if(isset($data['is_verify']) && $data['is_verify'] > -1) {
			$this -> db -> where('(_m.type = 1 or (_m.type = 2 and _m.is_valid_code = 1))');
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("transfer_coin_status ts", "ts.id = _m.status", "left");
		$this -> db -> join("users iu", "iu.id = _m.in_user_id", "left");
		$this -> db -> join("users ou", "ou.id = _m.out_user_id", "left");
		$this -> db -> join("coins co", "co.currency = _m.currency", "left");
		$this -> db -> join("transfer_coin_type tt", "tt.id = _m.type", "left");
	}

	function find_all_status() {
		$sql = "select * from transfer_coin_status ";
		$list = $this -> db -> query($sql) -> result();
		return $list;
	}
}
?>
