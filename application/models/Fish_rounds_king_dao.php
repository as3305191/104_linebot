<?php
class Fish_rounds_king_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('fish_rounds_king');

		$this -> alias_map = array(

		);
	}

	function list_rounds($tab_id, $hall_id){
		$this -> db -> where('tab_id', $tab_id);
		$this -> db -> where('hall_id', $hall_id);
		$this -> db -> order_by('id', 'desc');
		$this -> db -> limit(5);
		$list = $this -> find_all();
		return $list;
	}

	function count_rounds($tab_id, $hall_id){
		$list = $this -> list_rounds($tab_id, $hall_id);
		$last_id = 0;
		if(count($list) > 0) {
			$last_id = $list[0] -> fish_bet_id;
		}

		$this -> db -> where('tab_id', $tab_id);
		$this -> db -> where('hall_id', $hall_id);
		$this -> db -> where("id > {$last_id}");
		$this -> db -> from('fish_bet');
		$cnt = $this -> db -> count_all_results();
		// echo $this -> db -> last_query();
		return $cnt;
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
}
?>
