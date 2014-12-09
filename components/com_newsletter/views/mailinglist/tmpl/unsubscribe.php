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

$this->css()
     ->js();
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul>
			<li>
				<a class="btn icon-browse" href="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">
					<?php echo JText::_('COM_NEWSLETTER_BROWSE'); ?>
				</a>
			</li>
		</ul>
	</div>
</header>

<section class="main section">
	<?php
		if ($this->getError())
		{
			echo '<p class="error">' . $this->getError() . '</p>';
		}
	?>
	<div class="subscribe">
		<form action="index.php" method="post" id="hubForm">
			<fieldset>
				<legend><?php echo JText::_('COM_NEWSLETTER_MAILINGLISTS_UNSUBSCRIBE'); ?></legend>
				<p><?php echo JText::_('COM_NEWSLETTER_MAILINGLISTS_UNSUBSCRIBE_DESC'); ?></p>

				<p>
					<strong><?php echo $this->mailinglist->name; ?></strong><br />
					<span><?php echo $this->mailinglist->description; ?></span>
					<input type="hidden" name="t" value="<?php echo JRequest::getVar('t', '') ?>" />
					<input type="hidden" name="e" value="<?php echo JRequest::getVar('e', ''); ?>" />
				</p>

				<?php if ($this->mailinglist->id == '-1' && $this->juser->get('guest') == 1) : ?>
					<ol>
						<li>
							<?php if ($this->juser->get('guest')) : ?>
								<a href="login?return=<?php echo base64_encode( $_SERVER['REQUEST_URI'] ); ?>">
									<?php echo JText::_('COM_NEWSLETTER_MAILINGLISTS_LOGIN_TO_UNSUBSCRIBE'); ?>
								</a>
							<?php else : ?>
								<span class="complete">
									<?php echo JText::sprintf('COM_NEWSLETTER_MAILINGLISTS_LOGGEDIN_AS', $this->juser->get('username')); ?>
								</span>
							<?php endif; ?>
						</li>
					</ol>
				<?php else : ?>
					<label><?php echo JText::_('COM_NEWSLETTER_UNSUBSCRIBE_REASON'); ?>
						<select name="reason" id="reason">
							<option value=""><?php echo JText::_('COM_NEWSLETTER_UNSUBSCRIBE_REASON_DEFAULT'); ?></option>
							<option value="Too many emails"><?php echo JText::_('COM_NEWSLETTER_UNSUBSCRIBE_REASON_TOOMANY'); ?></option>
							<option value="Content isn't relevant to me"><?php echo JText::_('COM_NEWSLETTER_UNSUBSCRIBE_REASON_NOTRELEVANT'); ?></option>
							<option value="I don't remember signing up"><?php echo JText::_('COM_NEWSLETTER_UNSUBSCRIBE_REASON_NOTSIGNEDUP'); ?></option>
							<option value="Privacy concerns"><?php echo JText::_('COM_NEWSLETTER_UNSUBSCRIBE_REASON_PRIVACY'); ?></option>
							<option value="Other"><?php echo JText::_('COM_NEWSLETTER_UNSUBSCRIBE_REASON_OTHER'); ?></option>
						</select>
					</label>

					<label>
						<textarea rows="4" name="reason-alt" id="reason-alt" placeholder="<?php echo JText::_('COM_NEWSLETTER_UNSUBSCRIBE_REASON_OTHER_OTHER'); ?>"></textarea>
					</label>
				<?php endif; ?>
			</fieldset>
			<?php if (!$this->juser->get('guest') || $this->mailinglist->id != '-1') : ?>
				<p class="submit">
					<input type="submit" class="btn btn-success" value="<?php echo JText::_('COM_NEWSLETTER_UNSUBSCRIBE'); ?>">
				</p>
			<?php endif; ?>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="dounsubscribe" />
		</form>
	</div>
</section>