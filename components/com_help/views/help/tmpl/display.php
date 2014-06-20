<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<a name="help-top"></a>
<div class="help-header">
	<?php if ($this->page != 'index') : ?>
		<button class="back" onclick="window.history.back();" title="Go Back!">Go Back</button>
	<?php endif; ?>
</div>

<?php echo $this->content; ?>

<div class="help-footer">
	<a class="top" href="#help-top">Back to Top</a>
	<?php if ($this->page != 'index') : ?>
		<a class="index" href="<?php echo JRoute::_('index.php?option=com_help&component=' . str_replace('com_', '', $this->component) . '&page=index'); ?>">
			Index
		</a>
	<?php endif; ?>
	<p class="modified">
		Last Modified: <?php echo date('l, F d, Y @ g:ia', $this->modified); ?>
	</p>
</div>

<script>
var $ = (typeof(jq) !== "undefined" ? jq : jQuery);
$(document).ready(function(){
	var history = window.history;
	if (history.length > 1)
	{
		$('.back').show();
	}
});
</script>