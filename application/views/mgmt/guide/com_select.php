<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url('css/guide/share.css') ?>">
<style>
</style>
<div class="container-fluid">
	<h2 style="margin-left">
		遊戲選單
	</h2>
	<h3>請選擇您要遊戲的系統</h3>
	<div class="btn-group btn-matrix c-box">
		<?php foreach($company_list as $each): ?>
			<a class="btn btn-default col-sm-6" data-id="<?= $each -> id ?>"><?= $each -> company_name ?></a>
		<?php endforeach ?>
	</div>

	<h3>尚未結束任務</h3>
	<input type="hidden" id="login_user_id" value="<?= $login_user_id ?>" />
 	<div class="table-responsive">
		<table id="main_tb" class="table table-striped table-bordered table-hover" width="100%">
			<thead>
				<tr>
					<th class="min100">日期</th>
					<th class="min100">博弈系統</th>
					<th class="min100">桌號</th>
					<th class="min100">輸贏數量</th>
					<th class="min100">狀態</th>
					<th class="min150">建立時間</th>
				</tr>
			</thead>

			<tbody>
			</tbody>
		</table>
	</div>
</div>

<script>
var cCompanyId;
var cCompanyName;
$('.c-box a').on('click', function(){
	cCompanyId = $(this).data('id');
	cCompanyName = $(this).text();
	currentApp.waitingDialog.show('連接 ' + cCompanyName + ' 即時開獎資料匯入');
	setTimeout(function(){
		$('#main-frame').load(baseUrl + 'mgmt/guide/table_select?com_id=' + cCompanyId, function(){
			currentApp.waitingDialog.hide();
		});
	}, 10000);
});

// data table
var mDtTable;
var dtDefaultContent = '<a href="#deleteModal" role="button" data-toggle="modal"><i class="fa fa-trash fa-lg"></i></a>' ;

	var dtFnRowCallback =  function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {

   };

	var columnCfg = [
  	{data:'create_date'},
  	{data:'company_name'},
  	{data:'tab_name'},
  	{data:'balance'},
  	{data:'status'},
  	{data:'create_time'}
	];

$(function(){
    mDtTable = $('#main_tb')
    .DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		deferLoading: 0, // don't reload on init
		iDisplayLength: 10,
		sDom: "<'dt-toolbar'<'col-sm-12 col-xs-12'p>r>"+
						"<'t-box'"+
						"t"+
						">"+
						"<'dt-toolbar-footer'<'col-sm-6 col-xs-12'i><'col-xs-12 col-sm-6 hidden-xs'l>>",
		language: {
        url : baseUrl + "js/datatables-lang/zh-TW.json"
    },

		ajax: {
			url:baseUrl+ 'mgmt/guide/get_data',
			data: function(d) {
				d.user_id = $('#login_user_id').val();
				d.status = 0;
			},
			dataSrc:'items',
			dataType : 'json',
			type:'post'
		},

  	columns: columnCfg,

  	bSortCellsTop: true,
  	order: [[ 5, "desc" ]],
  	columnDefs: [
    {"targets":0,"orderable":false},
    {"targets":1,"orderable":false},
		{"targets":2,"orderable":false},
		{"targets":3,"orderable":false},
		{"targets":4,"orderable":false}
		] ,
		fnRowCallback : function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

					var _rtd = $(nRow).find('td');

					_rtd.addClass('pointer').on('click', function(){
						//app.doEdit(aData.id);
						$('#main-frame').load(baseUrl + 'mgmt/guide/main?clear_yn=yes&com_id=' + aData.com_id
						+ '&tab_id=' + aData.tab_id, function(){
							currentApp.waitingDialog.hide();
						});

						// remove all highlight first
						$(this).parent().parent().find('tr').removeClass('active');

						mDtTable._lastPk = aData.id;
						mDtTable._tr = $(this).parent();
						setTimeout(function(){
							mDtTable._tr.addClass('active');
						}, 100);
					});

					if(mDtTable._lastPk && aData.id && mDtTable._lastPk == aData.id) {
						$(nRow).addClass('active');
					}

			}

   });

	 $("#main-table thead th input[type=text]").on( 'keyup change', function () {
			 mDtTable
					 .column( $(this).parent().index()+':visible' )
					 .search( this.value )
					 .draw();
		});

		$('#ope_type_id').on('change', function(){
			tableReload();
		});

   tableReload();
});

function tableReload() {
	mDtTable.ajax.reload();
}

</script>
