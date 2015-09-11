<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$roles = $this->course->offering(0)->roles(array('alias' => '!student'));
$offerings = $this->course->offerings();
?>
<?php if ($this->getError()) { ?>
	<dl id="system-message">
		<dt><?php echo Lang::txt('COM_COURSES_ERROR'); ?></dt>
		<dd class="error"><?php echo implode('<br />', $this->getErrors()); ?></dd>
	</dl>
<?php } ?>
<div id="course-managers">
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" class="course-managers-form" id="hubForm">
		<fieldset>
			<div class="grid">
				<div class="col span7">
					<label for="field-usernames">
						<?php echo Lang::txt('COM_COURSES_ADD_MANAGER_LABEL'); ?>
						<?php
						$mc = Event::trigger('hubzero.onGetMultiEntry', array(array('members', 'usernames', 'field-usernames', '', '')));
						if (count($mc) > 0) {
							echo $mc[0];
						} else { ?>
							<input type="text" name="usernames" id="field-usernames" value="" />
						<?php } ?>
					</label>
				</div>
				<div class="col span4">
					<label for="field-role">
						<?php echo Lang::txt('Select role'); ?>
						<select name="role" id="field-role">
						<?php foreach ($roles as $role) { ?>
							<option value="<?php echo $role->id; ?>"><?php echo $this->escape(stripslashes($role->title)); ?></option>
						<?php } ?>
						<?php
						foreach ($offerings as $offering)
						{
							$oroles = $offering->roles(array('offering_id' => $offering->get('id')));
							if (!$oroles || !count($oroles))
							{
								continue;
							}
						?>
							<optgroup label="<?php echo Lang::txt('COM_COURSES_OFFERING') . ': ' . $this->escape($offering->get('title')); ?>">
							<?php foreach ($oroles as $role) { ?>
								<option value="<?php echo $role->id; ?>"><?php echo $this->escape(stripslashes($role->title)); ?></option>
							<?php } ?>
							</optgroup>
						<?php } ?>
						</select>
					</label>
				</div>
				<div class="col span1 omega">
					<p class="submit">
						<input type="submit" value="<?php echo Lang::txt('COM_COURSES_ADD'); ?>" />
					</p>
				</div>
			</div>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="tmpl" value="component" />
			<input type="hidden" name="id" value="<?php echo $this->course->get('id'); ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
			<input type="hidden" name="task" value="add" />
		</fieldset>

		<?php echo Html::input('token'); ?>
	</form>

	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" class="course-managers-form" id="managersForm">
		<table class="paramlist admintable">
			<thead>
				<tr>
					<th>
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
						<input type="hidden" name="tmpl" value="component" />
						<input type="hidden" name="id" value="<?php echo $this->course->get('id'); ?>" />
						<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
						<input type="hidden" name="task" id="task" value="remove" />
						<input type="submit" name="action" value="<?php echo Lang::txt('COM_COURSES_REMOVE'); ?>" />
					</th>
					<th colspan="2">
						<span id="notifier"></span>
					</th>
				</tr>
			</thead>
			<tbody>
<?php
		$managers = $this->course->managers(array(), true);
		if (count($managers) > 0)
		{
			$i = 0;
			foreach ($managers as $manager)
			{
				$u = User::getInstance($manager->get('user_id'));
				if (!is_object($u))
				{
					continue;
				}
?>
				<tr>
					<td>
						<input type="hidden" name="entries[<?php echo $i; ?>][course_id]" value="<?php echo $manager->get('course_id'); ?>" />
						<input type="hidden" name="entries[<?php echo $i; ?>][offering_id]" value="<?php echo $manager->get('offering_id', 0); ?>" />
						<input type="hidden" name="entries[<?php echo $i; ?>][section_id]" value="<?php echo $manager->get('section_id', 0); ?>" />
						<input type="hidden" name="entries[<?php echo $i; ?>][user_id]" value="<?php echo $u->get('id'); ?>" />
						<input type="checkbox" name="entries[<?php echo $i; ?>][select]" value="<?php echo $u->get('id'); ?>" />
					</td>
					<td class="paramlist_key">
						<a href="<?php echo Route::url('index.php?option=com_members&id=' . $u->get('id')); ?>">
							<?php echo $u->get('name') ? $this->escape($u->get('name')) . ' (' . $this->escape($u->get('username')) . ')' : Lang::txt('COM_COURSES_UNKNOWN'); ?>
						</a>
					</td>
					<td>
						<select name="entries[<?php echo $i; ?>][role_id]">
							<?php foreach ($roles as $role) { ?>
								<option value="<?php echo $role->id; ?>"<?php if ($manager->get('role_id') == $role->id) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($role->title)); ?></option>
							<?php } ?>
							<?php
							foreach ($offerings as $offering)
							{
								$oroles = $offering->roles(array('offering_id' => $offering->get('id')));
								if (!$oroles || !count($oroles))
								{
									continue;
								}
								?>
								<optgroup label="<?php echo Lang::txt('COM_COURSES_OFFERING') . ': ' . $this->escape($offering->get('title')); ?>">
								<?php foreach ($oroles as $role) { ?>
									<option value="<?php echo $role->id; ?>"<?php if ($manager->get('role_id') == $role->id) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($role->title)); ?></option>
								<?php } ?>
								</optgroup>
							<?php } ?>
						</select>
					</td>
				</tr>
<?php
				$i++;
			}
		}
?>
			</tbody>
		</table>

		<?php echo Html::input('token'); ?>
	</form>
</div>