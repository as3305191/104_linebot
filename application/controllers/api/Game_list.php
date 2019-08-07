<?php
class Game_list extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Game_list_dao', 'gl_dao');
		$this -> load -> model('Game_tiger_dao', 'game_tiger_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Quotes_record_dao', 'q_r_dao');

		$this -> load -> model('Com_tx_dao', 'ctx_dao');
	}

	public function testtest() {
		$i = array();
		$i['bet'] = 8;
		$i['user_id'] = 524;


		$n_res = $this -> curl -> simple_post("/api/Game_list/game_tiger", $i);
		$data = json_decode($n_res);
 		$this -> to_json($n_res);
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
		$bet = $temporarily_bet/8;
		$user_id = $this -> get_post('user_id');

			// 保證只有一組在執行
			// $key_id = $this -> game_tiger_dao -> get_key_id();
			// $un_done_list = array();
			// do {
			// 	$un_done_list = $this -> game_tiger_dao -> get_un_done_list($key_id);
			// } while(count($un_done_list) > 0);
			// ------------------------- start
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
			 $res['list']=	$match_arr;
			 // $res['_seven_b']=$counter_seven_b;
			 // $res['_seven_r']=$counter_seven_r;
			 // $res['_counter_bar']=$counter_bar;
			 // $res['_medal']=$counter_medal;
			 // $res['_bell']=$counter_bell;
			 // $res['_watermelon']=$counter_watermelon;
			 // $res['_grape']=$counter_grape;
			 // $res['_orange']=$counter_orange;
			 // $res['_cherry']=$counter_cherry;

					if($counter_seven_b ==1 &&
					$counter_seven_r ==1  &&
					$counter_bar ==1 &&
					$counter_medal ==1 &&
					$counter_bell ==1 &&
					$counter_watermelon ==1 &&
					$counter_grape==1 &&
					$counter_orange ==1 &&
					$counter_cherry ==1 ){
						$mag=100;
						$not_same = $this -> get_tx_price_list($bet,$mag);
						$res['not_line']="都不一樣";
					}


					if($counter_seven_b+$counter_seven_r>2){
						if($counter_seven_b+$counter_seven_r==3){
							$mag=12;
							$counter_seven1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_seven_b+$counter_seven_r==4){
							$mag=80;
							$counter_seven1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_seven_b+$counter_seven_r==5){
							$mag=400;
							$counter_seven1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_seven_b+$counter_seven_r==6){
							$mag=2000;
							$counter_seven1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_seven_b+$counter_seven_r==7){
							$mag=12000;
							$counter_seven1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_seven_b+$counter_seven_r==8){
							$mag=50000;
							$counter_seven1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_seven_b+$counter_seven_r==9){
							$mag=100000;
							$counter_seven1 = $this -> get_tx_price_list($bet,$mag);
						}
						$res['counter_seven']=$counter_seven1;
					}

					if($counter_bar>2){
						if($counter_bar==3){
							$mag=7;
							$counter_bar1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_bar==4){
							$mag=40;
							$counter_bar1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_bar==5){
							$mag=200;
							$counter_bar1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_bar==6){
							$mag=1000;
							$counter_bar1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_bar==7){
							$mag=6000;
							$counter_bar1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_bar==8){
							$mag=30000;
							$counter_bar1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_bar==9){
							$mag=70000;
							$counter_bar1 = $this -> get_tx_price_list($bet,$mag);
						}
						$res['counter_bar']=$counter_bar1;
					}

					if($counter_medal>2){
						if($counter_medal==3){
							$mag=5;
							$counter_medal1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_medal==4){
							$mag=20;
							$counter_medal1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_medal==5){
							$mag=100;
							$counter_medal1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_medal==6){
							$mag=500;
							$counter_medal1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_medal==7){
							$mag=3000;
							$counter_medal1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_medal==8){
							$mag=20000;
							$counter_medal1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_medal==9){
							$mag=60000;
							$counter_medal1 = $this -> get_tx_price_list($bet,$mag);
						}
						$res['counter_medal']=$counter_medal1;
					}

					if($counter_bell>2){
						if($counter_bell==3){
							$mag=4;
							$counter_bell1= $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_bell==4){
							$mag=10;
							$counter_bell1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_bell==5){
							$mag=50;
							$counter_bell1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_bell==6){
							$mag=250;
							$counter_bell1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_bell==7){
							$mag=1500;
							$counter_bell1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_bell==8){
							$mag=10000;
							$counter_bell1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_bell==9){
							$mag=50000;
							$counter_bell1 = $this -> get_tx_price_list($bet,$mag);
						}
						$res['counter_bell']=$counter_bell1;

					}

					if($counter_watermelon>3){
						if($counter_watermelon==4){
							$mag=10;
							$counter_watermelon1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_watermelon==5){
							$mag=40;
							$counter_watermelon1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_watermelon==6){
							$mag=200;
							$counter_watermelon1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_watermelon==7){
							$mag=1200;
							$counter_watermelon1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_watermelon==8){
							$mag=8000;
							$counter_watermelon1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_watermelon==9){
							$mag=40000;
							$counter_watermelon1 = $this -> get_tx_price_list($bet,$mag);
						}
						$res['counter_watermelon']=$counter_watermelon1;
					}

					if($counter_grape>3){
						if($counter_grape==4){
							$mag=7;
							$counter_grape1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_grape==5){
							$mag=30;
							$counter_grape1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_grape==6){
							$mag=150;
							$counter_grape1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_grape==7){
							$mag=900;
							$counter_grape1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_grape==8){
							$mag=6000;
							$counter_grape1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_grape==9){
							$mag=30000;
							$counter_grape1 = $this -> get_tx_price_list($bet,$mag);
						}
						$res['counter_grape']=$counter_grape1;
					}

					if($counter_cherry>3){
						if($counter_cherry==4){
							$mag=4;
							$counter_cherry1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_cherry==5){
							$mag=10;
							$counter_cherry1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_cherry==6){
							$mag=50;
							$counter_cherry1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_cherry==7){
							$mag=300;
							$counter_cherry1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_cherry==8){
							$mag=2000;
							$counter_cherry1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_cherry==9){
							$mag=1000;
							$counter_cherry1 = $this -> get_tx_price_list($bet,$mag);
						}
						$res['counter_cherry']=$counter_cherry1;
					}

					if($counter_orange>3){
						if($counter_orange==4){
							$mag=5;
							$counter_orange1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_orange==5){
							$mag=20;
							$counter_orange1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_orange==6){
							$mag=100;
							$counter_orange1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_orange==7){
							$mag=600;
							$counter_orange1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_orange==8){
							$mag=4000;
							$counter_orange1 = $this -> get_tx_price_list($bet,$mag);
						}
						if($counter_orange==9){
							$mag=20000;
							$counter_orange1 = $this -> get_tx_price_list($bet,$mag);
						}
						$res['counter_orange']=$counter_orange1;
					}

					// $res['overall']=$overall;


				if($match_arr[0][0]==$match_arr[0][1]&&$match_arr[0][0]==$match_arr[0][2]){
					$line2 = $this -> get_line_price($match_arr[0][0],$bet);
					$res['line2']=$line2;

				} else{
					if(substr($match_arr[0][0],0,-2)=="seven" && substr($match_arr[0][1],0,-2)=="seven" && substr($match_arr[0][2],0,-2)=="seven") {
						$mag=200;
						$line2 = $this -> get_tx_price_list($bet,$mag);
						$res['message2']=$line2 ;
					}
				}

				if($match_arr[1][0]==$match_arr[1][1]&&$match_arr[1][0]==$match_arr[1][2]){
					$line1 = $this -> get_line_price($match_arr[1][0],$bet);
					$res['line1']=$line1;
				} else{
					if(substr($match_arr[1][0],0,-2)=="seven" && substr($match_arr[1][1],0,-2)=="seven" && substr($match_arr[1][2],0,-2)=="seven") {
						$mag=200;
						$line1 = $this -> get_tx_price_list($bet,$mag);
						$res['message1']=$line1;
					}
				}

				if($match_arr[2][0]==$match_arr[2][1]&&$match_arr[2][0]==$match_arr[2][2]){
					$line3 = $this -> get_line_price($match_arr[2][0],$bet);
					$res['line3']=$line3;
				} else{
					if(substr($match_arr[2][0],0,-2)=="seven" && substr($match_arr[2][1],0,-2)=="seven" && substr($match_arr[2][2],0,-2)=="seven") {
						$mag=200;
						$line3 = $this -> get_tx_price_list($bet,$mag);
						$res['message3']=$line3;
					}
				}

				if($match_arr[0][0]==$match_arr[1][1]&&$match_arr[0][0]==$match_arr[2][2]){
					$line4 = $this -> get_line_price($match_arr[0][0],$bet);
					$res['line4']=$line4;
				} else{
					if(substr($match_arr[0][0],0,-2)=="seven" && substr($match_arr[1][1],0,-2)=="seven" && substr($match_arr[2][2],0,-2)=="seven") {
						$mag=200;
						$line4 = $this -> get_tx_price_list($bet,$mag);
						$res['message4']=$line4;
					}
				}

				if($match_arr[2][0]==$match_arr[1][1]&&$match_arr[2][0]==$match_arr[0][2]){
					$line5 = $this -> get_line_price($match_arr[2][0],$bet);
					$res['line5']=$line5;
				} else{
					if(substr($match_arr[2][0],0,-2)=="seven" && substr($match_arr[1][1],0,-2)=="seven" && substr($match_arr[0][2],0,-2)=="seven") {
						$mag=200;
						$line5 = $this -> get_tx_price_list($bet,$mag);
						$res['message5']=$line5;
					}
				}

				if($match_arr[0][0]==$match_arr[1][0]&&$match_arr[0][0]==$match_arr[2][0]){
					$line7 = $this -> get_line_price($match_arr[0][0],$bet);
					$res['line7']=$line7;
				} else{
					if(substr($match_arr[0][1],0,-2)=="seven" && substr($match_arr[1][1],0,-2)=="seven" && substr($match_arr[2][1],0,-2)=="seven") {
						$mag=200;
						$line7 = $this -> get_tx_price_list($bet,$mag);
						$res['message7']=$line7;
					}
				}

				if($match_arr[0][2]==$match_arr[1][2]&&$match_arr[0][2]==$match_arr[2][2]){
					$line6 = $this -> get_line_price($match_arr[0][2],$bet);
					$res['line6']=$line6;
				} else{
					if(substr($match_arr[0][2],0,-2)=="seven" && substr($match_arr[1][2],0,-2)=="seven" && substr($match_arr[2][2],0,-2)=="seven") {
						$mag=200;
						$line6 = $this -> get_tx_price_list($bet,$mag);
						$res['message6']=$line6;
					}
				}
				$total=$counter_seven1+
				$counter_bar1+
				$counter_medal1+
				$counter_bell1+
				$counter_watermelon1+
				$counter_grape1+
				$counter_orange1+
				$counter_cherry1+
				$not_same$line1+
				$line2+
				$line3+
				$line4+
				$line5+
				$line6+
				$line7+
				$line8;

				// $res['total']=$total;
		 		$this -> insert_total_price($bet,$total,$user_id,$match_arr);


			// $this -> to_json($res1);
	}

	public function insert_total_price($bet,$total,$user_id,$match_arr) {
		$res = array();
		// $res['success'] = TRUE;
		$bet_o=$bet*8;
		$for_q_amt=$total-$bet_o;
		$do_insert=$this -> q_r_dao -> insert_all_total($bet_o,$total,$for_q_amt,$user_id,$match_arr);
		$res['last_id']=$do_insert;

		$this -> to_json($res);
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
}
?>
