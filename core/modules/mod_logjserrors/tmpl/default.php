<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

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

		$.post('<?php echo rtrim(Request::base(true), "/"); ?>/core/modules/mod_logjserrors/mod_logjserrors.php', {
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