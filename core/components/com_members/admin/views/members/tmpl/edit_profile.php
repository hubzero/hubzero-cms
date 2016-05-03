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

/*
include_once Component::path('com_members') . DS . 'models' . DS . 'form' . DS . 'form.php';
include_once Component::path('com_members') . DS . 'models' . DS . 'profile' . DS . 'field.php';

$form = new Components\Members\Models\Form\Form('profile', array('control' => 'profile'));

$fields = Components\Members\Models\Profile\Field::all()
	->including(['options', function ($option){
		$option
			->select('*')
			->whereEquals('parent', 0);
	}])
	->whereEquals('parent', 0)
	->ordered()
	->rows();
*/
?>
<div class="grid">
	<div class="col span7">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_MEMBERS_DEMOGRAPHICS'); ?></span></legend>

			<?php /*foreach ($fields as $field): ?>
				<?php
				$input = $form->loadField($field, null, $this->profile->get($field->get('name')));
				if ($input)
				{
					echo '<div class="input-wrap"' . ($input->description ? ' data-hint="' . $this->escape($input->description) . '"' : '') . '>';
					echo $input->label;
					echo $input->input;
					echo '</div>';
				}
				else
				{
					?>
					<div class="input-wrap"<?php if ($field->get('description')) { echo ' data-hint="' . $this->escape($field->get('description')) . '"'; } ?>>
						<label for="profile-<?php echo $field->get('name'); ?>"><?php echo $field->get('label'); ?></label>
						<input type="text" name="profile[<?php echo $field->get('name'); ?>]" id="profile-<?php echo $field->get('name'); ?>" value="<?php echo $this->profile->get($field->get('name')); ?>" />
					</div>
					<?php
				}
				?>
			<?php endforeach;*/ ?>
			<p>Profile info.</p>
		</fieldset>
	</div>
	<div class="col span5">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_MEMBERS_MEDIA_PICTURE'); ?></span></legend>

			<?php if ($this->profile->get('id')): ?>
				<iframe height="420" name="filer" id="filer" src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=media&tmpl=component&id=' . $this->profile->get('id') . '&t=' . time()); ?>"></iframe>
			<?php else: ?>
				<p class="warning"><?php echo Lang::txt('COM_MEMBERS_PICTURE_ADDED_LATER'); ?></p>
			<?php endif; ?>
		</fieldset>
	</div>
</div>
