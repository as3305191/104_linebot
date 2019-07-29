<?php
class Customer_service_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('customer_service');

		$this -> alias_map = array(

 		);
	}

	function mark_read($user_id) {
		$list = $this -> get_unread($user_id);
		foreach($list as $each) {
			$this -> update(array(
				'is_read' =>  1
			), $each -> id);
		}
	}

	function get_unread($user_id) {
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('status', 1); // 已回覆
		$this -> db -> where('is_read', 0); // 未讀取
		$list = $this -> find_all();
		return $list;
	}

	function list_by_status($user_id, $status) {
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('status', $status);
		$this -> db -> from($this -> table_name . " as _m");
		$list = $this -> db -> get() -> result();
		return $list;
	}

	function list_all($user_id) {
		$this -> db -> where('user_id', $user_id);
		$this -> db -> from($this -> table_name . " as _m");
		$list = $this -> db -> get() -> result();
		return $list;
	}

	function query_ajax($data) {
		$start = $data['start'];
		$limit = $data['length'];
		$columns = $data['columns'];
		$search = $data['search'];
		$order = $data['order'];

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('u.account as user_account');
		$this -> db -> select('u.user_name as user_name');
		$this -> db -> select('u.nick_name as nick_name');


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
		if(!empty($data['id'])) {
			$this -> db -> where("_m.id", $data['id']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("users as u", "u.id = _m.user_id", "left");
	}

}
?>
