<?php
class Pool_record_details_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('pool_record_details');

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
		$this -> db -> select('p.product_name, p.price as product_price');

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

	function find_all_by_pool_record($pr_id) {
		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('u.account as user_account');

		// join
		$this -> ajax_from_join();

		$this -> db -> where('pool_record_id', $pr_id);

		$this -> db -> order_by('id', 'desc');

		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}

	function search_always($data) {
		if(isset($data['status']) && $data['status'] > -1) {
			$this -> db -> where('_m.status', $data['status']);
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
		$this -> db -> join("users u", "u.id = _m.user_id", "left");
	}

}
?>
