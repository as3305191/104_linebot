<?php
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class Water extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Com_tab_dao', 'dao');


	}

	public function test_sms() {

		AlibabaCloud::accessKeyClient('LTAI8jOk3rc03h1c', 'ryEA40EyE2AnogZc3XmxdW8WeIlA7l')
                        ->regionId('cn-hangzhou') // replace regionId as you need
                        ->asGlobalClient();

		try {
		    $result = AlibabaCloud::rpcRequest()
		                          ->product('Dysmsapi')
		                          // ->scheme('https') // https | http
		                          ->version('2017-05-25')
		                          ->action('SendSms')
		                          ->method('POST')
		                          ->options([
		                                        'query' => [
		                                          'PhoneNumbers' => '17374011706',
		                                          'SignName' => 'WnA娱乐',
		                                          'TemplateCode' => 'SMS_162199626',
		                                          'TemplateParam' => '{"code":"1111"}',
		                                        ],
		                                    ])
		                          ->request();
		    print_r($result->toArray());
		} catch (ClientException $e) {
		    echo $e->getErrorMessage() . PHP_EOL;
		} catch (ServerException $e) {
		    echo $e->getErrorMessage() . PHP_EOL;
		}
		echo "test sms";
	}

	public function test_enc() {
		// $res = array();
		// $res['success'] = $res;

		ob_start();
		$params = array(
			'HomeUrl' => 'www.XXX.com',
			'PlayerId' => 'zzzzaaaccc123',
		);
		$params = json_encode($params);
		$output = py_des3_encode($params);
		echo  $output;
	}

	/*
	這是審核後台 : http://scratch-demo-game.wangzugames.com:12063
點 商家審查 可以看到送審的商家 通過審核後
點 商家統計 最後一欄會出現你審核通
	*/
	public function reg_enc() {
		// $res = array();
		// $res['success'] = $res;

		ob_start();
		$params = array(
			'AgentName' => 'W&A',
			'UniqueId' => 'wa-lotterygame.com',
		);
		$params = json_encode($params);
		$output = py_des3_encode($params);
		echo  $output;

		$p = array();
		$p["Params"] = $output;
		$p["AccessToken"] = "NDgwZDBlMjUtMDYyYS00NmE4LTgxZDUtYTIyYjVlNjc1Y2Ez";
		$n_res = $this -> curl -> simple_post(WANG_URL . "RegisterAgent", $p);
		// $output = py_des3_decode($n_res);
		$obj = json_decode($n_res);
		$output = py_des3_decode($obj -> Data);
		echo  $output;
	}

	public function upd_enc() {
		// $res = array();
		// $res['success'] = $res;

		ob_start();
		$params = array(
			'UniqueId' => 'wa-lotterygame.com',
		);
		$params = json_encode($params);
		$output = py_des3_encode($params);
		echo  $output;

		$p = array();
		$p["Params"] = $output;
		$p["AccessToken"] = "NDgwZDBlMjUtMDYyYS00NmE4LTgxZDUtYTIyYjVlNjc1Y2Ez";
		$n_res = $this -> curl -> simple_post(WANG_URL . "UpdateAgent", $p);
		$output = py_des3_decode($n_res);
		$obj = json_decode($n_res);
		$output = py_des3_decode($obj -> Data);
		echo  $output;
	}

	public function test_dec() {
		$params = "9pt4y20suhGqberZALMNGT1UWGqHYGYJL4ejkb1P1VovdkcEN2WK2w==";
		$output = py_des3_decode($params);
		echo  $output;
	}

	public function test_des() {

		$txt = "U2FsdGVkX1+HxiFussgGzOcrilNN9AcWD92ohDZlOl7XTsfvzz34qAovNcubuobG2qKqtdULV1Se8vZ8iwI1GQrtXaEOVZiRbEbfkWfbpBE=";
		echo safe_base64_encode($txt);
	}

	//加密函数撰写

	function encrypt($source,$toencrypt){
		//加密用的key
		$key = $source;

		//使用3DES方法加密

		$encryptMethod = MCRYPT_TRIPLEDES;

		//初始化向量来增加安全性

		$iv = mcrypt_create_iv(mcrypt_get_iv_size($encryptMethod,MCRYPT_MODE_ECB), MCRYPT_RAND);

		//使用mcrypt_encrypt函数加密，MCRYPT_MODE_ECB表示使用ECB模式

		$encrypted_toencrypt = mcrypt_encrypt($encryptMethod, $key, $toencrypt, MCRYPT_MODE_ECB,$iv);

		//回传解密后字串

		return base64_encode($encrypted_toencrypt);

	}

	//解密函数撰写
	function decrypt($source,$todecrypt) {

		//解密用的key，必须跟加密用的key一样
		$key = $source;

		//解密前先解开base64码

		$todecrypt = base64_decode($todecrypt);

		//使用3DES方法解密

		$encryptMethod = MCRYPT_TRIPLEDES;

		//初始化向量来增加安全性

		$iv = mcrypt_create_iv(mcrypt_get_iv_size($encryptMethod,MCRYPT_MODE_ECB), MCRYPT_RAND);

		//使用mcrypt_decrypt函数解密，MCRYPT_MODE_ECB表示使用ECB模式

		$decrypted_todecrypt = mcrypt_decrypt($encryptMethod, $key, $todecrypt, MCRYPT_MODE_ECB,$iv);

		//回传解密后字串

		return $decrypted_todecrypt;
	}


}
?>
