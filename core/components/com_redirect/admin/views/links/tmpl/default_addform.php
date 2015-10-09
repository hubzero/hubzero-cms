<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

// No direct access.
defined('_HZEXEC_') or die();
?>

	<fieldset class="batch">
		<legend><?php echo Lang::txt('COM_REDIRECT_HEADING_UPDATE_LINKS'); ?></legend>

		<div class="grid">
			<div class="col span8">
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_REDIRECT_FIELD_NEW_URL_DESC'); ?>">
					<label for="new_url"><?php echo Lang::txt('COM_REDIRECT_FIELD_NEW_URL_LABEL'); ?></label>
					<input type="text" name="new_url" id="new_url" value="" size="50" title="<?php echo Lang::txt('COM_REDIRECT_FIELD_NEW_URL_DESC'); ?>" />
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_REDIRECT_FIELD_COMMENT_DESC'); ?>">
					<label for="comment"><?php echo Lang::txt('COM_REDIRECT_FIELD_COMMENT_LABEL'); ?></label>
					<input type="text" name="comment" id="comment" value="" size="50" title="<?php echo Lang::txt('COM_REDIRECT_FIELD_COMMENT_DESC'); ?>" />
				</div>
			</div>
			<div class="col span4">
				<div class="input-wrap">
					<button type="button" onclick="this.form.task.value='activate';this.form.submit();"><?php echo Lang::txt('COM_REDIRECT_BUTTON_UPDATE_LINKS'); ?></button>
				</div>
			</div>
		</div>
	</fieldset>
