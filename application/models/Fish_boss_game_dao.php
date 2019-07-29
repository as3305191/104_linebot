<?php
class Fish_boss_game_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('fish_boss_game');
	}
}
?>
