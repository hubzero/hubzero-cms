<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_TOOLS_USER_PREFS') . ': ' . $text, 'user.png');
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::spacer();
JToolBarHelper::cancel();

$user = \JUser::getInstance($this->row->user_id);

$base = str_replace('/administrator', '', rtrim(JURI::getInstance()->base(true), '/'));
?>

<script type="text/javascript">
	function submitbutton(pressbutton)
	{
		var form = document.adminForm;

		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		submitform( pressbutton );
	}

	jQuery(document).ready(function($){
		$('#class_id').on('change', function (e) {
			//e.preventDefault();

			var req = $.getJSON('index.php?option=<?php echo $this->option; ?>&controller=<?php echo $this->controller; ?>&task=getClassValues&class_id=' + $(this).val(), {}, function (data) {
				$.each(data, function (key, val) {
					var item = $('#field-'+key);
					item.val(val);

					if (e.target.options[e.target.selectedIndex].text == 'custom') {
						item.prop("readonly", false);
					} else {
						item.prop("readonly", true);
					}
				});
			});
		});
	});
</script>
<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<?php if ($this->getError()) : ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php endif; ?>

	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_TOOLS_USER_PREFS_LEGEND'); ?></span></legend>

			<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />

			<?php if (!$this->row->id) : ?>
				<div class="input-wrap">
					<script type="text/javascript" src="<?php echo $base; ?>/plugins/hubzero/autocompleter/autocompleter.js"></script>
					<script type="text/javascript">var plgAutocompleterCss = "<?php echo $base; ?>/plugins/hubzero/autocompleter/autocompleter.css";</script>

					<label for="field-user_id"><?php echo JText::_('COM_TOOLS_USER_PREFS_USER'); ?>:</label>
					<input type="text" name="fields[user_id]" id="field-user_id" data-options="members,multi," id="acmembers" class="autocomplete" value="" autocomplete="off" data-css="" data-script="<?php echo $base; ?>/administrator/index.php" />
					<span><?php echo JText::_('COM_TOOLS_USER_PREFS_USER_HINT'); ?></span>
				</div>
			<?php else : ?>
				<input type="hidden" name="fields[user_id]" id="field-user_id" value="<?php echo $this->row->user_id; ?>" />
			<?php endif; ?>
			<div class="input-wrap">
				<label for="class_id"><?php echo JText::_('COM_TOOLS_USER_PREFS_CLASS'); ?>:</label>
				<?php echo $this->classes; ?>
			</div>
			<div class="input-wrap">
				<label for="field-jobs"><?php echo JText::_('COM_TOOLS_USER_PREFS_JOBS'); ?>:</label>
				<input <?php echo ($this->row->class_id) ? 'readonly' : ''; ?> type="text" name="fields[jobs]" id="field-jobs" value="<?php echo $this->escape(stripslashes($this->row->jobs)); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-params"><?php echo JText::_('COM_TOOLS_USER_PREFS_PREFERENCES'); ?>:</label>
				<textarea name="fields[params]" id="field-params" cols="35" rows="5"><?php echo $this->escape(stripslashes($this->row->params)); ?></textarea>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_TOOLS_USER_PREFS_ID'); ?></th>
					<td><?php echo $this->row->user_id; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_TOOLS_USER_PREFS_USERNAME'); ?></th>
					<td><?php echo ($user ? $user->username : ''); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_TOOLS_USER_PREFS_NAME'); ?></th>
					<td><?php echo ($user ? $user->name : ''); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>
	<?php echo JHTML::_('form.token'); ?>
</form>