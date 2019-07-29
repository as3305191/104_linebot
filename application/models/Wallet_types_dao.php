<?php
class Wallet_types_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('wallet_types');
	}
}
?>
