<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */
defined('_HZEXEC_') or die();

JLoader::register('JHtmlUsers', JPATH_COMPONENT . '/helpers/html/users.php');

Html::register('users.spacer', array('JHtmlUsers', 'spacer'));

$fieldsets = $this->form->getFieldsets();
if (isset($fieldsets['core']))   unset($fieldsets['core']);
if (isset($fieldsets['params'])) unset($fieldsets['params']);

foreach ($fieldsets as $group => $fieldset): // Iterate through the form fieldsets
	$fields = $this->form->getFieldset($group);
	if (count($fields)):
?>
<fieldset id="users-profile-custom" class="users-profile-custom-<?php echo $group;?>">
	<?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.?>
	<legend><?php echo Lang::txt($fieldset->label); ?></legend>
	<?php endif;?>
	<dl>
	<?php foreach ($fields as $field):
		if (!$field->hidden) :?>
		<dt><?php echo $field->title; ?></dt>
		<dd>
			<?php if (Html::has('users.'.$field->id)):?>
				<?php echo Html::users($field->id, $field->value);?>
			<?php elseif (Html::has('users.'.$field->fieldname)):?>
				<?php echo Html::users($field->fieldname, $field->value);?>
			<?php elseif (Html::has('users.'.$field->type)):?>
				<?php echo Html::users($field->type, $field->value);?>
			<?php else:?>
				<?php echo Html::users('value', $field->value);?>
			<?php endif;?>
		</dd>
		<?php endif;?>
	<?php endforeach;?>
	</dl>
</fieldset>
	<?php endif;?>
<?php endforeach;?>
