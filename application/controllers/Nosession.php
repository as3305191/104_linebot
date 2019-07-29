<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nosession extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
	}

	public function index() {
		$data = array();
		show_404();
		// $this -> load -> view('nosession', $data);
	}
	
	public function my404() {
		$data = array();
		echo "404";
		// $this -> load -> view('nosession', $data);
	}

	public function close() {
		$data = array();

		$this -> load -> view('nosession_close', $data);
	}

}
