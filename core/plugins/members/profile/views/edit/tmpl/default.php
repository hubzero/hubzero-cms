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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if ($this->isUser) : ?>
	<div class="section-edit-container">
		<?php if ($this->registration == REG_READONLY) : ?>
			<p class="notice warning"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_READONLY', $this->title); ?></p>
		<?php else : ?>
			<div class="section-edit-content">
				<form action="<?php echo Route::url('index.php?option=com_members'); ?>" method="post" data-section-registation="<?php echo $this->registration_field; ?>" data-section-profile="<?php echo $this->profile_field; ?>">
					<span class="section-edit-errors"></span>

					<?php echo $this->inputs; ?>
					<?php echo $this->access; ?>

					<input type="submit" class="section-edit-submit" value="<?php echo Lang::txt('PLG_MEMBERS_PROFILE_SAVE'); ?>" />
					<input type="reset" class="section-edit-cancel" value="<?php echo Lang::txt('PLG_MEMBERS_PROFILE_CANCEL'); ?>" />
					<input type="hidden" name="field_to_check[]" value="<?php echo $this->registration_field; ?>" />
					<input type="hidden" name="option" value="com_members" />
					<input type="hidden" name="id" value="<?php echo $this->profile->get("uidNumber"); ?>" />
					<input type="hidden" name="task" value="save" />
					<input type="hidden" name="no_html" value="1" />
					<?php echo Html::input('token'); ?>
				</form>
			</div>
		<?php endif; ?>
	</div>
<?php endif;?>