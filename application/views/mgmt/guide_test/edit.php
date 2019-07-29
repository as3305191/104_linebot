<style>
.file-drag-handle {
	display: none;
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
		<div class="widget-toolbar pull-left">
			<a href="javascript:void(0);" id="" onclick="runResult();" class="btn btn-default btn-info">
				<i class="fa fa-save"></i>執行
			</a>
		</div>
		<?php endif ?>
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

				<fieldset>
					<div class="form-group">
						<label class="col-sm-3 col-md-3 control-label">下注循環數</label>
						<div class="col-sm-6 col-md-3">
							<div class="input-group">
							   <input id="loop_count" type="number" style="" required name="loop_count" class="form-control" value="<?= isset($item) ? $item -> loop_count : '' ?>" />
							   <input id="loop_content_json" type="hidden" name="loop_content" value="<?= isset($item) ? $item -> loop_content : '' ?>" />
								 <span class="input-group-btn">
									 <button class="btn btn-secondary" type="button">設定</button>
								 </span>
							</div>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">循環內容</label>
						<div class="col-md-6" id="loop_content">

						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-sm-3 col-md-3 control-label">局數</label>
						<div class="col-sm-6 col-md-3">
							<div class="input-group">
							   <input id="round_count" type="number" style="" name="round_count" required  class="form-control" value="<?= isset($item) ? $item -> round_count : '' ?>" />
								 <input id="round_content_json" type="hidden" name="round_content" value="<?= isset($item) ? $item -> round_content : '' ?>" />
								 <span class="input-group-btn">
									 <button class="btn btn-secondary" type="button">設定</button>
								 </span>
							</div>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-md-3 control-label">每局輸贏</label>
						<div class="col-md-3" id="round_content">

						</div>
						<div class="col-md-6">
							<table border="1" width="">
								<tbody id="draw_body">

								</tbody>
							</table>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<div class="form-group">
						<label class="col-sm-3 col-md-3 control-label">初始下注金額</label>
						<div class="col-sm-6 col-md-3">
							<div class="input-group">
							   <input id="init_bet" type="number" style="" required name="init_bet" class="form-control" value="<?= isset($item) ? $item -> init_bet : '' ?>" />
							</div>
						</div>
					</div>
				</fieldset>

			</form>

			<div id="result_body"></div>

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

	.c_box {
		position: relative;
		width: 20px;
		height: 20px;
	}
	.c_red {
		position: absolute;
		top:0;
		left: 0;
		font-size: 9px;
		line-height: 15px;
		text-align: center;
		color: red;
    background-color:#fff;
    border:3px solid red;
    height:20px;
    border-radius:50%;
    -moz-border-radius:50%;
    -webkit-border-radius:50%;
    width:20px;
	}

	.c_blue {
		position: absolute;
		top:0;
		left: 0;
    background-color:#fff;
    border:3px solid blue;
    height:20px;
    border-radius:50%;
    -moz-border-radius:50%;
    -webkit-border-radius:50%;
    width:20px;
	}
	.c_white {
		position: relative;
		top:0;
		left: 0;
    background-color:#fff;
    border:1px solid white;
    height:20px;
    border-radius:50%;
    -moz-border-radius:50%;
    -webkit-border-radius:50%;
    width:20px;
	}
	.c_green {
		position: absolute;
		z-index: 99;
		top:0;
		left: 0;
    height:20px;
    width:20px;
		color: green;
		text-align: center;
		line-height: 20px;
	}
</style>

<script type="text/template" data-template="listitem">
	<div>
		<label class="btn btn-default btn-lg ">
			${num}
		</label>
		<div class="btn-group" data-toggle="buttons">
			<label class="btn btn-default btn-lg ">
				<input type="radio" name="loop_${cnt}" value="1" autocomplete="off"> 莊
			</label>
			<label class="btn btn-default btn-lg ">
				<input type="radio" name="loop_${cnt}" value="-1" autocomplete="off"> 閒
			</label>
		</div>
	</div>
</script>

<script type="text/template" data-template="round_item">
	<div>
		<label class="btn btn-default btn-lg ">
			${num}
		</label>
		<div class="btn-group" data-toggle="buttons">
			<label class="btn btn-default btn-lg ">
				<input type="radio" name="round_${cnt}" value="1" autocomplete="off"> 莊
			</label>
			<label class="btn btn-default btn-lg ">
				<input type="radio" name="round_${cnt}" value="-1" autocomplete="off"> 閒
			</label>
			<label class="btn btn-default btn-lg ">
			 <input type="radio" name="round_${cnt}" value="0" autocomplete="off"> 和
			</label>
		</div>
	</div>
</script>

<script>
	function render(props) {
		return function(tok, i) { return (i % 2) ? props[tok] : tok; };
	}

	var itemTpl = $('script[data-template="listitem"]').text().split(/\$\{(.+?)\}/g);
	var roundTpl = $('script[data-template="round_item"]').text().split(/\$\{(.+?)\}/g);
	var loopStore = [], roundStore = [];

	function redrawLoop() {
		$('#loop_content').empty();
		for(var i = 0 ; i < loopStore.length ; i++) {
			var l = loopStore[i];
			var $item = $(itemTpl.map(render({cnt: i, num: i + 1})).join(''));
			$item.data('cnt', i);
			$('#loop_content').append($item);

			$('input[name=loop_' + i + '][value=' + l + ']').trigger('click');
		}
	}

	$('#loop_count').on('change', function(){
		if(!isNaN($('#loop_count').val())) {
			var cnt = parseInt($('#loop_count').val());
			loopStore = [];
			for(var i = 0 ; i < cnt ; i++) {
				loopStore.push(1);
			}
			redrawLoop();
		}
	});

	function redrawRound() {
		$('#round_content').empty();
		for(var i = 0 ; i < roundStore.length ; i++) {
			var r = roundStore[i];
			var $item = $(roundTpl.map(render({cnt: i, num: i + 1})).join(''));
			$item.data('cnt', i);
			$('#round_content').append($item);
			$('input[name=round_' + i + '][value=' + r + ']').trigger('click');
		}
		$('#round_content input').on('change', function(){
			getRoundStoreStr();
		});
	}

	$('#round_count').on('change', function(){
		if(!isNaN($('#round_count').val())) {
			var cnt = parseInt($('#round_count').val());
			$('#round_content').empty();
			roundStore = [];
			for(var i = 0 ; i < cnt ; i++) {
				roundStore.push(0);
			}
			redrawRound();
		}
	});

	$('#app-edit-form').bootstrapValidator({
		feedbackIcons : {
			valid : 'glyphicon glyphicon-ok',
			invalid : 'glyphicon glyphicon-remove',
			validating : 'glyphicon glyphicon-refresh'
		},
		fields: {

    }
	})
	.bootstrapValidator('validate');

	function getLoopStoreStr() {
		for(var i = 0 ; i < loopStore.length ; i++) {
			loopStore[i] = parseInt($('input[name=loop_' + i + ']:checked').val());
		}
		$('#loop_content_json').val(JSON.stringify(loopStore));

	}

	function getRoundStoreStr() {
		for(var i = 0 ; i < roundStore.length ; i++) {
			roundStore[i] = parseInt($('input[name=round_' + i + ']:checked').val());
		}
		$('#round_content_json').val(JSON.stringify(roundStore));

		genRoundMatrix();
	}

	function genRoundMatrix() {
		var isFirst = true;
		var $last;
		var $matrix = Create2DArray(100);
		var $matrix_tie = Create2DArray(100);
		var $max_j = 5;
		var $i = 0, $j = 0;
		var $i_diff = 0;
		var $last_i = 0, $last_j = 0;

		$.each(roundStore, function(){
			var me = parseInt(this);
			// console.log(me);
			if(me == 0) { // tie
				if(!$matrix_tie[$last_i][$last_j]) {
					$matrix_tie[$last_i][$last_j] = 0;
				}
				$matrix_tie[$last_i][$last_j]++;
			} else { // not tie
				if(isFirst) {
					// first
					isFirst = false;
					$matrix[$i + $i_diff][$j] = me;
				} else if(parseInt($last) == me) {
					$j++;
					while($j > $max_j || $matrix[$i + $i_diff][$j]) {
						$j--;
						$i_diff++;
					}

					$matrix[$i + $i_diff][$j] = me;
				} else {
					$i++;
					$j = 0;
					$i_diff = 0;
					while($matrix[$i + $i_diff][$j]) {
						$i++;
					}
					$matrix[$i + $i_diff][$j] = me;
				}

				$last_i = $i + $i_diff;
				$last_j = $j;

				$last = me;
			}
		});

		// draw matrix
		$max_i = 0;
		$max_j = 5;
		for(var i = 0 ; i < $matrix.length ; i++) {
			if($matrix[i].length > 0) {
				$max_i = i;
			}
		}

		$('#draw_body').empty();
		for(var j = 0 ; j < 6 ; j++) {
			var $tr = $('<tr></tr>').appendTo($('#draw_body'));
			for(var i = 0 ; i <= $max_i ; i++) {
				var $td = $('<td></td>').appendTo($tr);
				if($matrix[i][j]) {
					var $bx = $("<div class='c_box'></div>").append($(valName($matrix[i][j])));
					$td.append($bx);
					if($matrix_tie[i][j]) {
						// console.log($bx);
						$("<div class='c_green'>" + $matrix_tie[i][j] + "</div>").appendTo($bx);
						// console.log($bx);
					}
				} else {
					$td.append($('<div class="c_white"></div>'));
				}
			}
		}
	}

	function valName(v) {
		if(v == 1) {
			return "<div class='c_red'></div>";
		}
		if(v == -1) {
			return "<div class='c_blue'></div>";
		}
		return "xxxx";
	}
	function Create2DArray(rows) {
	  var arr = [];

	  for (var i=0;i<rows;i++) {
	     arr[i] = [];
	  }

	  return arr;
	}

	function parseAll() {
		var lJson = $('#loop_content_json').val();
		var rJson = $('#round_content_json').val();
		loopStore = JSON.parse(lJson);
		redrawLoop();
		roundStore = JSON.parse(rJson);
		redrawRound();
	}
	parseAll();

	getRoundStoreStr();

	function runResult() {
		getLoopStoreStr();
		getRoundStoreStr();

		$('#result_body').load('<?= base_url('mgmt/guide_test/run') ?>', {
			loop : $('#loop_content_json').val(),
			round: $('#round_content_json').val(),
			init_bet: $('#init_bet').val()

		}, function(){
			alert('complete');
		});
	}
</script>
