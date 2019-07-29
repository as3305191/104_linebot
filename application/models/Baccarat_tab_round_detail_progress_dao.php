<?php
class Baccarat_tab_round_detail_progress_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('baccarat_tab_round_detail_progress');

		$this -> alias_map = array(
			// 'corp_id' => '_m.corp_id'
			// 'pay_type_name' => 'pt.type_name'
 		);
	}

}
?>
