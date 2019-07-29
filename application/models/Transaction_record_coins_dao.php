<?php
class Transaction_record_coins_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('transaction_record_coins');

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


	//
	function find_by_parameter($m){
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> join("products p", "p.id = _m.product_id ", "left");

		// select
		$this -> db -> select('_m.*');
		$this -> db -> select('p.product_name');

		if(!empty($m['sn'])){
			$this -> db -> where("_m.sn", $m['sn'] );
		}

		$query = $this -> db -> get();
		$list = $query -> result();

		return $list;
	}

	function buy_record_coins($data) {
		$dt = $data['dt'];
		$e_dt = $data['e_dt'];
		$station_id = $data['station_id'];
		$multiple = $data['multiple'];
		// $container_sn = $data['container_sn'];
		$bypass_101 = $data['bypass_101'];
		$type = isset($data['type']) ? $data['type'] : 1;
		$this -> db -> from("$this->table_name as _m");

		$this -> db -> select("u.nick_name as buyer");
		$this -> db -> select("p.product_name as product_name");
		$this -> db -> select("DATE_FORMAT(_m.create_time,'%Y-%m-%d') as create_time");
		$this -> db -> select("_m.total as total");
		$this -> db -> select("_m.number as num");
		$this -> db -> select("_m.status as status");
		$this -> db -> select("_m.amt as amt");

		$this -> db -> join("users u", "u.id = _m.shop_user_id", "left");
		$this -> db -> join("products p", "p.id = _m.product_id", "left");

		if(!empty($multiple)) { // 1
			$this -> db -> where("(_m.create_time >= '{$dt}' and _m.create_time <= '{$e_dt} 23:59:59' )");
		} else { // 0
			if(!empty($dt)) {
				$this -> db -> where("_m.create_time like '{$dt} %'");
			}
		}


		$list = $this -> db -> get() -> result();
		return $list;
	}

	function get_sum_amt_day($date) {
	$this -> db -> from("$this->table_name as _m");

	$this -> db -> select("if(sum(_m.total) is null, 0, sum(_m.total)) as coin");
	$this -> db -> where("create_time like '$date%'");

	$query = $this -> db -> get();
	$list = $query -> result();

	return $list[0];
	}

	function find_user_coins($data, $is_count = FALSE) {

		$user_id = $data['user_id'];
		$start = $data['start'];
		$limit = $data['length'];

		// select
		$this -> db -> from("$this->table_name as _m");

		$this -> db -> select('_m.status');
		$this -> db -> select('_m.number');
		$this -> db -> select('_m.total ');
		$this -> db -> select('p.product_name as name');

		$this -> db -> join("products p", "p.id = _m.product_id", "left");

		$this -> db -> order_by('_m.create_time');

		if(!$is_count) {
			$this -> db -> limit($limit, $start);
		}

		$this -> db -> where('shop_user_id',$user_id);


		// query results
		if(!$is_count) {
			$query = $this -> db -> get();
			return $query -> result();
		} else {
			return $this -> db -> count_all_results();
		}

	}
}
?>
