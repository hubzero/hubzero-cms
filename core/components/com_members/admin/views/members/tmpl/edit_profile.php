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

$access = array();
foreach ($fields as $field)
{
	$access[$field->get('name')] = $field->get('access');
}

// Convert to XML so we can use the Form processor
$xml = Components\Members\Models\Profile\Field::toXml($fields);

$profiles = $this->profile->profiles()->ordered()->rows();

// Gather data to pass to the form processor
$data = new Hubzero\Config\Registry(
	Components\Members\Models\Profile::collect($profiles)
);
$data->set('tags', $this->profile->tags('string'));

foreach ($profiles as $profile)
{
	$d = (isset($access[$profile->get('profile_key')]) ? $access[$profile->get('profile_key')] : 1);
	$access[$profile->get('profile_key')] = $profile->get('access', $d);
}

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
				<div class="grid">
					<div class="col span9">
						<?php
						echo '<div class="input-wrap" style="padding-right: 0" id="input-' . $field->fieldname . '" ' . ($field->description ? ' data-hint="' . $this->escape($field->description) . '"' : '') . '>';
						echo $field->label;
						echo $field->input;
						if ($field->description)
						{
							echo '<span class="hint">' . $field->description . '</span>';
						}
						if (!isset($access[$field->fieldname]))
						{
							$access[$field->fieldname] = 1;
						}
						echo '</div>';
						?>
					</div>
					<div class="col span3">
						<div class="input-wrap" style="padding-left: 0">
							<label for="field-access-<?php echo $field->fieldname; ?>"><?php echo Lang::txt('Access'); ?>:</label>
							<select name="profileaccess[<?php echo $field->fieldname; ?>]" id="field-access-<?php echo $field->fieldname; ?>">
								<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $access[$field->fieldname]); ?>
							</select>
						</div>
					</div>
				</div>
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
