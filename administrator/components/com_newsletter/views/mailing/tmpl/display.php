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
JToolBarHelper::title(JText::_( 'COM_NEWSLETTER_NEWSLETTER_MAILINGS' ), 'mailing.png');

//add buttons to toolbar
//JToolBarHelper::custom('process', 'options', '', 'Process', false);
//JToolBarHelper::custom('processips', 'refresh', '', 'IPs', false);
JToolBarHelper::spacer();
JToolBarHelper::custom('tracking', 'stats', '', 'COM_NEWSLETTER_TOOLBAR_STATS');
JToolBarHelper::custom('stop', 'trash', '', 'COM_NEWSLETTER_TOOLBAR_STOP');
JToolBarHelper::spacer();
JToolBarHelper::preferences($this->option, '550');
?>

<script type="text/javascript">
Joomla.submitbutton = function(pressbutton)
{
	if (pressbutton == 'stop')
	{
		var message = '<?php echo JText::_('COM_NEWSLETTER_MAILING_STOP_CHECK'); ?>'
		if (!confirm( message ))
		{
			return;
		}
	}
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
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->mailings); ?>);" /></th>
				<th scope="col"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER'); ?></th>
				<th scope="col"><?php echo JText::_('COM_NEWSLETTER_MAILING_DATE'); ?></th>
				<th scope="col"><?php echo JText::_('COM_NEWSLETTER_MAILING_PERCENT_COMPLETE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->mailings) > 0) : ?>
				<?php foreach ($this->mailings as $k => $mailing) : ?>
					<tr>
						<td>
							<input type="checkbox" name="id[]" id="cb<?php echo $k;?>" value="<?php echo $mailing->mailing_id; ?>" onclick="isChecked(this.checked);" />
						</td>
						<td>
							<?php echo $mailing->newsletter_name; ?>
						</td>
						<td>
							<?php echo JHTML::_('date', $mailing->mailing_date, "F d, Y @ g:ia"); ?>
						</td>
						<td>
							<?php
								if ($mailing->emails_total != 0)
								{
									echo number_format(($mailing->emails_sent/$mailing->emails_total) * 100, 2) . ' %';
								}
								else
								{
									echo '0%';
								}
							 ?>
							(<?php echo number_format($mailing->emails_sent); ?> of <?php echo number_format($mailing->emails_total); ?> emails sent)
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="4">
						<?php echo JText::_('COM_NEWSLETTER_NO_MAILINGS'); ?>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_('form.token'); ?>
</form>