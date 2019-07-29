<?php
class MY_Model extends CI_Model {
	var $table_name = '';
	var $view_name = '';

	var $alias_map;

	function __construct() {
		parent::__construct();
		$this -> load -> helper('common');
	}

	function set_table_name($tb_name) {
		$this -> table_name = $tb_name;
		$this -> set_view_name($tb_name);
	}

	function set_view_name($tb_name) {
		$this -> view_name = $tb_name . '_view';
	}

	function get_db() {
		return $this -> db;
	}

	function count_all() {
		return $this -> db -> count_all($this -> table_name);
	}

	function count_all_by($name, $value) {
		$this -> db -> where($name, $value);
		$this -> db -> from($this -> table_name);
		return $this -> db -> count_all_results();
	}

	function count_all_query($columns) {
		foreach ($columns as $col) {
			$this -> db -> like($col -> key, $col -> value);
		}
		$this -> db -> from($this -> table_name);
		return $this -> db -> count_all_results();
	}

	function count_all_where($where) {
		$this -> db -> where($where);
		$this -> db -> from($this -> table_name);
		$result = $this -> db -> count_all_results();

		return $result;
	}

	function find_all() {
		$query = $this -> db -> get($this -> table_name);
		return $query -> result();
	}

	function find_exists_all() {
		$this -> db -> where('status', 0);
		$query = $this -> db -> get($this -> table_name);
		return $query -> result();
	}

	function find_all_view() {
		$query = $this -> db -> get($this -> view_name);
		return $query -> result();
	}

	function find_all_view_by($name, $value) {
		$this -> db -> where($name, $value);
		$query = $this -> db -> get($this -> view_name);
		return $query -> result();
	}

	function find_all_by($name, $value) {
		$this -> db -> where($name, $value);
		$query = $this -> db -> get($this -> table_name);
		return $query -> result();
	}

	function find_all_where($where) {
		$this -> db -> where($where);
		$query = $this -> db -> get($this -> table_name);
		return $query -> result();
	}

	function find_all_query($columns, $dir) {
		foreach ($columns as $col) {
			$this -> db -> like($col -> key, $col -> value);
		}

		$this -> db -> order_by("id", isset($dir) ? $dir : "asc");
		$query = $this -> db -> get($this -> table_name);
		return $query -> result();
	}

	function find_all_query_where($columns) {
		foreach ($columns as $key => $value) {
			$this -> db -> where($key, $value);
		}

		$query = $this -> db -> get($this -> table_name);
		return $query -> result();
	}



	function find_paging_all($start, $limit, $dir) {
		$this -> db -> limit($limit, $start);
		$this -> db -> order_by("id", isset($dir) ? $dir : "asc");
		$query = $this -> db -> get($this -> table_name);
		return $query -> result();
	}

	function find_paging_with_date_all($start, $limit) {
		$this -> db -> limit($limit, $start);
		$this -> db -> where('start_date <= now()');
		$this -> db -> order_by("start_date", 'desc');
		$query = $this -> db -> get($this -> table_name);
		return $query -> result();
	}

	function find_paging_all_query($start, $limit, $dir, $columns) {
		foreach ($columns as $col) {
			$this -> db -> like($col -> key, $col -> value);
		}

		$this -> db -> limit($limit, $start);
		$this -> db -> order_by("id", isset($dir) ? $dir : "asc");
		$query = $this -> db -> get($this -> table_name);
		return $query -> result();
	}

	function find_by_id($id) {
		$this -> db -> where('id', $id);
		$query = $this -> db -> get($this -> table_name);

		if ($query -> num_rows() > 0) {
			$row = $query -> row();
			return $row;
		}
		return NULL;
	}

	function find_view_by_id($id) {
		$this -> db -> where('id', $id);
		$query = $this -> db -> get($this -> view_name);

		if ($query -> num_rows() > 0) {
			$row = $query -> row();
			return $row;
		}
		return NULL;
	}

	function find_by($name, $value) {
		$this -> db -> where($name, $value);
		$query = $this -> db -> get($this -> table_name);

		if ($query -> num_rows() > 0) {
			$row = $query -> row();
			return $row;
		}
		return NULL;
	}

	function insert($data) {
		$this -> db -> insert($this -> table_name, $data);
		return $this -> db -> insert_id();
	}

	function update($data, $id) {
		$this -> db -> where('id', $id);
		$this -> db -> update($this -> table_name, $data);
	}

	function update_by($data, $column, $value) {
		$this -> db -> where($column, $value);
		$this -> db -> update($this -> table_name, $data);
	}

	function delete($id) {
		$this -> db -> delete($this -> table_name, array('id' => $id));
	}

	function delete_status($id, $user_id, $status = 1) {
		$data['status'] = $status;
		$data['delete_time'] = date('Y-m-d H:i:s');
		$data['delete_userid'] = $user_id;
		$this -> update($data, $id);
	}

	function delete_all_by($key, $value) {
		$this -> db -> where($key, $value);
		$this -> db -> delete($this -> table_name);
	}

	function empty_table(){
		$this -> db -> empty_table($this->table_name);
	}

	function each_column_setup($columns, $column_name, $query_type, $pre_fix = 'search_', $value_prefix = '') {
		if (isset($columns[$pre_fix . $column_name]) && strlen($columns[$pre_fix . $column_name]) > 0) {
			if ($query_type == 'where')
				$this -> db -> where($column_name, $columns[$pre_fix . $column_name]);
			else if ($query_type == 'where_in') {
				// special case for search_region
				if ($column_name == 'region')
					$column_name = 'city';
				$this -> db -> where_in($column_name, $columns['region_items']);
			} else if ($query_type == 'like')
				$this -> db -> like($column_name, $columns[$pre_fix . $column_name]);
			else if ($query_type == 'or_like')
				$this -> db -> or_like($column_name, $columns[$pre_fix . $column_name]);
			else {// special where
				$this -> db -> where($query_type, $columns[$pre_fix . $column_name]);
			}
		}
	}

	function each_column_alias_setup($columns, $column_name, $real_column_name,  $query_type, $pre_fix = 'search_', $value_prefix = '') {
		if (isset($columns[$pre_fix . $column_name]) && strlen($columns[$pre_fix . $column_name]) > 0) {
			if ($query_type == 'where')
				$this -> db -> where($real_column_name, $columns[$pre_fix . $column_name]);
			else if ($query_type == 'where_in') {
				// special case for search_region
				if ($column_name == 'region')
					$column_name = 'city';
				$this -> db -> where_in($column_name, $columns['region_items']);
			} else if ($query_type == 'like')
				$this -> db -> like($real_column_name, $columns[$pre_fix . $column_name]);
			else if ($query_type == 'or_like')
				$this -> db -> or_like($real_column_name, $columns[$pre_fix . $column_name]);
			else {// special where
				$this -> db -> where($query_type, $columns[$pre_fix . $column_name]);
			}
		}
	}

	function order_column_setup($columns, $column_name, $pre_fix = 'order_') {
		if (isset($columns[$pre_fix . $column_name]) && strlen($columns[$pre_fix . $column_name]) > 0) {
			$this -> db -> order_by($column_name, $columns[$pre_fix . $column_name]);
		}
	}

	function query_for_list($sql) {
		$query = $this -> db -> query($sql);
		return $query -> result();
	}

	// ws
	function check_where($where) {
		return $where == "" ? " where " : " and ";
	}

	function get_safe($arr, $p) {
		if (isset($arr[$p]))
			return $arr[$p];
		return FALSE;
	}

	 public function get_thumb_url_by_image_id($id) {
		$file_base_url = base_url();
		//$replace_pos = strpos($file_base_url, "plastic_surgery/");
		$replace_pos = strpos($file_base_url, "iimage/");
		$image_base_url = substr_replace($file_base_url, "plastic_surgery_img/", $replace_pos);
		$image_url = $image_base_url . "member_photos_thumb/" . 'thumb_' . $id . ".jpg";
		// return $image_url;
		return $image_url;
	}

	public function get_url_from_image_id($id) {
		$file_base_url = base_url();
		$replace_pos = strpos($file_base_url, "plastic_surgery/");
		$image_base_url = substr_replace($file_base_url, "plastic_surgery_img/", $replace_pos);
		$image_url = $image_base_url . "member_photos/" . $id . ".jpg";
		return $image_url;
	}

	// for ajax

	function count_all_ajax($data) {
		$columns = $data['columns'];
		$search = $data['search'];

		// search always
		$this -> search_always($data);

		// join
		$this -> ajax_from_join();

		// count results
		return $this -> db -> count_all_results();
	}

	function count_ajax($data) {
		$columns = $data['columns'];
		$search = $data['search'];

		// search always
		$this -> search_always($data);

		// join
		$this -> ajax_from_join();
		$this -> ajax_column_setup($columns, $search, $this -> alias_map);

		// count results
		return $this -> db -> count_all_results();
	}

	public function ajax_order_setup($order, $columns, $order_alias_map = array()){
		if(empty($order) || $order[0]['column'] == 0){
			return;
		}

		$col_name = $columns[$order[0]['column']]['data'];
		$dir = $order[0]['dir'];

		// check alias
		$col_name = !empty($order_alias_map[$col_name]) ? $order_alias_map[$col_name] : $col_name;
		$this -> db -> order_by($col_name, $dir);
	}

	function ajax_column_setup($columns, $search, $alias_map) {
		// search
		if(!empty($columns)) {
			foreach($columns as $col) {
				if(!empty($col['search']['value'])) {
					$col_name = $col['data'];
					$this -> db -> like($this -> get_alias_val($alias_map, $col_name), $col['search']['value']);
				}
			}
		}
	}

	function get_alias_val($alias_map, $key)
	{
		return (!empty($alias_map[$key]) ? $alias_map[$key] : $key);
	}

}
?>
