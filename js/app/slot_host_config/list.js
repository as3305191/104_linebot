var SlotHostConfigAppClass = (function(app) {
	app.basePath = "mgmt/slot_host_config/";

	app.init = function() {


		app.doSubmit = function() {
			$('#app-edit-form').submit();
		};

		return app;
	};

	// return self
	return app.init();
});
