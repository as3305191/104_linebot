<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Line_bot extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		$this -> load -> model('Transfer_gift_dao', 'tsg_dao');
		$this -> load -> model('Transfer_gift_allocation_dao', 'tsga_dao');

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
		$this -> load -> model('Game_pool_dao', 'game_pool_dao');
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
					"text" => "請輸入錢包地址",
				);
				$line_session = new stdClass;
				$line_session -> type = "贈禮_輸入錢包地址";
				$this -> users_dao -> update(array(
					"line_session" => json_encode($line_session)
				), $user -> id);
			}

			if($message -> text == '交流群組') {
				$msg_arr[] = array(
					"type" => "text",
					"text" => "http://line.me/ti/g/fkZ0uKbcmj",
				);
			}

			if($message -> text == '行情查詢') {
				$Date = date("Y-m-d");
				$price = $this -> d_q_dao -> find_d_q($Date);
				$price1 = floatval($price->now_price)*floatval(1.05);
				$price2 = floatval($price->now_price)*floatval(0.95);
				if(!empty($price)){
					$msg_arr[] = array(
						"type" => "text",
						"text" => "今日開盤均價: {$price->average_price}\n目前均價: {$price->now_price}\n建議買價: {$price1}\n建議賣價: {$price2}",
					);
				} else{
					$p = $this -> d_q_dao -> find_last_d_q($Date);
					$dtx = array();
					$dtx['date'] = $Date;
					$dtx['average_price'] = $p->last_price;
					$dtx['last_price'] = $p->last_price;
					$dtx['now_price'] = $p->now_price;
					$this -> d_q_dao -> insert($dtx);
					$price1 = floatval($p->now_price)*floatval(1.05);
					$price2 = floatval($p->now_price)*floatval(0.95);
					$msg_arr[] = array(
						"type" => "text",
						"text" => "今日開盤均價: {$p->last_price}\n目前均價: {$p->now_price}\n建議買價: {$price1}\n建議賣價: {$price2}",
					);
				}
				$sum_amt = intval($sum_amt);
				$gift_id = $user -> gift_id;
			}

			if($message -> text == "繳費服務") {
				$line_session = new stdClass;
				$line_session -> type = "購買金幣__請輸入金額";
				$this -> users_dao -> update(array(
					"line_session" => json_encode($line_session)
				), $user -> id);

				$msg_arr[] = array(
					"type" => "text",
					"text" => "＊如需離開購買流程請輸入 881",
				);

				$msg_arr[] = array(
					"type" => "text",
					"text" => "請輸入想購買的台幣金額？（單筆上限台幣10萬）",
				);
			}

			if($message -> text == "線上儲值123") {
				$msg_arr[] = array(
					"type" => 'image',
					"originalContentUrl" => "https://fish.17lineplay.com/coc_bot/img/line_game/game_big.jpg",
  				"previewImageUrl" =>  "https://fish.17lineplay.com/coc_bot/img/line_game/game_small.jpg"
				);
			}

			if($message -> text == "進入遊戲") {
				$line_session = new stdClass;
				$line_session -> type = "進入遊戲_超八";
				$this -> update_session($line_session, $user);

				$msg_arr[] = array(
					"type" => "text",
					"text" => "已進入遊戲，如需離開遊戲請輸入881",
				);
				$this -> show_super_8($msg_arr, TRUE);
			}

			if($message -> text == "功能選單") {
				$user_id=$user -> id;
				$this -> function_menu($msg_arr, $user_id);
			}

			// if($message -> text == '分享好友') {
			// 	$corp = $this -> corp_dao -> find_by_id(1);
			// 	$share_url = GAME_WEB_URL . "?promo={$user->gift_id}";
			// 	$line_share_url = urlencode("立即加入coc娛樂虛擬貨幣平台，讓你體驗漲的感覺及虛擬貨幣的娛樂應用" . GAME_WEB_URL . "?promo={$user->gift_id}");
			// 	$msg_arr[] = array(
			// 		"type" => "imagemap",
			// 		"baseUrl" => base_url("line_img/line_jpg/share/v1/1"),
			// 		"altText" => "分享好友",
			// 		"baseSize" => array(
			// 			"width" => "1200",
			// 			"height" => "810"
			// 		),
			// 		"actions" => array(
			// 			array(
			// 				"type" => "uri",
			// 				"linkUri" => "http://line.naver.jp/R/msg/text/?{$line_share_url}",
			// 				"area" => array(
			// 					"x" => 0,
			// 					"y" => 0,
			// 					"width" => 600,
			// 					"height" => 810
			// 				)
			// 			),
			//
			// 		)
			// 	);
			// }

			if($message -> text == '查詢分潤') {
				$list = $this -> tsga_dao -> find_list_limit(array('user_id'=> $user -> id ,'start' => 0,'length'=> 10));

				if(count($list) > 0){
					$cArray= array("近期分潤紀錄");
					foreach ($list as $each) {
						$value =' $'.$each->ope_amt.' '. mb_substr($each->create_time, 0, -3); ;
						array_push($cArray,$value);
					}
					$showContet = implode("\n",$cArray);
					$msg_arr[] = array(
						"type" => "text",
						"text" => " {$showContet}",
					);

				}else{

					$msg_arr[] = array(
						"type" => "text",
						"text" => "目前無紀錄" ,
					);
				}

			}

			if($message -> text == '錢包查詢') {
				$id=$user -> id;
				// $users = $this -> users_dao -> find_by_id($user -> id);
				$this -> wallet_card($msg_arr,$id);

			}



			if($message -> text == '複製地址') {
				$sum_amt = $this -> wtx_dao -> get_sum_amt($user -> id);
				$users = $this -> users_dao -> find_by_id($user -> id);
				$sum_amt = intval($sum_amt);
				$msg_arr[] = array(
					"type" => "text",
					"text" => "{$users->wallet_code}",
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
			$this -> clear_session($user);

		} elseif($line_session -> type == '進入遊戲_超八') {

			if($message -> text == "進入遊戲") {
				$msg_arr[] = array(
					"type" => "text",
					"text" => "已進入遊戲",
				);
				$this -> show_super_8($msg_arr);
			} elseif($message -> text == "遊戲說明") {
				$this -> show_super_8_manual($msg_arr);
			} elseif(mb_substr($message -> text,0,5)=="下注_超八" ) {
				$this -> bet_super_8($msg_arr, $message, $user);
			} else {
				$msg_arr[] = array(
					"type" => "text",
					"text" => "已進入「超八遊戲」，離開遊戲請輸入 881",
				);
			}

		} elseif($line_session -> type == "贈禮_輸入錢包地址") {
			$to_user = $this -> users_dao -> find_by_wallet_code_and_corp(1, $message -> text);
			if(empty($to_user)) {
				$msg_arr[] = array(
					"type" => "text",
					"text" => "查無此錢包地址，請輸入881取消此功能",
				);
			} else {
				$msg_arr[] = array(
					"type" => "text",
					"text" => "請輸入轉帳金額",
				);

				$line_session -> type = "贈禮_輸入轉帳金額";
				$line_session -> wallet_code = $message -> text;
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

					// 是否免手續費
					if($user -> is_bypass_service_fee == 1) {
						$ope_amt = 0;
					}

					$transfer_amt = floatval($amt) + floatval($ope_amt);
					if($transfer_amt > $sum_amt) {
						$msg_arr[] = array(
							"type" => "text",
							"text" => "金額不足，含手續費總計 {$transfer_amt}，如果想取消請輸入881取消此功能",
						);
					} else {
						$out_user = $this -> users_dao -> find_by_id($user -> id);
						$promo_user = $this -> users_dao -> find_by_id($user -> id); // for 向上分配
						$in_user = $this -> users_dao -> find_by("wallet_code", $line_session -> wallet_code);

						if(!empty($in_user)) {
							$samt =  $this -> wtx_dao -> get_sum_amt($out_user -> id);
							$ope_amt = floatval($amt) * floatval($ope_pct) / 100.0;

							// 是否免手續費
							if($user -> is_bypass_service_fee == 1) {
								$ope_amt = 0;
							}

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
								$get_current_ntd = $this -> q_r_dao -> get_current_ntd();
								$get_all_pool=$this -> game_pool_dao -> get_all_pool_amt();

								if($ope_amt > 0) {
									// 公司分潤
									$tx1 = array();
									$tx1['corp_id'] = $item -> corp_id;
									$amt1=floatval($ope_amt)/4.0;
									$tx1['amt'] =	$amt1;
									$tx1['income_type'] = "贈禮公司分潤";
									$tx1['income_id'] = $last_id;
									$tx1['note'] = "贈禮公司分潤 {$amt1}";
									$this -> ctx_dao -> insert($tx1);

									// 公司分潤 point
									$atx = array();
									$atx['tx_type'] = "add_coin_gift";
									$atx['tx_id'] = $last_id;
									$atx['corp_id'] = 1; // corp id
									$atx['user_id'] = 528; // 固定給小林
									$atx['amt'] = $amt1;
									$atx['brief'] = "贈禮公司分潤 {$amt1}";
									$this -> wtx_dao -> insert($atx);

									// 向上分配
									$promo_user_id = 0;
									$alloc_amt = floatval($ope_amt) / 4.0;

									do {
										// code...
										if(!empty($promo_user)){

											// 分配推薦人
											$promo_user_id = $promo_user -> promo_user_id;
											$aloc = array();
											$aloc['corp_id'] = $item -> corp_id;
											$aloc_amt = floatval($alloc_amt) *0.2;
											$aloc['amt'] =	$aloc_amt;
											$aloc['tx_type'] = "gift_transfer_allocation";
											$aloc['user_id'] = $promo_user_id;
											$aloc['tx_id'] = $last_id;
											$aloc['brief'] = "贈禮向上分配分潤 {$aloc_amt}";
											$aloc_id = $this -> wtx_dao -> insert($aloc);
											$m_ctx = $this -> wtx_dao -> find_by_id($aloc_id);

											// 分配記錄
											$aloc1 = array();
											$aloc1['corp_id'] = $item -> corp_id;
											$aloc1['transfter_gift_id'] = $last_id;
											$aloc1['ope_amt'] =	$aloc_amt;
											$aloc1['user_id'] = $promo_user_id;
											$this -> tsga_dao -> insert($aloc1);

											// 計算最後剩餘金額
											$residual_amt = $alloc_amt - $m_ctx -> amt;

											if($m_ctx -> amt == 0){
												$promo_user_id = 0;

												$aloc_com = array();
												$aloc_com['corp_id'] = $item -> corp_id;
												$aloc_com_amt = floatval($alloc_amt);
												$aloc_com['amt'] =	$aloc_com_amt;
												$aloc_com['income_type'] = "贈禮向上分配";
												$aloc_com['income_id'] = $last_id;
												$aloc_com['note'] = "贈禮向上分配分潤 {$aloc_com_amt}";
												$this -> ctx_dao -> insert($aloc_com);

												// 公司分潤 point
												$atx = array();
												$atx['tx_type'] = "add_coin_gift";
												$atx['tx_id'] = $last_id;
												$atx['corp_id'] = 1; // corp id
												$atx['user_id'] = 528; // 固定給小林
												$atx['amt'] = $aloc_com_amt;
												$atx['brief'] = "贈禮向上分配分潤 {$aloc_com_amt}";
												$this -> wtx_dao -> insert($atx);

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
													$aloc_com['corp_id'] = $item -> corp_id;
													$aloc_com_amt = floatval($alloc_amt);
													$aloc_com['amt'] =	$aloc_com_amt;
													$aloc_com['income_type'] = "贈禮向上分配";
													$aloc_com['income_id'] = $last_id;
													$aloc_com['note'] = "贈禮向上分配分潤 {$aloc_com_amt}";
													$this -> ctx_dao -> insert($aloc_com);

													// 公司分潤 point
													$atx = array();
													$atx['tx_type'] = "add_coin_gift";
													$atx['tx_id'] = $last_id;
													$atx['corp_id'] = 1; // corp id
													$atx['user_id'] = 528; // 固定給小林
													$atx['amt'] = $aloc_com_amt;
													$atx['brief'] = "贈禮向上分配分潤 {$aloc_com_amt}";
													$this -> wtx_dao -> insert($atx);
												}

											}

										}else{
											$promo_user_id = 0;

											// 向上分配給公司
											$aloc_com = array();
											$aloc_com['corp_id'] = $item -> corp_id;
											$aloc_com_amt = floatval($alloc_amt);
											$aloc_com['amt'] =	$aloc_com_amt;
											$aloc_com['income_type'] = "向上分配";
											$aloc_com['income_id'] = $last_id;
											$aloc_com['note'] = "贈禮向上分配分潤 {$aloc_com_amt}";
											$this -> ctx_dao -> insert($aloc_com);

											// 公司分潤 point
											$atx = array();
											$atx['tx_type'] = "add_coin_gift";
											$atx['tx_id'] = $last_id;
											$atx['corp_id'] = 1; // corp id
											$atx['user_id'] = 528; // 固定給小林
											$atx['amt'] = $aloc_com_amt;
											$atx['brief'] = "贈禮向上分配分潤 {$aloc_com_amt}";
											$this -> wtx_dao -> insert($atx);

										}
									} while ($promo_user_id > 0);
								}

								$get_all_wtx=$this -> wtx_dao -> get_sum_amt_total();
								$get_all_pool=$this -> game_pool_dao -> get_all_pool_amt();

								$ctx = array();
								$ctx['tx_type'] = "transfer_gift";
								$ctx['tx_id'] = $last_id;
								$ctx['point_change'] = -floatval($ope_amt)/2.0;
								$current_point= $get_all_wtx+$get_all_pool; // 加上彩池
								$ctx['current_point'] = $current_point;
								$ctx['ntd_change'] = 0;
								$ctx['current_ntd'] = $get_current_ntd -> current_ntd;
								$this -> q_r_dao -> insert($ctx);

								$Date = date("Y-m-d");

								$get_current_ntd1=$this -> q_r_dao -> get_current_ntd();
								$get_current_point1=$this -> q_r_dao -> get_current_point();
								$dq =  $this -> d_q_dao -> find_d_q($Date);
								$p1 = $this -> d_q_dao -> find_last_d_q($Date);
								$dtx = array();
								$dtx['date'] = $Date;
								$cp = floatval(intval($current_point)); // 避免除0問題
								$p = 0;
								if($cp != 0) {
									$p=floatval($get_current_ntd1->current_ntd)/floatval(intval($current_point));
								}
								$price=round($p,8);
								$dtx['average_price'] = $p1->last_price;
								$dtx['last_price'] = $price;
								$dtx['now_price'] = $price;
								if(!empty($dq)){
									$u_data['last_price'] = $price;
									$u_data['now_price'] =$price;
									$this -> d_q_dao -> update_by($u_data,'id',$dq->id);

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
								"text" => "查無收禮者" . $line_session -> wallet_code . "??",
							);
						}
					}
				}
			}

			// 儲值
		} elseif($line_session -> type == "購買金幣__請輸入金額") {
			if(strrpos($message -> text, "購買貨幣--數量--") === 0) {
				$amt = mb_substr($message -> text, 10);
				$pay_url = base_url("tx/do_tx?l_user_id={$user->wallet_code}&tx_amt={$amt}&tx_type=atm");
				$msg_arr[] = array(
					"type" => "text",
					"text" => "ATM繳費連結 $pay_url"
				);
				$this -> users_dao -> update(array(
					"line_session" => ''
				), $user -> id);

			} elseif(strrpos($message -> text, "購買貨幣--取消購買") === 0) {
				$msg_arr[] = array(
					"type" => "text",
					"text" => "該購買已經取消",
				);
				$this -> users_dao -> update(array(
					"line_session" => ""
				), $user -> id);
			} else {
				if($message -> text == "請輸入小於100000的金額") {
					return;
				}
				$amt = $message -> text;
				if(intval($amt) <= 100000) {
					$amt = intval($amt);

					$Date = date("Y-m-d");
					$price = $this -> d_q_dao -> find_d_q($Date);
					$price1 = floatval($price->now_price)*floatval(1.05);

					$msg_arr[] = array(
						"type" => "text",
						"text" => "現價買價 {$price1}",
					);
					$msg_arr[] = array(
						"type" => "text",
						"text" => "請確認金額 {$amt} 是否正確？",
					);

					$pay_url = base_url("tx/do_tx?l_user_id={$user->wallet_code}&tx_amt={$amt}&tx_type=atm");
					$msg_arr[] = array(
						"type" => "imagemap",
						"baseUrl" => base_url("line_img/line_jpg/yes_or_no/v2/1"),
						"altText" => "請確認金額 {$amt} 是否正確？",
						"baseSize" => array(
							"width" => "1040",
							"height" => "520"
						),

						"actions" => array(
							array(
								"type" => "text",
								"text" => "購買貨幣--數量--{$amt}",
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
				} else {
					$msg_arr[] = array(
						"type" => "text",
						"text" => "請輸入小於100000的金額",
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
			"originalContentUrl" => base_url("img/line_game/super8_manual.jpg"),
			"previewImageUrl" =>  base_url("img/line_game/super8_manual_thumb.jpg")
		);
	}

	private function bet_super_8(&$msg_arr, $message, $user) {
		if(mb_substr($message -> text,0,5)=="下注_超八" ) {
			$i = array();
			$i['user_id'] = $user -> id;
			$user_point=  $this -> wtx_dao -> get_sum_amt($user -> id);
			if(mb_substr($message -> text,-3) == "0.8"){
				if($user_point<0.8){
					$msg_arr[] = array(
						"type" => "text",
						"text" => "您的貨幣不到0.8,如果想取消請輸入881取消此功能",
					);
				} else{
					$i['bet'] = 0.8;
					$n_res = $this -> curl -> simple_post("/api/Game_list/game_tiger", $i);
					$data = json_decode($n_res);
					$id=$data->last_id;
					// $msg_arr[] = array(
					// 	"type" => "text",
					// 	"text" => "$n_res",
					// );
					$this -> show_super_8_second($msg_arr,$id, TRUE);

				}
			}
			if(mb_substr($message -> text,-2)=="08"){
				if($user_point<8){
					$msg_arr[] = array(
						"type" => "text",
						"text" => "您的貨幣不到8,如果想取消請輸入881取消此功能",
					);
				} else{
					$i['bet'] = 8;
					$n_res = $this -> curl -> simple_post("/api/Game_list/game_tiger", $i);
					$data = json_decode($n_res);
					$id=$data->last_id;
					// $msg_arr[] = array(
					// 	"type" => "text",
					// 	"text" => "$id",
					// );
					$this -> show_super_8_second($msg_arr,$id, TRUE);
				}
			}
			if(mb_substr($message -> text,-2)=="40"){
				if($user_point<40){
					$msg_arr[] = array(
						"type" => "text",
						"text" => "您的貨幣不到40,如果想取消請輸入881取消此功能",
					);
				} else{
					$i['bet'] = 40;
					$n_res = $this -> curl -> simple_post("/api/Game_list/game_tiger", $i);
					$data = json_decode($n_res);
					$id=$data->last_id;
					// $msg_arr[] = array(
					// 	"type" => "text",
					// 	"text" => "$id",
					// );
					$this -> show_super_8_second($msg_arr,$id, TRUE);

				}
			}
		}
	}

	private function show_super_8(&$msg_arr, $is_first = FALSE) {
		$msg_arr[] = array(
			"type" => "imagemap",
			"baseUrl" => base_url("line_img/line_jpg/0815/v1/1"),
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
					"text" => "下注_超八_0.8",
					"area" => array(
						"x" => 90,
						"y" => 1234,
						"width" => 208,
						"height" => 202
					)
				),
				array(
					"type" => "message",
					"text" => "下注_超八_08",
					"area" => array(
						"x" => 425,
						"y" => 1234,
						"width" => 208,
						"height" => 202
					)
				),
				array(
					"type" => "message",
					"text" => "下注_超八_40",
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


	private function show_super_8_second(&$msg_arr,$id ,$is_first = FALSE) {
		// $msg_arr[] = array(
		// 	"type" => "text",
		// 	"text" => base_url("line_img/line_result/{$id}/v1/1"),
		// );
		// return;
		$time = time();

		$msg_arr[] = array(
			"type" => "imagemap",
			"baseUrl" => base_url("line_img/line_result/{$id}/v1{$time}/1"),
			"altText" => "下注金額",
			"baseSize" => array(
				"width" => "1040",
				"height" => "1688"
			),
			"actions" => array(
				array(
					"type" => "message",
					"text" => "881",
					"area" => array(
						"x" => 64,
						"y" => 54,
						"width" => 903,
						"height" => 140
					)
				),
				array(
					"type" => "message",
					"text" => "下注_超八_0.8",
					"area" => array(
						"x" => 90,
						"y" => 1443,
						"width" => 213,
						"height" => 209
					)
				),
				array(
					"type" => "message",
					"text" => "下注_超八_08",
					"area" => array(
						"x" => 425,
						"y" => 1443,
						"width" => 213,
						"height" => 209
					)
				),
				array(
					"type" => "message",
					"text" => "下注_超八_40",
					"area" => array(
						"x" => 758,
						"y" => 1443,
						"width" => 213,
						"height" => 209
					)
				),
			)
		);

		// $msg_arr[] = array(
		// 	"type" => "text",
		// 	"text" => base_url("line_img/line_result/{$id}/v1{$time}/1"),
		// );
	}

	private function show_super_8_not_first($id) {
		$msg_arr[] = array(
			"type" => "text",
			"text" => base_url("line_img/line_result/{$id}/v1/1"),
		);
		return;
		$msg_arr[] = array(
			"type" => "imagemap",
			"baseUrl" => base_url("line_img/line_result/{$id}/v1/1"),
			"altText" => "下注金額",
			"baseSize" => array(
				"width" => "1040",
				"height" => "1500"
			),
			"actions" => array(
			array(
				"type" => "message",
				"text" => "881",
				"area" => array(
					"x" => 70,
					"y" => 54,
					"width" => 910,
					"height" => 130
					)
				),
				array(
					"type" => "message",
					"text" => "下注_超八_0.8",
					"area" => array(
						"x" => 90,
						"y" => 1234,
						"width" => 208,
						"height" => 202
					)
				),
				array(
					"type" => "message",
					"text" => "下注_超八_08",
					"area" => array(
						"x" => 425,
						"y" => 1234,
						"width" => 208,
						"height" => 202
					)
				),
				array(
					"type" => "message",
					"text" => "下注_超八_40",
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

	private function show_super_8_test(&$msg_arr, $is_first = FALSE) {
		$im = loadJpeg();
		header('Content-Type: image/png');
		$image = imagejpeg($im,'simpletext.jpg');

		echo $image;


	}

	private function wallet_card(&$msg_arr,$id) {
		$time = time();
		$msg_arr[] = array(
			"type" => "imagemap",
			"baseUrl" => base_url("line_img/line_gift/{$id}/v{$time}/8"),
			"altText" => "COC錢包",
			"baseSize" => array(
				"width" => "1040",
				"height" => "653"
			),
			"actions" => array(
				array(
					"type" => "message",
					"text" => "複製地址",
					"area" => array(
						"x" => 587,
						"y" => 479,
						"width" => 359,
						"height" => 109
					)
				),
			)
		);

	}


	private function function_menu(&$msg_arr,$user_id) {
		$user = $this -> users_dao -> find_by_id($user_id);
		$corp = $this -> corp_dao -> find_by_id(1);
		$share_url = GAME_WEB_URL . "?promo={$user->gift_id}";
		$line_share_url = urlencode("立即加入coc娛樂虛擬貨幣平台，讓你體驗漲的感覺及虛擬貨幣的娛樂應用\n" . GAME_WEB_URL . "?promo={$user->gift_id}");
		$msg_arr[] = array(
			"type" => "imagemap",
			"baseUrl" => base_url("line_img/line_jpg/share/v3/1"),
			"altText" => "功能選單",
			"baseSize" => array(
				"width" => "1040",
				"height" => "585"
			),
			"actions" => array(
				array(
					"type" => "uri",
					"linkUri" => "line://app/1603348495-3gP8o0wv",
					"area" => array(
						"x" => 16,
						"y" => 13,
						"width" => 486,
						"height" => 267
					)
				),
				array(
						"type" => "uri",
						"linkUri" => "http://line.naver.jp/R/msg/text/?{$line_share_url}",
						"area" => array(
							"x" => 539,
							"y" => 14,
							"width" => 486,
							"height" => 267
						)
				),
				array(
					"type" => "message",
					"text" => "繳費服務",
					"area" => array(
						"x" => 15,
						"y" => 309,
						"width" => 486,
						"height" => 267
					)
				),
				array(
					"type" => "uri",
					"linkUri" => "http://nav.cx/9Upx2Ou",
					"area" => array(
						"x" => 539,
						"y" => 309,
						"width" => 486,
						"height" => 267
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

	private function update_session($line_session, $user) {
		$this -> users_dao -> update(array(
			"line_session" => json_encode($line_session)
		), $user -> id);
	}

	private function clear_session($user) {
		$this -> users_dao -> update(array(
			"line_session" => ""
		), $user -> id);
	}

	function loadJpeg(){

		/* 尝试打开 */
		$im = @imagecreatefromjpeg(base_url("line_img/line_jpg/first_game/v1/1"));

		/* See if it failed */
		if(!$im)
		{
				/* Create a black image */
				$im  = imagecreatetruecolor(150, 30);
				$bgc = imagecolorallocate($im, 255, 255, 255);
				$tc  = imagecolorallocate($im, 0, 0, 0);

				imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

				/* Output an error message */
				imagestring($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
		}

		imagettftext($im, 30, 0, 190, 600, $black, $font, '1111');

		return $im;

	}


}
