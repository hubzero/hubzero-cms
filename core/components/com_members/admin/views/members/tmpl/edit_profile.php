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

include_once Component::path('com_members') . DS . 'models' . DS . 'profile' . DS . 'field.php';

$fields = Components\Members\Models\Profile\Field::all()
	->including(['options', function ($option){
		$option
			->select('*');
	}])
	->ordered()
	->rows();

// Convert to XML so we can use the Form processor
$xml = Components\Members\Models\Profile\Field::toXml($fields);

// Gather data to pass to the form processor
$data = new Hubzero\Config\Registry(
	Components\Members\Models\Profile::collect($this->profile->profiles()->ordered()->rows())
);
$data->set('tags', $this->profile->tags('string'));

// Create a new form
Hubzero\Form\Form::addFieldPath(Component::path('com_members') . DS . 'models' . DS . 'fields');

$form = new Hubzero\Form\Form('profile', array('control' => 'profile'));
$form->load($xml);
$form->bind($data);

$fields = $form->getFieldset('basic');
?>
<div class="grid">
	<div class="col span7">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_MEMBERS_PROFILE'); ?></span></legend>

			<?php foreach ($fields as $field): ?>
				<?php
				echo '<div class="input-wrap" id="input-' . $field->fieldname . '" ' . ($field->description ? ' data-hint="' . $this->escape($field->description) . '"' : '') . '>';
				echo $field->label;
				echo $field->input;
				if ($field->description)
				{
					echo '<span class="hint">' . $field->description . '</span>';
				}
				echo '</div>';
				?>
			<?php endforeach; ?>
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
