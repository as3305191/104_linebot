<?php
class Advance_play_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('advance_play');

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

	function find_rand($get_all,$type) {
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select('_m.*');
		$this -> db -> order_by('id', 'RANDOM');
		if($type==1){
			$this -> db -> where('_m.type',$type);
			$this -> db -> where('_m.total_multiple<',floatval($get_all));
		}
		if($type==0){
			$this -> db -> where('_m.type',$type);
			$this -> db -> where('_m.total_multiple<',floatval($get_all));
		}
		if($type==3){
			$this -> db -> where('_m.type',0);
			$this -> db -> where('_m.total_multiple',0.00000000);
		}
    $this -> db -> limit(1);
		$query = $this -> db -> get();
		$list = $query -> result();
		return $list;
	}
}
?>
