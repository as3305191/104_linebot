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
		$this -> load -> model('Corp_dao', 'corp_dao');
		$this -> load -> model('Game_pool_dao', 'game_pool_dao');


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
		$corp = $this -> corp_dao -> find_by_id(1);
		$pool_pct = $corp -> pool_pct;
		$multiple = floor(floatval($pool_pct)*$temporarily_bet);

		$idata['bet_type']=$temporarily_bet;
		$idata['pool_amt']=$multiple;
		$last_id = $this -> game_pool_dao -> insert($idata);

		$get_all=$this -> game_pool_dao -> get_sum_pool_amt($last_id,$temporarily_bet);

		$list = $this -> advance_play_dao -> find_rand($get_all);
		$advance_id = $list[0]->id;
		$total = floatval($list[0]->total_multiple)*$bet;
		$this -> insert_total_price($bet,$total,$user_id,$advance_id);

		// $this -> to_json($get_all);
		// $this -> to_json($list);

	}

	public function insert_total_price($bet,$total,$user_id,$advance_id) {
		$res1 = array();
		// $res['success'] = TRUE;
		$bet_o=$bet*8;
		$for_q_amt=$total-$bet_o;
		$do_insert=$this -> q_r_dao -> insert_all_total($bet_o,$total,$for_q_amt,$user_id,$advance_id);
		$res1['last_id']=$do_insert;

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
			$res['counter_seven']=1;

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
			$res['counter_bar']=1;

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
			$res['counter_medal']=1;

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
			$res['counter_bell']=1;

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
			$res['counter_watermelon']=1;

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
			$res['counter_grape']=1;

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
			$res['counter_cherry']=1;

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
			$res['counter_orange']=1;

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
