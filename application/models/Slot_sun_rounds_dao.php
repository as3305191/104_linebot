<?php
class Slot_sun_rounds_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('slot_sun_rounds');

		$this -> alias_map = array(

		);
	}

	function get_round_count($tab_id, $hall_id = 0, $is_100) {
		$this -> db -> where("tab_id", $tab_id);
		$this -> db -> where("hall_id", $hall_id);
		$this -> db -> where("is_100", $is_100);
		$this -> db -> order_by('id', 'desc');
		$this -> db -> limit(4);
		return $this -> find_all();
	}

	function find_last_one($is_100) {
		$this -> db -> limit(1);
		$this -> db -> order_by('id', 'desc');
		$this -> db -> where('is_100', $is_100);
		$list = $this -> find_all();
		return (count($list) > 0 ? $list[0] : NULL);
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
		$this -> db -> order_by('id', 'asc');

		// limit
		$this -> db -> limit($limit, $start);

		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}

	function search_always($data) {

	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
	}

	function find_one() {
		$list = $this -> find_all();
		return $list[0];
	}
}
?>
