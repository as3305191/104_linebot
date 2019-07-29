<?php
class Company_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('company');

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
		$this -> db -> where('_m.status', 0);
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
	}

	function find_all_company() {
		$this -> db -> where('status', 0);
		$list=  $this -> find_all();
		return $list;
	}
	function find_all_not_store() {
		$this -> db -> where('is_store', 0);
		$this -> db -> where('status', 0);
		$list=  $this -> find_all();
		return $list;
	}
}
?>
