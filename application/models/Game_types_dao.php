<?php
class Game_types_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('game_types');

		$this -> alias_map = array(

 		);
	}

}
?>
