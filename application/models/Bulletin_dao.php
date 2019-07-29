<?php
class Bulletin_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('bulletin');

		$this -> alias_map = array(

		);
	}

	function find_all_order() {
		$this -> db -> order_by('pos', 'asc');
		$this -> db -> where('status', 0);
		return $this -> find_all();
	}

	function find_all_order_by_type_name($type_name) {
		$this -> db -> order_by('pos', 'asc');
		$this -> db -> where('status', 0);
		$this -> db -> where('type_name', $type_name);
		return $this -> find_all();
	}

	function query_ajax($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('_m.*');

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
		// $this -> db -> where('_m.status', 0);
		if(isset($data['user_role_id']) && $data['user_role_id'] > -1) {
			$this -> db -> where('user_role_id', $data['user_role_id']);
		}
		if(isset($data['role_id']) && $data['role_id'] > -1) {
			$this -> db -> where('_m.role_id', $data['role_id']);
		}

		if(isset($data['corp_id']) && $data['corp_id'] > -1) {
			$this -> db -> where('_m.corp_id', $data['corp_id']);
		}

		if(isset($data['id']) && $data['id'] > -1) {
			$this -> db -> where('_m.id', $data['id']);
		}

		if(isset($data['type_name']) && $data['type_name'] > -1) {
			$this -> db -> where('_m.type_name', $data['type_name']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		// $this -> db -> join("users mu", "mu.id = _m.manager_id", "left");
		// $this -> db -> join("users iu", "iu.id = _m.intro_id", "left");
		// $this -> db -> join("roles r", "r.id = _m.role_id", "left");
	}

}
?>
