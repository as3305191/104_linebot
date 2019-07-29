<?php
class Dig_details_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('dig_details');

		$this -> alias_map = array(
			'user_account' => 'u.account'
 		);
	}

	function find_by_date($dig_record_id, $dt) {
		$this -> db -> where('gen_date', $dt);
		$this -> db -> where('dig_record_id', $dig_record_id);
		$this -> db -> where("(dig_detail_type_id <> 5)");

		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function query_ajax($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('dr.user_id, dr.start_date, dr.end_date');
		$this -> db -> select('u.account as user_account');

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

		if(isset($data['user_id']) && $data['user_id'] > -1) {
			$this -> db -> where('_m.user_id', $data['user_id']);
		}

		if(isset($data['id']) && $data['id'] > -1) {
			$this -> db -> where('_m.id', $data['id']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("dig_records dr", "dr.id = _m.dig_record_id", "left");
		$this -> db -> join("users u", "u.id = dr.user_id", "left");
	}

}
?>
