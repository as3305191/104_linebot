<?php

class Update_shell extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	public function update_fishgame() {
		echo "Update FishGame: \n";
		$res = shell_exec("sudo /home/pennyapple/up_fishgame");
		echo $res;
	}
}
?>
