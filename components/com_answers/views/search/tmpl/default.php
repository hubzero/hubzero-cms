<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juser =& JFactory::getUser();
$jconfig =& JFactory::getConfig();
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div id="content-header-extra">
	<ul id="useroptions">
<?php if ($this->task != 'myquestions') { ?>
		<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=myquestions'); ?>" class="myquestions"><span><?php echo JText::_('COM_ANSWERS_MY_QUESTIONS'); ?></span></a></li>
<?php } ?>
		<li class="last"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=new'); ?>" class="add"><span><?php echo JText::_('COM_ANSWERS_NEW_QUESTION'); ?></span></a></li>
	</ul>
</div><!-- / #content-header-extra -->
<div class="clear"></div>

<?php if (!$juser->get('guest') && $this->task == 'myquestions') { ?>
<ul class="breadcrumbtrail">
	<li class="first"><span class="myquestions"></span></li>
	<li class="first">
		<?php 
		echo ($this->filters['interest'] == 0 && $this->filters['assigned'] == 0) ? '<strong>' : '<a href="'.JRoute::_('index.php?option='.$this->option.'&task=myquestions').'">';
		echo JText::_('COM_ANSWERS_QUESTIONS_I_ASKED');
		echo ($this->filters['interest'] == 0 && $this->filters['assigned'] == 0) ? '</strong>' : '</a>';
		?>
	</li>
	<li>
		<?php
		echo ($this->filters['assigned'] == 1) ? '<strong>' : '<a href="'.JRoute::_('index.php?option='.$this->option.'&task=myquestions').'?assigned=1">';
		echo JText::_('COM_ANSWERS_QUESTIONS_RELATED_TO_CONTRIBUTIONS');
		echo ($this->filters['assigned'] == 1) ? '</strong>' : '</a>';
		?>
	</li>
	<li>
		<?php
	 	echo ($this->filters['interest'] == 1) ? '<strong>' : '<a href="'.JRoute::_('index.php?option='.$this->option.'&task=myquestions').'?interest=1">';
		echo JText::_('COM_ANSWERS_QUESTIONS_TAGGED_WITH_MY_INTERESTS');
		echo ($this->filters['interest'] == 1) ? '</strong>' : '</a>';
		?>
	</li>
</ul>
<?php } ?>

<div class="main section">
<form method="get" action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" id="adminForm">
	<div class="aside">
<?php if ($this->task != 'myquestions') { ?>
		<fieldset>
			<label>
				<?php echo JText::_('COM_ANSWERS_FIND_PHRASE'); ?>
				<input type="text" name="q" value="<?php echo $this->filters['q']; ?>" />
			</label>
			
			<label class="tagdisplay">
				<?php echo JText::_('COM_ANSWERS_AND_OR_TAG');
JPluginHelper::importPlugin( 'tageditor' );
$dispatcher =& JDispatcher::getInstance();	
$tf = $dispatcher->trigger( 'onTagsEdit', array(array('tags','actags','',$this->filters['tag'],'')) );
		
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
<?php } ?>
		<p><?php echo JText::_('COM_ANSWERS_CANT_FIND_ANSWER'); ?> <a href="<?php echo JRoute::_('index.php?option=com_kb'); ?>"><?php echo JText::_('COM_ANSWERS_KNOWLEDGE_BASE'); ?></a> <?php echo JText::_('COM_ANSWERS_OR_BY').' '.JText::_('COM_ANSWERS_SEARCH').'? '.JText::_('COM_ANSWERS_ASK_YOUR_FELLOW').' '.$jconfig->getValue('config.sitename').' '.JText::_('COM_ANSWERS_MEMBERS'); ?>!</p>
<?php if ($this->banking) { ?>
		<p><?php echo JText::_('COM_ANSWERS_START_EARNING').' '.$jconfig->getValue('config.sitename').' '.JText::_('COM_ANSWERS_COMMUNITY'); ?> <a href="<?php echo $this->infolink; ?>"><?php echo JText::_('COM_ANSWERS_EARN_MORE'); ?></a>.</p>
<?php } ?>		
	</div><!-- / .aside -->
	<div class="subject">
		<h3><?php echo JText::_('COM_ANSWERS_LATEST_QUESTIONS'); ?></h3>
<?php if (count($this->results) > 0) { ?>
		<ul class="questions plugin">
<?php 
		//$i = 1;
		foreach ($this->results as $row) 
		{
			$row->reports = (isset($row->reports)) ? $row->reports : 0;	
			$row->points = $row->points ? $row->points : 0;
			
			if (!$row->reports) {
				// author name
				$name = JText::_('COM_ANSWERS_ANONYMOUS');
				if ($row->anonymous == 0) {
					$user =& JUser::getInstance( $row->created_by );
					if (is_object($user)) {
						$name = $user->get('name');
					} else {
						$name = JText::_('COM_ANSWERS_UNKNOWN');
					}
				}
				
				$cls  = (isset($row->reward) && $row->reward == 1 && $this->banking) ? ' hasreward' : '';
				$cls .= ($row->state == 1) ? ' answered' : '';
				
				if ($row->question != '') {
					$row->question = stripslashes($row->question);
					$fulltext = htmlspecialchars(Hubzero_View_Helper_Html::purifyText($row->question));
				} else {
				 	$fulltext = stripslashes($row->subject);
				}
?>
			<li class="reg<?php echo $cls; ?>">
				<div class="ensemble_left">
					<h4><a href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id='.$row->id); ?>" title="<?php echo $fulltext; ?>"><?php echo stripslashes($row->subject); ?></a></h4>
					<p class="supplemental"><?php echo JText::sprintf('COM_ANSWERS_ASKED_BY', $name).' - '.JText::sprintf('COM_ANSWERS_TIME_AGO', $row->when); ?></p>
				</div>
				<div class="ensemble_right">
					<div class="statusupdate">
						<p>
							<?php echo $row->rcount; ?> <span class="responses_<?php echo ($row->rcount == 0) ? 'no' : 'yes'; ?>"><a href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id='.$row->id.'#answers'); ?>" title="<?php echo JText::sprintf('COM_ANSWERS_RESPONSES_TO_THIS_QUESTION', $row->rcount); ?>">&nbsp;</a></span> 
							<?php echo $row->helpful; ?> <span class="votes_<?php echo ($row->helpful == 0) ? 'no' : 'yes'; ?>"><a href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id='.$row->id.'&vote=1'); ?>" title="<?php echo JText::sprintf('COM_ANSWERS_RECOMMENDATIONS_AS_A_GOOD_QUESTION', $row->helpful); ?>">&nbsp;</a></span>
						</p>
						<?php echo ($row->state==1) ? '<span class="update_answered">'.JText::_('COM_ANSWERS_ANSWERED').'</span>' : ''; ?>
					</div>
					<div class="rewardarea">
<?php if (isset($row->reward) && $row->reward == 1 && $this->banking) { ?>
						<p>+ <?php echo $row->points; ?> <a href="<?php echo $this->infolink; ?>" title="<?php echo JText::sprintf('COM_ANSWERS_THERE_IS_A_REWARD_FOR_ANSWERING', $row->points); ?>">&nbsp;</a></p>
<?php } ?>
					</div>
				</div>
				<div style="clear:left"></div>
				&nbsp;
			</li>
<?php
			} else if ($row->reports) {
?>
			<li class="reg under_review">
				<h4 class="review"><?php echo JText::_('COM_ANSWERS_QUESTION_UNDER_REVIEW'); ?></h4>
				<p class="supplemental"><?php echo JText::sprintf('COM_ANSWERS_ASKED_BY', $name).' - '.JText::sprintf('COM_ANSWERS_TIME_AGO',$row->when); ?></p>
				&nbsp; 
			</li>
<?php
			}
		}
?>
		</ul>
		<?php echo $this->pageNav->getListFooter(); ?>
<?php } else { ?>
	<?php if(($filters['filterby']=="all" or !$filters['filterby']) && !$filters['tag'] && !$filters['q']) { ?>
		<p class="noresults"><?php echo JText::_('COM_ANSWERS_NO_RESULTS'); ?></p>
	<?php } else { ?>
		<p class="noresults"><?php echo JText::_('There are currently no questions based on your search selection.'); ?></p>
		<p class="nav_questions"><a href="<?php echo JRoute::_('index.php?option='.$this->option); ?>"><?php echo JText::_('View all questions'); ?></a></p>	
	<?php } ?>
<?php } ?>
	</div><!-- / .subject -->
</form>
</div><!-- / .main section -->
