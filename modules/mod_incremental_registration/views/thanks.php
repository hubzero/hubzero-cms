<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<div id="overlay"></div>
<div id="questions">
	<p>
		<?php echo Lang::txt('MOD_INCREMENTAL_REGISTRATION_THANK_YOU'); ?>
		<?php if ($award): ?>
			<?php echo Lang::txt('MOD_INCREMENTAL_REGISTRATION_AWARDS_EARNED', $award); ?>
		<?php endif; ?>
		<?php echo Lang::txt('MOD_INCREMENTAL_REGISTRATION_REDIRECTED_SOON'); ?>
	</p>

	<a href="<?php echo Request::getVar('REQUEST_URI', Request::getVar('REDIRECT_REQUEST_URI', '', 'server'), 'server'); ?>"><?php echo Lang::txt('MOD_INCREMENTAL_REGISTRATION_CLICK_IF_NOT_REDIRECTED'); ?></a>

	<script type="text/javascript">
		setTimeout(function() {
			var divs = ['overlay', 'questions'];
			for (var idx = 0; idx < divs.length; ++idx) {
				var div = document.getElementById(divs[idx]);
				div.parentNode.removeChild(div);
			}
		}, 4000);
	</script>
</div>
