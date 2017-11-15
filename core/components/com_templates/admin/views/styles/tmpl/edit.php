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

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Templates\Helpers\Utilities::getActions();
$isNew = $this->item->isNew();

Toolbar::title(
	$isNew ? Lang::txt('COM_TEMPLATES_MANAGER_ADD_STYLE')
			: Lang::txt('COM_TEMPLATES_MANAGER_EDIT_STYLE'),
	'thememanager'
);

// If not checked out, can save the item.
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}

// If an existing item, can save to a copy.
if (!$isNew && $canDo->get('core.create'))
{
	Toolbar::save2copy();
}

Toolbar::cancel();
Toolbar::divider();
Toolbar::help('style');

//Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');
Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'style.cancel' || document.formvalidator.isValid($('#item-form'))) {
			Joomla.submitform(task, $('#item-form'));
		}
	}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS');?></span></legend>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_TEMPLATES_FIELD_TITLE_LABEL'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[title]" id="field-title" maxlength="250" value="<?php echo $this->escape(stripslashes($this->item->get('title'))); ?>" />
				</div>

				<?php if ($this->item->client_id == 0): ?>
					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_TEMPLATES_FIELD_HOME_SITE_DESC'); ?>">
						<label for="field-home"><?php echo Lang::txt('COM_TEMPLATES_FIELD_HOME_LABEL'); ?>:</label><br />
						<select name="fields[home]" id="field-home">
							<option value="0"<?php if ($this->item->home == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JNO'); ?></option>
							<option value="1"<?php if ($this->item->home == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JALL'); ?></option>
						</select>
						<span class="hint"><?php echo Lang::txt('COM_TEMPLATES_FIELD_HOME_SITE_DESC'); ?></span>
					</div>
				<?php else: ?>
					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_TEMPLATES_FIELD_HOME_SITE_DESC'); ?>">
						<label id="fields_home-lbl" for="fields_home"><?php echo Lang::txt('COM_TEMPLATES_FIELD_HOME_LABEL'); ?>:</label>
						<fieldset id="fields_home" class="radio inputbox">
							<ul>
								<li>
									<input type="radio" id="fields_home0" name="fields[home]" value="0" <?php if ($this->item->home == 0) { echo ' checked="checked"'; } ?> />
									<label for="fields_home0"><?php echo Lang::txt('JNO'); ?></label>
								</li>
								<li>
									<input type="radio" id="fields_home1" name="fields[home]" value="1" <?php if ($this->item->home == 1) { echo ' checked="checked"'; } ?> />
									<label for="fields_home1"><?php echo Lang::txt('JYES'); ?></label>
								</li>
							</ul>
						</fieldset>
					</div>
				<?php endif; ?>
			</fieldset>

			<?php if (User::authorise('core.edit', 'com_menu') && $this->item->client_id==0):?>
				<?php if ($canDo->get('core.edit.state')) : ?>
					<?php echo $this->loadTemplate('assignment'); ?>
				<?php endif; ?>
			<?php endif;?>

			<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->escape($this->item->id); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="" />
			<?php echo Html::input('token'); ?>
		</div>

		<div class="col span5">
			<table class="meta">
				<tbody>
					<?php if ($this->item->id) : ?>
						<tr>
							<th><?php echo Lang::txt('JGLOBAL_FIELD_ID_LABEL'); ?></th>
							<td><?php echo $this->item->id; ?></td>
						</tr>
					<?php endif; ?>
					<?php if ($this->item->parent->xml) : ?>
						<?php if ($text = trim($this->item->parent->xml->get('description'))) : ?>
							<tr>
								<th><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_DESCRIPTION'); ?></th>
								<td><?php echo Lang::txt($text); ?></td>
							</tr>
						<?php endif; ?>
					<?php else : ?>
						<tr>
							<td colspan="2">
								<p class="error"><?php echo Lang::txt('COM_TEMPLATES_ERR_XML'); ?></p>
							</td>
						</tr>
					<?php endif; ?>
					<tr>
						<th><?php echo Lang::txt('COM_TEMPLATES_FIELD_TEMPLATE_LABEL'); ?></th>
						<td>
							<?php echo $this->item->template; ?>
							<input type="hidden" name="fields[template]" id="field-template" value="<?php echo $this->escape($this->item->template); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_TEMPLATES_FIELD_CLIENT_LABEL'); ?></th>
						<td>
							<?php echo $this->item->client_id == 0 ? Lang::txt('JSITE') : Lang::txt('JADMINISTRATOR'); ?>
							<input type="hidden" name="fields[client_id]" id="field-client_id" value="<?php echo $this->escape($this->item->client_id); ?>" />
						</td>
					</tr>
				</tbody>
			</table>

			<?php echo Html::sliders('start', 'template-sliders-' . $this->item->id); ?>

			<?php
				//get the menu parameters that are automatically set but may be modified.
				echo $this->loadTemplate('options');
			?>
			<div class="clr"></div>

			<?php echo Html::sliders('end'); ?>
		</div>
	</div>
</form>
