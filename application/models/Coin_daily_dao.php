<?php
class Coin_daily_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('coin_daily');

		$this -> alias_map = array(

		);
	}

	function find_by_currency_and_date($code, $date) {
		$this -> db -> where('currency', $code);
		$this -> db -> where('date', $date);
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		}
		return NULL;
	}
}
?>
