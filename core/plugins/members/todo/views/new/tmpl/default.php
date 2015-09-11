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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$url = 'index.php?option=com_members&id=' . $this->member->get('uidNumber') . '&active=todo';

?>

<div id="abox-content">
	<h3><?php echo Lang::txt('PLG_MEMBERS_TODO_ADD_TODO'); ?>
</h3>

<div class="pinboard">
	<form action="<?php echo Route::url($url . '&action=save'); ?>" method="post" id="plg-form" >
		<fieldset>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="uid" id="uid" value="<?php echo $this->member->get('uidNumber'); ?>" />
			<input type="hidden" name="active" value="todo" />
			<input type="hidden" name="action" value="save" />
			<?php echo Html::input('token'); ?>
		</fieldset>
		<section class="section intropage">
			<div id="td-item">
				<span class="pin">&nbsp;</span>
				<div class="todo-content">
					<textarea name="content" rows="10" cols="25" placeholder="<?php echo Lang::txt('PLG_MEMBERS_TODO_TYPEIT'); ?>"></textarea>
					<div class="todo-edits">
						<?php if (count($this->projects) > 0 ) { ?>
						<label><?php echo ucfirst(Lang::txt('PLG_MEMBERS_TODO_CHOOSE_PROJECT')); ?>:
							<select name="projectid">
							<?php foreach ($this->projects as $project) {
							?>
								<option value="<?php echo $project->get('id'); ?>"><?php echo stripslashes($project->get('title')) . '(' . $project->get('alias') . ')'; ?></option>
							<?php } ?>
							</select>
						</label>
						<?php } ?>

						<label><?php echo ucfirst(Lang::txt('PLG_MEMBERS_TODO_DUE')); ?>
							<input type="text" name="due" id="dued" class="duebox" placeholder="mm/dd/yyyy" value="" />
						</label>
						<p class="submitarea">
							<input type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_TODO_SAVE'); ?>" class="btn" />
						</p>
					</div>
				</div>
			</div>
		</section>
	</form>
</div>
</div>