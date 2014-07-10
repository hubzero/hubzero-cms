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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//set title
JToolBarHelper::title(JText::_('COM_NEWSLETTER_TEST_SENDING') . ': ' . $this->newsletter->name, 'newsletter.png');

//add buttons to toolbar
JToolBarHelper::custom('dosendtest', 'send','', 'COM_NEWSLETTER_TOOLBAR_SEND_TEST', false);
JToolBarHelper::cancel();
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	// do field validation
	submitform( pressbutton );
}
</script>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="index.php" method="post" name="adminForm">
	<div class="col width-100">
		<?php if ($this->newsletter->id != null) : ?>
			<a name="distribution"></a>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_NEWSLETTER_TEST_SENDING'); ?></legend>
				<table class="admintable">
					<tbody>
						<tr>
							<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER'); ?>:</th>
							<td>
								<?php echo $this->newsletter->name; ?>
							</td>
						</tr>
						<tr>
							<th width="200px">
								<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_TEST_EMAILS'); ?>:<br />
								<span class="hint"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_TEST_EMAILS_HINT'); ?></span>
							</th>
							<td>
								<input type="text" name="emails" placeholder="<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_TEST_EMAILS_PLACEHOLDER'); ?>" autocomplete="off" />
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		<?php endif; ?>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="dosendtest" />
	<input type="hidden" name="nid" value="<?php echo $this->newsletter->id; ?>" />
</form>