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

JToolBarHelper::title(JText::_('COM_SUPPORT') . ': ' . JText::_('COM_SUPPORT_TICKET') . ': ' . $text, 'support.png');
JToolBarHelper::save();
//JToolBarHelper::apply();
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('ticket');

$juser = JFactory::getUser();

JHTML::_('behavior.tooltip');
$this->css();

JPluginHelper::importPlugin('hubzero');
$dispatcher = JDispatcher::getInstance();

$browser = new \Hubzero\Browser\Detector();

$cc = array();
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>
<form action="index.php" method="post" name="adminForm" id="item-form" enctype="multipart/form-data">
	<div class="col width-70 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<input type="hidden" name="summary" id="field-summary" value="<?php echo $this->escape($this->row->get('summary')); ?>" size="50" />

			<div class="input-wrap">
				<label for="field-login"><?php echo JText::_('COM_SUPPORT_TICKET_FIELD_LOGIN'); ?>:</label>
				<input type="text" name="login" id="field-login" value="<?php echo $this->escape(trim($this->row->get('login'))); ?>" size="50" />
			</div>
			<div class="input-wrap">
				<label for="field-name"><?php echo JText::_('COM_SUPPORT_TICKET_FIELD_NAME'); ?>:</label>
				<input type="text" name="name" id="field-name" value="<?php echo $this->escape(trim($this->row->get('name'))); ?>" size="50" />
			</div>
			<div class="input-wrap">
				<label for="field-email"><?php echo JText::_('COM_SUPPORT_TICKET_FIELD_EMAIL'); ?>:</label>
				<input type="text" name="email" id="field-email" value="<?php echo $this->escape($this->row->get('email')); ?>" size="50" />
			</div>
			<div class="input-wrap">
				<label for="field-report"><?php echo JText::_('COM_SUPPORT_TICKET_FIELD_DESCRIPTION'); ?>:</label>
				<textarea name="report" id="field-report" cols="75" rows="15"><?php echo $this->escape(trim($this->row->content('raw'))); ?></textarea>
			</div>
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
					<label for="acgroup"><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_GROUP'); ?>:</label></td>
					<?php
					$gc = $dispatcher->trigger( 'onGetSingleEntryWithSelect', array(array('groups', 'group', 'acgroup','','','','owner')) );
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
					<select name="severity" id="field-severity">
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
					<select name="status" id="field-status">
						<?php $row = new SupportModelTicket(); ?>
						<optgroup label="<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_OPT_OPEN'); ?>">
							<option value="0" selected="selected"><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_OPT_NEW'); ?></option>
							<?php foreach ($this->row->statuses('open') as $status) { ?>
								<option value="<?php echo $status->get('id'); ?>"><?php echo $this->escape($status->get('title')); ?></option>
							<?php } ?>
						</optgroup>
						<optgroup label="<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_OPTGROUP_CLOSED'); ?>">
							<option value="0"><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_OPT_CLOSED'); ?></option>
							<?php foreach ($this->row->statuses('closed') as $status) { ?>
								<option value="<?php echo $status->get('id'); ?>"><?php echo $this->escape($status->get('title')); ?></option>
							<?php } ?>
						</optgroup>
					</select>
				</div>
			</div>
			<div class="clr"></div>

			<?php if (isset($this->lists['categories']) && $this->lists['categories']) { ?>
				<div class="input-wrap">
					<label for="ticket-field-category">
						<?php echo JText::_('COM_SUPPORT_TICKET_FIELD_CATEGORY'); ?>
						<select name="category" id="ticket-field-category">
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

			<div class="input-wrap">
				<label for="field-message"><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC'); ?></label>
				<?php
				$mc = $dispatcher->trigger('onGetMultiEntry', array(array('members', 'cc', 'acmembers', '', implode(', ', $cc))));
				if (count($mc) > 0) {
					echo $mc[0];
				} else { ?>
				<input type="text" name="cc" id="acmembers" value="<?php echo implode(', ', $cc); ?>" size="35" />
				<?php } ?>
			</div>
			<input type="hidden" name="section" value="1" />
			<input type="hidden" name="uas" value="<?php echo JRequest::getVar('HTTP_USER_AGENT', '', 'server'); ?>" />
			<input type="hidden" name="severity" value="normal" />
		</fieldset>
	</div><!-- / .col width-70 -->
	<div class="col width-30 fltrt">
		<p><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_FORM_EXPLANATION'); ?></p>
	</div><!-- / .col width-30 -->
	<div class="clr"></div>

	<input type="hidden" name="referer" value="<?php echo JRequest::getVar('HTTP_REFERER', NULL, 'server'); ?>" />
	<input type="hidden" name="os" value="<?php echo $browser->platform(); ?>" />
	<input type="hidden" name="osver" value="<?php echo $browser->platformVersion(); ?>" />
	<input type="hidden" name="browser" value="<?php echo $browser->name(); ?>" />
	<input type="hidden" name="browserver" value="<?php echo $browser->version(); ?>" />
	<input type="hidden" name="hostname" value="<?php echo gethostbyaddr(JRequest::getVar('REMOTE_ADDR','','server')); ?>" />
	<input type="hidden" name="uas" value="<?php echo JRequest::getVar('HTTP_USER_AGENT', '', 'server'); ?>" />

	<input type="hidden" name="id" id="ticketid" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="username" value="<?php echo $this->escape($juser->get('username')); ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>