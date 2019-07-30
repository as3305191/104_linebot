<?php
class Quotes_record_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('quotes_record');

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
}
?>
