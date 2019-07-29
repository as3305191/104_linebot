<?php
class Chat_msg_ad_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('chat_msg_ad');

		$this -> alias_map = array(
		);
	}

	function find_me_by_id($id) {
		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('u.nick_name');


		// join
		$this -> ajax_from_join();

		// search always
		$data['id'] = $id;
		$this -> search_always($data);

		// search

		// order

		// limit

		// query results
		$query = $this -> db -> get();
		return $query -> result()[0];
	}

	function query_ajax($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('u.nick_name');


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
		if(!empty($data['id'])) {
			$this -> db -> where('_m.id', $data['id']);
		}

	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("users u", "u.id = _m.user_id", "left");
		// $this -> db -> join("fleet f", "f.id = _m.fleet_id", "left");
	}

}
?>
