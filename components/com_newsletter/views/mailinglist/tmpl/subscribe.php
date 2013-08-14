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
defined('_JEXEC') or die( 'Restricted access' );
$mylistIds = array();
?>

<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul>
		<li>
			<a class="btn icon-browse" href="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">
				<?php echo JText::_('COM_NEWSLETTER_BROWSE'); ?>
			</a>
		</li>
	</ul>
</div>

<div class="main section">
	<?php
		if ($this->getError())
		{
			echo '<p class="error">' . $this->getError() . '</p>';
		}
	?>
	<div class="subject subscribe">
		<form action="index.php" method="post" id="hubForm">
			<?php if (count($this->mylists) > 0) : ?>
				<fieldset>
					<legend>My Mailing Lists</legend>
					<?php foreach ($this->mylists as $mylist) : ?>
						<?php $mylistIds[] = $mylist->mailinglistid; ?>
						<?php if ($mylist->status != 'removed') : ?>
							<label>
								<input type="checkbox" name="lists[]" value="<?php echo $mylist->mailinglistid; ?>" <?php echo ($mylist->status == 'active' || $mylist->status == 'inactive') ? 'checked="checked"' : ''; ?> />
								<strong><?php echo $mylist->name; ?></strong>
								<?php
									if ($mylist->status == 'active' || $mylist->status == 'inactive')
									{
										if (!$mylist->confirmed)
										{
											echo ' - <span title="NOT Confirmed :: Confirm your email subscription to this mailing list." class="unconfirmed tooltips">NOT Confirmed</span> <span class="unconfirmed-link">(<a href="'.JRoute::_('index.php?option=com_newsletter&task=resendconfirmation&mid='.$mylist->mailinglistid).'" class="">Click here to resend confirmation email</a>)</span>';
										}
									}
									else if ($mylist->status == 'unsubscribed') 
									{ 
										echo ' - <span class="unsubscribed">Currently Unsubscribed</span>'; 
									} 
								?>
								<span class="desc">
									<?php echo ($mylist->description) ? nl2br($mylist->description) : '(No description available.)'; ?>
								</span>
							</label>
						<?php endif; ?>
					<?php endforeach; ?>
				</fieldset>
			<?php endif; ?>
			<?php
				//remove any lists that i already belong to
				foreach ($this->alllists as $k => $list)
				{
					if (in_array($list->id, $mylistIds))
					{
						unset($this->alllists[$k]);
					}
				}
			?>
			<?php if (count($this->alllists) > 0) : ?>
				<fieldset>
					<legend>Public Mailing Lists You May Be Interested In</legend>
					<?php foreach ($this->alllists as $list) : ?>
						<label>
							<input type="checkbox" name="lists[]" value="<?php echo $list->id; ?>" />
							<strong><?php echo $list->name; ?></strong>
							<span class="desc"><?php echo ($list->description) ? nl2br($list->description) : '(No description available.)'; ?></span>
						</label>
					<?php endforeach; ?>
				</fieldset>
			<?php endif; ?>
			<?php if (count($this->mylists) > 0 || count($this->alllists) > 0) : ?>
				<p class="submit">
					<input type="submit" value="Save Mailing List Subscriptions">
				</p>
			<?php else: ?>
				<p class="info">
					Currently there are no mailing lists to subscribe to.
				</p>
			<?php endif; ?>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="domultisubscribe" />
		</form>
	</div>
</div>