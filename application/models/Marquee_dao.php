<?php
class Marquee_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('marquee');

		$this -> alias_map = array(

		);
	}

	function find_all_order() {
		$this -> db -> order_by('id', 'desc');
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
		$this -> db -> select('gt.game_type_name');

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
		if(isset($data['id']) && $data['id'] > -1) {
			$this -> db -> where('_m.id', $data['id']);
		}
		if(!empty($data['corp_id'])) {
			$this -> db -> where('_m.corp_id', $data['corp_id']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("game_types gt", "gt.id = _m.game_type", "left");
		// $this -> db -> join("users iu", "iu.id = _m.intro_id", "left");
		// $this -> db -> join("roles r", "r.id = _m.role_id", "left");
	}

}
?>
