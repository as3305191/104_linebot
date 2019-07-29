<?php
class Ranking_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('ranking');

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
		$this -> db -> select('u.nick_name as user_nick_name');

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
		// $this -> db -> where('_m.status', 0);
		$this -> db -> where('_m.date',$data['dt']);
	}

	function ajax_from_join() {
		// join
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("users as u", 'u.id = _m.user_id', 'left');
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


	function find_by_parameter($m){
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("users u", "u.id = _m.user_id", "left");

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('u.nick_name, u.line_picture');

		if(!empty($m['date'])){
			$this -> db -> where('_m.date',$m['date']);
		}

		if(!empty($m['s_date']) && !empty($m['e_date'])){
			$s_date = $m['s_date'];
			$e_date = $m['e_date'];
			$this -> db -> where("('{$s_date}' <= date and date <= '{$e_date}')");
		}

		$this -> db -> order_by('_m.score','desc');

		$query = $this -> db -> get();
		$list = $query -> result();

		return $list;
	}

	function group_by_parameter($m){
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("users u", "u.id = _m.user_id", "left");

		// select
		$this -> db -> select('sum(_m.score) as score');
		$this -> db -> select('_m.user_id');
		$this -> db -> select('u.nick_name, u.line_picture');

		if(!empty($m['s_date']) && !empty($m['e_date'])){
			$s_date = $m['s_date'];
			$e_date = $m['e_date'];
			$this -> db -> where("('{$s_date}' <= date and date <= '{$e_date}')");
		} elseif(!empty($m['date'])){
			$this -> db -> where('_m.date',$m['date']);
		} else {
			$this -> db -> where('_m.date','XXXXX');
		}

		if(!empty($m['user_id'])){
			$this -> db -> where('_m.user_id',$m['user_id']);
		}

		$this -> db -> group_by('_m.user_id');
		$this -> db -> order_by('score','desc');

		$this -> db -> limit(10);

		$query = $this -> db -> get();
		$list = $query -> result();

		return $list;
	}

	function find_by_user_and_date($user_id, $date) {
		$this -> db -> where("user_id", $user_id);
		$this -> db -> where("date", $date);
		$this -> db -> from("{$this->table_name}");
		$list = $this -> db -> get() -> result();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function find_not_manual_by_user_and_date($user_id, $date) {
		$this -> db -> where("is_manual", 0);
		$this -> db -> where("user_id", $user_id);
		$this -> db -> where("date", $date);
		$this -> db -> from("{$this->table_name}");
		$list = $this -> db -> get() -> result();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

}
?>
