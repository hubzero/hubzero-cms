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

$this->css();

$profiles = $this->profile->profiles()->ordered()->rows();

// Convert to XML so we can use the Form processor
$xml = Components\Members\Models\Profile\Field::toXml($this->fields, 'edit');

// Gather data to pass to the form processor
$data = new Hubzero\Config\Registry(
	Components\Members\Models\Profile::collect($profiles)
);

// Create a new form
Hubzero\Form\Form::addFieldPath(Component::path('com_members') . DS . 'models' . DS . 'fields');

$form = new Hubzero\Form\Form('profile', array('control' => 'profile'));
$form->load($xml);
$form->bind($data);

$fields = array();
foreach ($profiles as $profile)
{
	if (isset($fields[$profile->get('profile_key')]))
	{
		$values = $fields[$profile->get('profile_key')]->get('profile_value');
		if (!is_array($values))
		{
			$values = array($values);
		}
		$values[] = $profile->get('profile_value');

		$fields[$profile->get('profile_key')]->set('profile_value', $values);
	}
	else
	{
		$fields[$profile->get('profile_key')] = $profile;
	}
}
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header-extra -->

<section class="main section">
	<form id="hubForm" class="edit-profile" method="post" action="<?php echo Route::url('index.php?option=' . $this->option); ?>" enctype="multipart/form-data">

		<fieldset>
			<legend><?php echo Lang::txt('Contact Information'); ?></legend>
			<input type="hidden" name="id" value="<?php echo $this->profile->get('id'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="save" />

			<label>
				<?php echo Lang::txt('Visibility (who has access to my profile)'); ?>
				<?php echo Components\Members\Helpers\Html::selectAccess('access', $this->profile->get('access'), 'input-select'); ?>
			</label>

			<div class="grid">
				<div class="col span4">
					<label>
						<?php echo Lang::txt('FIRST_NAME'); ?>:
						<input type="text" name="name[first]" value="<?php echo $this->escape($this->profile->get('givenName')); ?>" />
					</label>
				</div>
				<div class="col span4">
					<label>
						<?php echo Lang::txt('MIDDLE_NAME'); ?>:
						<input type="text" name="name[middle]" value="<?php echo $this->escape($this->profile->get('middleName')); ?>" />
					</label>
				</div>
				<div class="col span4 omega">
					<label>
						<?php echo Lang::txt('LAST_NAME'); ?>:
						<input type="text" name="name[last]" value="<?php echo $this->escape($this->profile->get('surname')); ?>" />
					</label>
				</div>
			</div>

			<div class="grid">
				<div class="col span6">
					<label>
						<?php echo Lang::txt('Valid E-mail'); ?>:
						<input name="email" id="email" type="text" value="<?php echo $this->escape($this->profile->get('email')); ?>" />
					</label>
				</div>
				<div class="col span6 omega">
					<label>
						<?php echo Lang::txt('Confirm E-mail'); ?>:
						<input name="email2" id="email2" type="text" value="<?php echo $this->escape($this->profile->get('email')); ?>" />
					</label>
				</div>
			</div>
			<p class="warning">Important! If you change your E-Mail address you <strong>must</strong> confirm receipt of the confirmation e-mail in order to re-activate your account.</p>
		</fieldset>

		<fieldset>
			<legend><?php echo Lang::txt('Profile'); ?></legend>

			<?php foreach ($this->fields as $field): ?>
				<?php
				if (!isset($fields[$field->get('name')]))
				{
					$fields[$field->get('name')] = Components\Members\Models\Profile::blank();
					$fields[$field->get('name')]->set('access', 1);
				}

				$profile = $fields[$field->get('name')];
				?>
				<div class="input-wrap">
					<?php
					$value = $profile->get('profile_value');
					$value = $value ?: $this->profile->get($field->get('name'));
					if ($field->get('type') == 'tags')
					{
						$value = $this->profile->tags('string');
					}

					$formfield = $form->getField($field->get('name'));
					$formfield->setValue($value);
					?>
					<div class="grid">
						<div class="col span8">
							<?php
							echo $formfield->label;
							echo $formfield->input;
							?>
						</div>
						<div class="col span4 omega">
							<?php
							echo '<label>' . Lang::txt('COM_MEMBERS_FIELD_ACCESS')  . '</label>';
							echo Components\Members\Helpers\Html::selectAccess('access[' . $field->get('name') . ']',$field->get('access'),'input-select');
							?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</fieldset>

		<fieldset>
			<legend><?php echo Lang::txt('Updates'); ?></legend>

			<label for="sendEmail">
				<?php echo Lang::txt('COM_MEMBERS_PROFILE_EMAILUPDATES'); ?>
				<select name="sendEmail" id="sendEmail" class="input-select">';
					<?php
					$options = array(
						'-1' => Lang::txt('COM_MEMBERS_PROFILE_EMAILUPDATES_OPT_SELECT'),
						'1'  => Lang::txt('COM_MEMBERS_PROFILE_EMAILUPDATES_OPT_YES'),
						'0'  => Lang::txt('COM_MEMBERS_PROFILE_EMAILUPDATES_OPT_NO')
					);
					foreach ($options as $key => $value)
					{
						$sel = ($key == $this->profile->get('sendEmail')) ? 'selected="selected"' : '';
						echo '<option ' . $sel . ' value="' . $key . '">' . $value . '</option>';
					}
					?>
				</select>
				<span class="hint"><?php echo Lang::txt('COM_MEMBERS_PROFILE_EMAILUPDATES_EXPLANATION'); ?></span>
			</label>
		</fieldset><div class="clear"></div>

		<fieldset id="memberpicture">
			<legend><?php echo Lang::txt('MEMBER_PICTURE'); ?></legend>
			<iframe width="100%" height="350" border="0" name="filer" id="filer" src="<?php echo Route::url('index.php?option='.$this->option.'&controller=media&tmpl=component&file='.stripslashes($this->profile->get('picture')).'&amp;id='.$this->profile->get('id')); ?>"></iframe>
		</fieldset><div class="clear"></div>

		<?php echo Html::input('token'); ?>
		<p class="submit">
			<input class="btn btn-success" type="submit" name="submit" value="<?php echo Lang::txt('SAVE'); ?>" />
			<a class="btn secondary" href="<?php echo Route::url('index.php?option='.$this->option.'&task=cancel&id='. $this->profile->get('id')); ?>"><?php echo Lang::txt('CANCEL'); ?></a>
		</p>
	</form>
</section>
