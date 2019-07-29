<?php
class User_log_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('user_log');

		$this -> alias_map = array(
			'account' => '_m.account',
			'user_name' => '_m.user_name'
		);
	}


	function find_user_log($data, $is_count = FALSE) {

		$user_id = $data['user_id'];
		$start = $data['start'];
		$limit = $data['length'];

		// select
		$this -> db -> from("$this->table_name as _m");

		$this -> db -> select('_m.log_type');
		$this -> db -> select('_m.ip');
		$this -> db -> select('_m.create_time');


		if(!$is_count) {
			$this -> db -> limit($limit, $start);
		}
		$this -> db -> where('_m.user_id',$user_id);

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
