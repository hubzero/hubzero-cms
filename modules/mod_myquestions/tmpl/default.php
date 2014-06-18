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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$juser = JFactory::getUser();
?>
	<h4>
		<?php echo JText::_('MOD_MYQUESTIONS_OPEN_QUESTIONS'); ?>
		<small><a href="<?php echo JRoute::_('index.php?option=com_answers&task=search&area=mine&filterby=open'); ?>"><?php echo JText::_('MOD_MYQUESTIONS_VIEW_ALL'); ?></a></small>
	</h4>
<?php if ($this->openquestions) { ?>
	<ul class="compactlist">
	<?php
	for ($i=0; $i < count($this->openquestions); $i++)
	{
		if ($i < $this->limit_mine)
		{
			$rcount = $this->openquestions[$i]->get('rcount', 0);
			$rclass = ($rcount > 0) ?  'yes' : 'no';
			?>
			<li class="question">
				<a href="<?php echo JRoute::_($this->openquestions[$i]->link()); ?>">
					<?php echo $this->escape(\Hubzero\Utility\String::truncate($this->openquestions[$i]->subject('clean'), 60)); ?>
				</a>
				<span><span class="responses_<?php echo $rclass; ?>"><?php echo $rcount; ?></span></span>

			<?php if ($rcount > 0 && $this->banking) { ?>
				<p class="earnpoints"><?php echo JText::_('MOD_MYQUESTIONS_CLOSE_THIS_QUESTION') . ' ' . $this->escape($this->openquestions[$i]->get('maxaward', 0)) . ' ' . JText::_('MOD_MYQUESTIONS_POINTS'); ?></p>
			<?php } ?>
			</li>
			<?php
		}
	}
	?>
	</ul>
<?php } else { ?>
	<p><?php echo JText::_('MOD_MYQUESTIONS_NO_QUESTIONS'); ?></p>
<?php } ?>
	<ul class="module-nav">
		<li><a href="<?php echo JRoute::_('index.php?option=com_answers&task=new'); ?>"><?php echo JText::_('MOD_MYQUESTIONS_ADD_QUESTION'); ?></a></li>
	</ul>

<?php if ($this->show_assigned) { // Questions related to my contributions ?>
	<h4>
		<?php echo JText::_('MOD_MYQUESTIONS_OPEN_QUESTIONS_ON_CONTRIBUTIONS'); ?>
		<small><a href="<?php echo JRoute::_('index.php?option=com_answers&task=search&area=assigned&filterby=open'); ?>"><?php echo JText::_('MOD_MYQUESTIONS_VIEW_ALL'); ?></a></small>
	</h4>
	<?php if ($this->assigned) { ?>
		<p class="incentive"><span><?php echo strtolower(JText::_('MOD_MYQUESTIONS_BEST_ANSWER_MAY_EARN')); ?></span></p>
		<ul class="compactlist">
		<?php
		for ($i=0; $i < count($this->assigned); $i++)
		{
			if ($i < $this->limit_assigned)
			{
				?>
				<li class="question">
					<a href="<?php echo JRoute::_($this->assigned[$i]->link()); ?>">
						<?php echo $this->escape(\Hubzero\Utility\String::truncate($this->assigned[$i]->subject('clean'), 60)); ?>
					</a>
				<?php if ($this->banking) { ?>
					<span ><span class="pts"><?php echo $this->escape($this->assigned[$i]->get('maxaward', 0)) . ' ' . strtolower(JText::_('MOD_MYQUESTIONS_PTS')); ?></span></span>
				<?php } ?>
				</li>
				<?php
			}
		}
		?>
		</ul>
	<?php } else { ?>
		<p><?php echo JText::_('MOD_MYQUESTIONS_NO_QUESTIONS'); ?></p>
	<?php } ?>
<?php } ?>

<?php if ($this->show_interests) { // Questions of interest ?>
	<h4>
		<?php echo JText::_('MOD_MYQUESTIONS_QUESTIONS_TO_ANSWER'); ?>
		<small><a href="<?php echo JRoute::_('index.php?option=com_answers&task=search&area=interest&filterby=open'); ?>"><?php echo JText::_('MOD_MYQUESTIONS_VIEW_ALL'); ?></a></small>
	</h4>
	<p class="category-header-details">
	<?php if ($this->interests) { ?>
		<span class="configure">[<a href="<?php echo JRoute::_('index.php?option=com_members&task=edit&id=' . $juser->get('id')); ?>"><?php echo JText::_('MOD_MYQUESTIONS_EDIT'); ?></a>]</span>
	<?php } else { ?>
		<span class="configure">[<a href="<?php echo JRoute::_('index.php?option=com_members&task=edit&id=' . $juser->get('id')); ?>"><?php echo JText::_('MOD_MYQUESTIONS_ADD_INTERESTS'); ?></a>]</span>
	<?php } ?>
		<span class="q"><?php echo JText::_('MOD_MYQUESTIONS_MY_INTERESTS') . ': ' . $this->intext; ?></span>
	</p>
	<?php if ($this->otherquestions) { ?>
		<p class="incentive"><span><?php echo strtolower(JText::_('MOD_MYQUESTIONS_BEST_ANSWER_MAY_EARN')); ?></span></p>
		<ul class="compactlist">
		<?php
		for ($i=0; $i < count($this->otherquestions); $i++)
		{
			if ($i < $this->limit_interest)
			{
				?>
				<li class="question">
					<a href="<?php echo JRoute::_($this->otherquestions[$i]->link()); ?>">
						<?php echo $this->escape(\Hubzero\Utility\String::truncate($this->otherquestions[$i]->subject('clean'), 60)); ?>
					</a>
				<?php if ($this->banking) { ?>
					<span><span class="pts"><?php echo $this->escape($this->otherquestions[$i]->get('maxaward', 0)) . ' ' . strtolower(JText::_('MOD_MYQUESTIONS_PTS')); ?></span></span>
				<?php } ?>
				</li>
				<?php
			}
		}
		?>
		</ul>
	<?php } else { ?>
		<p><?php echo JText::_('MOD_MYQUESTIONS_NO_QUESTIONS'); ?></p>
	<?php } ?>
	<ul class="module-nav">
		<li><a href="<?php echo JRoute::_('index.php?option=com_answers&task=search&filterby=open'); ?>"><?php echo JText::_('MOD_MYQUESTIONS_ALL_OPEN_QUESTIONS'); ?></a></li>
	</ul>
<?php } ?>
