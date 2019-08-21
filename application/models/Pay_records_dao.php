<?php
class Pay_records_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('pay_records');

		$this -> alias_map = array(
			'user_account' => 'u.account',
			'sn' => '_m.sn'
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
		// $this -> db -> select('p.product_name, p.hours, p.price as product_price');
		$this -> db -> select('pt.type_name as pay_type_name');
		$this -> db -> select('ps.pay_status_name');
		$this -> db -> select('u.account as user_account');
		$this -> db -> select('u.line_picture');
		$this -> db -> select('u.nick_name');
		$this -> db -> select('c.corp_name as corp_name');

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

		$this -> fb -> log($this -> db -> last_query());
		return $query -> result();
	}

	function search_always($data) {
		if(isset($data['status']) && $data['status'] > -1) {
			$this -> db -> where('_m.status', $data['status']);
		}
		if(isset($data['user_id']) && $data['user_id'] > -1) {
			$this -> db -> where('_m.user_id', $data['user_id']);
		}

		if(isset($data['id']) && $data['id'] > -1) {
			$this -> db -> where('_m.id', $data['id']);
		}
		if(!empty($data['s_corp_id'])) {
			$this -> db -> where('_m.corp_id', $data['s_corp_id']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		// $this -> db -> join("products p", "p.id = _m.product_id", "left");
		$this -> db -> join("pay_types pt", "pt.id = _m.pay_type_id", "left");
		$this -> db -> join("pay_status ps", "ps.id = _m.status", "left");
		$this -> db -> join("users u", "u.id = _m.user_id", "left");
		$this -> db -> join("corp c", "c.id = _m.corp_id", "left");

	}

}
?>
