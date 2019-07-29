var RacingConfigAppClass = (function(app) {
	app.basePath = "mgmt/racing_config/";

	app.init = function() {


		app.doSubmit = function() {
			$('#app-edit-form').submit();
		};

		return app;
	};

	// return self
	return app.init();
});
