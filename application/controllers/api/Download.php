<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Download extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Pay_records_dao', 'dao');

		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Users_dao', 'u_dao');
		$this -> load -> model('Corp_dao', 'c_dao');

		//載入SDK(路徑可依系統規劃自行調整)
		include APPPATH . 'third_party/ECPay.Payment.Integration.php';
		include APPPATH . 'third_party/submit.class.php';
	}

	public function index() {
		$data = array();
		$this -> load -> view("download1", $data);
	}

  public function download1() {
		$data = array();
		$this -> load -> view("download1", $data);
	}


	public function upload_ipa() {
		$name = $_FILES['file']['name'];
		$tmp_name = $_FILES['file']['tmp_name'];
		$type = $_FILES['file']['type'];
		$size = $_FILES['file']['size'];

		if (!copy($tmp_name, "../download/gameapp.ipa")) {
		    echo "failed to copy $file...\n";
		}
		redirect("deploy");
	}
}
