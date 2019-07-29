<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Pay_records extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Pay_records_dao', 'dao');
		$this -> load -> model('Products_dao', 'p_dao');
		$this -> load -> model('Pay_types_dao', 'pt_dao');

		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Wallet_tx_btc_dao', 'wtx_btc_dao');
		$this -> load -> model('Wallet_tx_eth_dao', 'wtx_eth_dao');
		$this -> load -> model('Wallet_tx_ntd_dao', 'wtx_ntd_dao');
		$this -> load -> model('Wallet_tx_bdc_dao', 'wtx_bdc_dao');
		$this -> load -> model('Users_dao', 'u_dao');
		$this -> load -> model('Corp_dao', 'c_dao');

		$this -> load -> model('Cash_deposite_dao', 'cash_deposite_dao');


		//載入SDK(路徑可依系統規劃自行調整)
		include APPPATH . 'third_party/ECPay.Payment.Integration.php';
		include APPPATH . 'third_party/submit.class.php';
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$data['sum_amt'] = $this -> wtx_dao -> get_sum_amt($data['login_user_id']);
		$data['corp_list'] = $this -> c_dao -> find_all();

		$login_user = $this -> u_dao -> find_by_id($data['login_user_id']);
		$data['login_user'] = $login_user;
		$this->load->view('mgmt/pay_records/list', $data);
	}

	public function shop()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$this->load->view('mgmt/pay_records/shop', $data);
	}

	public function pay($id)
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$corp = $data['corp'];

		$q_data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order'
		));
		$q_data['id'] = $id;
		$list = $this -> dao -> query_ajax($q_data);
		$item = $list[0];

		// check chs
		$login_user = $this -> u_dao -> find_by_id($data['login_user_id']);
		if($item -> pay_type_id == 5 || $item -> pay_type_id == 6) {
			// $this -> pay_hui_he($item, $item -> pay_type_id, $corp);
			return;
		}

		if($item -> pay_type_id == 7) {
			//$this -> pay_bestpay($item, $item -> pay_type_id, $corp);
			return;
		}

		try {
    		$obj = new ECPay_AllInOne();

				$ChoosePayment = NULL;
				if($item -> pay_type_id == 1) { // atm
					$ChoosePayment = ECPay_PaymentMethod::ATM;
				}
				if($item -> pay_type_id == 2) { // cvs
					$ChoosePayment = ECPay_PaymentMethod::CVS;
				}
				if($item -> pay_type_id == 3) { // credit
					$ChoosePayment = ECPay_PaymentMethod::Credit;
				}

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
        $obj->Send['TradeDesc']         = $corp -> sys_name . " 繳款";                           //交易描述
        $obj->Send['ChoosePayment']     = $ChoosePayment;              			          //付款方式:ATM

        //訂單的商品資料
        array_push($obj->Send['Items'], array('Name' => $corp -> sys_name . " 繳款", 'Price' => intval($item -> amt),
                   'Currency' => "元", 'Quantity' => (int) "1", 'URL' => "dedwed"));

        //延伸參數(可依系統需求選擇是否代入)
				if($item -> pay_type_id == 1) { // atm
					$obj->SendExtend['ExpireDate'] = 3 ;     //繳費期限 (預設3天，最長60天，最短1天)
					$obj->SendExtend['PaymentInfoURL'] = base_url('api/user_buy/atm_callback'); //伺服器端回傳付款相關資訊。
					$obj->SendExtend['ClientRedirectURL'] = '';      //預設空值
				}
				if($item -> pay_type_id == 2) { // cvs
					$obj->SendExtend['Desc_1']            = $corp -> sys_name . " 繳款";      //交易描述1 會顯示在超商繳費平台的螢幕上。預設空值
					$obj->SendExtend['Desc_2']            = '';      //交易描述2 會顯示在超商繳費平台的螢幕上。預設空值
					$obj->SendExtend['Desc_3']            = '';      //交易描述3 會顯示在超商繳費平台的螢幕上。預設空值
					$obj->SendExtend['Desc_4']            = '';      //交易描述4 會顯示在超商繳費平台的螢幕上。預設空值
					$obj->SendExtend['PaymentInfoURL']    = base_url('api/user_buy/cvs_callback');      //預設空值
					$obj->SendExtend['ClientRedirectURL'] = '';      //預設空值
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

	function pay_hui_he($item, $pay_type_id, $corp) {
		//合作身份者id，以2088开头的16位纯数字
		$config['AppId']		= $corp -> huihepay_appid;

		//安全检验码，以数字和字母组成的32位字符
		$config['SecretKey']			= $corp -> huihepay_key;

		//签名方式 不需修改
		$config['SignType']    = strtoupper('MD5');

		$pay_type = 0;
		if($pay_type_id == 5) {
			$pay_type = 2;
		}
		if($pay_type_id == 6) {
			$pay_type = 6;
		}

		$parameter = array(
				"AppId" => $corp -> huihepay_appid,
				"Method" => "trade.page.pay",
				"Format" => "JSON",
				"Charset"	=> "UTF-8",
				"Version"	=> "1.0",
				"Timestamp"	=> date('Y-m-d H:i:s'),
				"PayType"	=> "$pay_type",
				"OutTradeNo"	=> $item -> sn,
				"TotalAmount"	=> $item -> amt,
				"Subject"	=> $corp -> sys_name . " 繳款",
				"Body" => $corp -> sys_name . " 繳款",
				// "NotifyUrl"	=> base_url('api/user_buy/hui_he_callback')
				"NotifyUrl"	=> "http://fu99.tw/api/user_buy/hui_he_callback"
		);

		//建立请求
		$submit = new Submit($config);
		$params = $submit->buildRequestPara($parameter);
		$n_res = $this -> curl -> simple_post("https://pay.huihepay.com/gateway",$params);
		return $n_res;
	}

	function pay_bestpay($item, $pay_type_id, $corp) {
		$config = array();
		$config["method"] = "submitOrderInfo";
		$config["out_trade_no"] = $item -> sn . '1';
		$config["body"] =  $corp -> sys_name . " 繳款";
		$config["total_fee"] = $item -> amt;
		$config["mch_create_ip"] = get_ip();
		$config["best_pay_id"] = $corp -> best_pay_id;
		$config["best_pay_key"] = $corp -> best_pay_key;
		$n_res = $this -> curl -> simple_post("/pay/request.php",$config);
		return $n_res;
	}

	public function finish() {
		$this -> load -> view('mgmt/pay_records/finish');
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'user_id'
		));

		$s_corp_id = $this -> get_post('s_corp_id');
		$s_data = $this -> setup_user_data(array());
		$login_user = $this -> u_dao -> find_by_id($s_data['login_user_id']);
		if($login_user -> role_id != 99) {
			$data['s_corp_id'] = $login_user -> corp_id;
		}
		if(!empty($s_corp_id)) {
			$data['s_corp_id'] = $s_corp_id;
		}


		$res['items'] = $this -> dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();
		$data['id'] = $id;
		if(!empty($id)) {
			$q_data = $this -> get_posts(array(
				'length',
				'start',
				'columns',
				'search',
				'order'
			));
			$q_data['id'] = $id;
			$list = $this -> dao -> query_ajax($q_data);
			$item = $list[0];

			$data['item'] = $item;

		}

		$s_data = $this -> setup_user_data(array());
		$login_user = $this -> u_dao -> find_by_id($s_data['login_user_id']);
		$is_foreign = FALSE;

		if($login_user -> lang == 'chs' || $login_user -> role_id == 99) {
			$is_foreign = TRUE;
		}

		if($is_foreign) {
			$data['pay_type_list'] = $this -> pt_dao -> find_all_foreign();
		} else {
			$data['pay_type_list'] = $this -> pt_dao -> find_all_local();
		}

		$this->load->view('mgmt/pay_records/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$s_data = $this -> setup_user_data(array());
		$data = $this -> get_posts(array(
			'amt',
			'pay_type_id'
		));
		$data['user_id'] = $s_data['login_user_id'];
		$corp = $s_data['corp'];

		if(empty($id)) {
			// insert
			$data['corp_id'] = $s_data['corp'] -> id;
			$data['sn'] = 'P' . date('YmdHis');
			$id = $this -> dao -> insert($data);
		} else {
			// update
			$this -> dao -> update($data, $id);
		}

		$item = $this -> dao -> find_by_id($id);
		// do update
		$pay_type_id = $item -> pay_type_id;
		if($pay_type_id == 5 || $pay_type_id == 6) { // huihepay
			$r_map = $this -> pay_hui_he($item, $item -> pay_type_id, $corp);
			$ret = json_decode($r_map);
			if($ret -> Code == 0) {
				$u_data['qr_code'] = $ret -> QrCode;
				$this -> dao -> update($u_data, $id);
				$res['qr_code'] = $ret -> QrCode;
			} else {
				$res['message'] = $ret -> Message;
			}
		}

		if($pay_type_id == 7) { // best pay
			$r_map = $this -> pay_bestpay($item, $pay_type_id, $corp);
			$res['ret'] = json_decode($r_map);
			$ret = json_decode($r_map);

			// update
			if($ret -> status == 200) {
				$u_data['pay_url'] = $ret -> pay_url;
				$u_data['token'] = $ret -> token_id;
				$this -> dao -> update($u_data, $id);
			} else {
				$res['message'] = $ret -> msg;
			}
		}

		$res['success'] = TRUE;
		$res['last_id'] = $id;
 		$this -> to_json($res);
	}

	public function sys_insert() {
		$res = array();
		$id = $this -> get_post('id');
		$type = $this -> get_post('type');
		$s_data = $this -> setup_user_data(array());
		$data = $this -> get_posts(array(
			'amt',
			'user_id'
		));
		// $data['pay_type_id'] = 4; // sys
		$data['status'] = 1; // 已繳款
		// $data['payment_time'] = date('Y-m-d H:i:s'); // 繳款時間
		$user = $this -> u_dao -> find_me($data['user_id']);
		if(empty($id)) {
			// insert
			// $data['corp_id'] = $user -> corp_id;
			// $data['sn'] = 'CD' . date('YmdHis');
			// $id = $this -> cash_deposite_dao -> insert($data);
			//
			// $pr = $this -> dao -> find_by_id($id);

			// commit tx
			$tx = array();
			$tx['corp_id'] = $user -> corp_id;
			$tx['tx_id'] = $user -> id;
			$tx['tx_type'] = "sys_insert";
			$tx['user_id'] = $user -> id;
			$amt = $data['amt'];
			$tx['amt'] = $amt;
			$tx['brief'] = "系統購買點數 {$amt} 點";
			$this -> wtx_dao -> insert($tx);

		} else {
			// update
			//$this -> dao -> update($data, $id);
		}

		$res['success'] = TRUE;
		$res['last_id'] = $id;
 		$this -> to_json($res);
	}

	public function sys_insert_coin() {
		$res = array();
		$id = $this -> get_post('id');
		$type = $this -> get_post('type');
		$s_data = $this -> setup_user_data(array());
		$data = $this -> get_posts(array(
			'amt',
			'user_id'
		));
		// commit tx
		$tx = array();
		$tx['user_id'] = $data['user_id'];

		$amt = $data['amt'];
		$tx['amt'] = $amt;
		$tx['type_id'] = 1; // buy by sys
		$tx['brief'] = "系統加值 $amt ";
		if($type == 'btc') {
			$this -> wtx_btc_dao -> insert($tx);
		}
		if($type == 'eth') {
			$this -> wtx_eth_dao -> insert($tx);
		}
		if($type == 'ntd') {
			$this -> wtx_ntd_dao -> insert($tx);
		}
		if($type == 'bdc') {
			$this -> wtx_bdc_dao -> insert($tx);
		}

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function insert_and_pay() {
		$res = array();
		$id = $this -> get_post('id');
		$s_data = $this -> setup_user_data(array());
		$data = $this -> get_posts(array(
			'amt',
			'pay_type_id'
		));
		$data['user_id'] = $s_data['login_user_id'];

		// insert
		$data['sn'] = 'P' . date('YmdHis');
		$id = $this -> dao -> insert($data);

		redirect(base_url("mgmt/pay_records/pay/$id"));
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}

	public function check_account($id) {
		$account = $this -> get_post('account');
		$list = $this -> dao -> find_all_by('account', $account);
		$res = array();
		if(!empty($id)) {
			if (count($list) > 0) {
				$item = $list[0];
				if($item -> id == $id) {
					$res['valid'] = TRUE;
				} else {
					$res['valid'] = FALSE;
				}

				$res['item'] = $item;
			} else {
				$res['valid'] = TRUE;
			}
		} else {
			if (count($list) > 0) {
				$res['valid'] = FALSE;
			} else {
				$res['valid'] = TRUE;
			}
		}

		$this -> to_json($res);
	}

	public function chg_user() {
		$user_id = $this -> get_post('user_id');
		$this -> session -> set_userdata('user_id', $user_id);
		$res = array();

		$this -> to_json($res);
	}
}
