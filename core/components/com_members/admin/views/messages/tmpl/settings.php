<?php
/**
 * @package     hubzero-cms
 * @author      Christopher Smoak <csmoak@purdue.edu>
 * @copyright   Copyright 2005-2015 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// No direct access
defined('_HZEXEC_') or die();

Lang::load('plg_members_messages', PATH_CORE . '/plugins/members/messages');
?>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_NO_COMPONENTS_FOUND'); ?></p>
<?php } else { ?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=savesettings'); ?>" method="post" name="adminForm" id="item-form">
		<table class="settings">
			<caption>
				<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MSG_SAVE_SETTINGS'); ?>" />
			</caption>
			<thead>
				<tr>
					<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_SENT_WHEN'); ?></th>
					<?php foreach ($this->notimethods as $notimethod) { ?>
						<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MSG_' . strtoupper($notimethod)); ?></th>
					<?php } ?>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="<?php echo (count($this->notimethods) + 1); ?>">
						<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MSG_SAVE_SETTINGS'); ?>" />
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$cls = 'even';

			$sheader = '';
			foreach ($this->components as $component)
			{
				if ($component->name != $sheader)
				{
					$sheader = $component->name;
					Lang::load($component->name, Component::path($component->name) . '/site');

					$display_header = Lang::hasKey($component->name) ? Lang::txt($component->name) : ucfirst(str_replace('com_', '', $component->name));
				?>
				<tr class="section-header">
					<th scope="col"><?php echo $this->escape($display_header); ?></th>
					<?php foreach ($this->notimethods as $notimethod) { ?>
						<th scope="col"><span class="<?php echo $notimethod; ?> iconed"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MSG_'.strtoupper($notimethod)); ?></span></th>
					<?php } ?>
				</tr>
				<?php
				}
				$cls = (($cls == 'even') ? 'odd' : 'even');
				?>
				<tr class="<?php echo $cls; ?>">
					<th scope="col"><?php echo $this->escape($component->title); ?></th>
					<?php echo \Components\Members\Admin\Controllers\Messages::selectMethod($this->notimethods, $component->action, $this->settings[$component->action]['methods'], $this->settings[$component->action]['ids']); ?>
				</tr>
			<?php
			}
			?>
			</tbody>
		</table>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="savesettings" />
		<input type="hidden" name="id" value="<?php echo $this->member->get('uidNumber'); ?>" />
		<input type="hidden" name="tmpl" value="<?php echo Request::getWord('tmpl'); ?>" />

		<?php echo Html::input('token'); ?>
	</form>
<?php } ?>