<?php
class Add_coin_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('add_coin');

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

	function find_d_q($Date) {
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select('_m.*');
		$this -> db -> where('date', $Date);
		$query = $this -> db -> get();
		$list = $query -> result();
		return $list[0];
	}

	function find_last_d_q() {
		$this -> db -> from("$this->table_name as _m");
		$this -> db -> select('_m.*');
		$this -> db -> order_by('create_time', 'desc');
		$query = $this -> db -> get();
		$list = $query -> result();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}

	function sum_all_ntd() {
		$this -> db -> select("sum(ntd) as samt");
		$this -> db -> from($this -> table_name);
		$list = $this -> db -> get() -> result();
		if(count($list) > 0) {
			$item = $list[0];
			return (!empty($item -> samt) ? $item -> samt : 0);
		}
		return 0;
	}
}
?>
