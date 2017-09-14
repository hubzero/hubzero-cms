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

Request::setVar('hidemainmenu', 1);

Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');

Toolbar::title($this->title, 'content');
if ($this->canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::save2copy();
	Toolbar::save2new();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('category');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'category.cancel' || document.formvalidator.isValid($('#item-form'))) {
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		} else {
			alert('<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo Route::url('index.php?option=com_categories&extension='.Request::getCmd('extension', 'com_content').'&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_CATEGORIES_FIELDSET_DETAILS');?></span></legend>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('title'); ?>
					<?php echo $this->form->getInput('title'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('alias'); ?>
					<?php echo $this->form->getInput('alias'); ?>
				</div>

				<?php echo $this->form->getInput('extension'); ?>
				<?php
				/*<div class="input-wrap">
					<?php echo $this->form->getLabel('extension'); ?>
					<?php echo $this->form->getInput('extension'); ?>
				</div>*/
				?>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('parent_id'); ?>
					<select name="fields[parent_id]" class="inputbox">
						<option value="1"> - No parent -</option>
						<?php foreach ($this->item->parents() as $parent): ?>
							<?php $selected = $this->item->get('parent_id') == $parent->get('id') ? 'selected="selected"' : ''; ?>
							<option value="<?php echo $parent->get('id'); ?>" <?php echo $selected; ?>>
								<?php echo $parent->nestedTitle(); ?>
							</option>
						<?php endforeach; ?>`
					</select>
				</div>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap">
							<?php echo $this->form->getLabel('published'); ?>
							<?php if ($this->canDo->get('core.edit.state')): ?>
								<?php echo $this->form->getInput('published'); ?>
							<?php else: ?>
							<select name="fields[published]" disabled="disabled">
								<option value="<?php echo $this->item->get('published');?>">
									<?php echo $this->item->published; ?>
								</option>
							</select>
							<?php endif; ?>
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap">
							<?php echo $this->form->getLabel('access'); ?>
							<?php echo $this->form->getInput('access'); ?>
						</div>
					</div>
				</div>

					<?php /*if ($this->canDo->get('core.admin')): ?>
						<div class="input-wrap">
							<span class="faux-label"><?php echo Lang::txt('JGLOBAL_ACTION_PERMISSIONS_LABEL'); ?></span>
							<div class="button2-left">
								<div class="blank">
									<button type="button" onclick="document.location.href='#access-rules';">
										<?php echo Lang::txt('JGLOBAL_PERMISSIONS_ANCHOR'); ?>
									</button>
								</div>
							</div>
						</div>
					<?php endif;*/ ?>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('language'); ?>
					<?php echo $this->form->getInput('language'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('description'); ?>
					<?php echo $this->form->getInput('description'); ?>
				</div>
			</fieldset>
		</div>

		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<td><?php echo Lang::txt('COM_CATEGORY_FIELD_ID'); ?></td>
						<td>
							<?php echo $this->item->get('id', 0); ?>
							<input type="hidden" name="fields[id]" value="<?php echo $this->item->get('id', 0); ?>" />
						</td>
					</tr>
					<tr>
						<td><?php echo Lang::txt('COM_CATEGORY_FIELD_CREATOR'); ?></td>
						<td>
							<?php echo User::getInstance($this->item->get('created_user_id'))->get('name'); ?>
							<input type="hidden" name="fields[created_user_id]" value="<?php echo $this->item->createdUserId; ?>" />
						</td>
					</tr>
					<tr>
						<td><?php echo Lang::txt('COM_CATEGORY_FIELD_CREATED'); ?></td>
						<td>
							<?php echo $this->item->get('created_time'); ?>
						</td>
					</tr>
					<?php if ($this->item->get('modified_time', false)): ?>
						<tr>
							<td><?php echo Lang::txt('COM_CATEGORY_FIELD_MODIFIER'); ?></td>
							<td>
								<?php echo User::getInstance($this->item->get('modified_user_id'))->get('name'); ?>
								<input type="hidden" name="fields[modified_user_id]" value="<?php echo $this->item->modifiedUserId;?>" />
							</td>
						</tr>
						<tr>
							<td><?php echo Lang::txt('COM_CATEGORY_FIELD_MODIFIED');?></td>
							<td>
								<?php echo $this->item->get('modified_time'); ?>
							</td>
						</tr>
                    <?php endif; ?>
				</tbody>
			</table>
			<?php echo Html::sliders('start', 'categories-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
				<?php echo $this->loadTemplate('options'); ?>
				<div class="clr"></div>

				<?php echo Html::sliders('panel', Lang::txt('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'meta-options'); ?>
				<fieldset class="panelform">
					<?php echo $this->loadTemplate('metadata'); ?>
				</fieldset>

				<?php $fieldSets = $this->form->getFieldsets('attribs'); ?>
				<?php foreach ($fieldSets as $name => $fieldSet) : ?>
					<?php $label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_CATEGORIES_'.$name.'_FIELDSET_LABEL'; ?>
					<?php if ($name != 'editorConfig' && $name != 'basic-limited') : ?>
						<?php echo Html::sliders('panel', Lang::txt($label), $name.'-options'); ?>
						<?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
							<p class="tip"><?php echo $this->escape(Lang::txt($fieldSet->description));?></p>
						<?php endif; ?>
						<fieldset class="panelform">
							<?php foreach ($this->form->getFieldset($name) as $field) : ?>
								<div class="input-wrap">
									<?php echo $field->label; ?>
									<?php echo $field->input; ?>
								</div>
							<?php endforeach; ?>
						</fieldset>
					<?php endif ?>
				<?php endforeach; ?>
			<?php echo Html::sliders('end'); ?>
		</div>
	</div>

	<?php if ($this->canDo->get('core.admin')): ?>
		<div class="width-100">

			<?php //echo Html::sliders('start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

			<?php //echo Html::sliders('panel', Lang::txt('COM_CATEGORIES_FIELDSET_RULES'), 'access-rules'); ?>
			<fieldset class="panelform">

				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>

			<?php //echo Html::sliders('end'); ?>
		</div>
	<?php endif; ?>

	<input type="hidden" name="task" value="" />
	<?php echo Html::input('token'); ?>
</form>
