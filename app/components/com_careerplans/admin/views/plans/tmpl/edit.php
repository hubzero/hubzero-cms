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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Careerplans\Helpers\Permissions::getActions();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_CAREERPLANS') . ': ' . $text, 'careerplan');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('entry');

Html::behavior('switcher', 'submenu');

$this->css();
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" class="editform" id="item-form">
	<div class="grid">
		<div class="col span8">
			<nav role="navigation" class="sub-navigation">
				<div id="submenu-box">
					<div class="submenu-box">
						<div class="submenu-pad">
							<ul id="submenu" class="member-nav">
								<?php
								$i = 0;
								foreach ($this->fieldsets as $fieldset)
								{
									?>
									<li><a href="#page-<?php echo $fieldset->get('name'); ?>" onclick="return false;" id="<?php echo $fieldset->get('name'); ?>" <?php if ($i == 0) { echo ' class="active"'; } ?>><?php echo $fieldset->get('label'); ?></a></li>
									<?php
									$i++;
								}
								?>
							</ul>
							<div class="clr"></div>
						</div>
					</div>
					<div class="clr"></div>
				</div>
			</nav><!-- / .sub-navigation -->

			<div id="member-document">
				<?php
				$i = 0;

				$answers = $this->careerplan
					->answers()
					->ordered()
					->rows();

				// Gather data to pass to the form processor
				$data = new Hubzero\Config\Registry(
					Components\Careerplans\Models\Careerplan::collect($answers)
				);

				foreach ($this->fieldsets as $fieldset)
				{
					$fields = $fieldset->fields()
						->including(['options', function ($option){
							$option
								->select('*');
						}])
						->ordered()
						->rows();

					// Convert to XML so we can use the Form processor
					$xml = Components\Careerplans\Models\Field::toXml($fields);

					// Create a new form
					Hubzero\Form\Form::addFieldPath(Component::path('com_careerplans') . DS . 'models' . DS . 'fields');

					$form = new Hubzero\Form\Form('careerplan', array('control' => 'questions'));
					$form->load($xml);
					$form->bind($data);

					$fields = $form->getFieldset('basic');
					?>
					<div id="page-<?php echo $fieldset->get('name'); ?>" class="tab">
						<fieldset class="adminform">
							<legend><span><?php echo $fieldset->get('label'); ?></span></legend>

							<?php foreach ($fields as $field): ?>
								<?php
								echo '<div class="input-wrap" id="input-' . $field->fieldname . '" ' . ($field->description ? ' data-hint="' . $this->escape($field->description) . '"' : '') . '>';
								if ($field->hidden)
								{
									echo '<label for="profile_' . $field->fieldname . '">' . $field->fieldname . '</label>';
									echo '<input type="text" name="' . $field->name . '" id="profile_' . $field->fieldname . '" value="' . $this->escape($field->value) . '" />';
								}
								else
								{
									echo $field->label;
									echo $field->input;
									if ($field->description)
									{
										echo '<span class="hint">' . $field->description . '</span>';
									}
								}
								echo '</div>';
								?>
							<?php endforeach; ?>
						</fieldset>
					</div>
					<?php
					$i++;
				}
				?>
			</div>
		</div>
		<div class="col span4">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_CAREERPLANS_FIELD_CREATED_BY'); ?></th>
						<td>
							<?php echo $this->careerplan->creator->get('name'); ?>
							<input type="hidden" name="fields[created_by]" value="<?php echo $this->careerplan->get('created_by'); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_CAREERPLANS_FIELD_CREATED'); ?></th>
						<td><?php echo $this->careerplan->get('created'); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_CAREERPLANS_FIELD_MODIFIED_BY'); ?></th>
						<td><?php echo (!$this->careerplan->get('modified_by') ? Lang::txt('COM_CAREERPLANS_NA') : $this->careerplan->modifier->get('name')); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_CAREERPLANS_FIELD_MODIFIED'); ?></th>
						<td><?php echo (!$this->careerplan->get('modified') ? Lang::txt('COM_CAREERPLANS_NA') : $this->careerplan->get('modified')); ?></td>
					</tr>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_CAREERPLANS_STATUS'); ?></span></legend>

				<div class="input-wrap">
					<label id="field_state-lbl" for="field_state"><?php echo Lang::txt('COM_CAREERPLANS_STATE'); ?></label>
					<select id="field_state" name="careerplan[state]">
						<option value="0"<?php if ($this->careerplan->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CAREERPLANS_STATE_DRAFT'); ?></option>
						<option value="1"<?php if ($this->careerplan->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CAREERPLANS_STATE_SUBMITTED'); ?></option>
						<option value="2"<?php if ($this->careerplan->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CAREERPLANS_STATE_ACCEPTED'); ?></option>
					</select>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<input type="hidden" name="id" value="<?php echo $this->careerplan->get('id'); ?>" />
	<input type="hidden" name="careerplan[id]" value="<?php echo $this->careerplan->get('id'); ?>" />

	<?php echo Html::input('token'); ?>
</form>