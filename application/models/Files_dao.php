<?php
class Files_dao extends MY_Model {

	function __construct() {
		parent::__construct();
		// initialize table name
		$this -> set_table_name("files");

		$this -> alias_map = array(
		);
	}

	function insert_file_data($i_data) {
		//insert data
		$file_path = $i_data['file_path'];
		$last_id = $this -> insert($i_data);

		return $last_id;
	}

	//delete image
	function delete_by_id($id){
		$this -> db -> where('id',$id);
		$this -> db ->delete($this -> table_name);
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
		$this -> db -> where('_m.image_path', 'general_img');

		if(!empty($data['store_id'])) {
			$this -> db -> where('store_id', $data['store_id']);
		}
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
	}

}
?>
