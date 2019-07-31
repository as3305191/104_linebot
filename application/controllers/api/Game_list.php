<?php
class Game_list extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Game_list_dao', 'gl_dao');
		$this -> load -> model('Game_tiger_dao', 'game_tiger_dao');

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
		$res = array("success" => TRUE);
		$data = array();
		$temporarily_bet = $this -> get_post('$bet');
		$bet = $temporarily_bet/8;
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

			$new_icon_arr =shuffle($icon_arr);
			$res['newarray']=$icon_arr;

			$match_arr = array(); // init match array
			for($i = 0 ; $i < 3 ; $i++) {
				for($j = 0 ; $j < 3 ; $j++) {
					for($k = 0 ; $k < 9 ; $k++) {
						$row1 = array_rand($res['newarray'],2);
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
			 $res1['d']=	$match_arr;
			 $res1['seven_b']=$counter_seven_b;
			 $res1['seven_r']=$counter_seven_r;
			 $res1['counter_bar']=$counter_bar;
			 $res1['medal']=$counter_medal;
			 $res1['bell']=$counter_bell;
			 $res1['watermelon']=$counter_watermelon;
			 $res1['grape']=$counter_grape;
			 $res1['orange']=$counter_orange;
			 $res1['cherry']=$counter_cherry;

					if($counter_seven_b ==1 &&
					$counter_seven_r ==1  &&
					$counter_bar ==1 &&
					$counter_medal ==1 &&
					$counter_bell ==1 &&
					$counter_watermelon ==1 &&
					$counter_grape==1 &&
					$counter_orange ==1 &&
					$counter_cherry ==1 ){
						$res1['not_line']="都不一樣";
					}
					if($counter_seven_b+$counter_seven_r>2){
						if($counter_seven_b+$counter_seven_r==3){
							$bet*12;
							$res1['counter_seven']="3";
						}
						if($counter_seven_b+$counter_seven_r==4){
							$bet*80;
							$res1['counter_seven']="4";
						}
						if($counter_seven_b+$counter_seven_r==5){
							$bet*400;
							$res1['counter_seven']="5";
						}
						if($counter_seven_b+$counter_seven_r==6){
							$bet*2000;
							$res1['counter_seven']="6";
						}
						if($counter_seven_b+$counter_seven_r==7){
							$bet*12000;
							$res1['counter_seven']="7";
						}
						if($counter_seven_b+$counter_seven_r==8){
							$bet*50000;
							$res1['counter_seven']="8";
						}
						if($counter_seven_b+$counter_seven_r==9){
							$bet*100000;
							$res1['counter_seven']="9";
						}
					}

					if($counter_bar>2){
						if($counter_bar==3){
							$res1['counter_bar']="3";
						}
						if($counter_bar==4){
							$res1['counter_bar']="4";
						}
						if($counter_bar==5){
							$res1['counter_bar']="5";
						}
						if($counter_bar==6){
							$res1['counter_bar']="6";
						}
						if($counter_bar==7){
							$res1['counter_bar']="7";
						}
						if($counter_bar==8){
							$res1['counter_bar']="8";
						}
						if($counter_bar==9){
							$res1['counter_bar']="9";
						}
					}

					if($counter_medal>2){
						if($counter_medal==3){
							$res1['counter_medal']="3";
						}
						if($counter_medal==4){
							$res1['counter_medal']="4";
						}
						if($counter_medal==5){
							$res1['counter_medal']="5";
						}
						if($counter_medal==6){
							$res1['counter_medal']="6";
						}
						if($counter_medal==7){
							$res1['counter_medal']="7";
						}
						if($counter_medal==8){
							$res1['counter_medal']="8";
						}
						if($counter_medal==9){
							$res1['counter_medal']="9";
						}
					}

					if($counter_bell>2){
						if($counter_bell==3){
							$res1['counter_bell']="3";
						}
						if($counter_bell==4){
							$res1['counter_bell']="4";
						}
						if($counter_bell==5){
							$res1['counter_bell']="5";
						}
						if($counter_bell==6){
							$res1['counter_bell']="6";
						}
						if($counter_bell==7){
							$res1['counter_bell']="7";
						}
						if($counter_bell==8){
							$res1['counter_bell']="8";
						}
						if($counter_bell==9){
							$res1['counter_bell']="9";
						}
					}

					if($counter_watermelon>3){
						if($counter_watermelon==4){
							$res1['counter_watermelon']="4";
						}
						if($counter_watermelon==5){
							$res1['counter_watermelon']="5";
						}
						if($counter_watermelon==6){
							$res1['counter_watermelon']="6";
						}
						if($counter_watermelon==7){
							$res1['counter_watermelon']="7";
						}
						if($counter_watermelon==8){
							$res1['counter_watermelon']="8";
						}
						if($counter_watermelon==9){
							$res1['counter_watermelon']="9";
						}
					}

					if($counter_grape>3){
						if($counter_grape==4){
							$res1['counter_grape']="4";
						}
						if($counter_grape==5){
							$res1['counter_grape']="5";
						}
						if($counter_grape==6){
							$res1['counter_grape']="6";
						}
						if($counter_grape==7){
							$res1['counter_grape']="7";
						}
						if($counter_grape==8){
							$res1['counter_grape']="8";
						}
						if($counter_grape==9){
							$res1['counter_grape']="9";
						}
					}

					if($counter_cherry>3){
						if($counter_cherry==4){
							$res1['counter_cherry']="4";
						}
						if($counter_cherry==5){
							$res1['counter_cherry']="5";
						}
						if($counter_cherry==6){
							$res1['counter_cherry']="6";
						}
						if($counter_cherry==7){
							$res1['counter_cherry']="7";
						}
						if($counter_cherry==8){
							$res1['counter_cherry']="8";
						}
						if($counter_cherry==9){
							$res1['counter_cherry']="9";
						}
					}

					if($counter_orange>3){
						if($counter_orange==4){
							$res1['counter_orange']="4";
						}
						if($counter_orange==5){
							$res1['counter_orange']="5";
						}
						if($counter_orange==6){
							$res1['counter_orange']="6";
						}
						if($counter_orange==7){
							$res1['counter_orange']="7";
						}
						if($counter_orange==8){
							$res1['counter_orange']="8";
						}
						if($counter_orange==9){
							$res1['counter_orange']="9";
						}
					}

				if($match_arr[0][0]==$match_arr[0][1]&&$match_arr[0][0]==$match_arr[0][2]){
					if($match_arr[0][0]=="seven_b"){
						$res1['line2']="seven_b";
					}
					if($match_arr[0][0]=="seven_r"){
						$res1['line2']="seven_r";
					}
					if($match_arr[0][0]=="bar"){
						$res1['line2']="bar";
					}
					if($match_arr[0][0]=="medal"){
						$res1['line2']="medal";
					}
					if($match_arr[0][0]=="bell"){
						$res1['line2']="bell";
					}
					if($match_arr[0][0]=="watermelon"){
						$res1['line2']="watermelon";
					}
					if($match_arr[0][0]=="grape"){
						$res1['line2']="grape";
					}
					if($match_arr[0][0]=="orange"){
						$res1['line2']="orange";
					}
					if($match_arr[0][0]=="cherry"){
						$res1['line2']="cherry";
					}

				} else{
					if(substr($match_arr[0][0],0,-2)=="seven" && substr($match_arr[0][1],0,-2)=="seven" && substr($match_arr[0][2],0,-2)=="seven") {
						$res1['message']="mixed7";
					}
				}

				if($match_arr[1][0]==$match_arr[1][1]&&$match_arr[1][0]==$match_arr[1][2]){
					if($match_arr[0][0]=="seven_b"){
					 $res1['line1']="seven_b";
				 }
				 if($match_arr[0][0]=="seven_r"){
					 $res1['line1']="seven_r";
				 }
				 if($match_arr[0][0]=="bar"){
					 $res1['line1']="bar";
				 }
				 if($match_arr[0][0]=="medal"){
					 $res1['line1']="medal";
				 }
				 if($match_arr[0][0]=="bell"){
					 $res1['line1']="bell";
				 }
				 if($match_arr[0][0]=="watermelon"){
					 $res1['line2']="watermelon";
				 }
				 if($match_arr[0][0]=="grape"){
					 $res1['line1']="grape";
				 }
				 if($match_arr[0][0]=="orange"){
					 $res1['line1']="orange";
				 }
				 if($match_arr[0][0]=="cherry"){
					 $res1['line1']="cherry";
				 }
				} else{
					if(substr($match_arr[1][0],0,-2)=="seven" && substr($match_arr[1][1],0,-2)=="seven" && substr($match_arr[1][2],0,-2)=="seven") {
						$res1['message']="mixed7";
					}
				}

				if($match_arr[2][0]==$match_arr[2][1]&&$match_arr[2][0]==$match_arr[2][2]){
					if($match_arr[0][0]=="seven_b"){
					 $res1['line3']="seven_b";
				 }
				 if($match_arr[0][0]=="seven_r"){
					 $res1['line3']="seven_r";
				 }
				 if($match_arr[0][0]=="bar"){
					 $res1['line3']="bar";
				 }
				 if($match_arr[0][0]=="medal"){
					 $res1['line3']="medal";
				 }
				 if($match_arr[0][0]=="bell"){
					 $res1['line3']="bell";
				 }
				 if($match_arr[0][0]=="watermelon"){
					 $res1['line3']="watermelon";
				 }
				 if($match_arr[0][0]=="grape"){
					 $res1['line3']="grape";
				 }
				 if($match_arr[0][0]=="orange"){
					 $res1['line3']="orange";
				 }
				 if($match_arr[0][0]=="cherry"){
					 $res1['line3']="cherry";
				 }
				} else{
					if(substr($match_arr[2][0],0,-2)=="seven" && substr($match_arr[2][1],0,-2)=="seven" && substr($match_arr[2][2],0,-2)=="seven") {
						$res1['message']="mixed7";
					}
				}

				if($match_arr[0][0]==$match_arr[1][1]&&$match_arr[0][0]==$match_arr[2][2]){
					if($match_arr[0][0]=="seven_b"){
					 $res1['line4']="seven_b";
				 }
				 if($match_arr[0][0]=="seven_r"){
					 $res1['line4']="seven_r";
				 }
				 if($match_arr[0][0]=="bar"){
					 $res1['line4']="bar";
				 }
				 if($match_arr[0][0]=="medal"){
					 $res1['line4']="medal";
				 }
				 if($match_arr[0][0]=="bell"){
					 $res1['line4']="bell";
				 }
				 if($match_arr[0][0]=="watermelon"){
					 $res1['line4']="watermelon";
				 }
				 if($match_arr[0][0]=="grape"){
					 $res1['line4']="grape";
				 }
				 if($match_arr[0][0]=="orange"){
					 $res1['line4']="orange";
				 }
				 if($match_arr[0][0]=="cherry"){
					 $res1['line4']="cherry";
				 }
				} else{
					if(substr($match_arr[0][0],0,-2)=="seven" && substr($match_arr[1][1],0,-2)=="seven" && substr($match_arr[2][2],0,-2)=="seven") {
						$res1['message']="mixed7";
					}
				}

				if($match_arr[2][0]==$match_arr[1][1]&&$match_arr[2][0]==$match_arr[0][2]){
					if($match_arr[0][0]=="seven_b"){
					 $res1['line5']="seven_b";
				 }
				 if($match_arr[0][0]=="seven_r"){
					 $res1['line5']="seven_r";
				 }
				 if($match_arr[0][0]=="bar"){
					 $res1['line5']="bar";
				 }
				 if($match_arr[0][0]=="medal"){
					 $res1['line5']="medal";
				 }
				 if($match_arr[0][0]=="bell"){
					 $res1['line5']="bell";
				 }
				 if($match_arr[0][0]=="watermelon"){
					 $res1['line5']="watermelon";
				 }
				 if($match_arr[0][0]=="grape"){
					 $res1['line5']="grape";
				 }
				 if($match_arr[0][0]=="orange"){
					 $res1['line5']="orange";
				 }
				 if($match_arr[0][0]=="cherry"){
					 $res1['line5']="cherry";
				 }
				} else{
					if(substr($match_arr[2][0],0,-2)=="seven" && substr($match_arr[1][1],0,-2)=="seven" && substr($match_arr[0][2],0,-2)=="seven") {
						$res1['message']="mixed7";
					}
				}

				if($match_arr[0][0]==$match_arr[1][0]&&$match_arr[0][0]==$match_arr[2][0]){
					if($match_arr[0][0]=="seven_b"){
					 $res1['line8']="seven_b";
				 }
				 if($match_arr[0][0]=="seven_r"){
					 $res1['line8']="seven_r";
				 }
				 if($match_arr[0][0]=="bar"){
					 $res1['line8']="bar";
				 }
				 if($match_arr[0][0]=="medal"){
					 $res1['line8']="medal";
				 }
				 if($match_arr[0][0]=="bell"){
					 $res1['line8']="bell";
				 }
				 if($match_arr[0][0]=="watermelon"){
					 $res1['line8']="watermelon";
				 }
				 if($match_arr[0][0]=="grape"){
					 $res1['line8']="grape";
				 }
				 if($match_arr[0][0]=="orange"){
					 $res1['line8']="orange";
				 }
				 if($match_arr[0][0]=="cherry"){
					 $res1['line8']="cherry";
				 }
				} else{
					if(substr($match_arr[0][0],0,-2)=="seven" && substr($match_arr[1][0],0,-2)=="seven" && substr($match_arr[2][0],0,-2)=="seven") {
						$res1['message']="mixed7";
					}
				}

				if($match_arr[0][1]==$match_arr[1][1]&&$match_arr[0][1]==$match_arr[2][1]){
					if($match_arr[0][0]=="seven_b"){
					 $res1['line7']="seven_b";
				 }
				 if($match_arr[0][0]=="seven_r"){
					 $res1['line7']="seven_r";
				 }
				 if($match_arr[0][0]=="bar"){
					 $res1['line7']="bar";
				 }
				 if($match_arr[0][0]=="medal"){
					 $res1['line7']="medal";
				 }
				 if($match_arr[0][0]=="bell"){
					 $res1['line7']="bell";
				 }
				 if($match_arr[0][0]=="watermelon"){
					 $res1['line7']="watermelon";
				 }
				 if($match_arr[0][0]=="grape"){
					 $res1['line7']="grape";
				 }
				 if($match_arr[0][0]=="orange"){
					 $res1['line7']="orange";
				 }
				 if($match_arr[0][0]=="cherry"){
					 $res1['line7']="cherry";
				 }
				} else{
					if(substr($match_arr[0][1],0,-2)=="seven" && substr($match_arr[1][1],0,-2)=="seven" && substr($match_arr[2][1],0,-2)=="seven") {
						$res1['message']="mixed7";
					}
				}

				if($match_arr[0][2]==$match_arr[1][2]&&$match_arr[0][2]==$match_arr[2][2]){
					if($match_arr[0][0]=="seven_b"){
					 $res1['line6']="seven_b";
				 }
				 if($match_arr[0][0]=="seven_r"){
					 $res1['line6']="seven_r";
				 }
				 if($match_arr[0][0]=="bar"){
					 $res1['line6']="bar";
				 }
				 if($match_arr[0][0]=="medal"){
					 $res1['line6']="medal";
				 }
				 if($match_arr[0][0]=="bell"){
					 $res1['line6']="bell";
				 }
				 if($match_arr[0][0]=="watermelon"){
					 $res1['line6']="watermelon";
				 }
				 if($match_arr[0][0]=="grape"){
					 $res1['line6']="grape";
				 }
				 if($match_arr[0][0]=="orange"){
					 $res1['line6']="orange";
				 }
				 if($match_arr[0][0]=="cherry"){
					 $res1['line6']="cherry";
				 }
				} else{
					if(substr($match_arr[0][2],0,-2)=="seven" && substr($match_arr[1][2],0,-2)=="seven" && substr($match_arr[2][2],0,-2)=="seven") {
						$res1['message']="mixed7";
					}
				}


			$this -> to_json($res1);

			// for($i=1;$i<=3;$i++){
			// 	$row1 = array_rand($res['newarray'],2);
			// 	$now_row1 = $icon_arr[$row1[0]];
			// 	echo ($icon_arr[$row1[0]])." ";
			// }
			// echo '<br>';
			// for($j=1;$j<=3;$j++){
			// 	$row2 = array_rand($res['newarray'],2);
			// 	$now_row2 = $icon_arr[$row2[0]];
			// 	echo ($icon_arr[$row2[0]])." ";
			// }
			// echo '<br>';
			// for($k=1;$k<=3;$k++){
			// 	$row3 = array_rand($res['newarray'],2);
			// 	$now_row3 = $icon_arr[$row3[0]];
			// 	echo ($icon_arr[$row3[0]])." ";
			// }
	}
}
?>
