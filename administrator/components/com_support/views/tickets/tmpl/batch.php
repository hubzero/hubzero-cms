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

if (!$this->tmpl)
{
	JToolBarHelper::title(JText::_('COM_SUPPORT') . ': ' . JText::_('COM_SUPPORT_TICKET') . ': ' . JText::_('Batch Process'), 'support.png');
	JToolBarHelper::save('process');
	JToolBarHelper::cancel();
	JToolBarHelper::spacer();
	JToolBarHelper::help('ticket');
}

$juser = JFactory::getUser();

JHTML::_('behavior.tooltip');
$this->css();

JPluginHelper::importPlugin('hubzero');
$dispatcher = JDispatcher::getInstance();

$cc = array();
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
function saveAndUpdate()
{
	$.post('index.php', $("#component-form").serialize(), function(data){
		var queries = $(data).find('#query-list');
		var tickets = $(data).find('#tktlist');

		window.parent.document.getElementById('query-list').innerHTML = queries.html();
		window.parent.document.getElementById('tktlist').innerHTML = tickets.html();

		window.top.setTimeout('window.parent.$.fancybox.close()', 700);
	});
}
</script>
<form action="index.php" method="post" name="adminForm" id="<?php echo ($this->tmpl == 'component') ? 'component-form' : 'item-form'; ?>" enctype="multipart/form-data">
	<?php if ($this->tmpl == 'component') { ?>
		<fieldset>
			<div class="configuration">
				<div class="configuration-options">
					<button type="button" onclick="saveAndUpdate();"><?php echo JText::_('Save'); ?></button>
					<button type="button" onclick="window.parent.$.fancybox.close();"><?php echo JText::_('Cancel'); ?></button>
				</div>
				<?php echo JText::_('Batch Process'); ?>
			</div>
		</fieldset>
		<input type="hidden" name="no_html" value="1" />
	<?php } ?>
	<div class="col width-100">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="actags"><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_TAGS'); ?></label>
				<?php
				$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags', '', '')) );
				if (count($tf) > 0) {
					echo $tf[0];
				} else { ?>
					<input type="text" name="tags" id="actags" value="" />
				<?php } ?>
			</div>

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="acgroup"><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_GROUP'); ?>:</label>
					<?php
					$gc = $dispatcher->trigger( 'onGetSingleEntryWithSelect', array(array('groups', 'fields[group]', 'acgroup','','','','owner')) );
					if (count($gc) > 0) {
						echo $gc[0];
					} else { ?>
					<input type="text" name="group" value="" id="acgroup" value="" size="30" autocomplete="off" />
					<?php } ?>
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_OWNER'); ?></label>
					<?php echo $this->lists['owner']; ?>
				</div>
			</div>
			<div class="clr"></div>

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="field-severity"><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_SEVERITY'); ?></label>
					<select name="fields[severity]" id="field-severity">
						<option value=""><?php echo JText::_('Select...'); ?></option>
						<option value="critical"><?php echo JText::_('COM_SUPPORT_TICKET_SEVERITY_CRITICAL'); ?></option>
						<option value="major"><?php echo JText::_('COM_SUPPORT_TICKET_SEVERITY_MAJOR'); ?></option>
						<option value="normal"><?php echo JText::_('COM_SUPPORT_TICKET_SEVERITY_NORMAL'); ?></option>
						<option value="minor"><?php echo JText::_('COM_SUPPORT_TICKET_SEVERITY_MINOR'); ?></option>
						<option value="trivial"><?php echo JText::_('COM_SUPPORT_TICKET_SEVERITY_TRIVIAL'); ?></option>
					</select>
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label for="field-status"><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_STATUS'); ?></label>
					<select name="fields[status]" id="field-status">
						<option value=""><?php echo JText::_('Select...'); ?></option>
						<?php $row = new SupportModelTicket(); ?>
						<optgroup label="<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_OPT_OPEN'); ?>">
							<?php foreach ($row->statuses('open') as $status) { ?>
								<option value="<?php echo $status->get('id'); ?>"><?php echo $this->escape($status->get('title')); ?></option>
							<?php } ?>
						</optgroup>
						<optgroup label="<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_OPTGROUP_CLOSED'); ?>">
							<option value="0"><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_OPT_CLOSED'); ?></option>
							<?php foreach ($row->statuses('closed') as $status) { ?>
								<option value="<?php echo $status->get('id'); ?>"><?php echo $this->escape($status->get('title')); ?></option>
							<?php } ?>
						</optgroup>
					</select>
				</div>
			</div>
			<div class="clr"></div>

			<?php if (isset($this->lists['categories']) && $this->lists['categories']) { ?>
				<div class="input-wrap">
					<label for="field-category">
						<?php echo JText::_('COM_SUPPORT_TICKET_FIELD_CATEGORY'); ?>
						<select name="fields[category]" id="field-category">
							<option value=""><?php echo JText::_('COM_SUPPORT_NONE'); ?></option>
							<?php
							foreach ($this->lists['categories'] as $category)
							{
								?>
							<option value="<?php echo $this->escape($category->alias); ?>"><?php echo $this->escape(stripslashes($category->title)); ?></option>
								<?php
							}
							?>
						</select>
					</label>
				</div>
			<?php } ?>

			<?php /*
			<div class="input-wrap">
				<label for="comment-field-access" class="private hasTip" title="<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_FORM_EXPLANATION'); ?>">
						<input type="checkbox" name="access" id="comment-field-access" value="1" />
						<span><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_FIELD_ACCESS'); ?></span>
					</label>
				<label for="comment-field-content">
					<span class="label"><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_LEGEND_COMMENTS'); ?></span>
					<textarea name="comment" id="comment-field-comment" cols="75" rows="5"></textarea>
				</label>
			</div>

			<div class="input-wrap">
				<label for="comment-field-message">
					<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC'); ?> <?php
					$mc = $dispatcher->trigger('onGetMultiEntry', array(
						array(
							'members',   // The component to call
							'cc',        // Name of the input field
							'comment-field-message', // ID of the input field
							'',          // CSS class(es) for the input field
							implode(', ', $cc) // The value of the input field
						)
					));
					if (count($mc) > 0) {
						echo '<span class="hint">' . JText::_('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS_AUTOCOMPLETE') . '</span>' . $mc[0];
					} else { ?> <span class="hint"><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS'); ?></span>
						<input type="text" name="cc" id="comment-field-message" value="<?php echo implode(', ', $cc); ?>" />
					<?php } ?>
				</label>
			</div>
			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="email_submitter">
						<input class="option" type="checkbox" name="email_submitter" id="email_submitter" value="1" checked="checked" />
						<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_SUBMITTER'); ?>
					</label>
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label for="email_owner">
						<input class="option" type="checkbox" name="email_owner" id="email_owner" value="1" checked="checked" />
						<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_OWNER'); ?>
					</label>
				</div>
			</div>
			<div class="clr"></div>*/ ?>
		</fieldset>
	</div>

	<?php foreach ($this->ids as $id) { ?>
		<input type="hidden" name="id[]" value="<?php echo $id; ?>" />
	<?php } ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="process" />

	<?php echo JHTML::_('form.token'); ?>
</form>