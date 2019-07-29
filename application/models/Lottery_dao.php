<?php
class Lottery_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('lottery');

		$this -> alias_map = array(

		);
	}

	function find_basic_item() {
		$this -> db -> where("is_basic", 1);
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function lottery_list() {
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select("_m.sn as sn");
		$this -> db -> select("_m.lottery_name as lottery_name");
		$this -> db -> select("_m.price as price");
		$this -> db -> select("_m.is_basic as is_basic");
		$this -> db -> select("_m.total_num as total_num");
		$this -> db -> select("_m.image_id as img");

		$list = $this -> db -> get() -> result();
		return $list;
	}

	function find_sn($sn) {
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> where("_m.sn",$sn);

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
		// $this -> db -> select('wt.type_name');
		// $this -> db -> select('c.company_name');

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
	}

	function random_one() {
		$list = $this -> db -> query("select * from {$this->table_name} order by rand() limit 1 ") -> result();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}
}
?>
