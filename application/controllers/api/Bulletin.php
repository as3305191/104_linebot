<?php
class Bulletin extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		// setup models
		$this -> load -> model('Bulletin_dao', 'bulletin_dao');
	}

	public function test() {
		echo "test";
	}

	public function list_all() {
		$res = array();
		$res['success'] = TRUE;
		$ope_pct = 1;

		$corp_id = $this -> get_post('corp_id');
		$type_name = $this -> get_post('type_name');

		$type_name_arr = array(
			'news',
			'bussiness',
			'activity',
			'cms'
		);
		if(empty($corp_id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			if(in_array($type_name, $type_name_arr)) {
				$list = $this -> bulletin_dao -> find_all_order_by_type_name($type_name);
				foreach($list as $each) {
					$each -> image_url = "";
					if(!empty($each -> image_id)) {
						$each -> image_url = base_url("api/images/get/{$each->image_id}");
					}
				}
				$res['type_name'] = $type_name;
				$res['list'] = $list;
			} else {
				$res['error_msg'] = "類型錯誤";
			}
		}
		$this -> to_json($res);
	}

	public function show_status() {
		$res = array();
		$res['success'] = TRUE;
		$ope_pct = 1;

		$user_id = $this -> get_post('user_id');

		if(empty($user_id)) {
			$res['error_msg'] = "缺少必要欄位";
		} else {
			$user = $this -> users_dao -> find_by_id($user_id);
			if($user -> lp_always_on == 1) {
				$res['lp_always_on'] = 1;
				$res['lp_on'] = TRUE;
			} else {
				if(!empty($user -> lp_due_date) && (strtotime(date('Y-m-d')) <= strtotime($user -> lp_due_date))) {
					// show due date
					$res['due_date'] = $user -> lp_due_date;
					$res['lp_on'] = TRUE;
				} else {
					$snum = $this -> lp_tx_dao -> sum_num_by_user($user_id);
					if($snum > 0) {
						$res['num'] = $snum;
						$res['lp_on'] = TRUE;
					} else {
						$res['lp_on'] = FALSE;
					}
				}
			}
		}
		$this -> to_json($res);
	}
}
?>
