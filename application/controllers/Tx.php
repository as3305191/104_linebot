<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Tx extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Pay_records_dao', 'dao');

		$this -> load -> model('Wallet_tx_dao', 'wtx_dao');
		$this -> load -> model('Users_dao', 'u_dao');
		$this -> load -> model('Corp_dao', 'c_dao');


		//載入SDK(路徑可依系統規劃自行調整)
		include APPPATH . 'third_party/ECPay.Payment.Integration.php';
		include APPPATH . 'third_party/submit.class.php';
	}

	public function pay_sl() {
		$this -> load -> view('pay_chs_sl', array());
	}

	public function pay_sl_p($order_id, $amt, $bank_code) {
		$this -> load -> view('pay_chs_sl_p', array(
			'order_id' => $order_id,
			'amt' => $amt,
			'bank_code' => $bank_code,
		));
	}

	public function index() {
		$this -> load -> view('pay_chs', array());
	}

	public function do_tx()
	{
		$data = array();

		$tx_type = $this -> get_get('tx_type');
		$tx_amt = $this -> get_get('tx_amt');
		$tx_amt = intval($tx_amt);
		$l_user_id = $this -> get_get('l_user_id');
		$gift_id = $this -> get_get('gift_id');
		//
		if(!empty($gift_id)) {
			$l_user_id = $gift_id;
		}

		// check chs
		$login_user = $this -> u_dao -> find_by('gift_id', $l_user_id);

		$corp = $this -> c_dao -> find_by_id(1);

		$data['user_id'] = $login_user -> id;
		$data['amt'] = ceil(floatval($tx_amt));
		$data['faa_amt'] = $tx_amt;
		$data['pay_type'] = $tx_type;
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
        $obj->Send['TradeDesc']         = "捕魚奪寶繳款 {$item->amt}";                           //交易描述
        $obj->Send['ChoosePayment']     = $ChoosePayment;              			          //付款方式:ATM

        //訂單的商品資料
        array_push($obj->Send['Items'], array('Name' => "捕魚奪寶繳款", 'Price' => intval($item -> amt),
                   'Currency' => "元", 'Quantity' => (int) "1", 'URL' => "dedwed"));

        //延伸參數(可依系統需求選擇是否代入)
				if($tx_type == 'atm') { // atm
					$obj->SendExtend['ExpireDate'] = 3 ;     //繳費期限 (預設3天，最長60天，最短1天)
					$obj->SendExtend['PaymentInfoURL'] = base_url('api/user_buy/atm_callback'); //伺服器端回傳付款相關資訊。
					// $obj->SendExtend['ClientRedirectURL'] = base_url('/');      //預設空值
				}
				if($tx_type == 'market') { // cvs
					$obj->SendExtend['Desc_1']            = "捕魚奪寶繳款";      //交易描述1 會顯示在超商繳費平台的螢幕上。預設空值
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
}
