<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Line_bot extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		$this -> load -> model('Transfer_gift_dao', 'tsg_dao');
		$this -> load -> model('Transfer_gift_friends_dao', 'tsgf_dao');

		$this -> load -> model('Users_dao', 'users_dao');
		$this -> load -> model('Post_log_dao', 'post_log_dao');
		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');

		$this -> load -> model('Transfer_coin_dao', 'tc_dao');
		$this -> load -> model('Com_tx_dao', 'ctx_dao');
		$this -> load -> model('Corp_dao', 'corp_dao');
		$this -> load -> model('Config_dao', 'config_dao');

		$this -> load -> model('Customer_service_line_dao', 'cs_line_dao');
		$this -> load -> model('Customer_service_line_room_dao', 'cs_line_room_dao');
		$this -> load -> model('Quotes_record_dao', 'q_r_dao');
		$this -> load -> model('Daily_quotes_dao', 'd_q_dao');
	}

	public function index() {
		$this -> do_log("Line_bot");
		$json_body = file_get_contents('php://input');
		$obj = json_decode($json_body);
		$events = $obj -> events;
		foreach($events as $evt) {
			if($evt -> type == "message") {
				$this -> parse_text($evt);
			}
			if($evt -> type == "follow") {
				$this -> parse_follow($evt);
			}
		}
	}

	function parse_text($evt) {
		// check room
		if(!empty($evt -> source -> userId)) {
			$this -> cs_line_room_dao -> check_room($evt -> source -> userId);
			$this -> cs_line_dao -> add_msg($evt);
		}


		$message = $evt -> message;
		$source = $evt -> source;

		// bind source to message
		$message -> source = $source;

		$user_id = $source -> userId;
		$user = $this -> users_dao -> find_by('line_sub', $user_id);

		if(empty($user)) {
			// return when no user
			return;
		}


		if($message -> type == "text") {
			$msg_arr = array();

			if(!empty($user -> line_session)) {
					$line_session = json_decode($user -> line_session);
					$this -> do_session_action($msg_arr, $message, $line_session, $user, $evt);
					return;
			}


			if($message -> text == 'COC幣發送') {
				$msg_arr[] = array(
					"type" => "text",
					"text" => "請輸入收禮ID",
				);
				$line_session = new stdClass;
				$line_session -> type = "贈禮_輸入收禮ID";
				$this -> users_dao -> update(array(
					"line_session" => json_encode($line_session)
				), $user -> id);
			}

			if($message -> text == '行情查詢') {
				$Date = date("Y-m-d");
				$price = $this -> d_q_dao -> find_d_q($Date);
				if(!empty($price)){
					$msg_arr[] = array(
						"type" => "text",
						"text" => "今日開盤均價: {$price->average_price}\n目前均價: {$price->now_price}",
					);
				} else{
					$price = $this -> d_q_dao -> find_last_d_q($Date);
					$dtx = array();
					$dtx['date'] = $Date;
					$dtx['last_price'] = $price->last_price;
					$dtx['now_price'] = $price->now_price;
					$this -> d_q_dao -> insert($dtx);
					$msg_arr[] = array(
						"type" => "text",
						"text" => "今日開盤均價: 今日為開盤\n昨天開盤均價: {$price->average_price}\n目前均價: {$price->now_price}",
					);
				}
				$sum_amt = intval($sum_amt);
				$gift_id = $user -> gift_id;

			}

			if($message -> text == "線上儲值") {
				$line_session = new stdClass;
				$line_session -> type = "購買金幣__請輸入金額";
				$this -> users_dao -> update(array(
					"line_session" => json_encode($line_session)
				), $user -> id);

				$msg_arr[] = array(
					"type" => "text",
					"text" => "請輸入金額，超商繳費最低100最高6000，ATM繳費最低100最高30000",
				);
			}


			if($message -> text == "線上儲值123") {
				// echo "hi,..";

				$msg_arr[] = array(
					"type" => 'image',
					// "text" => base_url('img/line_game/game.jpg'),
					"originalContentUrl" => "https://fish.17lineplay.com/coc_bot/img/line_game/game_big.jpg",
  				"previewImageUrl" =>  "https://fish.17lineplay.com/coc_bot/img/line_game/game_small.jpg"
				);
			}

			if($message -> text == "進入遊戲") {
				$line_session = new stdClass;
				$line_session -> type = "進入遊戲_超八";
				$this -> users_dao -> update(array(
					"line_session" => json_encode($line_session)
				), $user -> id);

				$msg_arr[] = array(
					"type" => "text",
					"text" => "已進入遊戲，如需離開遊戲請輸入881",
				);
				$this -> show_super_8($msg_arr, TRUE);
			}



			if($message -> text == '分享好友') {
				$share_url = GAME_WEB_URL . "?promo={$user->gift_id}";
				$msg_arr[] = array(
					"type" => "text",
					"text" => $share_url,
				);

			}

			if(substr($message -> text,0,-2)=="下注_超八" ) {
				$i = array();
				$i['user_id'] = $user -> id;
				if(substr($message -> text,-1)=="8"){
					$i['bet'] = 8;
				}
				if(substr($message -> text,-2)=="40"){
					$i['bet'] = 40;
				}
				if(substr($message -> text,-2)=="80"){
					$i['bet'] = 80;
				}
				$n_res = $this -> curl -> simple_post("/api/Game_list/game_tiger", $i);
				$data = json_decode($n_res);
				$msg_arr[] = array(
					"type" => "text",
					"text" => "$n_res",
				);

			}

			if($message -> text == '下注_超八_40') {
				$i = array();
				$i['bet'] = 40;
				$i['user_id'] = $user -> id;

				$n_res = $this -> curl -> simple_post("/api/Game_list/game_tiger", $i);
				$data = json_decode($n_res);
				$msg_arr[] = array(
					"type" => "text",
					"text" => "$n_res",
				);

			}

			if($message -> text == '下注_超八_80') {
				$i = array();
				$i['bet'] = 80;
				$i['user_id'] = $user -> id;

				$n_res = $this -> curl -> simple_post("/api/Game_list/game_tiger", $i);
				$data = json_decode($n_res);
				$msg_arr[] = array(
					"type" => "text",
					"text" => "$n_res",
				);

			}



			if($message -> text == '錢包查詢') {
				$sum_amt = $this -> wtx_dao -> get_sum_amt($user -> id);
				$users = $this -> users_dao -> find_by_id($user -> id);
				$sum_amt = intval($sum_amt);
				$gift_id = $user -> gift_id;
				$msg_arr[] = array(
					"type" => "text",
					"text" => "您的餘額： {$sum_amt}\n您的贈禮ID為: $gift_id\n您的錢包地址為: {$users->wallet_code}",
				);
			}


			// send message
			if(count($msg_arr) > 0) {
				$p = array();
				$p['replyToken'] = $evt -> replyToken;
				$p['messages'] = $msg_arr;
				$res = call_line_api("POST", "https://api.line.me/v2/bot/message/reply", json_encode($p), CHANNEL_ACCESS_TOKEN);
			}

		}
	}

	function parse_follow($evt) {
		$msg_arr = array();

		// check room
		if(!empty($evt -> source -> userId)) {
			$this -> cs_line_room_dao -> check_room($evt -> source -> userId);
		}

		// send message 暫時關閉
		if(count($msg_arr) > 0 && FALSE) {
			$p = array();
			$p['replyToken'] = $evt -> replyToken;
			$p['messages'] = $msg_arr;
			$res = call_line_api("POST", "https://api.line.me/v2/bot/message/reply", json_encode($p), CHANNEL_ACCESS_TOKEN);
		}
	}

	function do_session_action($msg_arr, $message, $line_session, $user, $evt) {
		if($message -> text == '881') {
			$msg_arr[] = array(
				"type" => "text",
				"text" => "已取消此功能",
			);
			$this -> users_dao -> update(array(
				"line_session" => ""
			), $user -> id);


		} elseif($line_session -> type == '進入遊戲_超八') {

			if($message -> text == "進入遊戲") {
				$msg_arr[] = array(
					"type" => "text",
					"text" => "已進入遊戲",
				);
				$this -> show_super_8($msg_arr);
			}

			if($message -> text == "遊戲說明") {
				$this -> show_super_8_manual($msg_arr);
			}

		} elseif($line_session -> type == "贈禮_輸入收禮ID") {
			$to_user = $this -> users_dao -> find_by_gift_id_and_corp(1, $message -> text);
			if(empty($to_user)) {
				$msg_arr[] = array(
					"type" => "text",
					"text" => "查無此收禮ID，請輸入881取消此功能",
				);
			} else {
				$msg_arr[] = array(
					"type" => "text",
					"text" => "請輸入轉帳金額",
				);

				$line_session -> type = "贈禮_輸入轉帳金額";
				$line_session -> gift_id = $message -> text;
				$line_session -> to_user_id = $to_user -> id;
				$this -> users_dao -> update(array(
					"line_session" => json_encode($line_session)
				), $user -> id);
			}

		// 贈禮_輸入轉帳金額
		} elseif($line_session -> type == "贈禮_輸入轉帳金額") {
			$amt = intval($message -> text);
			$sum_amt = $this -> wtx_dao -> get_sum_amt($user -> id);

			$msg_arr[] = array(
				"type" => "text",
				"text" => "金額 {$amt}",
			);

			$sum_amt = intval($sum_amt);
			$msg_arr[] = array(
				"type" => "text",
				"text" => "餘額 {$sum_amt}",
			);

			if($amt == 0) {
				$msg_arr[] = array(
					"type" => "text",
					"text" => "金額不得為0",
				);
			} elseif($amt > $sum_amt) {
				$msg_arr[] = array(
					"type" => "text",
					"text" => "金額不足，如果想取消請輸入881取消此功能",
				);
			} else {
				$line_session -> type = "贈禮_輸入轉帳金額_確認中";
				$line_session -> amt = $amt;
				$this -> users_dao -> update(array(
					"line_session" => json_encode($line_session)
				), $user -> id);

				$msg_arr[] = array(
					"type" => "imagemap",
					"baseUrl" => base_url("line_img/line_jpg/yes_or_no/v1/1"),
					"altText" => "是否確定轉帳？",
					"baseSize" => array(
						"width" => "1040",
						"height" => "520"
					),
					"actions" => array(
						array(
							"type" => "message",
							"text" => "是",
							"area" => array(
								"x" => 0,
								"y" => 0,
								"width" => 520,
								"height" => 520
							)
						),
						array(
							"type" => "message",
							"text" => "881",
							"area" => array(
								"x" => 520,
								"y" => 0,
								"width" => 520,
								"height" => 520
							)
						),
					)
				);
			}
		} elseif($line_session -> type == "贈禮_輸入轉帳金額_確認中") {
			if($message -> text == "是") {
				$amt = intval($line_session -> amt);
				$sum_amt = $this -> wtx_dao -> get_sum_amt($user -> id);

				if($amt > $sum_amt) {
					$msg_arr[] = array(
						"type" => "text",
						"text" => "金額不足，如果想取消請輸入881取消此功能",
					);
				} else {
					// 贈禮
					$config = $this -> config_dao -> find_by_id(1);
					$ope_pct = $config -> transfer_gift_pct; // 1%
					$ope_amt = floatval($amt) * floatval($ope_pct) / 100.0;
					$transfer_amt = floatval($amt) + floatval($ope_amt);
					if($transfer_amt > $sum_amt) {
						$msg_arr[] = array(
							"type" => "text",
							"text" => "金額不足，含手續費總計 {$transfer_amt}，如果想取消請輸入881取消此功能",
						);
					} else {
						$out_user = $this -> users_dao -> find_by_id($user -> id);
						$in_user = $this -> users_dao -> find_by("gift_id", $line_session -> gift_id);

						if(!empty($in_user)) {
							$samt =  $this -> wtx_dao -> get_sum_amt($out_user -> id);
							$ope_amt = floatval($amt) * floatval($ope_pct) / 100.0;
							$transfer_amt = floatval($amt) + floatval($ope_amt);
							if($transfer_amt > $samt) {
								$res['error_msg'] = "餘額不足";
								$res['samt'] = $samt;
								$res['transfer_amt'] = $transfer_amt;
							} else {

								$i = array();
								$i['corp_id'] = $in_user -> corp_id;
								$i['amt'] = $amt;
								$i['out_user_id'] = $out_user -> id;
								$i['in_user_id'] = $in_user -> id;
								$i['ope_pct'] = $ope_pct;
								$i['ope_amt'] = $ope_amt;
								$i['status'] = 2;
								$i['is_line'] = 1;
								$last_id = $this -> tsg_dao ->  insert($i);
								$item = $this -> tsg_dao -> find_by_id($last_id);

								$u = array();
								$u['transfer_amt'] = $transfer_amt;
								$u['sn'] = "TG" . date("YmdHi") . $last_id;

								$code = $this -> get_reg_code();
								$u['gift_code'] = $code;
								$this -> tsg_dao -> update($u, $last_id);

								$res['last_id'] = $last_id;
								$res['code'] = $code;

								// 㽪禮扣點
								$tx = array();
								$tx['tx_type'] = "gift_transfer";
								$tx['tx_id'] = $last_id;
								$tx['corp_id'] = $item -> corp_id; // corp id
								$tx['user_id'] = $item -> out_user_id;
								$tx['amt'] = -(floatval($item->amt)+floatval($item->ope_amt));
								$tx['brief'] = "$out_user->nick_name 贈禮給 $in_user->nick_name - {$item->amt} 扣點 {$transfer_amt} 手續費 {$item->ope_amt}";
								// $tx['brief'] = "$out_user->nick_name 贈禮給 $in_user->nick_name - {$item->amt}";

								$this -> wtx_dao -> insert($tx);

								// 接收贈禮
								$atx = array();
								$atx['tx_type'] = "gift_transfer_accept";
								$atx['tx_id'] = $last_id;
								$atx['corp_id'] = $item -> corp_id; // corp id
								$atx['user_id'] = $item -> in_user_id;
								$atx['amt'] = $item->amt;
								$atx['brief'] = "$in_user->nick_name 從 $out_user->nick_name 接受贈禮 {$item->amt}";
								$this -> wtx_dao -> insert($atx);

								$samt2 =  $this -> wtx_dao -> get_sum_amt_all($last_id);
								$ctx = array();
								$ctx['tx_type'] = "transfer_gift";
								$ctx['tx_id'] = $last_id;
								$ctx['point_change'] = -floatval($ope_amt)/2.0;
								$ctx['current_point'] =$samt2;
								$ctx['ntd_change'] = 0;
								$ctx['current_ntd'] =0;
								$this -> q_r_dao -> insert($ctx);

								$tx1 = array();
								$tx1['corp_id'] = $item -> corp_id;
								$amt1=floatval($ope_amt)/4.0;
								$tx1['amt'] =	$amt1;
								$tx1['income_type'] = "贈禮公司分潤";
								$tx1['income_id'] = $last_id;
								$tx1['note'] = "贈禮公司分潤 {$amt1}";
								$this -> ctx_dao -> insert($tx1);

								$Date = date("Y-m-d");
								$samt1 =  $this -> wtx_dao -> get_sum_amt_all($last_id);
								$sntd =  $this -> q_r_dao -> get_sum_ntd($last_id);
								$dq =  $this -> d_q_dao -> find_d_q($Date);
								$dtx = array();
								$dtx['date'] = $Date;
								$dtx['average_price'] = floatval($sntd)/floatval($samt1);
								$dtx['last_price'] = floatval($sntd)/floatval($samt1);
								$dtx['now_price'] = floatval($sntd)/floatval($samt1);
								if(!empty($dq)){
									$u_data['last_price'] = floatval($sntd)/floatval($samt1);
									$u_data['now_price'] = floatval($sntd)/floatval($samt1);
									$this -> d_q_dao -> update_by($u_data,id,$dq->id);

								} else{
									$this -> d_q_dao -> insert($dtx);
								}

								$p = array();
								$p['to'] = $in_user -> line_sub;
								$p['messages'][] = array(
									"type" => "text",
									"text" => "從 $out_user->nick_name 接受贈禮 {$item->amt}"
								);
								$res = call_line_api("POST", "https://api.line.me/v2/bot/message/push", json_encode($p), CHANNEL_ACCESS_TOKEN);



								$msg_arr[] = array(
									"type" => "text",
									"text" => "贈禮完成",
								);

								// clear session
								$this -> clear_session($user);
							}
						} else {
							$msg_arr[] = array(
								"type" => "text",
								"text" => "查無收禮者" . $line_session -> gift_id . "??",
							);
						}
					}
				}

			}
		}

		// 儲值
		if($line_session -> type == "購買金幣__請輸入金額") {
			if(strrpos($message -> text, "購買金幣--金額--") === 0) {
				$amt = mb_substr($message -> text, 10);
				$pay_url = base_url("tx/do_tx?l_user_id={$user->id}&tx_amt={$amt}&tx_type=atm");
				$msg_arr[] = array(
					"type" => "text",
					"text" => "ATM繳費連結 $pay_url"
				);

				$pay_url = base_url("tx/do_tx?l_user_id={$user->id}&tx_amt={$amt}&tx_type=market");
				$msg_arr[] = array(
					"type" => "text",
					"text" => "超商繳費連結 $pay_url"
				);

				// $last_id = $this -> payment_dao -> insert(array(
				// 	'user_id' => $user -> id,
				// 	'type_name' => "Etag",
				// 	'uid' => $line_session -> uid,
				// 	'car_plate' => $line_session -> car_plate,
				// 	'amt' => $amt,
				// ));
				//
				// $sn = date("YmdHi")."{$last_id}";
				// $this -> payment_dao -> update(array(
				// 	"sn" => $sn
				// ), $last_id);
				//

				$this -> users_dao -> update(array(
					"line_session" => ''
				), $user -> id);
				//
				// $msg_arr[] = array(
				// 	"type" => "text",
				// 	"text" => "本繳費單 {$sn} 將送審金幣繳費系統，在金幣足夠下將直接進行扣款金幣，系統將在繳費完成後通知用戶。"
				// );

			} elseif(strrpos($message -> text, "購買金幣--取消購買") === 0) {
				$msg_arr[] = array(
					"type" => "text",
					"text" => "該購買已經取消",
				);
				$this -> users_dao -> update(array(
					"line_session" => ""
				), $user -> id);
			} else {
				if($message -> text == "請輸入金額，至少1000金幣") {
					return;
				}
				$amt = $message -> text;
				if(intval($amt) >= 1000) {
					$amt = intval($amt);
					$msg_arr[] = array(
						"type" => "text",
						"text" => "請確認金額 {$amt} 是否正確？",
					);
					$msg_arr[] = array(
						"type" => "imagemap",
						"baseUrl" => "https://wa-lotterygame.com/wa_backend/line_img/line_jpg/yes_or_no/v1/1",
						"altText" => "請確認金額 {$amt} 是否正確？",
						"baseSize" => array(
							"width" => "1040",
							"height" => "520"
						),
						"actions" => array(
							array(
								"type" => "message",
								"text" => "購買金幣--金額--{$amt}",
								"area" => array(
									"x" => 0,
									"y" => 0,
									"width" => 520,
									"height" => 520
								)
							),
							array(
								"type" => "message",
								"text" => "購買金幣--取消購買",
								"area" => array(
									"x" => 520,
									"y" => 0,
									"width" => 520,
									"height" => 520
								)
							),
						)
					);
				} else {
					$msg_arr[] = array(
						"type" => "text",
						"text" => "請輸入大於1000的數字",
					);
				}
			}
		}

		// send message
		if(count($msg_arr) > 0) {
			$p = array();
			$p['replyToken'] = $evt -> replyToken;
			$p['messages'] = $msg_arr;
			$res = call_line_api("POST", "https://api.line.me/v2/bot/message/reply", json_encode($p), CHANNEL_ACCESS_TOKEN);
		}
	}

	private function show_super_8_manual(&$msg_arr) {
		$msg_arr[] = array(
			"type" => 'image',
			"originalContentUrl" => "https://fish.17lineplay.com/coc_bot/img/line_game/game_big.jpg",
			"previewImageUrl" =>  "https://fish.17lineplay.com/coc_bot/img/line_game/game_small.jpg"
		);
	}

	private function show_super_8(&$msg_arr, $is_first = FALSE) {
		$msg_arr[] = array(
			"type" => "imagemap",
			"baseUrl" => base_url("line_img/line_jpg/first_game/v1/1"),
			"altText" => "下注金額",
			"baseSize" => array(
				"width" => "1040",
				"height" => "1500"
			),
			"actions" => array(
				array(
					"type" => "message",
					"text" => "遊戲說明",
					"area" => array(
						"x" => 90,
						"y" => 845,
						"width" => 880,
						"height" => 202
					)
				),
				array(
					"type" => "message",
					"text" => "下注_超八_8",
					"area" => array(
						"x" => 90,
						"y" => 1234,
						"width" => 208,
						"height" => 202
					)
				),
				array(
					"type" => "message",
					"text" => "下注_超八_40",
					"area" => array(
						"x" => 425,
						"y" => 1234,
						"width" => 208,
						"height" => 202
					)
				),
				array(
					"type" => "message",
					"text" => "下注_超八_80",
					"area" => array(
						"x" => 758,
						"y" => 1234,
						"width" => 208,
						"height" => 202
					)
				),
			)
		);
	}

	private function do_log($tag = '', $note = '') {
		$i_data['post'] =json_encode($_POST, JSON_UNESCAPED_UNICODE);
		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$i_data['tag'] = $tag;
		$i_data['full_path'] = $actual_link;
		$i_data['note'] = $note;
		$i_data['q'] = file_get_contents('php://input');
		$i_data['h'] = json_encode(getallheaders());
		$this -> post_log_dao -> insert($i_data);
	}

	private function get_reg_code() {
		$digits = 6;
		return rand(pow(10, $digits - 1), pow(10, $digits) - 1);
	}

	private function clear_session($user) {
		$this -> users_dao -> update(array(
			"line_session" => ""
		), $user -> id);
	}
}
