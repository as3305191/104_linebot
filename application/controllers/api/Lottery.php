<?php
class Lottery extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		// setup models
		$this -> load -> model('Lottery_tx_dao', 'l_tx_dao');
		$this -> load -> model('Fish_tab_lottery_dao', 'f_t_l_dao');

	}

	public function test() {
		echo "test";
	}

	// 期號列表
	public function tab_lottery_list() {
		$res = array();
		$res['success'] = TRUE;

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];

		$list = $this -> f_t_l_dao -> find_by_parameter_with_user_info($user_id);
		$res['list'] = $list;

		$this -> to_json($res);
	}

	// 我的摸彩券
	public function my_lottery_list() {
		$res = array();
		$res['success'] = TRUE;

		$payload = $this -> get_payload();
		$user_id = $payload['user_id'];
		$fish_tab_lottery_id = $this -> get_post('fish_tab_lottery_id');

		if(!empty($user_id)) {
			$list = $this -> l_tx_dao -> find_by_parameter(array('user_id' => $user_id,'fish_tab_lottery_id' => $fish_tab_lottery_id));
			foreach($list as $each){
				$id  = $each -> id;
				$open_lottery_tx_id  = $each -> open_lottery_tx_id;
				if($id == $open_lottery_tx_id){
					$each -> is_win = 1;
				}else{
					$each -> is_win = 0;
				}
			}
			$res['list'] = $list;
		} else {
			$res['error_msg'] = "缺少必要欄位";
		}
		$this -> to_json($res);
	}




}
?>
