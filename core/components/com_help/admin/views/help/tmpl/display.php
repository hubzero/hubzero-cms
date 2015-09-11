<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<div class="help-header" id="help-top">
	<?php if ($this->page != 'index') : ?>
		<button id="back" class="back" onclick="window.history.back();" title="<?php echo Lang::txt('COM_HELP_GO_BACK'); ?>"><?php echo Lang::txt('COM_HELP_GO_BACK'); ?></button>
	<?php endif; ?>
</div>

<?php echo $this->content; ?>

<div class="help-footer">
	<a class="top" href="#help-top"><?php echo Lang::txt('COM_HELP_BACK_TO_TOP'); ?></a>
	<?php if ($this->page != 'index') : ?>
		<a class="index" href="<?php echo Route::url('index.php?option=com_help&component=' . $this->component . '&page=index'); ?>">
			<?php echo Lang::txt('COM_HELP_INDEX'); ?>
		</a>
	<?php endif; ?>
	<p class="modified">
		<?php echo Lang::txt('COM_HELP_LAST_MODIFIED', date('l, F d, Y @ g:ia', $this->modified)); ?>
	</p>
</div>

<script type="text/javascript">
window.onload = function() {
	var history = window.history,
		back    = document.getElementById('back');

	if (history.length > 1 && back !== null) {
		back.style.display = "block";
	}
};
</script>