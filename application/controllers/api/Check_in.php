<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Check_in extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Post_log_dao', 'post_log_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');

		$this -> load -> model('Transfer_coin_dao', 'tc_dao');
		$this -> load -> model('Com_tx_dao', 'ctx_dao');

		$this -> load -> model('Baccarat_tab_round_detail_dao', 'btrd_dao');
		$this -> load -> model('Baccarat_tab_round_dao', 'btr_dao');
    $this -> load -> model('Products_items_dao', 'products_items_dao');
		$this -> load -> model('Product_strengthen_dao', 'product_strengthen_dao');
		$this -> load -> model('Check_in_dao', 'check_in_dao');
		$this -> load -> model('Fish_login_reward_dao', 'fish_lr_dao');
		$this -> load -> model('Fish_online_dao', 'fish_online');
		$this -> load -> model('Fish_daily_task_dao', 'fish_d_t_dao');

	}



	public function do_check_in() {
		$res = array();
		$payload = $this -> get_payload();
		$user_id = $payload["user_id"];
		$getDate= date("Y-m-d");
		$get_time= date("i");
		$get_h= date("H");
		$get_s= date("s");

		$time=$get_h*3600+$get_time*60+$get_s;
		$idata['user_id']=$user_id;
		$idata['date']=$getDate;

		$count_find_check = $this-> check_in_dao ->find_check($user_id,$getDate);

		$select_time = $this-> check_in_dao ->find_time($user_id,$getDate);

		$time1 = 0;
		if(!empty($select_time)) {
			$select_time1 = date('i',strtotime($select_time->create_time));
			$select_hour = date('H',strtotime($select_time->create_time));
			$select_s = date('s',strtotime($select_time->create_time));
			$time1=$select_hour*3600+$select_time1*60+$select_s;
		}

		$tx = array();
		$tx['corp_id'] = $payload["corp_id"]; // corp id
		$tx['user_id'] = $user_id; // user id
		$amt = 20000;
		$tx['amt'] = $amt;
		$tx['tx_type'] = "check_in_reward"; // 自訂類型
		$tx['tx_id'] = $user_id; // 資料來源ID, 可以放入最後一次check_in的id
		$tx['brief'] = "簽到獎勵 {$amt}"; // 文字描述，給自己看的

		if(!empty($user_id)){
			if(!empty($count_find_check) && !empty($select_time)){
				if($count_find_check[0]->total<9){
					if($time-$time1>=300 ){
						$this -> check_in_dao -> insert($idata);

						// wallet tx
						$this -> wtx_dao -> insert($tx); // Wallet_tx_dao

						// wtx
						$tx1 = array();
						$tx1['corp_id'] = $payload["corp_id"];
						$tx1['amt'] = -$amt;
						$tx1['income_type'] = "check_in_reward";
						$tx1['income_id'] = $user_id;
						$tx1['note'] = "頒發給{$user_id}簽到獎勵 {$amt}";
						$this -> ctx_dao -> insert($tx1);

						$res['message']="成功";
					} else{
						$res['message']="距離上次簽到還沒5分鐘";
					}
				} else if($count_find_check[0]->total==9){
					if($time-$time1>=300 ){
						$this -> check_in_dao -> insert($idata);

						// wallet tx
						$this -> wtx_dao -> insert($tx); // Wallet_tx_dao

						// wtx
						$tx1 = array();
						$tx1['corp_id'] = $payload["corp_id"];
						$tx1['amt'] = -$amt;
						$tx1['income_type'] = "check_in_reward";
						$tx1['income_id'] = $user_id;
						$tx1['note'] = "頒發給{$user_id}簽到獎勵 {$amt}";
						$this -> ctx_dao -> insert($tx1);

						$res['message']="第10次簽到";
					} else{
						$res['message']="距離上次簽到還沒5分鐘";
					}
				} else {
					$res['message']="您已經簽到10次啦！";
				}
			} else{
				$this -> check_in_dao -> insert($idata);

				// wallet tx
				$this -> wtx_dao -> insert($tx); // Wallet_tx_dao

				// wtx
				$tx1 = array();
				$tx1['corp_id'] = $payload["corp_id"];
				$tx1['amt'] = -$amt;
				$tx1['income_type'] = "check_in_reward";
				$tx1['income_id'] = $user_id;
				$tx1['note'] = "頒發給{$user_id}簽到獎勵 {$amt}";
				$this -> ctx_dao -> insert($tx1);
				$res['message']="本日首次簽到";
			}
		}
		$this -> to_json($res);
	}

	public function check_in_list() {
		$res = array();
		$payload = $this -> get_payload();
		$user_id = $payload["user_id"];
		$getDate = date("Y-m-d");

		$user = $this -> users_dao -> find_by_id($user_id);
		if(!empty($user)) {

			$count_find_check = $this-> check_in_dao ->find_check($user_id,$getDate);
			$list = $this-> check_in_dao ->find_list($user_id,$getDate);

			$rem_sec = 0;
			$ecllapsed = 0;
			$total = 0;
			if(!empty($list) && !empty($count_find_check)){
				$total = $count_find_check[0]->total;

				if(count($list) > 0) {
					$last = $list[count($list) - 1];
					$ecllapsed = time() - strtotime($last -> create_time);
					$rem_sec = 5 * 60 - $ecllapsed;
					if($rem_sec < 0) {
						$rem_sec = 0;
					}
				}
			}
			$res['total'] = "{$total}";
			$res['check_in_list'] = $list;
			$res['ecllapsed'] = "{$ecllapsed}";
			$res['remaining'] = "{$rem_sec}";
		} else {
			$res['error_msg'] = "缺少必須欄位";
		}
		$this -> to_json($res);

	}

	public function login_reward() {
		$res = array();
		$payload = $this -> get_payload();
		$user_id = $payload["user_id"];
		$day_of_week = date("w");
		$getDate= date("Y-m-d");
		$idata['day_of_week']=$day_of_week;
		$idata['login_date']=$getDate;
		$idata['user_id']=$user_id;

		$receive = $this -> get_post("receive");
		$u_data['is_receive']=1;


		$idata1['user_id']=$user_id;

		if(!empty($user_id)){
			$is_login = $this-> fish_lr_dao -> find_login($user_id,$getDate);
			if(!empty($is_login)){
				if(!empty($receive)&&$is_login->is_receive==0){
					$this-> fish_lr_dao -> update_by($u_data,id,$is_login->id);
					$tx = array();
					$tx['corp_id'] = $payload["corp_id"]; // corp id
					$tx['user_id'] = $user_id;
					$tx['tx_type'] = "fish_login_reward";
					$tx['tx_id'] = $is_login->id; // 股東收入
					$tx['brief'] = "會員 {$user_id} 領取獎勵";

					$tx1 = array();
					$tx1['corp_id'] = $payload["corp_id"];
					$tx1['income_type'] = "fish_login_reward";
					$tx1['income_id'] = $user_id;
					$tx1['note'] = "頒發給{$user_id}登入獎勵";

					switch ($day_of_week)
						{
						case "0":
							$tx['amt'] = 50000;
							$this -> wtx_dao -> insert($tx);
							$tx1['amt'] = -50000;
							$this -> ctx_dao -> insert($tx1);
							$res['message0'] = "領取成功";
						  break;
						case "1":
						$tx['amt'] = 20000;
						$this -> wtx_dao -> insert($tx);
						$tx1['amt'] = -20000;
						$this -> ctx_dao -> insert($tx1);
						$res['message1'] = "領取成功";
						  break;
						case "2":
						$idata1['product_id']=23;
						$this -> products_items_dao -> insert($idata1);
						$res['message2'] = "領取成功";
						  break;
						case "3":
						$tx['amt'] = 20000;
						$this -> wtx_dao -> insert($tx);
						$tx1['amt'] = -20000;
						$this -> ctx_dao -> insert($tx1);
						$res['message3'] = "領取成功";
						  break;
						case "4":
						$idata1['product_id']=13;
						$this -> products_items_dao -> insert($idata1);
						$res['message4'] = "領取成功";

							break;
						case "5":
						$idata1['product_id']=14;
						$this -> products_items_dao -> insert($idata1);
						$res['message5'] = "領取成功";

							break;
						case "6":
						$idata1['product_id']=15;
						$this -> products_items_dao -> insert($idata1);
						$res['message6'] = "領取成功";
							break;
						}
				}
			} else{
				if(!empty($receive)){
					$idata['is_receive']=1;
					$this-> fish_lr_dao -> insert($idata);
					$tx = array();
					$tx['corp_id'] = $payload["corp_id"]; // corp id
					$tx['user_id'] = $user_id;
					$tx['tx_type'] = "fish_login_reward";
					$tx['tx_id'] = $is_login->id; // 股東收入
					$tx['brief'] = "會員 {$user_id} 領取獎勵";

					$tx1 = array();
					$tx1['corp_id'] = $payload["corp_id"];
					$tx1['income_type'] = "fish_login_reward";
					$tx1['income_id'] = $user_id;
					$tx1['note'] = "頒發給{$user_id}登入獎勵";

					switch ($day_of_week)
						{
						case "0":
							$tx['amt'] = 50000;
							$this -> wtx_dao -> insert($tx);
							$tx1['amt'] = -50000;
							$this -> ctx_dao -> insert($tx1);
							$res['message0'] = "領取成功";
							break;
						case "1":
						$tx['amt'] = 20000;
						$this -> wtx_dao -> insert($tx);
						$tx1['amt'] = -20000;
						$this -> ctx_dao -> insert($tx1);
						$res['message1'] = "領取成功";
							break;
						case "2":
						$idata1['product_id']=23;
						$this -> products_items_dao -> insert($idata1);
						$res['message2'] = "領取成功";
							break;
						case "3":
						$tx['amt'] = 20000;
						$this -> wtx_dao -> insert($tx);
						$tx1['amt'] = -20000;
						$this -> ctx_dao -> insert($tx1);
						$res['message3'] = "領取成功";
							break;
						case "4":
						$idata1['product_id']=13;
						$this -> products_items_dao -> insert($idata1);
						$res['message4'] = "領取成功";

							break;
						case "5":
						$idata1['product_id']=14;
						$this -> products_items_dao -> insert($idata1);
						$res['message5'] = "領取成功";

							break;
						case "6":
						$idata1['product_id']=15;
						$this -> products_items_dao -> insert($idata1);
						$res['message6'] = "領取成功";
							break;
						}
				} else{
					$this-> fish_lr_dao -> insert($idata);
					$res['message'] = "登入成功";
				}
			}
		}
		$this -> to_json($res);
	}

	public function on_line_reward() {
		$res = array();
		$payload = $this -> get_payload();
		$user_id = $payload["user_id"];
		$getDate = date("Y-m-d");
		$receive_10 = $this -> get_post("receive_10");
		$receive_30 = $this -> get_post("receive_30");
		$receive_60 = $this -> get_post("receive_60");
		$receive_90 = $this -> get_post("receive_90");
		$receive_120 = $this -> get_post("receive_120");
		$receive_180 = $this -> get_post("receive_180");

		$idata['user_id']=$user_id;
		$idata['online_date']=$getDate;

		$find_online = $this -> fish_online -> find_on_line_re($user_id,$getDate);

		$tx = array();
		$tx['corp_id'] = $payload["corp_id"]; // corp id
		$tx['user_id'] = $user_id;
		$tx['tx_type'] = "fish_online";
		$tx['tx_id'] = $find_online->id;
		$tx['brief'] = "會員 {$user_id} 領取在線獎勵";

		$tx1 = array();
		$tx1['corp_id'] = $payload["corp_id"];
		$tx1['income_type'] = "fish_online";
		$tx1['income_id'] = $user_id;
		$tx1['note'] = "頒發給{$user_id}在線獎勵";

		if(empty($find_online)){
			$this-> fish_online ->insert($idata);
			$res['message120'] = "剛上線";
		} else{
			if(!empty($receive_10)){
				if($find_online->is_receive_10==0){
					if($find_online->online_seconds>=10*60)	{
						$tx['amt'] = 10000;
						$tx1['amt'] = -10000;
						$this-> fish_online -> update_by(array("is_receive_10"=>1),id,$find_online->id);
						$this -> wtx_dao -> insert($tx);
						$this -> ctx_dao -> insert($tx1);
						$res['message10'] = "領取成功";

					}
				}
			}

			if(!empty($receive_30)){
				if($find_online->is_receive_30==0){
					if($find_online->online_seconds>=30*60)	{
						$tx['amt'] = 20000;
						$tx1['amt'] = -20000;
						$this-> fish_online -> update_by(array("is_receive_30"=>1),id,$find_online->id);
						$this -> wtx_dao -> insert($tx);
						$this -> ctx_dao -> insert($tx1);
						$res['message30'] = "領取成功";

					}
				}
			}
			if(!empty($receive_60)){
				if($find_online->is_receive_60==0){
					if($find_online->online_seconds>=60*60)	{
						$tx['amt'] = 30000;
						$tx1['amt'] = -30000;
						$this-> fish_online -> update_by(array("is_receive_60"=>1),id,$find_online->id);
						$this -> wtx_dao -> insert($tx);
						$this -> ctx_dao -> insert($tx1);
						$res['message60'] = "領取成功";

					}
				}
			}
			if(!empty($receive_90)){
				if($find_online->is_receive_90==0){
					if($find_online->online_seconds>=90*60)	{
						$tx['amt'] = 40000;
						$tx1['amt'] = -40000;
						$this-> fish_online -> update_by(array("is_receive_90"=>1),id,$find_online->id);
						$this -> wtx_dao -> insert($tx);
						$this -> ctx_dao -> insert($tx1);
						$res['message90'] = "領取成功";
					}
				}
			}
			if(!empty($receive_120)){
				if($find_online->is_receive_120==0){
					if($find_online->online_seconds>=120*60)	{
						$tx['amt'] = 50000;
						$tx1['amt'] = -50000;
						$this-> fish_online -> update_by(array("is_receive_120"=>1),id,$find_online->id);
						$this -> wtx_dao -> insert($tx);
						$this -> ctx_dao -> insert($tx1);
						$res['message120'] = "領取成功";
					}
				}
			}
			if(!empty($receive_180)){
				if($find_online->is_receive_180==0){
					if($find_online->online_seconds>=180*60)	{
						$this-> fish_online -> update_by(array("is_receive_180"=>1),id,$find_online->id);
						$idata_1['user_id']=$user_id;
						$idata_1['product_id']=23;
						$this -> products_items_dao -> insert($idata_1);
						$res['message180'] = "領取成功";
					}
				}
			}
		}
		$this -> to_json($res);
	}

	public function day_missions() {
		$res = array();
		$payload = $this -> get_payload();
		$user_id = $payload["user_id"];
		$getDate = date("Y-m-d");
		$is_fb = $this -> get_post("is_fb");
		$is_20fish = $this -> get_post("is_20fish");
		$is_strengthen = $this -> get_post("is_strengthen");
		$is_lottery = $this -> get_post("is_lottery");
		$is_chat = $this -> get_post("is_chat");
		$is_fish_king = $this -> get_post("is_fish_king");

		$idata['user_id']=$user_id;
		$idata['online_data']=$getDate;
		$tx = array();
		$tx['corp_id'] = $payload["corp_id"]; // corp id
		$tx['user_id'] = $user_id;
		$tx['tx_type'] = "Fish_daily_task";
		$tx['tx_id'] = $find_day_mission->id;
		$tx['brief'] = "會員 {$user_id} 領取每日任務獎勵";

		$tx1 = array();
		$tx1['corp_id'] = $payload["corp_id"];
		$tx1['income_type'] = "Fish_daily_task";
		$tx1['income_id'] = $user_id;
		$tx1['note'] = "頒發給{$user_id}每日任務獎勵";

		$find_day_mission = $this-> fish_d_t_dao -> find_day_mission($user_id,$getDate);
		if(empty($find_day_mission)){
			$this->fish_d_t_dao->insert($idata);
			$res['message'] = "任務未完成";
		} else{
			if(!empty($is_fb)){
				if($find_day_mission->is_fb==0){
						$tx['amt'] = 20000;
						$tx1['amt'] = -20000;
						$this-> fish_d_t_dao -> update_by(array("is_fb"=>1),id,$find_day_mission->id);
						$this -> wtx_dao -> insert($tx);
						$this -> ctx_dao -> insert($tx1);
						$res['message_fb'] = "領取成功";
				}
			}
			if(!empty($is_20fish)){
				if($find_day_mission->is_20fish==0){
						$tx['amt'] = 10000;
						$tx1['amt'] = -10000;
						$this-> fish_d_t_dao -> update_by(array("is_20fish"=>1),id,$find_day_mission->id);
						$this -> wtx_dao -> insert($tx);
						$this -> ctx_dao -> insert($tx1);
						$res['message_20fish'] = "領取成功";
				}
			}
			if(!empty($is_strengthen)){
				if($find_day_mission->is_strengthen==0){
						$tx['amt'] = 20000;
						$tx1['amt'] = -20000;
						$this-> fish_d_t_dao -> update_by(array("is_strengthen"=>1),id,$find_day_mission->id);
						$this -> wtx_dao -> insert($tx);
						$this -> ctx_dao -> insert($tx1);
						$res['message_strengthen'] = "領取成功";
				}
			}
			if(!empty($is_lottery)){
				if($find_day_mission->is_lottery==0){
						$tx['amt'] = 20000;
						$tx1['amt'] = -20000;
						$this-> fish_d_t_dao -> update_by(array("is_lottery"=>1),id,$find_day_mission->id);
						$this -> wtx_dao -> insert($tx);
						$this -> ctx_dao -> insert($tx1);
						$res['message_lottery'] = "領取成功";
				}
			}
			if(!empty($is_chat)){
				if($find_day_mission->is_chat==0){
						$tx['amt'] = 10000;
						$tx1['amt'] = -10000;
						$this-> fish_d_t_dao -> update_by(array("is_chat"=>1),id,$find_day_mission->id);
						$this -> wtx_dao -> insert($tx);
						$this -> ctx_dao -> insert($tx1);
						$res['message_chat'] = "領取成功";
				}
			}
			if(!empty($is_fish_king)){
				if($find_online->is_fish_king==0){
					$this-> fish_d_t_dao -> update_by(array("is_fish_king"=>1),id,$find_day_mission->id);
					$idata_1['user_id']=$user_id;
					$idata_1['product_id']=13;
					$idata_2['user_id']=$user_id;
					$idata_2['product_id']=14;
					$idata_3['user_id']=$user_id;
					$idata_3['product_id']=15;
					$this -> products_items_dao -> insert($idata_1);
					$this -> products_items_dao -> insert($idata_2);
					$this -> products_items_dao -> insert($idata_3);

					$res['message_fish_king'] = "領取成功";
				}
			}
		}
		$this -> to_json($res);
	}
}
