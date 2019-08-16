<?php
class Game_list extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Game_list_dao', 'gl_dao');
		$this -> load -> model('Game_tiger_dao', 'game_tiger_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Quotes_record_dao', 'q_r_dao');

		$this -> load -> model('Com_tx_dao', 'ctx_dao');
		$this -> load -> model('Advance_play_dao', 'advance_play_dao');
		$this -> load -> model('Config_dao', 'config_dao');
		$this -> load -> model('Game_pool_dao', 'game_pool_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');

		$this -> load -> model('Transfer_gift_allocation_dao', 'tsga_dao');


	}

	public function testtest() {
		$list = $this -> advance_play_dao -> find_rand();


		$this -> to_json($list[0]->total_multiple);
	}


	public function go($game_id, $user_id) {
		$user = $this -> users_dao -> find_by_id($user_id);
		$game = $this -> gl_dao -> find_by_id($game_id);
		if(empty($user)) {
			echo "查無使用者";
			return;
		}
		if(empty($game)) {
			echo "查無此遊戲";
			return;
		}
		if($game -> is_wang == 1) {
			$this -> go_wang($game, $user);
		} else {
			if($game -> id == 1) {
				redirect("https://wa-lotterygame.com/games/{$game->file_path}?user_id={$user_id}&tab_id={$user->corp_id}&hall_id=0");
			} else {
				redirect("https://wa-lotterygame.com/games/{$game->file_path}?corp_id={$user->corp_id}&user_id={$user_id}");
			}
		}
	}

	public function go_wang($game, $user) {
		$res = array('success' => TRUE);

		if(!empty($user)) {
			$corp = $this -> corp_dao -> find_by_id($user -> corp_id);
			$params = array(
				'SessionId' => "{$user->wang_session_id}",
				'GameId' => "$game->wang_game_id",
			);
			$res['params'] = $params;

			$params = json_encode($params, JSON_NUMERIC_CHECK);
			$output = py_des3_encode($params);

			$p = array();
			$p["Params"] = $output;
			$p["AccessToken"] = $corp -> wang_token;
			$n_res = $this -> curl -> simple_post(WANG_URL . "RedirectToGame", $p);
			$res['n_res'] = json_decode($n_res);
			$res = json_decode($n_res);
			if($res -> ErrorCode == 0) {
				$res = py_des3_decode($res -> Data);
				$obj = json_decode($res);
				redirect($obj -> RedirectUrl);
			} else {
				$this -> to_json($res);
			}
			return;
		} else {
			$res['error_msg'] = "查無使用者";
 		}

		$this -> to_json($res);
	}

	public function status() {
		$res = array();
		$res['list'] = $this -> gl_dao -> find_all();
		$this -> to_json($res);
	}

	public function status_by_corp() {
		$res = array("success" => TRUE);
		$this -> gl_dao -> check_clone();

		$corp_id = $this -> get_post('corp_id');
		if(!empty($corp_id)) {
			$res['list'] = $this -> gl_dao -> find_all_by_corp($corp_id);
		} else {
			$res['error_msg'] = "缺少corp_id";
		}

		$this -> to_json($res);
	}

	public function game_tiger() {
		// $res = array("success" => TRUE);
		$res = array();
		$temporarily_bet = $this -> get_post('bet');
		// $temporarily_bet = 8;

		$bet = $temporarily_bet/8;
		$user_id = $this -> get_post('user_id');
		// $user_id =524;

			// 保證只有一組在執行
			// $key_id = $this -> game_tiger_dao -> get_key_id();
			// $un_done_list = array();
			// do {
			// 	$un_done_list = $this -> game_tiger_dao -> get_un_done_list($key_id);
			// } while(count($un_done_list) > 0);
			// ------------------------- start
		$config = $this -> config_dao -> find_by_id(1);//設定%的地方
		$pool_normal_pct = floatval($config -> normal_pct);//一般彩池
		$pool_overall_pct = floatval($config -> overall_pct);//全盤彩池
		$pool_cross_pct = floatval($config -> cross_pct);//跨區彩池

		$multiple_normal = floatval($pool_normal_pct)*$temporarily_bet;
		$multiple_overall = floatval($pool_overall_pct)*$temporarily_bet;
		$multiple_cross = floatval($pool_cross_pct)*$temporarily_bet;

		$company3 = floatval($config -> com_pct)*$temporarily_bet;//公司3％

		$idata_00['bet_type']="40";
		$idata_00['pool_amt']=$temporarily_bet*floatval(0.01);
		$idata_00['type']=0;
		$idata_00['type_status']="100-199倍";
		$this -> game_pool_dao -> insert($idata_00);//

		$idata_00['bet_type']="0.8";
		$idata_00['pool_amt']=$temporarily_bet*floatval(0.01);
		$idata_00['type']=0;
		$idata_00['type_status']="100-199倍";
		$this -> game_pool_dao -> insert($idata_00);//

		$idata_00['bet_type']=$temporarily_bet;
		$idata_00['pool_amt']=$temporarily_bet*floatval(0.25);
		$idata_00['type']=0;
		$idata_00['type_status']="10-50倍";
		$this -> game_pool_dao -> insert($idata_00);//

		$idata_01['bet_type']=$temporarily_bet;
		$idata_01['pool_amt']=$temporarily_bet*floatval(0.23);
		$idata_01['type']=0;
		$idata_01['type_status']="1-99倍";
		$this -> game_pool_dao -> insert($idata_01);

		$idata_02['bet_type']=$temporarily_bet;
		$idata_02['pool_amt']=$temporarily_bet*floatval(0.2);
		$idata_02['type']=0;
		$idata_02['type_status']="100-199倍";
		$this -> game_pool_dao -> insert($idata_02);

		$idata_03['bet_type']=$temporarily_bet;
		$idata_03['pool_amt']=$temporarily_bet*floatval(0.1);
		$idata_03['type']=0;
		$idata_03['type_status']="200-299倍";
		$this -> game_pool_dao -> insert($idata_03);

		$idata_04['bet_type']=$temporarily_bet;
		$idata_04['pool_amt']=$temporarily_bet*floatval(0.05);
		$idata_04['type']=0;
		$idata_04['type_status']="300-399倍";
		$this -> game_pool_dao -> insert($idata_04);

		$idata_10['bet_type']=$temporarily_bet;
		$idata_10['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_10['type']=1;
		$idata_10['type_status']="任意7_seven";
		$this -> game_pool_dao -> insert($idata_10);

		$idata_11['bet_type']=$temporarily_bet;
		$idata_11['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_11['type']=1;
		$idata_11['type_status']="任意7_bar";
		$this -> game_pool_dao -> insert($idata_11);

		$idata_12['bet_type']=$temporarily_bet;
		$idata_12['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_12['type']=1;
		$idata_12['type_status']="任意7_medal";
		$this -> game_pool_dao -> insert($idata_12);

		$idata_13['bet_type']=$temporarily_bet;
		$idata_13['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_13['type']=1;
		$idata_13['type_status']="任意7_bell";
		$this -> game_pool_dao -> insert($idata_13);

		$idata_14['bet_type']=$temporarily_bet;
		$idata_14['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_14['type']=1;
		$idata_14['type_status']="任意7_watermelon";
		$this -> game_pool_dao -> insert($idata_14);

		$idata_15['bet_type']=$temporarily_bet;
		$idata_15['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_15['type']=1;
		$idata_15['type_status']="任意7_grape";
		$this -> game_pool_dao -> insert($idata_15);

		$idata_16['bet_type']=$temporarily_bet;
		$idata_16['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_16['type']=1;
		$idata_16['type_status']="任意7_orange";
		$this -> game_pool_dao -> insert($idata_16);

		$idata_17['bet_type']=$temporarily_bet;
		$idata_17['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_17['type']=1;
		$idata_17['type_status']="任意7_cherry";
		$this -> game_pool_dao -> insert($idata_17);

		$idata_20['bet_type']=$temporarily_bet;
		$idata_20['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_20['type']=1;
		$idata_20['type_status']="任意8_seven";
		$this -> game_pool_dao -> insert($idata_20);

		$idata_21['bet_type']=$temporarily_bet;
		$idata_21['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_21['type']=1;
		$idata_21['type_status']="任意8_bar";
		$this -> game_pool_dao -> insert($idata_21);

		$idata_22['bet_type']=$temporarily_bet;
		$idata_22['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_22['type']=1;
		$idata_22['type_status']="任意8_medal";
		$this -> game_pool_dao -> insert($idata_22);

		$idata_23['bet_type']=$temporarily_bet;
		$idata_23['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_23['type']=1;
		$idata_23['type_status']="任意8_bell";
		$this -> game_pool_dao -> insert($idata_23);

		$idata_24['bet_type']=$temporarily_bet;
		$idata_24['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_24['type']=1;
		$idata_24['type_status']="任意8_watermelon";
		$this -> game_pool_dao -> insert($idata_24);

		$idata_25['bet_type']=$temporarily_bet;
		$idata_25['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_25['type']=1;
		$idata_25['type_status']="任意8_grape";
		$this -> game_pool_dao -> insert($idata_25);

		$idata_26['bet_type']=$temporarily_bet;
		$idata_26['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_26['type']=1;
		$idata_26['type_status']="任意8_orange";
		$this -> game_pool_dao -> insert($idata_26);

		$idata_27['bet_type']=$temporarily_bet;
		$idata_27['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_27['type']=1;
		$idata_27['type_status']="任意8_cherry";
		$this -> game_pool_dao -> insert($idata_27);

		$idata_30['bet_type']=$temporarily_bet;
		$idata_30['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_30['type']=1;
		$idata_30['type_status']="任意9_seven";
		$this -> game_pool_dao -> insert($idata_30);

		$idata_31['bet_type']=$temporarily_bet;
		$idata_31['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_31['type']=1;
		$idata_31['type_status']="任意9_bar";
		$this -> game_pool_dao -> insert($idata_31);

		$idata_32['bet_type']=$temporarily_bet;
		$idata_32['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_32['type']=1;
		$idata_32['type_status']="任意9_medal";
		$this -> game_pool_dao -> insert($idata_32);

		$idata_33['bet_type']=$temporarily_bet;
		$idata_33['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_33['type']=1;
		$idata_33['type_status']="任意9_bell";
		$this -> game_pool_dao -> insert($idata_33);

		$idata_34['bet_type']=$temporarily_bet;
		$idata_34['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_34['type']=1;
		$idata_34['type_status']="任意9_watermelon";
		$this -> game_pool_dao -> insert($idata_34);

		$idata_35['bet_type']=$temporarily_bet;
		$idata_35['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_35['type']=1;
		$idata_35['type_status']="任意9_grape";
		$this -> game_pool_dao -> insert($idata_35);

		$idata_36['bet_type']=$temporarily_bet;
		$idata_36['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_36['type']=1;
		$idata_36['type_status']="任意9_orange";
		$this -> game_pool_dao -> insert($idata_36);

		$idata_37['bet_type']=$temporarily_bet;
		$idata_37['pool_amt']=$temporarily_bet*floatval(0.005);
		$idata_37['type']=1;
		$idata_37['type_status']="任意9_cherry";
		$last_id = $this -> game_pool_dao -> insert($idata_37);
		// $get_all=$this -> game_pool_dao -> get_sum_pool_amt($last_id,$temporarily_bet);
		// $find_multiple=floatval($get_all)/$bet;
		$total_magnification=0;

		$p=mt_rand(1,100);
		if($p>=$config->normal_winning&&$p<=$config->overall_winning){//全盤
			if($p==35){//任意7_seven
				$type_status="任意7_seven";
				$total_magnification=6;
			}
			if($p==36){//任意7_bar
				$type_status="任意7_bar";
				$total_magnification=7;
			}
			if($p==37){//任意7_medal
				$type_status="任意7_medal";
				$total_magnification=8;
			}
			if($p==38){//任意7_bell
				$type_status="任意7_bell";
				$total_magnification=9;
			}
			if($p==39){//任意7_watermelon
				$type_status="任意7_watermelon";
				$total_magnification=10;
			}
			if($p==40){//任意7_grape
				$type_status="任意7_grape";
				$total_magnification=11;
			}
			if($p==41){//任意7_orange
				$type_status="任意7_orange";
				$total_magnification=12;
			}
			if($p==42){//任意7_cherry
				$type_status="任意7_cherry";
				$total_magnification=13;
			}

			 if($p==43){//任意8_seven
				$type_status="任意8_seven";
				$total_magnification=14;
			}
			if($p==44){//任意8_bar
				$type_status="任意8_bar";
				$total_magnification=15;
			}
			if($p==45){//任意8_medal
				$type_status="任意8_medal";
				$total_magnification=16;
			}
			if($p==46){//任意8_bell
				$type_status="任意8_bell";
				$total_magnification=17;
			}
			if($p==47){//任意8_watermelon
				$type_status="任意8_watermelon";
				$total_magnification=18;
			}
			if($p==48){//任意8_grape
				$type_status="任意8_grape";
				$total_magnification=19;
			}
			if($p==49){//任意8_orange
				$type_status="任意8_orange";
				$total_magnification=20;
			}
			if($p==50){//任意8_cherry
				$type_status="任意8_cherry";
				$total_magnification=21;
			}
			 if($p==51){//任意9_seven
				$type_status="任意9_seven";
				$total_magnification=22;
			}
			if($p==52){//任意9_bar
				$type_status="任意9_bar";
				$total_magnification=23;
			}
			if($p==53){//任意9_medal
				$type_status="任意9_medal";
				$total_magnification=24;
			}
			if($p==54){//任意9_bell
				$type_status="任意9_bell";
				$total_magnification=25;
			}
			if($p==55){//任意9_watermelon
				$type_status="任意9_watermelon";
				$total_magnification=26;
			}
			if($p==56){//任意9_grape
				$type_status="任意9_grape";
				$total_magnification=27;
			}
			if($p==57){//任意9_orange
				$type_status="任意9_orange";
				$total_magnification=28;
			}
			if($p==58){//任意9_cherry
				$type_status="任意9_cherry";
				$total_magnification=29;
			}

			$type =1;
			$get_all=$this -> game_pool_dao -> get_sum_pool_amt($last_id,$temporarily_bet,$type,$type_status);
			$find_multiple=floatval($get_all)/$bet;
			$list = $this -> advance_play_dao -> find_rand($find_multiple,$type,$total_magnification);

		} elseif($p<=$config->normal_winning) {//一般

			if($p>=1&&$p<=15){//10-50
				$type_status="10-50倍";
				$total_magnification=1;
			}
			if($p>=16&&$p<=25){//51-99
				$type_status="51-99倍 ";
				$total_magnification=2;
			}
			if ($p>=26&&$p<=30) {//100-199
				$type_status="100-199倍";
				$total_magnification=3;
			}
			if ($p>=31&&$p<=33) {//200-299
				$type_status="200-299倍";
				$total_magnification=4;
			}
			if ($p==34) {//300-399
				$type_status="300-399倍";
				$total_magnification=5;
			}
			$type =0;
			$get_all=$this -> game_pool_dao -> get_sum_pool_amt($last_id,$temporarily_bet,$type,$type_status);
			$find_multiple=floatval($get_all)/$bet;
			$list = $this -> advance_play_dao -> find_rand($find_multiple,$type,$total_magnification);

		} elseif ($p>intval($config->overall_winning)+intval($config->normal_winning)) {//沒中
			$type =3;
			$type_status=0;
			$get_all=$this -> game_pool_dao -> get_sum_pool_amt($last_id,$temporarily_bet,$type,$type_status);
			$find_multiple=floatval($get_all)/$bet;
			$list = $this -> advance_play_dao -> find_rand($find_multiple,$type,$total_magnification);
		}

		$advance_id = $list[0]->id;
		$total = floatval($list[0]->total_multiple)*$bet;
		$this -> insert_total_price($bet,$total,$user_id,$advance_id,$company3,$type,$type_status);
		$qqq['$list']=$list;
		// $qqq['$find_multiple']=$find_multiple;
		$qqq['$p']=$p;
		$qqq['$p12']=$total_magnification;

		$this -> to_json($qqq);

	}

	public function insert_total_price($bet,$total,$user_id,$advance_id,$company3,$type,$type_status) {
		$res1 = array();
		// $res['success'] = TRUE;
		$bet_o=$bet*8;
		$for_q_amt=$total-$bet_o;
		$do_insert=$this -> q_r_dao -> insert_all_total($bet_o,$total,$for_q_amt,$user_id,$advance_id,$type,$type_status);
		$res1['last_id']=$do_insert;


		// 1%介紹人拆分往上1%公司1%消滅
		$last_id = $do_insert;
		$promo_user = $this -> users_dao -> find_by_id($user_id);

		$aloc_com_1 = array();
		$aloc_com_1['corp_id'] = $promo_user -> corp_id;
		$aloc_com_1_amt = floatval($company3) / 3.0;
		$aloc_com_1['amt'] =	$aloc_com_1_amt;
		$aloc_com_1['income_type'] = "下注向上分配";
		$aloc_com_1['income_id'] = $last_id;
		$aloc_com_1['note'] = "下注向上分配分潤 {$aloc_com_1_amt}";
		$this -> ctx_dao -> insert($aloc_com_1);

		// 向上分配
		$promo_user_id = 0;
		$alloc_amt = floatval($company3) / 3.0;//公司拆帳1/3部分

		do {
			// code...
			if(!empty($promo_user)){

				// 分配推薦人
				$promo_user_id = $promo_user -> promo_user_id;
				$aloc = array();
				$aloc['corp_id'] = $promo_user -> corp_id;
				$aloc_amt = floatval($alloc_amt) *0.2;
				$aloc['amt'] =	$aloc_amt;
				$aloc['tx_type'] = "bet_allocation";
				$aloc['user_id'] = $promo_user_id;
				$aloc['tx_id'] = $last_id;
				$aloc['brief'] = "下注向上分配分潤 {$aloc_amt}";
				$aloc_id = $this -> wtx_dao -> insert($aloc);
				$m_ctx = $this -> wtx_dao -> find_by_id($aloc_id);

				// 分配記錄
				$aloc1 = array();
				$aloc1['corp_id'] = $promo_user -> corp_id;
				$aloc1['transfter_gift_id'] = 0;
				$aloc1['game_id'] = $last_id;
				$aloc1['ope_amt'] =	$aloc_amt;
				$aloc1['user_id'] = $promo_user_id;
				$this -> tsga_dao -> insert($aloc1);

				// 計算最後剩餘金額
				$residual_amt = $alloc_amt - $m_ctx -> amt;

				if($m_ctx -> amt == 0){
					$promo_user_id = 0;

					$aloc_com = array();
					$aloc_com['corp_id'] = $promo_user -> corp_id;
					$aloc_com_amt = floatval($alloc_amt);
					$aloc_com['amt'] =	$aloc_com_amt;
					$aloc_com['income_type'] = "下注向上分配";
					$aloc_com['income_id'] = $last_id;
					$aloc_com['note'] = "下注向上分配分潤 {$aloc_com_amt}";
					$this -> ctx_dao -> insert($aloc_com);

				}else {
					$alloc_amt = $residual_amt;
					// 搜尋上一層推薦人
					$promo_user = $this -> users_dao -> find_by_id($promo_user_id);
					if(!empty($promo_user)){
						$promo_user_id = $promo_user -> promo_user_id;
					}else{
						$promo_user_id = 0;
					}

					if($promo_user_id == 0){
						$aloc_com = array();
						$aloc_com['corp_id'] = 1;
						$aloc_com_amt = floatval($alloc_amt);
						$aloc_com['amt'] =	$aloc_com_amt;
						$aloc_com['income_type'] = "下注向上分配";
						$aloc_com['income_id'] = $last_id;
						$aloc_com['note'] = "下注向上分配分潤 {$aloc_com_amt}";
						$this -> ctx_dao -> insert($aloc_com);
					}

				}

			}else{
				$promo_user_id = 0;

				// 向上分配給公司
				$aloc_com = array();
				$aloc_com['corp_id'] = 1;
				$aloc_com_amt = floatval($alloc_amt);
				$aloc_com['amt'] =	$aloc_com_amt;
				$aloc_com['income_type'] = "下注向上分配";
				$aloc_com['income_id'] = $last_id;
				$aloc_com['note'] = "下注向上分配分潤 {$aloc_com_amt}";
				$this -> ctx_dao -> insert($aloc_com);

			}
		} while ($promo_user_id > 0);

		$this -> to_json($res1);
	}

	public function get_tx_price_list($bet,$mag) {
		$win_amt=$bet*$mag;

		return $win_amt;
	}

	public function get_line_price($winning_item,$bet) {
		$price = 0;

		if($winning_item=="seven_b"){
			$price=$bet*1000;
		}
		if($winning_item=="seven_r"){
			$price=$bet*300;
		}
		if($winning_item=="bar"){
			$price=$bet*100;
		}
		if($winning_item=="medal"){
			$price=$bet*50;
		}
		if($winning_item=="bell"){
			$price=$bet*30;
		}
		if($winning_item=="watermelon"){
			$price=$bet*20;
		}
		if($winning_item=="grape"){
			$price=$bet*16;
		}
		if($winning_item=="orange"){
			$price=$bet*14;
		}
		if($winning_item=="cherry"){
			$price=$bet*10;
		}
		return $price;

	}
	public function ttt($value='123')
	{
		$this->to_json ("123");
	}

	public function advance_play(){


		for($aa=0;$aa<1000;$aa++){
			$icon_arr = array(
				'seven_b',
				'seven_r',
				'bar',
				'medal',
				'bell',
				'watermelon',
				'grape',
				'orange',
				'cherry',
			);
			//-------計算出現次數
			$counter_seven_b = 0;
			$counter_seven_r = 0;
			$counter_bar = 0;
			$counter_medal = 0;
			$counter_bell = 0;
			$counter_watermelon = 0;
			$counter_grape= 0;
			$counter_orange = 0;
			$counter_cherry = 0;
			$counter_seven= 0;

			$counter_seven_b1 = 0;
			$counter_seven_r1 = 0;
			$counter_bar1 = 0;
			$counter_medal1 = 0;
			$counter_bell1 = 0;
			$counter_watermelon1 = 0;
			$counter_grape1= 0;
			$counter_orange1 = 0;
			$counter_cherry1 = 0;
			$counter_seven1= 0;

			$not_same=0;

			$line1=0;
			$line2=0;
			$line3=0;
			$line4=0;
			$line5=0;
			$line6=0;
			$line7=0;
			$line8=0;
			$system=0;
			$num=0;
			$new_icon_arr =shuffle($icon_arr);
			$res1['newarray']=$icon_arr;
		$match_arr = array(); // init match array
		$res = array(); // init match array

		for($i = 0 ; $i < 3 ; $i++) {
			for($j = 0 ; $j < 3 ; $j++) {
				for($k = 0 ; $k < 9 ; $k++) {
					$row1 = array_rand($res1['newarray'],2);
				}
				$match_arr[$i][$j] =$icon_arr[$row1[0]];
				if($icon_arr[$row1[0]]=="seven_b"){
					$counter_seven_b++;
				}
				if($icon_arr[$row1[0]]=="seven_r"){
					$counter_seven_r++;
				}
				if($icon_arr[$row1[0]]=="bar"){
					$counter_bar++;
				}
				if($icon_arr[$row1[0]]=="medal"){
					$counter_medal++;
				}
				if($icon_arr[$row1[0]]=="bell"){
					$counter_bell++;
				}
				if($icon_arr[$row1[0]]=="watermelon"){
					$counter_watermelon++;
				}
				if($icon_arr[$row1[0]]=="grape"){
					$counter_grape++;
				}
				if($icon_arr[$row1[0]]=="orange"){
					$counter_orange++;
				}
				if($icon_arr[$row1[0]]=="cherry"){
					$counter_cherry++;
				}
			}
		}
		if($counter_seven_b ==1 &&
		$counter_seven_r ==1  &&
		$counter_bar ==1 &&
		$counter_medal ==1 &&
		$counter_bell ==1 &&
		$counter_watermelon ==1 &&
		$counter_grape==1 &&
		$counter_orange ==1 &&
		$counter_cherry ==1 ){
			$res['not_line']=1;
			$not_same=100;
		}


		if($counter_seven_b+$counter_seven_r>2){
			if($counter_seven_b+$counter_seven_r==3){
				$counter_seven1=12;
			}
			if($counter_seven_b+$counter_seven_r==4){
				$counter_seven1=80;
			}
			if($counter_seven_b+$counter_seven_r==5){
				$counter_seven1=400;
			}
			if($counter_seven_b+$counter_seven_r==6){
				$counter_seven1=2000;
			}
			if($counter_seven_b+$counter_seven_r==7){
				$counter_seven1=12000;
			}
			if($counter_seven_b+$counter_seven_r==8){
				$counter_seven1=50000;
			}
			if($counter_seven_b+$counter_seven_r==9){
				$counter_seven1=100000;

			}
			$res['seven']=1;
			$res['counter_seven']=$counter_seven_b+$counter_seven_r;
			if($counter_seven_b+$counter_seven_r>=7){
				$system="seven";
				$num=$counter_seven_b+$counter_seven_r;
			}
		}

		if($counter_bar>2){
			if($counter_bar==3){
				$counter_bar1=7;
			}
			if($counter_bar==4){
				$counter_bar1=40;
			}
			if($counter_bar==5){
				$counter_bar1=200;
			}
			if($counter_bar==6){
				$counter_bar1=1000;
			}
			if($counter_bar==7){
				$counter_bar1=6000;

			}
			if($counter_bar==8){
				$counter_bar1=30000;

			}
			if($counter_bar==9){
				$counter_bar1=70000;

			}
			$res['bar']=1;
			$res['counter_bar']=$counter_bar;
			if($counter_bar>=7){
				$system="bar";
				$num=$counter_bar;
			}
		}

		if($counter_medal>2){
			if($counter_medal==3){
				$counter_medal1=5;
			}
			if($counter_medal==4){
				$counter_medal1=20;
			}
			if($counter_medal==5){
				$counter_medal1=100;
			}
			if($counter_medal==6){
				$counter_medal1=500;
			}
			if($counter_medal==7){
				$counter_medal1=3000;

			}
			if($counter_medal==8){
				$counter_medal1=20000;

			}
			if($counter_medal==9){
				$counter_medal1=60000;

			}
			$res['medal']=1;
			$res['counter_medal']=$counter_medal;
			if($counter_medal>=7){
				$system="medal";
				$num=$counter_medal;
			}
		}

		if($counter_bell>2){
			if($counter_bell==3){
				$counter_bell1=4;
			}
			if($counter_bell==4){
				$counter_bell1=10;
			}
			if($counter_bell==5){
				$counter_bell1=50;
			}
			if($counter_bell==6){
				$counter_bell1=250;
			}
			if($counter_bell==7){
				$counter_bell1=1500;

			}
			if($counter_bell==8){
				$counter_bell1=10000;

			}
			if($counter_bell==9){
				$counter_bell1=50000;

			}
			$res['bell']=1;
			$res['counter_bell']=$counter_bell;
			if($counter_bell>=7){
				$system="bell";
				$num=$counter_bell;
			}
		}

		if($counter_watermelon>3){
			if($counter_watermelon==4){
				$counter_watermelon1=10;
			}
			if($counter_watermelon==5){
				$counter_watermelon1=40;
			}
			if($counter_watermelon==6){
				$counter_watermelon1=200;
			}
			if($counter_watermelon==7){
				$counter_watermelon1=1200;

			}
			if($counter_watermelon==8){
				$counter_watermelon1=8000;

			}
			if($counter_watermelon==9){
				$counter_watermelon1=40000;

			}
			$res['watermelon']=1;
			$res['counter_watermelon']=$counter_watermelon;
			if($counter_watermelon>=7){
				$system="watermelon";
				$num=$counter_watermelon;
			}
		}

		if($counter_grape>3){
			if($counter_grape==4){
				$counter_grape1=7;
			}
			if($counter_grape==5){
				$counter_grape1=30;
			}
			if($counter_grape==6){
				$counter_grape1=150;
			}
			if($counter_grape==7){
				$counter_grape1=900;

			}
			if($counter_grape==8){
				$counter_grape1=6000;

			}
			if($counter_grape==9){
				$counter_grape1=30000;

			}
			$res['grape']=1;
			$res['counter_grape']=$counter_grape;
			if($counter_grape>=7){
				$system="grape";
				$num=$counter_grape;
			}
		}

		if($counter_cherry>3){
			if($counter_cherry==4){
				$counter_cherry1=4;
			}
			if($counter_cherry==5){
				$counter_cherry1=10;
			}
			if($counter_cherry==6){
				$counter_cherry1=50;
			}
			if($counter_cherry==7){
				$counter_cherry1=300;

			}
			if($counter_cherry==8){
				$counter_cherry1=2000;

			}
			if($counter_cherry==9){
				$counter_cherry1=1000;

			}
			$res['cherry']=1;
			$res['counter_cherry']=$counter_cherry;
			if($counter_cherry>=7){
				$system="cherry";
				$num=$counter_cherry;
			}
		}

		if($counter_orange>3){
			if($counter_orange==4){
				$counter_orange1=5;
			}
			if($counter_orange==5){
				$counter_orange1=20;
			}
			if($counter_orange==6){
				$counter_orange1=100;
			}
			if($counter_orange==7){
				$counter_orange1=600;

			}
			if($counter_orange==8){
				$counter_orange1=4000;

			}
			if($counter_orange==9){
				$counter_orange1=20000;

			}
			$res['orange']=1;
			$res['counter_orange']=$counter_orange;
			if($counter_orange>=7){
				$system="orange";
				$num=$counter_orange;
			}
		}

		if($match_arr[0][0]==$match_arr[0][1]&&$match_arr[0][0]==$match_arr[0][2]){
			$line2 = $this -> get_line_price1($match_arr[0][0]);
			$res['line2']=1;


		} else{
			if(substr($match_arr[0][0],0,-2)=="seven" && substr($match_arr[0][1],0,-2)=="seven" && substr($match_arr[0][2],0,-2)=="seven") {
				$line2=200;
				$res['line2']=1;

			}
		}

		if($match_arr[1][0]==$match_arr[1][1]&&$match_arr[1][0]==$match_arr[1][2]){
			$line1 = $this -> get_line_price1($match_arr[1][0]);
			$res['line1']=1;

		} else{
			if(substr($match_arr[1][0],0,-2)=="seven" && substr($match_arr[1][1],0,-2)=="seven" && substr($match_arr[1][2],0,-2)=="seven") {
				$line1=200;
				$res['line1']=1;

			}
		}

		if($match_arr[2][0]==$match_arr[2][1]&&$match_arr[2][0]==$match_arr[2][2]){
			$line3 = $this -> get_line_price1($match_arr[2][0]);
			$res['line3']=1;

		} else{
			if(substr($match_arr[2][0],0,-2)=="seven" && substr($match_arr[2][1],0,-2)=="seven" && substr($match_arr[2][2],0,-2)=="seven") {
				$line3=200;
				$res['line3']=1;

			}
		}

		if($match_arr[0][0]==$match_arr[1][1]&&$match_arr[0][0]==$match_arr[2][2]){
			$line4 = $this -> get_line_price1($match_arr[0][0]);
			$res['line4']=1;

		} else{
			if(substr($match_arr[0][0],0,-2)=="seven" && substr($match_arr[1][1],0,-2)=="seven" && substr($match_arr[2][2],0,-2)=="seven") {
				$line4=200;
				$res['line4']=1;

			}
		}

		if($match_arr[2][0]==$match_arr[1][1]&&$match_arr[2][0]==$match_arr[0][2]){
			$line5 = $this -> get_line_price1($match_arr[2][0]);
			$res['line5']=1;

		} else{
			if(substr($match_arr[2][0],0,-2)=="seven" && substr($match_arr[1][1],0,-2)=="seven" && substr($match_arr[0][2],0,-2)=="seven") {
				$line5=200;
				$res['line5']=1;

			}
		}

		if($match_arr[0][1]==$match_arr[1][1]&&$match_arr[0][1]==$match_arr[2][1]){
			$line7 = $this -> get_line_price1($match_arr[0][1]);
			$res['line7']=1;

		} else{
			if(substr($match_arr[0][1],0,-2)=="seven" && substr($match_arr[1][1],0,-2)=="seven" && substr($match_arr[2][1],0,-2)=="seven") {
				$line7=200;
				$res['line7']=1;

			}
		}

		if($match_arr[0][2]==$match_arr[1][2]&&$match_arr[0][2]==$match_arr[2][2]){
			$line6 = $this -> get_line_price1($match_arr[0][2]);
			$res['line6']=1;

		} else{
			if(substr($match_arr[0][2],0,-2)=="seven" && substr($match_arr[1][2],0,-2)=="seven" && substr($match_arr[2][2],0,-2)=="seven") {
				$line6=200;
				$res['line6']=1;

			}
		}

		if($match_arr[0][0]==$match_arr[1][0]&&$match_arr[0][0]==$match_arr[2][0]){
			$line8 = $this -> get_line_price1($match_arr[0][0]);
			$res['line8']=1;

		} else{
			if(substr($match_arr[0][0],0,-2)=="seven" && substr($match_arr[1][0],0,-2)=="seven" && substr($match_arr[2][0],0,-2)=="seven") {
				$line8=200;
				$res['line8']=1;

			}
		}
		$overall=$counter_seven1+$counter_bar1+$counter_medal1+$counter_bell1+$counter_watermelon1+$counter_grape1+$counter_orange1+$counter_cherry1+$not_same;
		$overall1=$line1+$line2+$line3+$line4+$line5+$line6+$line7+$line8;
		$total=$overall1+$overall;
		$value = json_encode($match_arr);
		$value1 = json_encode($res);

		$tx_11['result'] = $value;
		$tx_11['total_multiple'] = $total;
		$tx_11['win_result'] = $value1;
		$tx_11['counter_system'] = $system;
		$tx_11['counter_num'] = $num;

		$last_id=	$this -> advance_play_dao -> insert($tx_11);
	}

	}

	public function get_line_price1($winning_item) {
		$price = 0;

		if($winning_item=="seven_b"){
			$price=1000;
		}
		if($winning_item=="seven_r"){
			$price=300;
		}
		if($winning_item=="bar"){
			$price=100;
		}
		if($winning_item=="medal"){
			$price=50;
		}
		if($winning_item=="bell"){
			$price=30;
		}
		if($winning_item=="watermelon"){
			$price=20;
		}
		if($winning_item=="grape"){
			$price=16;
		}
		if($winning_item=="orange"){
			$price=14;
		}
		if($winning_item=="cherry"){
			$price=10;
		}
		return $price;

	}
	// function get_sum_amt_all() {
	// 	$p = floatval(1000000000)/floatval(1000000000.00135032);
	// 	// $price=round($p,8);
	// 	$this->to_json ($p);
	//
	// }
}
?>
