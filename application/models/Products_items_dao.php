<?php
class Products_items_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('product_items');

		$this -> alias_map = array(
			// 'store_name' => 'st.store_name'
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
		// $this -> db -> select('st.store_name as store_name');

		// join
		$this -> ajax_from_join();

		// search always
		$this -> search_always($data);

		// search
		$this -> ajax_column_setup($columns, $search, $this -> alias_map);

		// order
		$this -> ajax_order_setup($order, $columns, $this -> alias_map);
		$this -> db -> order_by('_m.id', 'desc');

		// limit
		$this -> db -> limit($limit, $start);

		// query results
		$query = $this -> db -> get();
		return $query -> result();
	}

	function search_always($data) {
		$this -> db -> where('_m.status', 0);
		$this -> db -> where('_m.corp_id',$data['corp_id']);
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		// $this -> db -> join("corp as co", 'st.id = _m.store_id', 'left');
	}

	function find_by_account($account) {
		$this -> db -> where('account', $account);
		$query = $this -> db -> get($this -> table_name);
		foreach ($query->result() as $row){
		    return $row;
		}
		return NULL;
	}

	function query_all($f, &$res = array()) {
		// select
		$this -> db -> select('_m.*');
		// $this -> db -> select('st.store_name as store_name');
		// join
		$this -> ajax_from_join();

		if(!empty($f['id'])) {
			$this -> db -> where('_m.id', $f['id']);
		}

		$this -> db -> order_by('_m.id', 'desc');

		// search always
		$this -> search_always($f);

		// limit
		if(empty($f['page'])) {
			$page = 0;
		} else {
			$page = intval($f['page']);
		}

		if(empty($f['limit'])) {
			// default is 10
			$limit = 10;
		} else {
			$limit = intval($f['limit']);
		}

		$res['page'] = $page;
		$res['limit'] = $limit;

		$start = $page * $limit;
		$this -> db -> limit($limit, $start);

		// query results
		$query = $this -> db -> get();
		$list = $query -> result();

		foreach($list as $each) {
			if(!empty($each -> image_id)) {
				$each -> image_url = get_img_url($each -> image_id);
				$each -> thumb_url = get_thumb_url($each -> image_id);
			}
		}

		return $list;
	}

	//override
	function ajax_column_setup($columns, $search, $alias_map) {
		// search
		foreach($columns as $col) {
			if(!empty($col['search']['value'])) {
				if($col['data'] == 'mul_cate'){
					$mul_cate_val = explode(',',$col['search']['value']);
					foreach($mul_cate_val as $each){
						//** key step
							$this -> db -> where("FIND_IN_SET('$each',_m.main_cate) <>", 0);
					}

				}else{
					$col_name = $col['data'];
					$this -> db -> like($this -> get_alias_val($alias_map, $col_name), $col['search']['value']);
				}

			}
		}
	}

	//複製商品 檔名用
	function find_copy($product_name){
		$this -> db -> like('product_name',$product_name,'after');
		$this -> db -> order_by('create_time','desc');
		return $this -> find_exists_all();
	}

	function find_weapon($each_id) {
		$this -> db -> from("$this->table_name as _m");
		// $this -> db -> join("product p", "p.id = _m.product_id", "left");
		$this -> db -> where("_m.id",$each_id);
		return $this -> db -> count_all_results();

	}

	function find_all_article($id,$i) {
		$this -> db -> from("$this->table_name as _m");

		$this -> db -> select('if(COUNT(_m.product_id) is NULL, 0, COUNT(_m.product_id)) as total');
		$this -> db -> select('_m.product_id');

		$this -> db -> where("_m.is_delete<>",1);
		$this -> db -> where("_m.user_id",$id);
		$this -> db -> where("_m.product_id",$i);

		$this -> db -> group_by("_m.product_id");

		$query = $this -> db -> get();
		return $query -> result();
	}

	function find_id($product_id){
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select('_m.*');
		$this -> db -> where('_m.id',$product_id);

		$query = $this -> db -> get();
		return $query -> result();
	}

	function find_id_1($material_id){
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select('_m.*');
		$this -> db -> where('_m.id',$material_id);

		$query = $this -> db -> get();
		return $query -> result();
	}

	function find_owner($user_id,$product_id){
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select('_m.*');
		$this -> db -> where('_m.product_id',$product_id);
		$this -> db -> where('_m.user_id',$user_id);

		$query = $this -> db -> get();
		return $query -> result();
	}

	function find_owners($user_id,$material_id){
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select('_m.*');
		$this -> db -> where('_m.id',$material_id);
		$this -> db -> where('_m.user_id',$user_id);

		$query = $this -> db -> get();
		return $query -> result();
	}
}
?>
