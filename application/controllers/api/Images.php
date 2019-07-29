<?php
class Images extends MY_Base_Controller {
	var $mime_map = array(
			'image/jpeg' => 'jpg',
			'image/png' => 'png',
			'image/gif' => 'gif'
		);


	function __construct() {
		parent::__construct();
		$this -> load -> model('Images_dao', 'dao');
	}

	public function index() {
		echo "index";

	}

	public function do_sms() {
		echo "sms123";
		$m_acc = "0970632144";
		$m_pwd = "aaa123";
		$mobile = "0925815921";
		$msg = "test";

		$n_res = $this -> curl -> simple_get("http://smexpress.mitake.com.tw/SmSendGet.asp"
				. "?username=$m_acc&password=$m_pwd&dstaddr=$mobile&DestName=SteveYeh&dlvtime=&vldtime=&smbody=$msg");

				$txt = iconv("big5","UTF-8", $n_res);
				echo $txt;
	}

	public function test() {

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
		        'smtp_user'     => 'root',
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

	public function get($id, $is_thumb = '') {
		$data = array();
		if (!empty($id)) {
			$obj = $this -> dao -> find_by_id($id);
			if(!empty($obj)) {
				$img = (empty($is_thumb) ? $obj -> img : $obj -> img_thumb);
				if(!empty($is_thumb)) {
					if($is_thumb == '200x200') {
						$img = $this -> resizeBlob($img, 200, 200);
					}
				}
				// $download_file_name = IMG_DIR . $img_path;
				// header("Content-Disposition: attachment; filename=" . $obj -> image_name);
				header("Content-type: " . $obj -> mine);
				header("Content-Length: " . strlen($img));

				ob_clean();
				flush();
				echo $img;
				exit ;
			}
		}
		show_404();
	}

	public function get_avatar($suer_id, $is_thumb = '') {
		$data = array();
		if (!empty($suer_id)) {
			$user = $this -> users_dao -> find_by_id($suer_id);
			$obj = $this -> dao -> find_by_id($user -> image_id);
			if(!empty($obj)) {
				$img = (empty($is_thumb) ? $obj -> img : $obj -> img_thumb);
				// $download_file_name = IMG_DIR . $img_path;
				// header("Content-Disposition: attachment; filename=" . $obj -> image_name);
				header("Content-type: " . $obj -> mine);
				header("Content-Length: " . strlen($img));

				ob_clean();
				flush();
				echo $img;
				exit ;
			}
		}
		show_404();
	}

	function get_ext($mime) {
		if(array_key_exists($mime, $this -> mime_map)) {
			$val = $this -> mime_map[$mime];
			return $val;
		}
		return 'jpg';
	}

	public function upload_base64($type="") {
		//android
		$res['success'] = TRUE;
		$info = '';

		$user_id = $this -> get_post('user_id');;
		$image_name = $this -> get_post('image_name');;
		// $mime = $this -> get_post('mime');
		$image_base64 = $this -> get_post('image_base64');

		$mime = get_mime_by_extension($image_name);

		if (!($mime == 'image/jpg'|| $mime == 'image/jpeg' || $mime == 'image/png' || $mime == 'image/gif')) {
			$info = "非支援上傳格式(jpeg/jpg/png/gif)";
			$success = FALSE;
		}

		// open up the file and extract the data/content from it
		$i_data['mime'] = $mime;
		$i_data['image_path'] = "";
		$last_id = $this -> dao -> insert_image_data($i_data);

		$this -> dao -> update(array('image_name' => "{$last_id}.jpg"), $last_id);
		// $ext = $this -> get_ext($mime);
		// $img_name = $last_id . '.' . $ext;

		// save file
		$m_dir =  "./img/upload/";
		if(!file_exists($m_dir)) {
			mkdir($m_dir);
		}
		// $img_path = $m_dir. $img_name;
		// file_put_contents($img_name,base64_decode($image_base64));
		$img_content = base64_decode($image_base64);
		//
		// $res['img_path'] = $img_path;
		// update image info
		$this -> dao -> update(array(
			'img' => $img_content
		), $last_id);

		// resize
		$img_content = $this -> resizeBlob($img_content, 300, 300);
		//
		// $res['image_id'] = $last_id;
		//
		// $img_content = file_get_contents($img_path);
		$this -> dao -> update(array(
			'img_thumb' => $img_content
		), $last_id);

		$res['image_id'] = $last_id;

		if($type == "avatar" && !empty($user_id)) {
			// 上傳大頭照
			$this -> users_dao -> update(array(
				'image_id' => $last_id
			), $user_id);
		}
		$this -> to_json($res);
	}

	//image upload section
	public function upload_form($image_path="") {
		$res['success'] = TRUE;

		$info = '';

		$name = $_FILES['file']['name'];
		$tmp_name = $_FILES['file']['tmp_name'];
		$type = $_FILES['file']['type'];
		$size = $_FILES['file']['size'];

		if (!($type == 'image/jpeg' || $type == 'image/png' || $type == 'image/gif' || $type =='image/jpg')) {
			$info = "非支援上傳格式(jpeg/jpg/png/gif)";
			$success = FALSE;
		}

		// open up the file and extract the data/content from it
		$i_data['image_name'] = $name;
		$i_data['mime'] = $type;
		$i_data['image_path'] = $image_path;
		$i_data['image_size'] = $size;

		// set store id
		$store_id = $this -> get_post('store_id');
		if(!empty($store_id)) {
			$i_data['store_id'] = $store_id;
		}

		$img_content = file_get_contents($tmp_name);

		$image_info = getimagesize($tmp_name);
		$image_width = $image_info[0];
		$image_height = $image_info[1];
		$i_data['width'] = $image_width;
		$i_data['height'] = $image_height;
		$i_data['img'] = $img_content;
		$last_id = $this -> dao -> insert_image_data($i_data);
		if (!empty($last_id)) {


			// resize
			$this -> resize($tmp_name, 300, 300);

			$img_content = file_get_contents($tmp_name);
			$this -> dao -> update(array(
				'img_thumb' => $img_content
			), $last_id);


			$user_id = $this -> get_post('user_id');
			if($image_path == "avatar" && !empty($user_id)) {
				// 上傳大頭照
				$this -> users_dao -> update(array(
					'image_id' => $last_id
				), $user_id);
			}
			$res['user_id'] = $user_id;
			$res['last_id'] = $last_id;
			$this -> to_json($res);
		}

	}
	//upload

	//images upload ios
	public function upload() {
		$id = $this -> get_post('id');//driver_id

		$path = $this -> get_post('path');
		//行照 driver_registration_img
		//執業登記 driver_license_img


		$device = $this -> get_post('device');
		//android
		//ios




		$info = '';

		if($device == 'android'){//base64
			$image_name = $this -> get_post('image_name');
			$mime = $this -> get_post('mime');
			$image_base64 = $this -> get_post('image');

			if (!($mime == 'image/jpeg'|| $mime == 'image/jpeg' || $mime == 'image/png' || $mime == 'image/gif')) {
				$info = "非支援上傳格式(jpeg/jpg/png/gif)";
				$success = FALSE;
			}
			//
					// open up the file and extract the data/content from it
			$i_data['image_name'] = $image_name;
			$i_data['mime'] = $mime;
			$i_data['image_path'] = $path;
			$last_id = $this -> dao -> insert_image_data($i_data);

			$ext = $this -> get_ext($mime);
			$img_name = $last_id . '.' . $ext;

			// save file
			$m_dir = IMG_DIR . "$path/";
			if(!file_exists($m_dir)) {
				mkdir($m_dir);
			}
			$img_path = $m_dir . $img_name;
			file_put_contents($path,base64_decode($image_base64));

			// update image info
			$image_info = getimagesize($path);
			$image_width = $image_info[0];
			$image_height = $image_info[1];
			$u_data['width'] = $image_width;
			$u_data['height'] = $image_height;
			$o_size = filesize($path);
			$u_data['image_size'] = $o_size;

			// thumb
			$m_dir = IMG_DIR . $path . "_thumb/";
			if(!file_exists($m_dir)) {
				mkdir($m_dir);
			}
			// copy to thumb file
			$img_thumb_path = $m_dir . $img_name;
			$extract = fopen($path, 'r');
			$target = fopen($img_thumb_path, 'w');
			$image = fread($extract, $o_size);
			fwrite($target, $image);
			fclose($extract);
			fclose($target);
			// resize
			$this -> resize($img_thumb_path, 300, 300);

			$res['image_id'] = $last_id;

			if($path == 'driver_registration_img') {
				$driver_id = $this -> get_post('id');
				$this -> driver_img_dao -> insert(array(
					'driver_id'=>$driver_id,
					'image_id'=>$last_id,
					'type'=>'registration'
				));
			}

			if($path == 'driver_license_img') {
				// $driver_id = $this -> get_post('id');
				// //update to driver
				// $this -> drivers_dao -> update(array(
				// 	'license_id'=>$last_id
				// ),$driver_id);
				$driver_id = $this -> get_post('id');
				$this -> driver_img_dao -> insert(array(
					'driver_id'=>$driver_id,
					'image_id'=>$last_id,
					'type'=>'license'
				));
			}

			$this -> to_json($res);
		}elseif($device == 'ios'){
			$name = $_FILES['file']['name'];
			$tmp_name = $_FILES['file']['tmp_name'];
			$type = $_FILES['file']['type'];
			$size = $_FILES['file']['size'];

			$json_data = array();
			list($width, $height, $typeb, $attr) = getimagesize($tmp_name);

			if (!($type == 'image/jpeg'|| $type == 'image/jpeg' || $type == 'image/png' || $type == 'image/gif')) {
				$info = "非支援上傳格式(jpeg/jpg/png/gif)";
				$success = FALSE;
			}

			$m_dir = IMG_DIR . $path.'/';
			$m_thumb_dir = IMG_DIR . $path ."_thumb/";
			$ext = $this -> get_ext($type);
			$image_path = $path;
			$img_id = $this -> dao -> insert(array(
					// 'shuttle_log_id' => $shuttle_log_id,
					'mime' => $type,
					'image_size' => $size,
					'image_path' => $image_path,
					'image_name' => 'image.'.$ext
			));

			//update to images
			$i_u_data = array(
				'image_url'=>$image_path.'/'.$img_id.'.'.$ext,
				'image_thumb_url'=>$image_path.'_thumb/'.$img_id.'.'.$ext
			);
			$this -> dao -> update($i_u_data,$img_id);


			if($path == 'driver_registration_img') {
				// $driver_id = $this -> get_post('id');
				// //update to driver
				// $this -> drivers_dao -> update(array(
				// 	'registration_id'=>$img_id
				// ),$driver_id);
				$driver_id = $this -> get_post('id');
				$this -> driver_img_dao -> insert(array(
					'driver_id'=>$driver_id,
					'image_id'=>$img_id,
					'type'=>'registration'
				));
			}

			if($path == 'driver_license_img') {
				// $driver_id = $this -> get_post('id');
				// //update to driver
				// $this -> drivers_dao -> update(array(
				// 	'license_id'=>$img_id
				// ),$driver_id);
				$driver_id = $this -> get_post('id');
				$this -> driver_img_dao -> insert(array(
					'driver_id'=>$driver_id,
					'image_id'=>$img_id,
					'type'=>'license'
				));
			}

			if(!file_exists($m_dir)) {
				mkdir($m_dir);
			}

			$img_name = $img_id . '.' . $ext;
			// $thumb_img_name = 'thumb_' . $img_id . '.' . $ext;
			$thumb_img_name = $img_name;
			$extract = fopen($tmp_name, 'r');
			$target = fopen($m_dir . $img_name, 'w');

			// save image
			$image = fread($extract, $size);
			fwrite($target, $image);
			fclose($extract);
			fclose($target);

			// resize
			$m_dir = IMG_DIR . $path . "_thumb/";
			if(!file_exists($m_dir)) {
				mkdir($m_dir);
			}
			$this -> resize($tmp_name, 200, 200);
			$thumb_extract = fopen($tmp_name, 'r');
			$thumb_image = fread($thumb_extract, $size);
			$thumb_target = fopen($m_thumb_dir . $thumb_img_name, 'w');
			fwrite($thumb_target, $thumb_image);
			fclose($thumb_extract);
			fclose($thumb_target);

			$json_data['info'] = $info;
			$json_data['image_id'] = $img_id;

			// success
			$json_data['success'] = TRUE;
			$this -> to_json($json_data);
		}





	}


	public function resizeBlob($image, $width = 400, $height = 400) {
		// load an image
		$i = new Imagick();
		$i -> readImageBlob($image);

		// get the current image dimensions
		$geo = $i->getImageGeometry();

		// crop the image
		if(($geo['width']/$width) < ($geo['height']/$height))
		{
		    $i->cropImage($geo['width'], floor($height*$geo['width']/$width), 0, (($geo['height']-($height*$geo['width']/$width))/2));
		}
		else
		{
		    $i->cropImage(ceil($width*$geo['height']/$height), $geo['height'], (($geo['width']-($width*$geo['height']/$height))/2), 0);
		}


    $i->thumbnailImage($width, $height, true);
		return $i->getImagesBlob();
		// $i->writeImage(realpath($img_path));
	}


	//image upload
	public function upload_img_base64() {
		//android
		$res['success'] = TRUE;
		$info = '';

		$image_name = $this -> get_post('image_name');;
		$image_base64 = $this -> get_post('image_base64');

		$mime = get_mime_by_extension($image_name);

		if (!($mime == 'image/jpg'|| $mime == 'image/jpeg' || $mime == 'image/png' || $mime == 'image/gif')) {
			$info = "非支援上傳格式(jpeg/jpg/png/gif)";
			$success = FALSE;
		}

		// open up the file and extract the data/content from it
		$i_data['mime'] = $mime;
		$i_data['image_path'] = "question_img";
		$last_id = $this -> dao -> insert_image_data($i_data);

		$this -> dao -> update(array('image_name' => "{$last_id}.jpg"), $last_id);
		// $ext = $this -> get_ext($mime);
		// $img_name = $last_id . '.' . $ext;

		// save file
		$m_dir =  "./img/upload/";
		if(!file_exists($m_dir)) {
			mkdir($m_dir);
		}
		// $img_path = $m_dir. $img_name;
		// file_put_contents($img_name,base64_decode($image_base64));
		$img_content = base64_decode($image_base64);
		//
		// $res['img_path'] = $img_path;
		// update image info
		$this -> dao -> update(array(
			'img' => $img_content
		), $last_id);

		// resize
		$img_content = $this -> resizeBlob($img_content, 500, 500);
		//
		$res['image_id'] = $last_id;
		//
		// $img_content = file_get_contents($img_path);
		$this -> dao -> update(array(
			'img_thumb' => $img_content
		), $last_id);


		$this -> to_json($res);

  }



}
?>
