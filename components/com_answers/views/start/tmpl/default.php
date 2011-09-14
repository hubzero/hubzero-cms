<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div id="content-header-extra">
	<ul id="useroptions">
		<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=myquestions'); ?>" class="myquestions"><span><?php echo JText::_('COM_ANSWERS_MY_QUESTIONS'); ?></span></a></li>
		<li class="last"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=new'); ?>" class="add"><span><?php echo JText::_('COM_ANSWERS_NEW_QUESTION'); ?></span></a></li>
	</ul>
</div><!-- / #content-header-extra -->
<div class="clear"></div>

<div class="main section">
<form method="get" action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" id="adminForm">
	<div class="aside">
		<fieldset>
			<label>
				<?php echo JText::_('COM_ANSWERS_FIND_PHRASE'); ?>
				<input type="text" name="q" value="<?php echo $this->filters['q']; ?>" />
			</label>
			
			<label class="tagdisplay">
				<?php echo JText::_('COM_ANSWERS_AND_OR_TAG');
JPluginHelper::importPlugin( 'hubzero' );
$dispatcher =& JDispatcher::getInstance();
$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','',$this->filters['tag'])) );

if (count($tf) > 0) {
	echo $tf[0];
} else { ?>
				<input type="text" name="tag" id="tags-men" value="<?php echo $this->filters['tag']; ?>" />
<?php } ?>
			</label>
			
			<label>
				<?php echo JText::_('COM_ANSWERS_IN'); ?>
				<select name="filterby">
					<option value="all"<?php echo ($this->filters['filterby'] == 'all') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_ANSWERS_ALL_QUESTIONS'); ?></option>
					<option value="open"<?php echo ($this->filters['filterby'] == 'open') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_ANSWERS_OPEN_QUESTIONS'); ?></option>
					<option value="closed"<?php echo ($this->filters['filterby'] == 'closed') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_ANSWERS_CLOSED_QUESTIONS'); ?></option>
<?php if ($this->task != 'myquestions') { ?>
					<option value="mine"<?php echo ($this->filters['filterby'] == 'mine') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_ANSWERS_MY_QUESTIONS'); ?></option>
<?php } ?>
				</select>
			</label>
			
			<label>
				<?php echo JText::_('COM_ANSWERS_SORTBY'); ?>
				<select name="sortby">
<?php if ($this->banking) { ?>
					<option value="rewards"<?php echo ($this->filters['sortby'] == 'rewards') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_ANSWERS_REWARDS'); ?></option>
<?php } else { ?>
					<option value="rewards"<?php echo ($this->filters['sortby'] == 'recent') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_ANSWERS_MOST_RECENT'); ?></option>
<?php } ?>
					<option value="votes"<?php echo ($this->filters['sortby'] == 'votes') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_ANSWERS_RECOMMENDATIONS'); ?></option>
					<option value="status"<?php echo ($this->filters['sortby'] == 'status') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_ANSWERS_OPEN_CLOSED'); ?></option>
					<option value="responses"<?php echo ($this->filters['sortby'] == 'responses') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_ANSWERS_NUMBER_OF_RESPONSES'); ?></option>
					<option value="date"<?php echo ($this->filters['sortby'] == 'date') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_ANSWERS_DATE'); ?></option>
				</select>
			</label>
		
<?php if (isset($this->filters['interest'])) { ?>
			<input type="hidden" name="interest" value="<?php echo $this->filters['interest']; ?>" />
			<input type="hidden" name="assigned" value="<?php echo $this->filters['assigned']; ?>" />
<?php } ?>
		
			<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
			<input type="submit" value="<?php echo JText::_('COM_ANSWERS_GO'); ?>" />
		</fieldset>
		
		<p><?php echo JText::_('COM_ANSWERS_CANT_FIND_ANSWER'); ?> <a href="<?php echo JRoute::_('index.php?option=com_kb'); ?>"><?php echo JText::_('COM_ANSWERS_KNOWLEDGE_BASE'); ?></a> <?php echo JText::_('COM_ANSWERS_OR_BY').' '.JText::_('COM_ANSWERS_SEARCH').'? '.JText::_('COM_ANSWERS_ASK_YOUR_FELLOW').' '.$hubShortName.' '.JText::_('COM_ANSWERS_MEMBERS'); ?>!</p>
<?php if ($this->banking) { ?>
		<p><?php echo JText::_('COM_ANSWERS_START_EARNING').' '.$hubShortName.' '.JText::_('COM_ANSWERS_COMMUNITY'); ?> <a href="<?php echo $this->infolink; ?>"><?php echo JText::_('COM_ANSWERS_EARN_MORE'); ?></a>.</p>
<?php } ?>		
	</div><!-- / .aside -->
	<div class="subject">
		<h3><?php echo JText::_('COM_ANSWERS_LATEST_QUESTIONS'); ?></h3>
		
		<?php echo $pageNav->getListFooter(); ?>
	</div><!-- / .subject -->
</form>
</div><!-- / .main section -->
