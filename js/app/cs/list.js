var CsAppClass = (function(app) {
	app.basePath = "mgmt/cs/";

	app.init = function() {
		return app;
	};

	app.reloadData = function() {
		$('#chat_list').load(baseUrl + app.basePath + 'chat_list/' + $('#login_user_id').val(), function (){

		});
	}

	app.doSend = function() {
		$.ajax({
			type: "POST",
			url: baseUrl + app.basePath + 'do_send',
			data: {
				send_user_id : $('#login_user_id').val(),
				user_id : $('#login_user_id').val(),
				msg: $('#send_message').val()
			},
			dataType: 'json',
			success: function(data)
			{
					if(data && data.last_id > 0) {
						$('#send_message').val('');
						app.reloadData();
					} else {
						alert('資料新增有誤');
					}
			}
		});
	}

	// do reload
	app.reloadData();

	// return self
	return app.init();
});
