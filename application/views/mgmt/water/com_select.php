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

</div>
<script>
/**
 * Plugin for using queue for multiple ajax requests.
 *
 * @autor Pavel Máca
 * @github https://github.com/PavelMaca
 * @license MIT
 */

(function($) {
    var AjaxQueue = function(options){
        this.options = options || {};

        var oldComplete = options.complete || function(){};
        var completeCallback = function(XMLHttpRequest, textStatus) {

            (function() {
                oldComplete(XMLHttpRequest, textStatus);
            })();

            $.ajaxQueue.currentRequest = null;
            $.ajaxQueue.startNextRequest();
        };
        this.options.complete = completeCallback;
    };

    AjaxQueue.prototype = {
        options: {},
        perform: function() {
            $.ajax(this.options);
        }
    }

    $.ajaxQueue = {
        queue: [],

        currentRequest: null,

        stopped: false,

        stop: function(){
            $.ajaxQueue.stopped = true;

        },

        run: function(){
            $.ajaxQueue.stopped = false;
            $.ajaxQueue.startNextRequest();
        },

        clear: function(){
            $.ajaxQueue.queue = [];
            $.ajaxQueue.currentRequest = null;
        },

        addRequest: function(options){
            var request = new AjaxQueue(options);

            $.ajaxQueue.queue.push(request);
            $.ajaxQueue.startNextRequest();
        },

        startNextRequest: function() {
            if ($.ajaxQueue.currentRequest) {
                return false;
            }

            var request = $.ajaxQueue.queue.shift();
            if (request) {
                $.ajaxQueue.currentRequest = request;
                request.perform();
            }
        }
    }
})(jQuery);
</script>
<script>
var cCompanyId;
var cCompanyName;

if(window._wInterval) {
	clearInterval(window._wInterval);
}

function tabReload(){
	if(location.hash != '#mgmt/water') {
		if(window._wInterval) {
			clearInterval(window._wInterval);
		}
	}
	$('#main-frame').load(baseUrl + 'mgmt/water/table_select?com_id=' + cCompanyId, function(){
		currentApp.waitingDialog.hide();
	});
}


$('.c-box a').on('click', function(){
	cCompanyId = $(this).data('id');
	cCompanyName = $(this).text();
	currentApp.waitingDialog.show('連接 ' + cCompanyName + ' 即時開獎資料匯入');
	setTimeout(function(){
		window._wInterval = setInterval(function(){
			tabReload();
		}, 2000);
	}, 8000);

});


</script>
