<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$canDo = UsersHelper::getActions();
?>

<script type="text/javascript">
Joomla.submitbutton = function(task)
{
	if (task == 'level.cancel' || document.formvalidator.isValid($('#item-form'))) {
		Joomla.submitform(task, document.getElementById('item-form'));
	}
}
/*
jQuery(document).ready(function($){
	$('#user-groups').find('input').each(function(i){
		// Event to check all child groups.
		$(i).on('check', function(e){
			// Check the child groups.
			$('#user-groups').find('input').each(function(c){
				if (this.attr('rel') == c.id) {
					c.prop('checked', true);
					c.prop('disabled', true);
					c.trigger('check');
				}
			});
		})
		.on('uncheck', function(e){
			// Uncheck the parent groups.
			$('#user-groups').find('input').each(function(c){
				if (c.attr('rel') == this.id) {
					c.prop('checked', false);
					c.prop('disabled', false);
					c.trigger('uncheck');
				}
			});
		})
		// Bind to the click event to check/uncheck child/parent groups.
		.on('click', function(e){
			// Check the child groups.
			$('#user-groups').find('input').each(function(c){
				if (this.attr('rel') == c.id) {
					c.prop('checked', true);
					if (this.attr('checked')) {
						c.prop('disabled', true);
					} else {
						c.prop('disabled', false);
					}
					c.trigger('check');
				}
			});

			// Uncheck the parent groups.
			$('#user-groups').find('input').each(function(c){
				if (c.attr('rel') == this.id) {
					c.prop('checked', false);
					c.prop('disabled', false);
					c.trigger('uncheck');
				}
			});
		});

		// Initialise the widget.
		if (i.prop('checked')) {
			i.trigger('click');
		}
	});
});
*/
</script>


<form action="<?php echo JRoute::_('index.php?option=com_users&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_USERS_LEVEL_DETAILS');?></span></legend>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('title'); ?>
				<?php echo $this->form->getInput('title'); ?>
			</div>
		</fieldset>
	</div>

	<div class="col width-50 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_USERS_USER_GROUPS_HAVING_ACCESS');?></span></legend>

			<div class="input-wrap">
				<?php echo JHtml::_('access.usergroups', 'jform[rules]', $this->item->rules); ?>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
