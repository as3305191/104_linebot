<?php
class Ranking_dbc_weekly_dao extends MY_Model {

	function __construct() {
		parent::__construct();

		// initialize table name
		parent::set_table_name('ranking_bdc_weekly');

		$this -> alias_map = array(

		);
	}

	function find_by_user_and_year_week($user_id, $year_week_id) {
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('year_week_id', $year_week_id);
		$list = $this -> find_all();
		if(count($list) > 0) {
			return $list[0];
		} else {
			return NULL;
		}
	}

	function list_rank_by_year_week($corp_id, $year_week_id, $limit = 100) {
		$this -> db -> select("_m.amt");
		$this -> db -> select("u.nick_name");
		$this -> db -> select("u.image_id");

		$this -> db -> where('_m.corp_id', $corp_id);
		$this -> db -> where('_m.year_week_id', $year_week_id);
		$this -> db -> where('_m.amt >= 0.5');
		$this -> db -> from($this -> table_name . " as _m");
		$this -> db -> join("users u", "u.id = _m.user_id", "left");

		$this -> db -> order_by('amt', 'desc');

		$this -> db -> limit($limit);
		$list = $this -> db -> get() -> result();
		$rank = 1;
		foreach ($list as $each) {
			$each -> rank = $rank++;
			$each -> image_url = !empty($each -> image_id) ? IMG_URL . $each -> image_id : '';
			$each -> image_url_thumb = !empty($each -> image_id) ? IMG_URL . $each -> image_id . '/thumb' : '';
		}
		return $list;
	}

	function list_year_week($corp_id) {
		$sql = "select distinct year_week_id from ranking_bdc_weekly where corp_id = {$corp_id} order by year_week_id desc";
		$list = $this -> db -> query($sql) -> result();
		return $list;
	}
}
?>
