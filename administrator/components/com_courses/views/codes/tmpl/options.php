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
defined('_JEXEC') or die('Restricted access');

$tmpl = JRequest::getVar('tmpl', '');

if ($tmpl != 'component')
{
	JToolBarHelper::title(JText::_('COM_COURSES') . ': ' . JText::_('COM_COURSES_COUPON_CODE') . ': ' . JText::_('COM_COURSES_GENERATE'), 'course.png');
	if ($canDo->get('core.edit'))
	{
		JToolBarHelper::save();
	}
	JToolBarHelper::cancel();
}

JHTML::_('behavior.framework', true);

$jconfig = JFactory::getConfig();
$offset = $jconfig->getValue('config.offset');

$year  = strftime("%Y", time()+($offset*60*60));
$month = strftime("%m", time()+($offset*60*60));
$day   = strftime("%d", time()+($offset*60*60));

//$nextMonth = date("m", mktime(0, 0, 0, $month, $day + 7, $year));
$nextYear  = date("Y", mktime(0, 0, 0, $month+1, $day, $year));
$nextMonth = date("m", mktime(0, 0, 0, $month+1, $day, $year));
$nextDay   = date("d", mktime(0, 0, 0, $month+1, $day, $year));
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// form field validation
	if (form.num.value == '') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_INFORMATION'); ?>');
	} else {
		submitform(pressbutton);
	}
	window.top.setTimeout("window.parent.location='index.php?option=<?php echo $this->option; ?>&controller=<?php echo $this->controller; ?>&section=<?php echo $this->section->get('id'); ?>'", 700);
}

jQuery(document).ready(function($){
	$(window).on('keypress', function(){
		if (window.event.keyCode == 13) {
			submitbutton('generate');
		}
	})
});
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<form action="index.php" method="post" name="adminForm" id="item-form">
<?php if ($tmpl == 'component') { ?>
	<fieldset>
		<div class="configuration">
			<div class="configuration-options">
				<button type="button" onclick="submitbutton('generate');"><?php echo JText::_('COM_COURSES_GENERATE');?></button>
				<button type="button" onclick="window.parent.$.fancybox.close();"><?php echo JText::_('COM_COURSES_CANCEL');?></button>
			</div>

			<?php echo JText::_('COM_COURSES_GENERATE_CODES') ?>
		</div>
	</fieldset>
<?php } ?>
	<div class="col width-100">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<input type="hidden" name="section" value="<?php echo $this->section->get('id'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="no_html" value="<?php echo ($tmpl == 'component') ? '1' : '0'; ?>">
			<input type="hidden" name="task" value="generate" />

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="field-num"><?php echo JText::_('COM_COURSES_FIELD_NUMBER_OF_CODES'); ?>:</label></td>
						<td colspan="3"><input type="text" name="num" id="field-num" value="" size="5" /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-expires-year"><?php echo JText::_('COM_COURSES_FIELD_EXPIRES'); ?>:</label></td>
						<td>YYYY<input type="text" name="expires[year]" id="field-expires-year" value="<?php echo $nextYear; ?>" size="4" /></td>
						<td>MM<input type="text" name="expires[month]" id="field-expires-month" value="<?php echo $nextMonth; ?>" size="2" /></td>
						<td>DD<input type="text" name="expires[day]" id="field-expires-day" value="<?php echo $nextDay; ?>" size="2" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>

	<?php echo JHTML::_('form.token'); ?>
</form>
