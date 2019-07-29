<?php
class User_buy extends MY_Base_Controller {

	function __construct() {
		parent::__construct();

		// setup models
		$this -> load -> model('Pay_records_dao', 'dao');
		$this -> load -> model('Post_log_dao', 'post_log_dao');

		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Bonus_tx_dao', 'tx_dao');
		$this -> load -> model('Users_dao', 'u_dao');
		$this -> load -> model('Products_dao', 'p_dao');

		//載入SDK(路徑可依系統規劃自行調整)
		include APPPATH . 'third_party/ECPay.Payment.Integration.php';
		include APPPATH . 'third_party/submit.class.php';
	}

	public function do_pay($gift_id, $amt) {
		$data = array();

		$tx_type = 'atm';
		$tx_amt = $amt;
		$tx_amt = intval($tx_amt);

		// check chs
		$login_user = $this -> users_dao -> find_by("gift_id", $gift_id);
		$corp = $this -> corp_dao -> find_by_id($login_user -> corp_id);
		$l_user_id = $login_user -> id;


		$data['user_id'] = $l_user_id;
		$data['corp_id'] = $login_user -> corp_id;
		$data['amt'] = $tx_amt;
		// $data['faa_amt'] = $tx_amt;
		// $data['pay_type'] = $tx_type;
		$data['sn'] = 'P' . date('YmdHis');
		$id = $this -> dao -> insert($data);
		$item = $this -> dao -> find_by_id($id);


		try {
    		$obj = new ECPay_AllInOne();

				$ChoosePayment = NULL;
				if($tx_type == 'atm') { // atm
					$ChoosePayment = ECPay_PaymentMethod::ATM;
				}
				if($tx_type == 'market') { // cvs
					$ChoosePayment = ECPay_PaymentMethod::CVS;
				}
				// if($item -> pay_type_id == 3) { // credit
				// 	$ChoosePayment = ECPay_PaymentMethod::Credit;
				// }

        //服務參數
				if($corp -> merchant_id == '2000132') {
					$obj->ServiceURL  = "https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5";
				} else {
					$obj->ServiceURL  = "https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5";
				}
        // $obj->ServiceURL  = "https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5";   //服務位置
        //$obj->ServiceURL  = "https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5";   //服務位置
        $obj->HashKey     = $corp -> hash_key;                                            //測試用Hashkey，請自行帶入ECPay提供的HashKey
        $obj->HashIV      = $corp -> hash_iv; ;                                            //測試用HashIV，請自行帶入ECPay提供的HashIV
        $obj->MerchantID  = $corp -> merchant_id;                                                      //測試用MerchantID，請自行帶入ECPay提供的MerchantID


        //基本參數(請依系統規劃自行調整)
        $obj->Send['ReturnURL']         = base_url('api/user_buy/notify') ;    		//付款完成通知回傳的網址
        $obj->Send['MerchantTradeNo']   =  $item -> sn ;                             //訂單編號
        $obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                        //交易時間
        $obj->Send['TotalAmount']       = $item -> amt;                                       //交易金額
        $obj->Send['TradeDesc']         = "{$corp->corp_name} 繳款";                           //交易描述
        $obj->Send['ChoosePayment']     = $ChoosePayment;              			          //付款方式:ATM

        //訂單的商品資料
        array_push($obj->Send['Items'], array('Name' => "{$corp->corp_name} 繳款", 'Price' => intval($item -> amt),
                   'Currency' => "元", 'Quantity' => (int) "1", 'URL' => "dedwed"));

        //延伸參數(可依系統需求選擇是否代入)
				if($tx_type == 'atm') { // atm
					$obj->SendExtend['ExpireDate'] = 3 ;     //繳費期限 (預設3天，最長60天，最短1天)
					$obj->SendExtend['PaymentInfoURL'] = base_url('api/user_buy/atm_callback'); //伺服器端回傳付款相關資訊。
					// $obj->SendExtend['ClientRedirectURL'] = base_url('/');      //預設空值
				}
				if($tx_type == 'market') { // cvs
					$obj->SendExtend['Desc_1']            = " 繳款";      //交易描述1 會顯示在超商繳費平台的螢幕上。預設空值
					$obj->SendExtend['Desc_2']            = '';      //交易描述2 會顯示在超商繳費平台的螢幕上。預設空值
					$obj->SendExtend['Desc_3']            = '';      //交易描述3 會顯示在超商繳費平台的螢幕上。預設空值
					$obj->SendExtend['Desc_4']            = '';      //交易描述4 會顯示在超商繳費平台的螢幕上。預設空值
					$obj->SendExtend['PaymentInfoURL']    = base_url('api/user_buy/cvs_callback');      //預設空值
					// $obj->SendExtend['ClientRedirectURL'] = base_url('/');      //預設空值
					$obj->SendExtend['StoreExpireDate']   = '';      //預設空值
				}
				if($item -> pay_type_id == 3) { // credit
					$obj->SendExtend['CreditInstallment'] = 0 ;    //分期期數，預設0(不分期)
					$obj->SendExtend['InstallmentAmount'] = 0 ;    //使用刷卡分期的付款金額，預設0(不分期)
					$obj->SendExtend['Redeem'] = false ;           //是否使用紅利折抵，預設false
					$obj->SendExtend['UnionPay'] = true;          //是否為聯營卡，預設false;
				}

        //產生訂單(auto submit至ECPay)
        $html = $obj->CheckOut();
        echo $html;

    } catch (Exception $e) {
    	echo $e->getMessage();
    }
	}

	public function atm_callback() {
		$this -> do_log('atm_callback');

		$data = json_decode(json_encode($_POST, JSON_UNESCAPED_UNICODE));
		// $data = json_decode('{"BankCode":"005","ExpireDate":"2019\/07\/29","MerchantID":"2000132","MerchantTradeNo":"P20190726102942","PaymentType":"ATM_LAND","RtnCode":"2","RtnMsg":"Get VirtualAccount Succeeded","TradeAmt":"150","TradeDate":"2019\/07\/26 10:31:23","TradeNo":"1907261029420783","vAccount":"5219821052189380","StoreID":"","CustomField1":"","CustomField2":"","CustomField3":"","CustomField4":"","CheckMacValue":"432EB6BEEC84FC8B544AD7675520BDD5"}');

		$RetCode = $data -> RtnCode;
		if($RetCode == 2) {
			// do write back
			$BankCode = $data -> BankCode;
			$ExpireDate = $data -> ExpireDate;
			$sn = $data -> MerchantTradeNo;
			$TradeNo = $data -> TradeNo;
			$vAccount = $data -> vAccount;
			$PaymentType = $data -> PaymentType;

			$br = $this -> dao -> find_by('sn', $sn);
			if(!empty($br)) {
				$u_data = array();
				$u_data['bank_code'] = $BankCode;
				$u_data['v_account'] = $vAccount;
				$u_data['trade_no'] = $TradeNo;
				$u_data['payment_type'] = $PaymentType;
				$u_data['expire_date'] = date('Y-m-d', strtotime($ExpireDate));
				$this -> dao -> update($u_data, $br -> id);

				// send line bot message
				$out_user = $this -> u_dao -> find_by_id($br -> user_id);
				if(!empty($out_user -> line_sub)) {
					$p = array();
					$p['to'] = $out_user -> line_sub;
					$tx_amt = intval($br->amt);
					$p['messages'][] = array(
						"type" => "text",
						"text" => "儲值系統為ATM轉帳功能,請轉至銀行代號 {$BankCode},帳號 {$vAccount},金額 {$tx_amt} 此帳號為虛擬帳號僅限轉帳一次使用，繳費完成後金幣自動入帳"
					);
					$res = call_line_api("POST", "https://api.line.me/v2/bot/message/push", json_encode($p), CHANNEL_ACCESS_TOKEN);
				}
			} else {
				echo "atm_callback123";
			}
		}

		echo "atm_callback";
	}

	public function cvs_callback() {
		$this -> do_log('cvs_callback');

		$data = json_decode(json_encode($_POST, JSON_UNESCAPED_UNICODE));

		$RetCode = $data -> RtnCode;
		if($RetCode == 10100073) {
			// do write back
			$BankCode = $data -> BankCode;
			$ExpireDate = $data -> ExpireDate;
			$sn = $data -> MerchantTradeNo;
			$TradeNo = $data -> TradeNo;
			$vAccount = $data -> vAccount;
			$PaymentType = $data -> PaymentType;
			$PaymentNo = $data -> PaymentNo;

			$br = $this -> dao -> find_by('sn', $sn);
			if(!empty($br)) {
				$u_data = array();
				$u_data['bank_code'] = $BankCode;
				$u_data['v_account'] = $vAccount;
				$u_data['trade_no'] = $TradeNo;
				$u_data['payment_type'] = $PaymentType;
				$u_data['payment_no'] = $PaymentNo;
				$u_data['expire_time'] = $ExpireDate;
				$this -> dao -> update($u_data, $br -> id);

				// send line bot message
				if(!empty($out_user -> line_sub)) {
					$out_user = $this -> u_dao -> find_by_id($br -> user_id);
					$p = array();
					$p['to'] = $out_user -> line_sub;
					$tx_amt = intval($br->amt);
					$p['messages'][] = array(
						"type" => "text",
						"text" => "儲值系統為便利超商轉帳功能,代碼 {$PaymentNo}, 金額 {$tx_amt} 繳費完成後金幣自動入帳"
					);
					$res = call_line_api("POST", "https://api.line.me/v2/bot/message/push", json_encode($p), CHANNEL_ACCESS_TOKEN);
				}

			}
		}

		echo "cvs_callback";
	}

	public function hui_he_callback() {
		$this -> do_log('hui_he_callback');

		$data = json_decode(json_encode($_POST, JSON_UNESCAPED_UNICODE));

		$Code = $data -> Code;
		if($Code == 0) {
			// do write back
			$sn = $data -> OutTradeNo;

			$pr = $this -> dao -> find_by('sn', $sn);
			if(!empty($pr) && $pr -> status == 0) { // not paid

				// update pay record
				$u_data = array();
				$u_data['payment_time'] = date('Y-m-d H:i:s');
				$u_data['status'] = 1; // paid
				$this -> dao -> update($u_data, $pr -> id);

				// commit tx
				$tx = array();
				$tx['pay_record_id'] = $pr -> id;
				$tx['user_id'] = $pr -> user_id;
				$tx['amt'] = $pr -> amt;
				$tx['type_id'] = 1;
				$tx['brief'] = "購買點數 $pr->amt 點";
				$this -> wtx_dao -> insert($tx);

				echo "SUCCESS";
				return;
			}
		}

		echo "FAILURE";
	}

	public function best_pay_callback() {
		$this -> do_log('best_pay_callback');

		// $data = json_decode(json_encode($_POST, JSON_UNESCAPED_UNICODE));
		//
		// $Code = $data -> Code;
		// if($Code == 0) {
		// 	// do write back
		// 	$sn = $data -> OutTradeNo;
		//
		// 	$pr = $this -> dao -> find_by('sn', $sn);
		// 	if(!empty($pr) && $pr -> status == 0) { // not paid
		//
		// 		// update pay record
		// 		$u_data = array();
		// 		$u_data['payment_time'] = date('Y-m-d H:i:s');
		// 		$u_data['status'] = 1; // paid
		// 		$this -> dao -> update($u_data, $pr -> id);
		//
		// 		// commit tx
		// 		$tx = array();
		// 		$tx['pay_record_id'] = $pr -> id;
		// 		$tx['user_id'] = $pr -> user_id;
		// 		$tx['amt'] = $pr -> amt;
		// 		$tx['type_id'] = 1;
		// 		$tx['brief'] = "購買點數 $pr->amt 點";
		// 		$this -> wtx_dao -> insert($tx);
		//
		// 		echo "SUCCESS";
		// 		return;
		// 	}
		// }
		//
		// echo "FAILURE";
	}

	public function notify_chs() {
		$this -> do_log('notify_chs');
		$data = json_decode(json_encode($_POST, JSON_UNESCAPED_UNICODE));
		// $data = json_decode($this->security->xss_clean($this->input->raw_input_stream));

		$RetCode = $data -> returncode;
		if($RetCode == '00') {
			// do write back
			$sn = $data -> orderid;

			$pr = $this -> dao -> find_by('sn', $sn);
			if(!empty($pr) && $pr -> status == 0) { // not paid

				// update pay record
				$u_data = array();
				$u_data['payment_time'] = date("Y-m-d H:i:s");
				$u_data['status'] = 1; // paid
				$this -> dao -> update($u_data, $pr -> id);

				// commit tx

				$tx = array();
				$tx['pay_record_id'] = $pr -> id;
				$tx['corp_id'] = $pr -> corp_id;
				$tx['user_id'] = $pr -> user_id;

				$real_amt = $pr -> amt * 450; // 450倍
				$tx['amt'] = $real_amt;
				$tx['type_id'] = 100;
				$tx['brief'] = "購買點數 $real_amt 點";
				$thresh_tx_id = $this -> wtx_dao -> insert($tx);

				// send line bot message
				$out_user = $this -> u_dao -> find_by_id($pr -> user_id);
				$p = array();
				$p['to'] = $out_user -> line_sub;
				$tx_amt = intval($pr->faa_amt);
				$p['messages'][] = array(
					"type" => "text",
					"text" => "繳費成功，入帳 {$tx_amt} 金幣"
				);
				$res = call_line_api("POST", "https://api.line.me/v2/bot/message/push", json_encode($p), CHANNEL_ACCESS_TOKEN);

			}
		}

		echo "ok";

	}

	public function notify_chs_2() {
		$this -> do_log('notify_chs_2');
		echo "ok";

	}

	public function notify() {
		$this -> do_log('notify');

		$amt_arr = array(
			150,
			300,
			450,
			1490,
			2990,
			6000,
			10000,
			20000,
			30000,
		);
		$coin_arr = array(
			750000,
			1500000,
			2250000,
			7450000,
			15000000,
			30300000,
			50800000,
			102000000,
			153600000,
		);

		$data = json_decode(json_encode($_POST, JSON_UNESCAPED_UNICODE));
		// $data = json_decode($this->security->xss_clean($this->input->raw_input_stream));

		$RetCode = $data -> RtnCode;
		if($RetCode == 1) {
			// do write back
			$PaymentDate = $data -> PaymentDate;
			$sn = $data -> MerchantTradeNo;

			$pr = $this -> dao -> find_by('sn', $sn);
			if(!empty($pr) && $pr -> status == 0) { // not paid

				// update pay record
				$u_data = array();
				$u_data['payment_time'] = $PaymentDate;
				$u_data['status'] = 1; // paid
				$this -> dao -> update($u_data, $pr -> id);

				// commit tx
				$tx = array();
				$tx['tx_id'] = $pr -> id;
				$tx['tx_type'] = "user_pay";
				$tx['corp_id'] = $pr -> corp_id;
				$tx['user_id'] = $pr -> user_id;

				$amt_idx = array_search(intval($pr->amt), $amt_arr);
				$tx_amt = $coin_arr[$amt_idx];

				$tx['amt'] = $tx_amt;
				$tx['brief'] = "購買金幣 {$tx_amt} 花費 {$pr->amt}";
				$this -> wtx_dao -> insert($tx);

				// send line bot message
				$out_user = $this -> u_dao -> find_by_id($pr -> user_id);
				$p = array();
				$p['to'] = $out_user -> line_sub;
				$p['messages'][] = array(
					"type" => "text",
					"text" => "{$pr->sn} 繳費成功，入帳 {$tx_amt} 金幣"
				);
				$res = call_line_api("POST", "https://api.line.me/v2/bot/message/push", json_encode($p), CHANNEL_ACCESS_TOKEN);
				// intro
				// $tx = array();
				// $tx['buy_record_id'] = $br -> id;
				// $tx['user_id'] = $b_user -> intro_id;
				// $tx['total_price'] = $br -> total_price;
				// $tx['percent'] = 20;
				// $tx['amt'] = $br -> total_price * 20.0 / 100.0;
				// $this -> tx_dao -> insert($tx);
				//
				// // manager
				// $tx = array();
				// $tx['buy_record_id'] = $br -> id;
				// $tx['user_id'] = $b_user -> manager_id;
				// $tx['total_price'] = $br -> total_price;
				// $tx['percent'] = 10;
				// $tx['amt'] = $br -> total_price * 10.0 / 100.0;
				// $this -> tx_dao -> insert($tx);
			}
		}

		echo "1|OK";
	}

	private function do_log($tag = '') {
		$i_data['post'] =json_encode($_POST, JSON_UNESCAPED_UNICODE);
		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$i_data['tag'] = $tag;
		$i_data['full_path'] = $actual_link;
		$this -> post_log_dao -> insert($i_data);
	}

	public function mail_to() {
		$msg = "您好，歡迎使用本系統";
		$email = "test@gmail.com";
		$config = array(
		        'crlf'          => "\r\n",
		        'newline'       => "\r\n",
		        'charset'       => 'utf-8',
		        'protocol'      => 'smtp',
		        'mailtype'      => 'html',
		        'smtp_host'     => 'localhost',
		        'smtp_port'     => '25',
		        'smtp_user'     => 'qweq9999',
		        'smtp_pass'     => 'qweq9999'
		);

		$this->load->library('email');
		$this->email->initialize($config);
		$this->email->from('service@king88.tw');
		$this->email->to($email);

		$this->email->subject('歡迎使用本系統');
		$this->email->message($msg);

		if($this->email->send()){
		    $res = "ok";
		}else{
		    $res = "faild";
		}

		echo $res;
	}

	function do_pay_chs($user_id, $amt, $type = '') {
		$res = array();
		$res['amt'] = $amt;
		$res['user_id'] = $user_id;
		$res['type'] = $type;

		$tx_type = $type;
		$tx_amt = $amt;
		$tx_amt = intval($tx_amt);
		$l_user_id = $user_id;

		// check chs
		$login_user = $this -> users_dao -> find_by_id($l_user_id);

		$corp = $this -> corp_dao -> find_by_id($login_user -> corp_id);

		$data['user_id'] = $l_user_id;
		$data['corp_id'] = $login_user -> corp_id;
		$data['amt'] = $tx_amt;
		// $data['faa_amt'] = $tx_amt;
		// $data['pay_type'] = $tx_type;
		$sn = 'P' . date('YmdHis');
		$data['sn'] = $sn;

		$id = $this -> dao -> insert($data);
		$item = $this -> dao -> find_by_id($id);

		if($type == "net") {
			redirect("http://139.162.73.191:8080/Deom/netbankpay3?order_id={$sn}&amt={$tx_amt}&type={$type}");
		}
		if($type == "net_2") {
			redirect("http://139.162.73.191:8080/Deom/netbankpay3?order_id={$sn}&amt={$tx_amt}&type={$type}");
		}
		if($type == "alipay_922") {
			redirect("tx/pay_sl_p/{$sn}/{$tx_amt}/922");
		}
		if($type == "alipay_923") {
			redirect("tx/pay_sl_p/{$sn}/{$tx_amt}/923");
		}
		$this -> to_json($res);
	}

}
?>
