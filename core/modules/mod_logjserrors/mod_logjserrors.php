<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Steve Snyder
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

if (Request::method() == 'POST')
{
	// Write to hubzero logs if available.
	// Otherwise fallback to the app logs.
	$path = Config::get('log_path');
	if (is_dir('/var/log/hubzero'))
	{
		$path = '/var/log/hubzero';
	}

	$log = array();
	array_map(function($k) use(&$log)
	{
		if (!array_key_exists($k, $_POST))
		{
			header('HTTP/1.1 422 Unprocessable Entity');
			exit();
		}
		$log[$k] = $_POST[$k];
	}, array('message', 'file', 'line', 'url', 'navigator'));

	$fh = fopen($path . '/client_error.log', 'a');
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
