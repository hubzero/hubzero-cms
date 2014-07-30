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
JToolBarHelper::title(JText::_('Send Newsletter') . ': ' . $this->newsletter->name, 'newsletter.png');

//add buttons to toolbar
JToolBarHelper::custom('dosendnewsletter', 'send', '', 'Send Newsletter', false);
JToolBarHelper::cancel();
?>

<script type="text/javascript">


jQuery(document).ready(function($){
	var $ = jq;

	$('#mailinglist').on('change', function(event) {
		var value = $(this).val();
		if (value != '' && value != 0)
		{
			$.ajax({
				type: 'get',
				dataType: 'json',
				url: 'index.php?option=com_newsletter&controller=mailinglist&task=emailcount&mailinglistid='+value+'&no_html=1',
				success: function(data)
				{
					var emailCount = data.length;
					
					//show count
					$('#mailinglist-count').show();
					
					//set actual counter
					$('#mailinglist-count').find('#mailinglist-count-count').html(emailCount);		

					//add list of emails
					$('#mailinglist-emails').html('<br />--------------------------------<br />' + data.join('<br />'));
				}
			});
		}
		else
		{
			$('#mailinglist-count').hide();
		}
	});

	$('#mailinglist-count').hide();
});

function submitbutton(pressbutton) 
{
	//are we trying to send newsletter
	if(pressbutton == 'dosendnewsletter')
	{
		//check to make sure we all set to go
		if (!HUB.Administrator.Newsletter.sendNewsletterCheck())
		{
			return;
		}
		
		//double check with user
		if (!HUB.Administrator.Newsletter.sendNewsletterDoubleCheck())
		{
			return;
		}
	}
	
	//submit form
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
		<?php if($this->newsletter->id != null) : ?>
			<a name="distribution"></a>
			<fieldset class="adminform">
				<legend><?php echo JText::_('Send Newsletter'); ?></legend>
				<table class="admintable">
					<tbody>
						<tr>
							<th width="200px"><?php echo JText::_('Newsletter:'); ?></th>
							<td>
								<?php echo $this->newsletter->name; ?>
								<input type="hidden" name="newsletter-name" id="newsletter-name" value="<?php echo $this->newsletter->name; ?>" />
							</td>
						</tr>
						<tr>
							<th><?php echo JText::_('Newsletter Sent Previously:'); ?></th>
							<td>
								<?php if($this->newsletter->sent) : ?>
									<?php
										$sql = "SELECT date FROM #__newsletter_mailings WHERE nid={$this->newsletter->id} ORDER BY date DESC LIMIT 1";
										$this->database->setQuery( $sql );
										$lastDateSent = $this->database->loadResult();
									?>
									<font color="green">
										<strong><?php echo JText::_('Yes'); ?></strong>
									</font>-
									<?php if ($lastDateSent) : ?>
										<?php echo JHTML::_('date', $lastDateSent, "l, F d, Y @ g:ia"); ?>
									<?php else : ?>
										<?php echo JText::_('no record of mailing was found.'); ?>
									<?php endif; ?>
								<?php else : ?>
									<strong>
										<font color="red"><?php echo JText::_('No'); ?></font>
									</strong>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<th width="200px"><?php echo JText::_('Schedule:'); ?></th>
							<td>
								<div id="scheduler">
									<input type="radio" name="scheduler" value="1" checked="checked" /> Send Now <br />
									<input type="radio" name="scheduler" value="0" /> Send Later <br />
									
									<div id="scheduler-alt">
										Date:
										<input type="text" name="scheduler_date" id="scheduler_date" style="width:auto;" />
										Time: 
										<select name="scheduler_date_hour" id="scheduler_date_hour" style="width:auto;">
											<option value="">--</option>
											<?php for($i = 1, $n = 13; $i < $n; $i++) : ?>
												<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
											<?php endfor; ?>
										</select>
								
										<select name="scheduler_date_minute" id="scheduler_date_minute" style="width:auto;">
											<option value="">--</option>
											<?php for($i = 0, $n = 60; $i < $n; $i+=5) : ?>
												<?php
													if ($i == '0')
													{
														$i = '00';
													}
													else if ($i == '5')
													{
														$i = '05';
													}
										
												?>
												<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
											<?php endfor; ?>
										</select>
								
										<select name="scheduler_date_meridian" id="scheduler_date_meridian" style="width:auto;">
											<option value="">--</option>
											<option value="am">AM</option>
											<option value="pm">PM</option>
										</select>
										EST
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<th><?php echo JText::_('Mailing List:'); ?></th>
							<td>
								<select name="mailinglist" id="mailinglist">
									<option value=""><?php echo JText::_('- Select Mailing List &mdash;'); ?></option>
									<option value="-1"><?php echo JText::_('Default List - All HUB members with their email preference Set to Yes'); ?></option>
									<?php foreach ($this->mailinglists as $list) : ?>
										<option value="<?php echo $list->id; ?>"><?php echo $list->name; ?></option>
									<?php endforeach; ?>
								</select>
								<br /><br />
								<p id="mailinglist-count">
									<?php echo JText::_('# of Members to Receive Newsletter: '); ?><span id="mailinglist-count-count"></span>
									<span id="mailinglist-emails"></span>
								</p>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		<?php endif; ?>
	</div>
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="dosendnewsletter" />
	<input type="hidden" name="nid" value="<?php echo $this->newsletter->id; ?>" />
</form>