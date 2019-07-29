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
		$this -> load -> model('Files_dao', 'file_dao');
		$this -> load -> model('Users_dao', 'users_dao');

		// setup base path
		$this -> base_path = 'mgmt/images';
		$this -> load -> helper('url');
	}

	function index() {
		$data = array();

		$data['page_title'] = '歡迎進入iImage管理介面';

		// page setup
		$data['path'] = $this -> base_path . '/list';

		// user data setup
		$data = $this -> setup_user_data($data);

		// search setup
		$search_keys = array('id', 'image_name', 'description');
		$columns = $this -> setup_search_columns($search_keys);
		$data = array_merge($data, $columns);

		// paging
		$data = array_merge($data, $this -> getPagingData($columns));

		//setup url and thumb_url
		foreach($data['items'] as $each){
			$file_base_url = base_url();
			$replace_pos = strpos($file_base_url, "plastic_surgery/");
			$image_base_url = substr_replace($file_base_url, "plastic_surgery_img/", $replace_pos);

			$image_url = $image_base_url . $each->image_url;
			$image_thumb_url = $image_base_url . $each->image_thumb_url;

			$each->image_url = $image_url;
			$each->image_thumb_url = $image_thumb_url;
		}

		// load view
		$nav = $this -> get_get_post('nonav');
		$data['nonav'] = $nav;
		if(empty($nav)) {
			$this -> load -> view('mgmt/admin_template/header', $data);
			$this -> load -> view('mgmt/admin_template/navigation');
		} else {
			$this -> load -> view('mgmt/admin_template/header_nohead', $data);
			$this -> load -> view('mgmt/admin_template/navigation_nobar');
		}

		$this -> load -> view('mgmt/images/list');
		if(empty($nav)) {
			$this -> load -> view('mgmt/admin_template/footer');
		} else {
			$this -> load -> view('mgmt/admin_template/footer_nofoot');
		}
	}

	//image upload section
	public function upload_file($file_path) {
		$info = '';

		$name = $_FILES['video_file']['name'];
		$tmp_name = $_FILES['video_file']['tmp_name'];
		$type = $_FILES['video_file']['type'];
		$size = $_FILES['video_file']['size'];

		// open up the file and extract the data/content from it
		$i_data['file_name'] = $name;
		$i_data['mime'] = $type;
		$i_data['file_path'] = $file_path;
		$i_data['file_size'] = $size;
		$i_data['file_url'] = $file_path;

		$last_id = $this -> file_dao -> insert_file_data($i_data);
		if (!empty($last_id)) {
			$m_dir = IMG_DIR . "$file_path/";
			if(!file_exists($m_dir)) {
				mkdir($m_dir);
			}

			$ext = $this -> get_ext($type);
			$file_name = $last_id . '.' . $ext;
			$extract = fopen($tmp_name, 'r');
			$target = fopen($m_dir . $name, 'w');

			// save image
			$file = fread($extract, $size);
			fwrite($target, $file);
			fclose($extract);
			fclose($target);

			$url = 'mgmt/images/delete_file/' . $last_id;
			$res = [
			    'initialPreview' => [
			   		base_url('mgmt/images/get_file/' . $last_id .'/file.mp4')
			    ],
			    'initialPreviewConfig' => [
			        [
								'type' =>  "video",
								'size' =>  $size,
								'filetype' =>  $type,
								'caption' => "$name",
								'filename' => "$name",
								'downloadUrl' => base_url('mgmt/images/get_file/' . $last_id .'/file.mp4'),
								'url' => $url,
								'key' => $last_id
							]
			    ],
			    "id" => $last_id
			];

			$this -> to_json($res);
		}

	}

	//image upload section
	public function upload($image_path) {
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

			$url = 'mgmt/images/delete/' . $last_id;
			$res = [
			    'initialPreview' => [
			   		base_url('mgmt/images/get/' . $last_id)
			    ],
			    'initialPreviewConfig' => [
			        ['caption' => "$name", 'size' => $size, 'width' => '120px', 'url' => $url, 'key' => $last_id]
			    ],
			    "id" => $last_id
			];

			//可重複上圖
			if($image_path == 'product_img' || $image_path == 'store_img') {
				$res['append'] = TRUE;
			} else {
				$res['append'] = FALSE;
			}

			$this -> to_json($res);
		}

	}

	public function upload_base64($image_path) {
		$info = '';

		// $image_name = $this -> get_post('image_name');
		$image_base64 = $this -> get_post('image_base64');
		$user_id = $this -> get_post('user_id');
		$mime = "image/jpg";

		// open up the file and extract the data/content from it
		// $i_data['image_name'] = $image_name;
		$i_data['mime'] = $mime;
		$i_data['image_path'] = $image_path;
		$last_id = $this -> dao -> insert_image_data($i_data);

		$this -> dao -> update(array(
			'image_name' => $last_id . ".jpg"
		), $last_id);

		$ext = 'jpg';
		$img_name = $last_id . '.' . $ext;

		// save file
		$m_dir = IMG_DIR . "$image_path/";
		if(!file_exists($m_dir)) {
			mkdir($m_dir);
		}
		$img_path = $m_dir . $img_name;
		file_put_contents($img_path,base64_decode($image_base64));

		// update image info
		$image_info = getimagesize($img_path);
		$image_width = $image_info[0];
		$image_height = $image_info[1];
		$u_data['width'] = $image_width;
		$u_data['height'] = $image_height;
		$o_size = filesize($img_path);
		$u_data['image_size'] = $o_size;

		// thumb
		$m_dir = IMG_DIR . $image_path . "_thumb/";
		if(!file_exists($m_dir)) {
			mkdir($m_dir);
		}
		// copy to thumb file
		$img_thumb_path = $m_dir . $img_name;
		$extract = fopen($img_path, 'r');
		$target = fopen($img_thumb_path, 'w');
		$image = fread($extract, $o_size);
		fwrite($target, $image);
		fclose($extract);
		fclose($target);
		// resize
		$this -> resize($img_thumb_path, 300, 300);

		$res['image_id'] = $last_id;

		if(!empty($user_id)) {
			$this -> users_dao -> update(array(
				'image_id' => $last_id
			), $user_id);
			$res['user_id'] = $user_id;
		}
		$this -> to_json($res);
	}

	public function get($id, $is_thumb = '') {
		$data = array();
		if (!empty($id)) {
			$obj = $this -> dao -> find_by_id($id);
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

	public function get_file($id, $file_name = '') {
		$data = array();
		if (!empty($id)) {
			$obj = $this -> file_dao -> find_by_id($id);
			if(!empty($obj)) {
				$file_url = $obj -> file_url;
				$download_file_name = IMG_DIR . $file_url . '/' . $obj -> file_name;

				$fp = @fopen($download_file_name, 'rb');
				$size   = filesize($download_file_name); // File size
				$length = $size;           // Content length
				$start  = 0;               // Start byte
				$end    = $size - 1;       // End byte
				header('Content-type: video/mp4');
				//header("Accept-Ranges: 0-$length");
				header("Accept-Ranges: bytes");
				if (isset($_SERVER['HTTP_RANGE'])) {
				    $c_start = $start;
				    $c_end   = $end;
				    list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
				    if (strpos($range, ',') !== false) {
				        header('HTTP/1.1 416 Requested Range Not Satisfiable');
				        header("Content-Range: bytes $start-$end/$size");
				        exit;
				    }
				    if ($range == '-') {
				        $c_start = $size - substr($range, 1);
				    }else{
				        $range  = explode('-', $range);
				        $c_start = $range[0];
				        $c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
				    }
				    $c_end = ($c_end > $end) ? $end : $c_end;
				    if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
				        header('HTTP/1.1 416 Requested Range Not Satisfiable');
				        header("Content-Range: bytes $start-$end/$size");
				        exit;
				    }
				    $start  = $c_start;
				    $end    = $c_end;
				    $length = $end - $start + 1;
				    fseek($fp, $start);
				    header('HTTP/1.1 206 Partial Content');
				}
				header("Content-Range: bytes $start-$end/$size");
				header("Content-Length: ".$length);
				$buffer = 1024 * 8;
				while(!feof($fp) && ($p = ftell($fp)) <= $end) {
				    if ($p + $buffer > $end) {
				        $buffer = $end - $p + 1;
				    }
				    set_time_limit(0);
				    echo fread($fp, $buffer);
				    flush();
				}
				fclose($fp);
				exit();
				// die();
				//
				// header("Content-Disposition: attachment; filename=" . $obj -> file_name);
				// header("Content-type: " . $obj -> mine);
				// header("Content-Length: " . filesize($download_file_name));
				//
				// ob_clean();
				// flush();
				// readfile($download_file_name);
				// exit ;
			}
		}
		// show_404();
	}

	//image upload section
	public function upload_terms($image_path) {
		$info = '';
		$f_name = 'file';

		if($image_path == 'dm_image') {
			$f_name = 'upload';
		}

		$name = $_FILES[$f_name]['name'];
		$tmp_name = $_FILES[$f_name]['tmp_name'];
		$type = $_FILES[$f_name]['type'];
		$size = $_FILES[$f_name]['size'];

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

		$image_info = getimagesize($_FILES[$f_name]["tmp_name"]);
		$image_width = $image_info[0];
		$image_height = $image_info[1];
		$i_data['width'] = $image_width;
		$i_data['height'] = $image_height;
		$last_id = $this -> dao -> insert_image_data($i_data);
		if (!empty($last_id)) {
			$m_dir = IMG_DIR . "$image_path/";
			if(!file_exists($m_dir)) {
				mkdir($m_dir);
			}

			$ext = $this -> get_ext($type);
			$ext = 'jpg';
			$img_name = $last_id . '.' . $ext;
			$extract = fopen($tmp_name, 'r');
			$target = fopen($m_dir . $img_name, 'w');

			// save image
			$image = fread($extract, $size);
			fwrite($target, $image);
			fclose($extract);
			fclose($target);

			$m_dir = IMG_DIR . $image_path . "_thumb/";

			if(!file_exists($m_dir)) {
				mkdir($m_dir);
			}

			// resize
			$this -> resize($tmp_name, 300, 300);
			$thumb_extract = fopen($tmp_name, 'r');
			$thumb_image = fread($thumb_extract, $size);
			$thumb_target = fopen($m_dir . $img_name, 'w');
			fwrite($thumb_target, $thumb_image);
			fclose($thumb_extract);
			fclose($thumb_target);

			$del_url = base_url('mgmt/images/delete/' . $last_id);

			if($image_path == 'dm_image') {
				echo '<script type="text/javascript">' .
						    'window.parent.CKEDITOR.tools.callFunction("1", "' . base_url('mgmt/images/get/' . $last_id) . '", "");' .
							'</script>';
				return;
			} else {
				$res = [
				    'initialPreview' => [
				   		base_url('mgmt/images/get/' . $last_id)
				    ],
				    'initialPreviewConfig' => [
				        ['caption' => "$name", 'size' => $size, 'width' => '120px', 'url' => $del_url, 'key' => $last_id]
				    ],
				    "id" => $last_id
				];

				//可重複上圖
				if($image_path == 'product_img' || $image_path == 'store_img') {
					$res['append'] = TRUE;
				} else {
					$res['append'] = FALSE;
				}
			}

			$this -> to_json($res);
		}
	}

	function get_ext($mime) {
		if(array_key_exists($mime, $this -> mime_map)) {
			$val = $this -> mime_map[$mime];
			return $val;
		}
		return 'jpg';
	}

	public function resize($img_path, $width = 400, $height = 400) {
		// $config['image_library'] = 'imagemagick';
		// $config['source_image'] = $img_path;
		// $config['create_thumb'] = FALSE;
		// $config['maintain_ratio'] = TRUE;
		// $config['width'] = $width;
		// $config['height'] = $height;
//
		// $this -> load -> library('image_lib', $config);
		// $this -> image_lib -> resize();

		/*
		$imagick = new Imagick(realpath($img_path));
    // $imagick->setbackgroundcolor('rgb(64, 64, 64)');

    $i_width = $imagick->getImageWidth();
		$i_height = $imagick->getImageHeight();
    	if($width > $i_width && $height > $i_height) {
			// do nothing
			return;
		}
		*/

		// load an image
		$i = new Imagick(realpath($img_path));
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
		$i->writeImage(realpath($img_path));
	}

	public function delete($id){
		$res = array();
		//$this -> dao -> delete($id);
		$res['success'] = TRUE;
		$this -> to_json($res);
	}

	public function delete_file($id){
		$res = array();
		//$this -> dao -> delete($id);
		$res['success'] = TRUE;
		$this -> to_json($res);
	}

	public function delete_status($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}

	public function update_title() {
		$res['success'] = true;
		$id = $this -> get_post('pk');
		$val = $this -> get_post('value');
		$u_data['title'] = $val;
		$this -> dao -> update($u_data, $id);
		$res['sql'] = $this -> dao -> db -> last_query();
		$this -> to_json($res);
	}

	public function browser(){
		$data = array();

		// user data setup
		$data['allow_store_login'] = TRUE;
		$data = $this -> setup_user_data($data);
		$this->load->view('layout/browser', $data);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order',
			'store_id'
		));

		$res['items'] = $this -> dao -> query_ajax($data);
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();
		$data['id'] = $id;
		if(!empty($id)) {
			$item = $this -> dao -> find_by_id($id);
			if(!empty($item -> image_id)) {
				$item -> img = $this -> img_dao -> find_by_id($item -> image_id);
			}

			$data['item'] = $item;
		}
		$this->load->view('mgmt/images/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');
		$data = $this -> get_posts(array(
			'account',
			'password',
			'user_name',
			'email',
			'image_id'
		));

		if(empty($id)) {
			// insert
			$this -> dao -> insert($data);
		} else {
			// update
			$this -> dao -> update($data, $id);
		}

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function upload_multiple($image_path){

		$res = array();
		foreach($_FILES['file']['tmp_name'] as $key => $tmp_name)
        {
            $file_name = $key.$_FILES['file']['name'][$key];
            $file_size =$_FILES['file']['size'][$key];
            $file_tmp =$_FILES['file']['tmp_name'][$key];
            $file_type=$_FILES['file']['type'][$key];
						$this -> upload_helper($image_path,$file_name,$file_size,$file_tmp,$file_type,$res);
        }
		$this -> to_json($res);
	}

	public function upload_helper($image_path,$name,$size,$tmp_name,$type,&$res){
		$info = '';

		//$name = $_FILES['file']['name'];
		// $tmp_name = $_FILES['file']['tmp_name'];
		// $type = $_FILES['file']['type'];
		// $size = $_FILES['file']['size'];

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

		$image_info = getimagesize($tmp_name);
		$image_width = $image_info[0];
		$image_height = $image_info[1];
		$i_data['width'] = $image_width;
		$i_data['height'] = $image_height;
		$last_id = $this -> dao -> insert_image_data($i_data);
		if (!empty($last_id)) {
			$m_dir = IMG_DIR . "$image_path/";
			if(!file_exists($m_dir)) {
				mkdir($m_dir);
			}

			$ext = $this -> get_ext($type);
			$ext = 'jpg';
			$img_name = $last_id . '.' . $ext;
			$extract = fopen($tmp_name, 'r');
			$target = fopen($m_dir . $img_name, 'w');

			// save image
			$image = fread($extract, $size);
			fwrite($target, $image);
			fclose($extract);
			fclose($target);

			$m_dir = IMG_DIR . $image_path . "_thumb/";

			if(!file_exists($m_dir)) {
				mkdir($m_dir);
			}

			// resize
			$this -> resize($tmp_name, 300, 300);
			$thumb_extract = fopen($tmp_name, 'r');
			$thumb_image = fread($thumb_extract, $size);
			$thumb_target = fopen($m_dir . $img_name, 'w');
			fwrite($thumb_target, $thumb_image);
			fclose($thumb_extract);
			fclose($thumb_target);

			$url = 'mgmt/images/delete/' . $last_id;
			// $res = [
			//     'initialPreview' => [
			// 		 		base_url('mgmt/images/get/' . $last_id)
			//     ],
			//     'initialPreviewConfig' => [
			//         ['caption' => "$name", 'size' => $size, 'width' => '120px', 'url' => $url, 'key' => $last_id]
			//     ],
			//     "id" => $last_id
			// ];
			$res['initialPreview'][] = base_url('mgmt/images/get/' . $last_id);
			$res['initialPreviewConfig'][] = ['caption' => "$name", 'size' => $size, 'width' => '120px', 'url' => $url, 'key' => $last_id];
			$res['id'][] = $last_id;
			//可重複上圖
			if($image_path == 'product_img' || $image_path == 'store_img') {
				$res['append'][] = TRUE;
			} else {
				$res['append'][] = FALSE;
			}

			// $this -> to_json($res);
		}
	}

}
?>
