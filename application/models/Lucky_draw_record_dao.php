<?php
class Lucky_draw_record_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('lucky_draw_record');

		$this -> alias_map = array(

 		);
	}

	function list_winner($corp_id, $limit = 5) {
		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('u.account as user_account');
		$this -> db -> select('u.user_name as user_name');
		$this -> db -> select('u.image_id as image_id');
		$this -> db -> select('u.nick_name as nick_name');

		// join
		$this -> ajax_from_join();

		$this -> db -> where('is_win', 1);
		$this -> db -> where('_m.corp_id', $corp_id);

		// order
		$this -> db -> order_by('id', 'desc');

		// limit
		$this -> db -> limit($limit);

		// query results
		$query = $this -> db -> get();
		return $query -> result();
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

	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("users as u", "u.id = _m.user_id", "left");
	}

}
?>
