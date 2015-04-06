<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
//load user_profile plugin language
$lang = Lang::getRoot();
$lang->load( 'plg_user_profile', JPATH_ADMINISTRATOR );
?>
<div class="profile-edit<?php echo $this->pageclass_sfx?>">
<?php if ($this->params->get('show_page_heading')) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>

<form id="member-profile" action="<?php echo Route::url('index.php?option=com_users&task=profile.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
<?php foreach ($this->form->getFieldsets() as $group => $fieldset):// Iterate through the form fieldsets and display each one.?>
	<?php $fields = $this->form->getFieldset($group);?>
	<?php if (count($fields)):?>
	<fieldset>
		<?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.?>
		<legend><?php echo Lang::txt($fieldset->label); ?></legend>
		<?php endif;?>
		<dl>
		<?php foreach ($fields as $field):// Iterate through the fields in the set and display them.?>
			<?php if ($field->hidden):// If the field is hidden, just display the input.?>
				<?php echo $field->input;?>
			<?php else:?>
				<dt>
					<?php echo $field->label; ?>
					<?php if (!$field->required && $field->type!='Spacer' && $field->name!='jform[username]'): ?>
						<span class="optional"><?php echo Lang::txt('COM_USERS_OPTIONAL'); ?></span>
					<?php endif; ?>
				</dt>
				<dd><?php echo $field->input; ?></dd>
			<?php endif;?>
		<?php endforeach;?>
		</dl>
	</fieldset>
	<?php endif;?>
<?php endforeach;?>

		<div>
			<button type="submit" class="validate"><span><?php echo Lang::txt('JSUBMIT'); ?></span></button>
			<?php echo Lang::txt('COM_USERS_OR'); ?>
			<a href="<?php echo Route::url(''); ?>" title="<?php echo Lang::txt('JCANCEL'); ?>"><?php echo Lang::txt('JCANCEL'); ?></a>

			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="profile.save" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
