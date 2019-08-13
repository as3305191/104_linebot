<?php
class Game_pool_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('game_pool');

		$this -> alias_map = array(

		);
	}

	function find_check_in($user_id,$getDate) {
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select('_m.*');

		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('tx_id', $user_id);

		$this -> db -> where('tx_type', 'check_in_reward');
		$this -> db -> where("create_time like '$getDate%'");

		$query = $this -> db -> get();
		$list = $query -> result();
		return $list;
	}

	function get_sum_ntd($last_id) {
		$this -> db -> select("sum(ntd_change) as sntd");
		$this -> db -> where('tx_id<=',$last_id);
		$list = $this -> find_all();
		if(count($list) > 0) {
			$itm = $list[0];
			return (!empty($itm -> sntd) ? $itm -> sntd : 0);
		}
		return 0;
	}
	function get_sum_ntd1($last_id) {
		$this -> db -> select("sum(ntd_change) as sntd");
		$this -> db -> where('id<',$last_id+1);
		$list = $this -> find_all();
		if(count($list) > 0) {
			$itm = $list[0];
			return (!empty($itm -> sntd) ? $itm -> sntd : 0);
		}
		return 0;
	}

	function get_current_point() {
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select('_m.current_point');

		$this -> db -> where('current_point<>',0.00000000);
		$this -> db -> order_by('id','desc');

		$query = $this -> db -> get();
		$list = $query -> result();
		return $list[0];
	}

	function get_current_point1($last_id) {
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select('_m.current_point');

		$this -> db -> where('current_point<>',0.00000000);
		$this -> db -> where('id',$last_id);

		$query = $this -> db -> get();
		$list = $query -> result();
		return $list[0];
	}

	function get_current_ntd() {
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select('_m.current_ntd');

		$this -> db -> where('current_ntd<>',0.00000000);
		$this -> db -> order_by('id','desc');

		$query = $this -> db -> get();
		$list = $query -> result();
		return $list[0];
	}

	function get_sum_pool_amt($last_id,$temporarily_bet,$type) {
		$this -> db -> select("sum(pool_amt) as pool_amt");
		$this -> db -> where('id<=',$last_id);
		$this -> db -> where('bet_type',$temporarily_bet);
		if($type==1){
			$this -> db -> where('type',$type);
		}
		if($type==0){
			$this -> db -> where('type',$type);
		}
		if($type==3){
			$this -> db -> where('type',0);
		}
		$list = $this -> find_all();
		if(count($list) > 0) {
			$itm = $list[0];
			return (!empty($itm -> pool_amt) ? $itm -> pool_amt : 0);
		}
		return 0;
	}

	function get_all_pool_amt() {
		$this -> db -> select("sum(pool_amt) as pool_amt");
		$list = $this -> find_all();
		if(count($list) > 0) {
			$itm = $list[0];
			return (!empty($itm -> pool_amt) ? $itm -> pool_amt : 0);
		}
		return 0;
	}
	function find_all_pool_now($data, $is_count = FALSE) {
		$start = $data['start'];
		$limit = $data['length'];
		// select
		$this -> db -> from("$this->table_name as _m");

		$this -> db -> select("sum(pool_amt) as pool_amt");
		// $this -> db -> select("type");
		$this -> db -> select("bet_type");
		$this -> db -> where('type',0);


		if(!$is_count) {
			$this -> db -> limit($limit, $start);
		}

		$this -> db -> group_by('bet_type');
		// $this -> db -> order_by('bet_type','asc');

		// $this -> db -> group_by('type');

		// query results
		if(!$is_count) {
			$query = $this -> db -> get();
			return $query -> result();
		} else {
			return $this -> db -> count_all_results();
		}

	}

	function sum_amt_by_type($bet_type,$type) {
		$this -> db -> select("sum(pool_amt) as amt");
		// $this -> db -> select("bet_type");

		$this -> db -> where("type", $type);
		$this -> db -> where("bet_type", $bet_type);
		// $this -> db -> group_by('bet_type');
		$this -> db -> order_by('bet_type','asc');

		$list = $this -> find_all();
		if(!empty($list)) {
			$item = $list[0];
			if(!empty($item -> amt)) {
				return $item -> amt;
			}
		}
		return 0;

	}
}
?>
