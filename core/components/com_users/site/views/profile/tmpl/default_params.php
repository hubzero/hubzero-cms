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
Html::register('users.helpsite', array('JHtmlUsers', 'helpsite'));
Html::register('users.templatestyle', array('JHtmlUsers', 'templatestyle'));
Html::register('users.admin_language', array('JHtmlUsers', 'admin_language'));
Html::register('users.language', array('JHtmlUsers', 'language'));
Html::register('users.editor', array('JHtmlUsers', 'editor'));

?>
<?php $fields = $this->form->getFieldset('params'); ?>
<?php if (count($fields)): ?>
<fieldset id="users-profile-custom">
	<legend><?php echo Lang::txt('COM_USERS_SETTINGS_FIELDSET_LABEL'); ?></legend>
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
