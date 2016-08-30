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

$canDo = \Components\Groups\Helpers\Permissions::getActions('group');

Toolbar::title($this->group->get('description') . ': ' . Lang::txt('COM_GROUPS_PAGES_MODULES'), 'groups.png');

if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	submitform(pressbutton);
}
</script>

<?php require_once dirname(dirname(__DIR__)) . DS . 'pages' . DS . 'tmpl' . DS . 'menu.php'; ?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->group->cn); ?>" name="adminForm" id="item-form" method="post">
	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_GROUPS_MODULES_DETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_GROUPS_MODULES_TITLE'); ?>:</label>
					<input type="text" name="module[title]" id="field-title" value="<?php echo $this->escape($this->module->get('title')); ?>" size="50" />
				</div>
				<div class="input-wrap">
					<label for="field-position"><?php echo Lang::txt('COM_GROUPS_MODULES_POSITION'); ?>:</label>
					<input type="text" name="module[position]" id="field-position" value="<?php echo $this->escape($this->module->get('position')); ?>" size="50" />
				</div>
				<div class="input-wrap">
					<label for="field-type"><?php echo Lang::txt('COM_GROUPS_MODULES_STATUS'); ?>:</label>
					<select name="module[state]" id="field-type">
						<?php
						$states = array(
							1 => Lang::txt('COM_GROUPS_MODULES_STATUS_PUBLISHED'),
							0 => Lang::txt('COM_GROUPS_MODULES_STATUS_UNPUBLISHED'),
							2 => Lang::txt('COM_GROUPS_MODULES_STATUS_DELETED')
						);

						foreach ($states as $k => $v)
						{
							$sel = ($this->module->get('state') == $k) ? 'selected="selected"' : '';
							echo '<option '.$sel.' value="'.$k.'">'.$v.'</option>';
						}
						?>
					</select>
				</div>
				<div class="input-wrap">
					<label for="field-ordering"><?php echo Lang::txt('COM_GROUPS_MODULES_ORDERING'); ?>:</label>
					<select name="module[ordering]" id="field-ordering">
						<?php foreach ($this->order as $k => $order) : ?>
							<?php $sel = ($order->get('title') == $this->module->get('title')) ? 'selected="selected"' : ''; ?>
							<option <?php echo $sel ;?> value="<?php echo ($k + 1); ?>"><?php echo ($k + 1) . '. ' . $order->get('title'); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</fieldset>
			<?php
				// get module menus
				$menus = $this->module->menu('list');
				$activeMenu = (!$this->module->get('id')) ? array(0) : array();
				foreach ($menus as $menu)
				{
					$activeMenu[] = $menu->get('pageid');
				}
			?>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_GROUPS_MODULES_MENU_ASSIGNMENT'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-assignment"><?php echo Lang::txt('COM_GROUPS_MODULES_MODULE_ASSIGNMENT'); ?>:</label>
					<select name="menu[assignment]" id="field-assignment">
						<option value="0"><?php echo Lang::txt('COM_GROUPS_MODULES_MODULE_ASSIGNMENT_ALL'); ?></option>
						<option <?php if (!in_array(0, $activeMenu)) { echo 'selected="selected"'; } ?> value=""><?php echo Lang::txt('COM_GROUPS_MODULES_MODULE_ASSIGNMENT_SELECTED'); ?></option>
					</select>
				</div>

				<fieldset class="adminform">
					<legend><?php echo Lang::txt('COM_GROUPS_MODULES_MENU_SELECTION'); ?></legend>

					<?php foreach ($this->pages as $i => $page) : ?>
						<div class="input-wrap">
							<label for="assigned<?php echo $i; ?>">
								<?php $ckd = (in_array($page->get('id'), $activeMenu) || in_array(0, $activeMenu)) ? 'checked="checked"' : ''; ?>
								<input type="checkbox" class="option" <?php echo $ckd; ?> name="menu[assigned][]" id="assigned<?php echo $i; ?>" value="<?php echo $page->get('id'); ?>" /> <?php echo $page->get('title'); ?>
							</label>
						</div>
					<?php endforeach; ?>
				</fieldset>
			</fieldset>
		</div>
		<div class="col span6">
			<?php if ($this->module->get('id')) : ?>
				<table class="meta">
					<tbody>
						<tr>
							<th><?php echo Lang::txt('COM_GROUPS_MODULES_OWNER'); ?></th>
							<td><?php echo $this->group->get('description'); ?></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_GROUPS_MODULES_ID'); ?></th>
							<td><?php echo $this->module->get('id'); ?></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_GROUPS_MODULES_CREATED'); ?></th>
							<td><?php echo Date::of($this->module->get('created'))->toLocal('F j, Y @ g:ia'); ?></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_GROUPS_MODULES_CREATED_BY'); ?></th>
							<td>
								<?php
									$profile = User::getInstance($this->module->get('created_by'));
									echo (is_object($profile)) ? $profile->get('name') . ' (' . $profile->get('id') . ')' : Lang::txt('COM_GROUPS_PAGES_SYSTEM');
								?>
							</td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_GROUPS_MODULES_LAST_MODIFIED'); ?></th>
							<td>
								<?php
									$modified = '--';
									if ($this->module->get('modified_by') != null)
									{
										$modified = Date::of($this->module->get('modified'))->toLocal('F j, Y @ g:ia');
									}
									echo $modified;
								?>
							</td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_GROUPS_MODULES_LAST_MODIFIED_BY'); ?></th>
							<td>
								<?php
									$modified_by = '--';
									if ($this->module->get('modified_by') != null)
									{
										$profile = User::getInstance($this->module->get('modified_by'));
										$modified_by = (is_object($profile)) ? $profile->get('name') . ' (' . $profile->get('id') . ')' : Lang::txt('COM_GROUPS_PAGES_SYSTEM');
									}
									echo $modified_by;
								?>
							</td>
						</tr>
					</tbody>
				</table>
			<?php endif; ?>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_GROUPS_MODULES_MODULE_CONTENT'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-content"><?php echo Lang::txt('COM_GROUPS_MODULES_CONTENT'); ?>:</label>
					<textarea name="module[content]" id="field-content" rows="20"><?php echo $this->module->get('content'); ?></textarea>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="module[id]" value="<?php echo $this->module->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo Html::input('token'); ?>
</form>