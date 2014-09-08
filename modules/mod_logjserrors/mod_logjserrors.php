<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
 * All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Steve Snyder
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	// Write to hubzero logs if available.
	// Otherwise fallback to the Joomla logs.
	$path = JFactory::getConfig()->getValue('config.log_path');
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

		$.post('<?php echo rtrim(JURI::base(true), "/"); ?>/modules/mod_logjserrors/mod_logjserrors.php', {
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
