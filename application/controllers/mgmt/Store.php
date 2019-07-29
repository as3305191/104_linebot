<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Store extends MY_Base_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('Store_dao', 'dao');
		$this -> load -> model('Images_dao', 'img_dao');
		$this -> load -> model('Stores_images_dao', 'simg_dao');
		$this -> load -> model('Stores_rank_dao', 'srank_dao');
		$this -> load -> model('Product_post_dao', 'ppost_dao');
		$this -> load -> model('Products_dao', 'p_dao');
		$this -> load -> model('Product_cate_dao', 'pc_dao');
		$this -> load -> model('Stores_template_dao', 'st_dao');
		$this -> load -> model('Store_cate_dao', 'sc_dao');
		$this -> load -> model('Store_mulcate_dao', 'smul_dao');

		$this -> load -> model('Zip_dao', 'zip_dao');
	}

	public function index()
	{
		$data = array();
		$data = $this -> setup_user_data($data);
		$this->load->view('mgmt/store/list', $data);
	}

	public function get_data() {
		$res = array();
		$data = $this -> get_posts(array(
			'length',
			'start',
			'columns',
			'search',
			'order'
		));

		$parent_id = $this -> get_post('parent_id');
		if(!empty($parent_id)) {
			$data['parent_id'] = $parent_id;
		}

		$items = $this -> dao -> query_ajax($data);

		foreach($items as $item) {
			if(!empty($item -> image_id)) {
				$item -> img_url = get_img_url($item -> image_id);
			}
			$s_id = $item -> id;
			$item->store_mul_cate = $this -> smul_dao -> find_mulcate($s_id);
		}

		$res['items'] = $items;
		$res['recordsFiltered'] = $this -> dao -> count_ajax($data);
		$res['recordsTotal'] = $this -> dao -> count_all_ajax($data);

		$this -> to_json($res);
	}

	public function edit($id) {
		$data = array();

		//product_cate
		$f = array();
		$f['parent_id'] = 0;
		$main_cates = $this -> pc_dao -> query_all($f);
		$data['main_cates'] = $main_cates;

		// store cate
		$f = array();
		$f['parent_id'] = 0;
		$store_main_cates = $this -> sc_dao -> query_all($f);
		$data['store_main_cates'] = $store_main_cates;

		$data['mul_cates'] = array();
		$mul = $this -> smul_dao -> find_all_by('store_id',$id);
		if(!empty($mul)){
			foreach($mul as $each){
				array_push($data['mul_cates'],$each->store_cateid);
			}
		}

		$city_list = array();
		$district_list = array();

		$data['id'] = $id;
		$data['default_img'] = 'http://placehold.it/200x200';
		$data['store_rank'] = $this -> srank_dao -> find_all();

		// city and district
		$data['city_list'] = $this -> zip_dao -> find_all_city(TRUE);

		if(!empty($id)) {
			// update
			$item = $this -> dao -> find_by_id($id);

			// find images
			$item -> images = $this -> simg_dao -> find_image_by_product_id($id);
			$data['item'] = $item;

			// district
			$data['district_list'] = $this -> zip_dao -> find_district_by_city($item -> city);

			$f = $this -> st_dao -> find_by('store_id',$id);
			if(!empty($f)){
				$product_1 = $this -> p_dao -> find_by_id($f->product_1);
				$product_2 = $this -> p_dao -> find_by_id($f->product_2);
				$product_3 = $this -> p_dao -> find_by_id($f->product_3);
				$product_1 = check_is_up($product_1);
				$product_2 = check_is_up($product_2);
				$product_3 = check_is_up($product_3);

				$data['product_1'] = $product_1;
				$data['product_2'] = $product_2;
				$data['product_3'] = $product_3;

				// set subtitle
				$data['subtitle'] = $f -> subtitle;

				if($f->image_id > 0){
					$data['product_image'] = $this -> img_dao -> find_all_by('id',$f->image_id);
				}else{
					$imagesList = $item -> images;
					if(count($imagesList) > 0){
						$data['product_image'] = array($imagesList[0]);
					}
				}
			} else {
				$imagesList = $item -> images;
				if(count($imagesList) > 0){
					$data['product_image'] = array($imagesList[0]);
				}
			}
		} else {
			// insert
			$data['district_list'] = $this -> zip_dao -> find_district_by_city($data['city_list'][0]-> city);
		}
		$this->load->view('mgmt/store/edit', $data);
	}

	public function insert() {
		$res = array();
		$id = $this -> get_post('id');

		//open time array
		$time_arr = array();
		for($i=0;$i<7;$i++){
			array_push($time_arr,'open_'.$i);
			array_push($time_arr,'close_'.$i);
		}

		$data = $this -> get_posts(
		array_merge(
			array(
				'store_name',
				'password',
				'phone',
				'city',
				'district',
				'address',
				'email',
				'rank',
				'lat',
				'lng'
			),$time_arr));

		if(empty($id)) {
			// insert
			$data['account'] = $this -> get_post('account');
			$this -> dao -> insert($data);
		} else {
			// update
			$this -> dao -> update($data, $id);
		}

		// add images
		$img_id_str = $this -> get_post('img_id_list');
		$img_id_list = explode(',', $img_id_str);
		$this -> simg_dao -> add_imgs($id, $img_id_list);

		//template
		$temp = $this->get_posts(array(
			'product_1',
			'product_2',
			'product_3',
		));
		$temp['image_id'] = $this -> get_post('product_image_id');
		$temp['store_id'] = $id;
		$temp['subtitle'] = $this -> get_post('subtitle');

		$k = $this -> st_dao -> find_by('store_id',$id);
		if(empty($k)){
			//insert
			$this -> st_dao -> insert($temp);
		}else{
			//update
			$this -> st_dao -> update($temp,$k->id);
		}

		// add store_cate
		$cate_list = $this -> get_post('store_cate');
		$this -> smul_dao -> delete_all_by('store_id',$id);
		//update to table:products main_cate
		if(!empty($cate_list)) {
			$mulcate_id = implode(",",$cate_list);
			$this -> dao -> update(array('main_cate'=>$mulcate_id),$id);
			foreach($cate_list as $each){
				$insert_data = array(
					'store_id'=>$id,
					'store_cateid'=>$each
				);
				$this -> smul_dao -> insert($insert_data);
			}
		}

		$res['success'] = TRUE;
 		$this -> to_json($res);
	}

	public function delete($id) {
		$res['success'] = TRUE;
		$this -> dao -> delete_status($id, $this -> session -> userdata('user_id'));
		$this -> to_json($res);
	}

	public function setup_user_data($data){
		parent::setup_user_data($data);
		//setup store_cate
		$f = array();
		$f['parent_id'] = 0;
		$main_cates = $this -> sc_dao -> query_all($f);
		$data['store_cates'] = $main_cates;


		//rank
		$data['store_rank'] = $this -> srank_dao -> find_all();
		return $data;
	}

	public function check_account($store_id) {
		$account = $this -> get_post('account');
		$list = $this -> dao -> find_all_by('account', $account);
		$res = array();
		if (count($list) > 0) {
			if(empty($store_id)) {
				$res['valid'] = FALSE;
			} else {
				$item = $list[0];
				if($item -> id == $store_id) {
					$res['valid'] = TRUE;
				} else {
					$res['valid'] = FALSE;
				}
			}
		} else {
			$res['valid'] = TRUE;
		}
		$this -> to_json($res);
	}
}
