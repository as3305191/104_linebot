<style>
.file-drag-handle {
	display: none;
}
.btn_1 {
    background-color: #FFD22F !important;
    color: #F57316 !important;
  }
</style>
<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-sortable="false">
	<header>
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="back_parent" onclick="currentApp.backTo()" class="btn btn-default ">
				<i class="fa fa-arrow-circle-left"></i>返回
			</a>
		</div>
		<?php if($login_user -> role_id == 99 || $login_user -> role_id == 1):?>
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="" onclick="currentApp.doSubmit()" class="btn btn-default btn-danger">
				<i class="fa fa-save"></i>存檔
			</a>
		</div>
		<?php endif ?>
		<?php if(!empty($item) && $login_user -> role_id == 99):?>
		<div class="widget-toolbar pull-left">
			<input type="number" class="fonm-control input-xs" id="pay_amt" />
			<a href="javascript:void(0);" id="do_pay" onclick="doPay()" class="btn btn-default btn-warning">
				<i class="fa fa-dollar"></i>系統加值
			</a>
		</div>


		<?php endif ?>
		<?php if(!empty($item) && ($login_user -> role_id == 99 || $login_user -> role_id == 1)):?>

		<div class="widget-toolbar pull-left">
			金幣：<span style="color: red;"><?= sp_color(number_format($sum_amt, 8)) ?></span>
		</div>
		<?php endif ?>

		<div class="widget-toolbar pull-left">
			<input type="number" class="fonm-control input-xs" id="score_amt" />
			<a href="javascript:void(0);" id="" onclick="doModScore()" class="btn btn-default btn-warning">
				<i class="fa fa-dollar"></i>增減分數
			</a>
		</div>
		<div class="widget-toolbar pull-left">
			今日分數：<span style="color: red;"><?= $score ?></span>
		</div>
	</header>

	<!-- widget div-->
	<div>
		<!-- widget edit box -->
		<div class="jarviswidget-editbox">
			<!-- This area used as dropdown edit box -->
			<input class="form-control" type="text">
		</div>
		<!-- end widget edit box -->

		<!-- widget content -->
		<div class="widget-body">

			<form id="app-edit-form" method="post" class="form-horizontal">
				<input type="hidden" name="id" id="item_id" value="<?= isset($item) ? $item -> id : '' ?>" />
				<div class="form-group" style="padding:0px 26px">
            <div class="col-md-12 col-xs-12 col-sm-12 no-padding" style="">
                <button type="button" class="new_information btn_roles btn_1" style="margin:7px;border-radius:5px;border:1.5px solid #ccc;background-color:#FFFFFF;color:#A5A4A4;width:200px;height:50px" onclick="showmetable('new_information')">新增資料</button>
                <button type="button" class="lottery btn_roles" style="margin:7px;border-radius:5px;border:1.5px solid #ccc;background-color:#FFFFFF;color:#A5A4A4;width:200px;height:50px" onclick="showmetable('lottery')">摸彩卷</button>
                <button type="button" class="items btn_roles" style="margin:7px;border-radius:5px;border:1.5px solid #ccc;background-color:#FFFFFF;color:#A5A4A4;width:200px;height:50px" onclick="showmetable('items')">道具</button>
                <button type="button" class="recharge_record btn_roles" style="margin:7px;border-radius:5px;border:1.5px solid #ccc;background-color:#FFFFFF;color:#A5A4A4;width:200px;height:50px" onclick="showmetable('recharge_record')">充值紀錄</button>
                <button type="button" class="login_record btn_roles" style="margin:7px;border-radius:5px;border:1.5px solid #ccc;background-color:#FFFFFF;color:#A5A4A4;width:200px;height:50px" onclick="showmetable('login_record')">登錄紀錄</button>
                <button type="button" class="gift_record btn_roles" style="margin:7px;border-radius:5px;border:1.5px solid #ccc;background-color:#FFFFFF;color:#A5A4A4;width:200px;height:50px" onclick="showmetable('gift_record')">贈禮紀錄</button>
                <button type="button" class="catch_fish_record btn_roles" style="margin:7px;border-radius:5px;border:1.5px solid #ccc;background-color:#FFFFFF;color:#A5A4A4;width:200px;height:50px" onclick="showmetable('catch_fish_record')">捕魚紀錄</button>
                <button type="button" class="store_record btn_roles" style="margin:7px;border-radius:5px;border:1.5px solid #ccc;background-color:#FFFFFF;color:#A5A4A4;width:200px;height:50px" onclick="showmetable('store_record')">商城紀錄</button>
                <button type="button" class="buy_record btn_roles" style="margin:7px;border-radius:5px;border:1.5px solid #ccc;background-color:#FFFFFF;color:#A5A4A4;width:200px;height:50px" onclick="showmetable('buy_record')">交易紀錄</button>
								<button type="button" class="talk_record btn_roles" style="margin:7px;border-radius:5px;border:1.5px solid #ccc;background-color:#FFFFFF;color:#A5A4A4;width:200px;height:50px" onclick="showmetable('talk_record')">密談紀錄</button>
								<button type="button" class="friends btn_roles" style="margin:7px;border-radius:5px;border:1.5px solid #ccc;background-color:#FFFFFF;color:#A5A4A4;width:200px;height:50px" onclick="showmetable('friends')">好友</button>

            </div>
            <div class="clearfix"></div>
        </div>
        <hr/>
				<div class="table_1" id="new_information" style="">
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">遊戲暱稱</label>
						<div class="col-md-6">
							<input type="text"  class="form-control" name="nick_name" value="<?= isset($item) ? $item -> nick_name : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">頭貼</label>
						<div class="col-md-6">
							<img src="<?= isset($item) ? $item -> line_picture : '' ?>" style="height:150px" border="圖片邊框">
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">帳號</label>
						<div class="col-md-6">
							<input type="text" required class="form-control"  name="account" value="<?= isset($item) ? $item -> account : '' ?>" <?= isset($item) ? 'readonly' : '' ?> />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">贈禮ID</label>
						<div class="col-md-6">
						   <input type="text" id="gift_id" name="gift_id"  class="form-control" value="<?= isset($item) ? $item -> gift_id : '' ?>" readonly="readonly" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">真實姓名</label>
						<div class="col-md-6">
							<input type="text"  class="form-control" name="line_name" value="<?= isset($item) ? $item -> line_name : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">聯絡電話</label>
						<div class="col-md-6">
							<input type="text"  class="form-control" name="mobile" value="<?= isset($item) ? $item -> mobile : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">電子郵件</label>
						<div class="col-md-6">
							<input type="text"  class="form-control" name="email" value="<?= isset($item) ? $item -> email : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">出生年/月/日</label>
						<div class="col-md-6">
							<input id="birth" name="birthday" class="form-control" placeholder="請輸入日期" type="text" class="dt_picker" value="<?= isset($item) ? $item -> birthday : '' ?>" />
						</div>
					</div>
				</fieldset>
				
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">地址</label>
						<div class="col-md-6">
							<input type="text"  class="form-control" name="address" value="<?= isset($item) ? $item -> address : '' ?>" />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">推廣網址</label>
						<div class="col-md-6">
							<div class="input-group">
							  <input type="text" id="code_url" required class="form-control" value="<?= isset($item) ? GAME_WEB_URL . "?promo={$item->gift_id}" : '' ?>" readonly="readonly" />
							   <span class="input-group-btn">
						        <button type="button" class="btn" onclick="copyToClipboard('#code_url')" >複製</button>
							   </span>
							</div>

						</div>
					</div>
				</fieldset>


				<?php if($login_user -> role_id == 1 || $login_user -> role_id == 99):?>
				<!-- <fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">密碼</label>
						<div class="col-md-6">
							<input type="text" required class="form-control" name="password" value="<?= isset($item) ? $item -> password : '' ?>" />
						</div>
					</div>
				</fieldset> -->
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">是否通過簡訊驗證</label>
						<div class="col-md-6">
							<select name="is_valid_mobile" class="form-control">
								<option value="1" <?= isset($item) && $item -> is_valid_mobile == '1' ? 'selected' : '' ?>>通過</option>
								<option value="0" <?= isset($item) && $item -> is_valid_mobile == '0' ? 'selected' : '' ?>>未通過</option>
							</select>
						</div>
					</div>
				</fieldset>
				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">不出現在富豪榜</label>
						<div class="col-md-6">
							<select name="is_bypass_sum_amt_rank" class="form-control">
								<option value="1" <?= isset($item) && $item -> is_bypass_sum_amt_rank == '1' ? 'selected' : '' ?>>是</option>
								<option value="0" <?= isset($item) && $item -> is_bypass_sum_amt_rank == '0' ? 'selected' : '' ?>>否</option>
							</select>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">免手續費</label>
						<div class="col-md-6">
							<select name="is_bypass_service_fee" class="form-control">
								<option value="1" <?= isset($item) && $item -> is_bypass_service_fee == '1' ? 'selected' : '' ?>>是</option>
								<option value="0" <?= isset($item) && $item -> is_bypass_service_fee == '0' ? 'selected' : '' ?>>否</option>
							</select>
						</div>
					</div>
				</fieldset>
			</div>
			<!-- 	<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">身份證</label>
						<div class="col-md-6">
							<?php if(isset($item) && !empty($item -> uid_image_id)):?>
								<img id="del_uid_image" src="<?=  base_url('mgmt/images/get/' . $item -> uid_image_id) ?>" height="200" />
								<button type="button" onclick="$('#del_uid_image_id').val('<?= $item -> uid_image_id ?>');$('#del_uid_image').remove()">刪除</button>
								<input type="hidden" id="del_uid_image_id" name="del_uid_image_id" />
							<?php else: ?>
								<p class="form-control-static" style="color:red;">尚未上傳</p>
							<?php endif ?>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">手持 證件</label>
						<div class="col-md-6">
							<?php if(!empty($item -> bank_image_id_2)):?>
								<img id="del_bank_image_2" src="<?=  base_url('mgmt/images/get/' . $item -> bank_image_id_2) ?>" height="200" />
								<button type="button" onclick="$('#del_bank_image_id_2').val('<?= $item -> bank_image_id_2 ?>');$('#del_bank_image_2').remove()">刪除</button>
								<input type="hidden" id="del_bank_image_id_2" name="del_uid_image_id" />
							<?php else: ?>
								<p class="form-control-static" style="color:red;">尚未上傳</p>
							<?php endif ?>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">個人簽字自拍</label>
						<div class="col-md-6">
							<?php if(!empty($item -> uid_image_id_2)):?>
								<img id="del_uid_image_2" src="<?=  base_url('mgmt/images/get/' . $item -> uid_image_id_2) ?>" height="200" />
								<button type="button" onclick="$('#del_uid_image_id_2').val('<?= $item -> uid_image_id_2 ?>');$('#del_uid_image_2').remove()">刪除</button>
								<input type="hidden" id="del_uid_image_id_2" name="del_uid_image_id_2" />
							<?php else: ?>
								<p class="form-control-static" style="color:red;">尚未上傳</p>
							<?php endif ?>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">是否通過身份驗證</label>
						<div class="col-md-6">
							<select name="is_valid_uid" class="form-control">
								<option value="Y" <?= isset($item) && $item -> is_valid_uid == '1' ? 'selected' : '' ?>>通過</option>
								<option value="N" <?= isset($item) && $item -> is_valid_uid == '0' ? 'selected' : '' ?>>未通過</option>
							</select>
						</div>
					</div>
				</fieldset>
				<hr/>
				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">銀行封面</label>
						<div class="col-md-6">
							<?php if(isset($item) && !empty($item -> bank_image_id)):?>
								<img id="del_bank_image" src="<?=  base_url('mgmt/images/get/' . $item -> bank_image_id) ?>" height="200" />
								<button type="button" onclick="$('#del_bank_image_id').val('<?= $item -> bank_image_id ?>');$('#del_bank_image').remove()">刪除</button>
								<input type="hidden" id="del_bank_image_id" name="del_bank_image_id" />
							<?php else: ?>
								<p class="form-control-static" style="color:red;">尚未上傳</p>
							<?php endif ?>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">銀行</label>
						<div class="col-md-6">
							<select name="bank_id" class="form-control input" >
			          <?php if(!empty($bank_list)) : ?>
			            <?php foreach($bank_list as $each) : ?>
			              <option value="<?= $each -> bank_id ?>" <?= $item -> bank_id == $each -> bank_id ? 'selected' : '' ?> ><?= $each -> bank_name ?> (<?= $each -> bank_id ?>)</option>
			            <?php endforeach ?>
			          <?php endif ?>
			        </select>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">銀行帳號</label>
						<div class="col-md-6">
							<input type="text" name="bank_account" class="form-control" value="<?= isset($item) ? $item -> bank_account : '' ?>" />
						</div>
					</div>
				</fieldset>


				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">是否通過銀行驗證</label>
						<div class="col-md-6">
							<select name="is_valid_bank" class="form-control">
								<option value="Y" <?= isset($item) && $item -> is_valid_bank == '1' ? 'selected' : '' ?>>通過</option>
								<option value="N" <?= isset($item) && $item -> is_valid_bank == '0' ? 'selected' : '' ?>>未通過</option>
							</select>
						</div>
					</div>
				</fieldset>
				<?php endif ?>
				<hr/>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">名稱</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="user_name" value="<?= isset($item) ? $item -> user_name : '' ?>" />
						</div>
					</div>
				</fieldset> -->



				<!-- <fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">Email</label>
						<div class="col-md-6">
							<input type="email" class="form-control" name="email" value="<?= isset($item) ? $item -> email : '' ?>" />
						</div>
					</div>
				</fieldset>

				<?php if($login_user -> role_id == 99):?>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">權限角色</label>
						<div class="col-md-6">
							<select name="role_id" id="user_role_id" class="form-control" <?= $login_user -> role_id == 99
								|| !isset($item) ? '' : 'disabled' ?>>
								<option value="0" disabled>無</option>
								<?php foreach($role_list as $each): ?>
									<option
										<?= $login_user -> role_id == 99  ? '' : 'disabled' ?>
										<?= $each -> id == 99 ? 'disabled' : '' ?>
										value="<?= $each -> id?>"
										<?= isset($item) && $item -> role_id == $each -> id ? 'selected' : '' ?>
										<?= is_disabled_4role($login_user -> role_id, $each -> id) ?> >
										<?=  $each -> role_name ?>
									</option>
								<?php endforeach ?>
							</select>
						</div>
					</div>
				</fieldset>
				<?php endif ?>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">個人照</label>
						<div class="col-md-6">
							<input id="image_id" name="image_id" type="hidden" value="<?= isset($item) ? $item -> image_id : '' ?>">
							<input id="file-input" name="file" type="file" class="file-loading form-control">
						</div>
					</div>
				</fieldset>
			</hr> -->

			<header>
				<form id="app-lottery-edit-form" method="post" class="form-horizontal">
					<input type="hidden" name="tab_id" value="<?= !empty($item) ? $item -> id : '' ?>" />

					<!-- <div class="widget-toolbar pull-left"> -->
						<!-- <fieldset>
							<div class="form-group">
								<label class="col-md-6 control-label">摸彩序號</label>
								<div class="col-md-6">
									<input type="text"  class="form-control" name="lottery_sn" value="" />
								</div>
							</div>
						</fieldset> -->
					<!-- </div> -->
					<!-- <div class="widget-toolbar pull-left"> -->
						<!-- <fieldset>
							<div class="form-group">
								<label class="col-md-6 control-label">期數</label>
								<div class="col-md-6">
									<input type="text"  class="form-control" name="lottery_no" value="" />
								</div>
							</div>
						</fieldset> -->
					<!-- </div> -->
					<!-- <div class="widget-toolbar pull-left">
						<a href="javascript:void(0);" onclick="currentApp.lotteryList.doSubmit()" class="btn btn-default btn-danger">
						收尋
						</a>
					</div> -->
				</form>
				<div class="table_1" id="lottery" style="display:none">
					<div class="col-md-3 "  style="">
						期數：<select name="" id="lottery_select" class="form-control">
							<option value="0">全部</option>
							<?php foreach ($list_tab as $each_tab): ?>
								<option value="<?=$each_tab->id?>"><?=$each_tab->lottery_no?></option>
							<?php endforeach; ?>
						</select>
					</div>
				<table id="lottery_list" class="table table-striped table-bordered table-hover" width="100%">
					<thead>
						<tr>
							<th class="min100">期數</th>
							<th class="min100">摸彩名稱</th>
							<th class="min100">摸彩卷序號</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
				<div class="table_1" id="items" style="display:none">
					<div class="col-md-2 "  style="">
						<select name="" id="items_select" class="form-control">
							<?php foreach ($produsts as $each_produsts): ?>
								<option value="<?=$each_produsts->id?>"><?=$each_produsts->product_name?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<input type="hidden" id="user_id" value="<?= $id ?>" />
					<div class="col-md-3">

						<input type="text" id="for_i" class="form-control" placeholder="請輸入數量"  value="" />
					</div>

						<a href="javascript:void(0);" id="" onclick="new_items()" class="btn btn-default " style="background:lightgreen">
							新增道具
						</a>

				<table id="dt_list_1" class="table table-striped table-bordered table-hover" width="100%">
					<thead>
						<tr>
							<th class="min50"></th>
							<th class="min50">E武士刀</th>
							<th class="min50">D武士刀</th>
							<th class="min50">C武士刀</th>
							<th class="min50">B武士刀</th>
							<th class="min50">A武士刀</th>
							<th class="min50">S武士刀</th>
							<th class="min50">E砲塔</th>
							<th class="min50">D砲塔</th>
							<th class="min50">C砲塔</th>
							<th class="min50">B砲塔</th>
							<th class="min50">A砲塔</th>
							<th class="min50">S砲塔</th>
							<th class="min50">電池A</th>
							<th class="min50">晶片B</th>
							<th class="min50">齒輪C</th>
							<th class="min50">強化材料</th>
						</tr>
					</thead>
					<tbody>

						<tr>
								<td class="min50">數量：</td>
							<?php foreach ($article as $each_article): ?>
								<td class="min50"><?= isset($each_article -> total )  ? $each_article -> total  : '0' ?></td>
							<?php endforeach; ?>
							<td class="min50"><?= isset( $material )  ? $material[0] -> total  : '0' ?></td>
						</tr>
						<tr>
							<td class="min50">強化：</td>
							<?php foreach ($p_level as $each_p_level): ?>
								<td class="min50"><?= isset($each_p_level -> level )  ? $each_p_level -> level  : '0' ?></td>
							<?php endforeach; ?>
							<td class="min50">0</td>
							<td class="min50">0</td>
							<td class="min50">0</td>
							<td class="min50">0</td>

						</tr>
					</tbody>
				</table>

				<table id="level_record_list" class="table table-striped table-bordered table-hover" width="100%">
					<thead>
						<tr>
							<th class="min100">物品</th>
							<th class="min100">強化等級</th>
							<th class="min100">狀態</th>
							<th class="min100">時間</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="table_1" id="recharge_record" style="display:none">
				<table id="recharge_record_list" class="table table-striped table-bordered table-hover" width="100%">
					<thead>
						<tr>
							<th class="min100">物品</th>
							<th class="min100">數量</th>
							<th class="min100">金額</th>
							<th class="min100">狀態</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				</div>
			<div class="table_1" id="login_record" style="display:none">
				<table id="login_list" class="table table-striped table-bordered table-hover" width="100%">
					<thead>
						<tr>
							<th class="min100">登入方式</th>
							<th class="min100">ip</th>
							<th class="min100">登入時間</th>

						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="table_1" id="gift_record" style="display:none">
				<div class="col-md-3 "  style="">
					篩選：<select name="" id="gift_select" class="form-control">
						<option value="-1">全部</option>
						<option value="0">贈禮</option>
						<option value="1">收禮</option>
					</select>
				</div>
				<table id="gift_record_list" class="table table-striped table-bordered table-hover" width="100%">
					<thead>
						<tr>
							<th class="min100">贈送禮</th>
							<th class="min100">贈禮人暱稱</th>
							<th class="min100">收禮人暱稱</th>
							<th class="min100">數量</th>
							<th class="min100">狀態</th>
							<th class="min100">時間</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="table_1" id="catch_fish_record" style="display:none">
				<table id="catch_fish_list" class="table table-striped table-bordered table-hover" width="100%">
					<thead>
						<tr>
							<th class="min100">狀態</th>
							<th class="min100">對象</th>
							<th class="min100">魚彩池</th>
							<th class="min100">魚王彩池</th>
							<th class="min100">JP彩池</th>
							<th class="min100">寶箱彩池</th>
							<th class="min100">魚王名稱</th>
							<th class="min100">必殺技</th>
							<th class="min100">掉落物品</th>
							<th class="min100">時間</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="table_1" id="store_record" style="display:none">
				<table id="store_list" class="table table-striped table-bordered table-hover" width="100%">
					<thead>
						<tr>
							<th class="min100">物品</th>
							<th class="min100">價格</th>
							<th class="min100">數量</th>
							<th class="min100">花費</th>
							<th class="min100">時間</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="table_1" id="buy_record" style="display:none">
				<table id="buy_list" class="table table-striped table-bordered table-hover" width="100%">
					<thead>
						<tr>
							<th class="min100">物品</th>
							<th class="min100">狀態</th>
							<th class="min100">價格</th>
							<th class="min100">數量</th>
							<th class="min100">花費</th>
							<th class="min100">時間</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="table_1" id="talk_record" style="display:none">
				<table id="talk_list" class="table table-striped table-bordered table-hover" width="100%">
					<thead>
						<tr>
							<th class="min100">接收對象</th>
							<th class="min100">接收人員</th>
							<th class="min100">內容</th>
							<th class="min100">時間</th>

						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="table_1" id="friends" style="display:none">
				<table id="friends_list" class="table table-striped table-bordered table-hover" width="100%">
					<thead>
						<tr>
							<th class="min100">好友</th>
							<th class="min100">封鎖</th>
							<th class="min100">時間</th>

						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			</form>

		</div>
		<!-- end widget content -->

	</div>
	<!-- end widget div -->

</div>
<!-- end widget -->
<style>
	.kv-file-zoom {
		display: none;
	}
</style>

<script>



	$("#file-input").fileinput({
        <?php if(!empty($item -> img)): ?>
        	initialPreview: [
        		'<?=  base_url('mgmt/images/get/' . $item -> img -> id) ?>'
        	],
        	initialPreviewConfig: [{
        		'caption' : '<?= $item -> img -> image_name ?>',
        		'size' : <?= $item -> img -> image_size ?>,
        		'width' : '120px',
        		'url' : '<?= base_url('mgmt/images/delete/' . $item -> img -> id)  ?>',
        		'key' : <?= $item -> img -> id ?>
        	}],
        <?php else: ?>
        	initialPreview: [],
        	initialPreviewConfig: [],
        <?php endif ?>
        initialPreviewAsData: true,
        maxFileCount: 1,
        uploadUrl: 'mgmt/images/upload/user_img',
        uploadExtraData: {
        }
    }).on('fileselect', function(event, numFiles, label) {
    	$("#file-input").fileinput('upload');
	}).on('fileuploaded', function(event, data, previewId, index) {
	   var id = data.response.id;
		$('#image_id').val(id);
	}).on('fileuploaderror', function(event, data, previewId, index) {
		alert('upload error');
	}).on('filedeleted', function(event, key) {
		$('#image_id').val(0);
	});


	// select2
	$('#company_id').select2();
	$('#cooperative_id').select2();
	$('#fleet_id').select2();

	$('#group_id').select2();

	function copyToClipboard(element) {
	  var $input = $("<input>");
	  $("body").append($input);
	  $input.val($(element).val());

		if (navigator.userAgent.match(/ipad|ipod|iphone/i)) {
		  var el = $input.get(0);
		  var editable = el.contentEditable;
		  var readOnly = el.readOnly;
		  el.contentEditable = true;
		  el.readOnly = false;
		  var range = document.createRange();
		  range.selectNodeContents(el);
		  var sel = window.getSelection();
		  sel.removeAllRanges();
		  sel.addRange(range);
		  el.setSelectionRange(0, 999999);
		  el.contentEditable = editable;
		  el.readOnly = readOnly;
		} else {
		  $input.select();
		}

	  document.execCommand("copy");
	  $input.remove();

		alert('複製成功');
	}

	$(".dt_picker").datetimepicker({
		format : 'YYYY-MM-DD'
	}).on('dp.change',function(event){

	});

	function showmetable(id) {
	    //   document.getElementById(id).show();
	    $('.table_1').hide();
	    $('#'+id).show();
	    $('.btn_roles').removeClass('btn_1');
	    $('.'+id).addClass('btn_1');
	  }

		function new_items() {
			$.ajax({
				url: '<?= base_url() ?>' + 'mgmt/users/insert_items',
				type: 'POST',
				data: {
					user_id: $('#user_id').val(),
					product_id: $('#items_select').val(),
					for_i: $('#for_i').val()
				},
				dataType: 'json',
				success: function(d) {
					if(d.success){
						currentApp.doEdit($('#user_id').val());

					}
					if(d.success1){
						currentApp.doEdit($('#user_id').val());

					}
				},
				failure:function(){
					alert('faialure');
				}
			});
		}

	function doPay() {
		if(confirm('是否確認？')) {
			$.ajax({
				url: '<?= base_url() ?>' + 'mgmt/pay_records/sys_insert',
				type: 'POST',
				data: {
					user_id: $('#item_id').val(),
					amt: $('#pay_amt').val()
				},
				dataType: 'json',
				success: function(d) {
					currentApp.doEdit($('#item_id').val());

				},
				failure:function(){
					alert('faialure');
				}
			});
		}
	}

	function doModScore() {
		if(confirm('是否確認？')) {
			$.ajax({
				url: '<?= base_url() ?>' + 'mgmt/ranking/sys_insert',
				type: 'POST',
				data: {
					user_id: $('#item_id').val(),
					score: $('#score_amt').val()
				},
				dataType: 'json',
				success: function(d) {
					currentApp.doEdit($('#item_id').val());
				},
				failure:function(){
					alert('faialure');
				}
			});
		}
	}

	function doPayBtc() {
		if(confirm('是否購買BTC？')) {
			$.ajax({
				url: '<?= base_url() ?>' + 'mgmt/pay_records/sys_insert_coin',
				type: 'POST',
				data: {
					user_id: $('#item_id').val(),
					type: 'btc',
					amt: $('#pay_amt_btc').val()
				},
				dataType: 'json',
				success: function(d) {
					alert('購買成功');
					currentApp.doEdit($('#item_id').val());
				},
				failure:function(){
					alert('faialure');
				}
			});
		}
	}
	function doPayEth() {
		if(confirm('是否購買ETH？')) {
			$.ajax({
				url: '<?= base_url() ?>' + 'mgmt/pay_records/sys_insert_coin',
				type: 'POST',
				data: {
					user_id: $('#item_id').val(),
					type: 'eth',
					amt: $('#pay_amt_eth').val()
				},
				dataType: 'json',
				success: function(d) {
					alert('購買成功');
					currentApp.doEdit($('#item_id').val());
				},
				failure:function(){
					alert('faialure');
				}
			});
		}
	}
	function doPayNtd() {
		if(confirm('是否購買NTD？')) {
			$.ajax({
				url: '<?= base_url() ?>' + 'mgmt/pay_records/sys_insert_coin',
				type: 'POST',
				data: {
					user_id: $('#item_id').val(),
					type: 'ntd',
					amt: $('#pay_amt_ntd').val()
				},
				dataType: 'json',
				success: function(d) {
					alert('購買成功');
					currentApp.doEdit($('#item_id').val());
				},
				failure:function(){
					alert('faialure');
				}
			});
		}
	}

	function doPayBdc() {
		if(confirm('是否購買藍鑽幣？')) {
			$.ajax({
				url: '<?= base_url() ?>' + 'mgmt/pay_records/sys_insert_coin',
				type: 'POST',
				data: {
					user_id: $('#item_id').val(),
					type: 'bdc',
					amt: $('#pay_amt_bdc').val()
				},
				dataType: 'json',
				success: function(d) {
					alert('購買成功');
					currentApp.doEdit($('#item_id').val());
				},
				failure:function(){
					alert('faialure');
				}
			});
		}
	}

	function doClearWash() {
		if(confirm('是否清除？')) {
			$.ajax({
				url: '<?= base_url() ?>' + 'mgmt/users/clear_wash',
				type: 'POST',
				data: {
					user_id: $('#item_id').val()
				},
				dataType: 'json',
				success: function(d) {
					alert('已完成');
					currentApp.doEdit($('#item_id').val());
				},
				failure:function(){
					alert('faialure');
				}
			});
		}
	}


	currentApp.lotteryList = new FishtableLotteryAppClass(new BaseAppClass({}));
	currentApp.giftList = new FishtableGiftAppClass(new BaseAppClass({}));
	currentApp.rechargeList = new RechargerecordAppClass(new BaseAppClass({}));
	currentApp.storeList = new StoreAppClass(new BaseAppClass({}));
	currentApp.buyList = new BuyAppClass(new BaseAppClass({}));
	currentApp.friendsList = new FriendsAppClass(new BaseAppClass({}));
	currentApp.talkList = new TalkAppClass(new BaseAppClass({}));
	currentApp.caughtfishList = new CaughtfishAppClass(new BaseAppClass({}));
	currentApp.levelrecordfishList = new LevelrecordAppClass(new BaseAppClass({}));
	currentApp.loginList = new LoginAppClass(new BaseAppClass({}));

</script>
