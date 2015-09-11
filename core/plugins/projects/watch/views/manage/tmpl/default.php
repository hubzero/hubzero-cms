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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

?>

<div id="abox-content">
	<h3><?php echo $this->watch ? Lang::txt('PLG_PROJECTS_WATCH_MANAGE') : Lang::txt('PLG_PROJECTS_WATCH_SUBSCRIBE'); ?></h3>
	<form id="hubForm-ajax" method="post" action="<?php echo Route::url($this->project->link() . '&active=watch&action=save'); ?>">
		<fieldset >
			<input type="hidden" name="id" value="<?php echo $this->project->get('id'); ?>" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="task" value="view" />
			<input type="hidden" name="active" value="watch" />
			<input type="hidden" name="ajax" value="1" />
			<input type="hidden" name="option" value="com_projects" />
			<h5><?php echo Lang::txt('PLG_PROJECTS_WATCH_SUBSCRIBE_CATEGORIES'); ?>:</h5>

			<ul class="cat-list">
			<?php foreach ($this->cats as $name => $checked) { ?>
				<li>
					<input type="checkbox" name="category[<?php echo $name; ?>]" value="1" <?php if ($checked == 1) { echo 'checked="checked"'; } ?> /> <span class="cat-icon"><span class="<?php echo $name; ?>"></span></span> <span><?php echo Lang::txt('PLG_PROJECTS_WATCH_' . strtoupper($name)); ?></span>
				</li>
			<?php  } ?>
			</ul>
			<div class="delivery">
				<p><?php echo Lang::txt('PLG_PROJECTS_WATCH_UPDATES_DELIVERED_TO_EMAIL', User::get('email')); ?></p>
			<h5><?php echo Lang::txt('PLG_PROJECTS_WATCH_UPDATES_FREQUENCY'); ?>:</h5>
			<ul class="cat-list">
				<li>
					<input type="radio" name="frequency" value="immediate" checked="checked" /> <span><?php echo Lang::txt('PLG_PROJECTS_WATCH_FREQUENCY_IMMEDIATE'); ?></span>
				</li>
			</ul>
			</div>

			<p class="submitarea">
				<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PROJECTS_WATCH_SAVE'); ?>" />
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('PLG_PROJECTS_WATCH_CANCEL'); ?>" />
			</p>
			<?php echo Html::input('token'); ?>
		</fieldset>
	</form>
</div>