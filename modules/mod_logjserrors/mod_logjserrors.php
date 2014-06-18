<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$log = array();
	array_map(function($k) use(&$log) {
		if (!array_key_exists($k, $_POST)) {
			header('HTTP/1.1 422 Unprocessable Entity');
			exit();
		}
		$log[$k] = $_POST[$k];
	}, array('message', 'file', 'line', 'url', 'navigator'));
	$fh = fopen('/var/log/hubzero/client_error.log', 'a');
	fwrite($fh, print_r($log, 1));
	fclose($fh);
	exit();
}
?>
<script type="text/javascript">
jQuery(function($) {
	var handlingError = false;
	window.onerror = function(msg, file, line) {
		if (handlingError) {
			return;
		}
		handlingError = true;
		try {
			msg = JSON.stringify(msg);
		}
		catch (ex) {
			// probably threw a recursive structure. oh well.
		}

		$.post('/modules/mod_logjserrors/mod_logjserrors.php', {
			'message': msg,
			'file': file,
			'line': line,
			'url': window.location.toString(),
			'navigator': JSON.stringify(podify(navigator))
		}).done(function() { handlingError = false; });
	};

	var podify = function(val) {
		var pod = {};
		for (var k in val) {
			switch (typeof val[k]) {
				case 'function': continue;
				case 'object':
					if (val[k] === null) {
						pod[k] = null;
					}
					else if (k == 'plugins') {
						var plg = [];
						for (var idx = 0; idx < val[k].length; ++idx) {
							plg.push(val[k][idx].name + ' ' + val[k][idx].description + (val[k][idx].version ? ' ' + val[k][idx].version : ''));
						}
						pod[k] = plg;
					}
				continue;
				default:
					pod[k] = val[k];
			}
		}
		return pod;
	};
});
</script>
