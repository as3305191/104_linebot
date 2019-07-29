<?php
class Buy_records_status_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('buy_records_status');

		$this -> alias_map = array(

 		);
	}
}
?>
