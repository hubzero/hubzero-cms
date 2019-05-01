<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
						if (!isset($access[$field->fieldname]))
						{
							$access[$field->fieldname] = 1;
						}
						echo '</div>';
						?>
					</div>
					<div class="col span3 omega">
						<div class="input-wrap">
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

		<?php
		if ($lnks = Hubzero\Auth\Link::find_by_user_id($this->profile->get('id')))
		{
			?>
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_MEMBERS_AUTHENTICATOR_DATA'); ?></legend>
				<?php
				foreach ($lnks as $lnk)
				{
					$extrafields = Hubzero\Auth\Link\Data::all()
						->whereEquals('link_id', $lnk['id'])
						->rows();

					if ($extrafields->count() > 0)
					{
						?>
						<fieldset class="radio authenticators">
							<legend><?php echo $this->escape($lnk['auth_domain_name']); ?></legend>

							<?php
							foreach ($extrafields as $extrafield)
							{
								?>
								<div class="input-wrap">
									<label for="<?php echo $extrafield->get('link_id') . '_' . $extrafield->get('domain_key') . '_' . $extrafield->get('id'); ?>"><?php echo $this->escape($extrafield->get('domain_key')); ?></label>
									<input type="text" name="<?php echo $extrafield->get('link_id') . '_' . $extrafield->get('domain_key') . '_' . $extrafield->get('id'); ?>" value="<?php echo $this->escape($extrafield->get('domain_value')); ?>" readonly="readonly" />
								</div>
								<?php
							}
							?>
						</fieldset>
						<?php
					}
				}
				?>
			</fieldset>
			<?php
		}
		?>
	</div>
</div>
