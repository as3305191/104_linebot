<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Line extends MY_Base_Controller {

	function __construct() {
		parent::__construct();


	}

	public function iagree() {
    $data = array();
    $this -> load -> view('line/iagree');
    }

	function phone_binding01() {
    $data = array();
    $this -> load -> view('line/phone_binding01');
			}

	function phone_binding02() {
    $data = array();
    $this -> load -> view('line/phone_binding02');
	}

  function phone_binding03() {
    $data = array();
    $this -> load -> view('line/phone_binding03');
	}

  function app_download() {
    $data = array();
    $this -> load -> view('line/app_download');
	}
}
