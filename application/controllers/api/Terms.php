<?php
class Terms extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		$this -> load -> model('Terms_dao', 'terms_dao');
	}

	public function get_one($id) {
		$res['success'] = TRUE;
		$res['item'] = $this -> terms_dao -> find_by_id($id);
		$this -> to_json($res);
	}

	public function get_one_html($id, $v = '') {
		$data['title'] = $v;
		$data['item'] = $this -> terms_dao -> find_by_id($id);
		$this -> load -> view('terms', $data);
	}

}
?>
