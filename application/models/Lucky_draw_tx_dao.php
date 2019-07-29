<?php
class Lucky_draw_tx_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('lucky_draw_tx');

		$this -> alias_map = array(

		);
	}

	function sum_num_by_user($user_id) {
		$sql = "select sum(num) as sum_num from $this->table_name where user_id = $user_id ";
		$list = $this -> db -> query($sql) -> result();

		$sum_num = 0;
		if(count($list) > 0) {
			$sum_num = $list[0] -> sum_num;
			$sum_num = empty($sum_num) ? 0 : $sum_num;
		}
		return $sum_num;
	}
}
?>
