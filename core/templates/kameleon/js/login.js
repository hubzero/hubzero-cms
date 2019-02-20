jQuery(document).ready(function($){
	(function worker() {
		$.ajax({
			url: 'index.php',
			complete: function() {
				setTimeout(worker, 3540000);
			}
		});
	})();

	if (document.getElementById('form-login')) {
		document.getElementById('form-login').username.select();
		document.getElementById('form-login').username.focus();
	}
});
