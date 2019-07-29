<?php
class Baccarat_win_reward_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('baccarat_win_reward');

		$this -> alias_map = array(

		);
	}

}
?>
