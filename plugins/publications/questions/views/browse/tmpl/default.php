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
?>
<h3>
	<a name="questions"></a>
	<span><a href="/answers/question/new/?tag=<?php echo $this->filters['rawtag']; ?>" class="add"><?php echo JText::_('PLG_PUBLICATION_QUESTIONS_ASK_A_QUESTION_ABOUT_PUBLICATION'); ?></a></span>
	<?php echo JText::_('PLG_PUBLICATION_QUESTIONS_RECENT_QUESTIONS'); ?> 
</h3>
<?php
if ($this->rows) {
	ximport('Hubzero_Document');
	Hubzero_Document::addComponentStylesheet('com_answers');		
	
	/*if ($this->count > 0 && ($this->count > $this->limit)) {
		$tag = ($this->publication->type== 7) ? 'tool'.$this->publication->alias : 'publication'.$this->publication->id;
		$title .= ' (<a href="'.JRoute::_('index.php?option=com_answers&task=search&?tag='.$tag.'&sortby=withinplugin').'">'.JText::_('PLG_PUBLICATION_QUESTIONS_VIEW_ALL') .' '.$this->count.'</a>)';
	} else {
		$title .= ' ('.$this->count.')';
	}*/
?>
	<ul class="questions plugin">
<?php
	$i=1;
	$database =& JFactory::getDBO();

	require_once( JPATH_ROOT.DS.'components'.DS.'com_answers'.DS.'helpers'.DS.'tags.php' );
	$tagging = new AnswersTags( $database );
	
	// Check for abuse reports on an item
	$ra = new ReportAbuse( $database );
	
	foreach ($this->rows as $row) 
	{
		// Incoming
		$filters = array();
		$filters['id']  = $row->id;
		$filters['category']  = 'question';
		$filters['state']  = 0;
		
		$row->reports = $ra->getCount( $filters );		
		$row->created = Hubzero_View_Helper_Html::mkt($row->created);
		$row->when = Hubzero_View_Helper_Html::timeAgo($row->created);
		$row->points = $row->points ? $row->points : 0;
		
		if (!$row->reports && $i<= $this->limit) {
			$i++;	
			$link_on = JRoute::_('index.php?option=com_answers&task=question&id='.$row->id);

			$tags = $tagging->get_tag_cloud( 0, 0, $row->id );
			
			$alt_r = JText::sprintf('PLG_PUBLICATION_QUESTIONS_RESPONSES_TO_THIS_QUESTION', $row->rcount);
			$alt_v = JText::sprintf('PLG_PUBLICATION_QUESTIONS_RECOMMENDATION_AS_A_GOOD_QUESTION', $row->helpful);

			// author name
			$name = JText::_('PLG_PUBLICATION_QUESTIONS_ANONYMOUS');
			if ($row->anonymous == 0) {
				$juser =& JUser::getInstance( $row->created_by );
				if (is_object($juser)) {
					$name = $juser->get('name');
				} else {
					$name = JText::_('PLG_PUBLICATION_QUESTIONS_UNKNOWN');
				}
			}

			if ($row->question != '') {
				$fulltext = htmlspecialchars(Hubzero_View_Helper_Html::purifyText(stripslashes($row->question)));
			} else {
			 	$fulltext = stripslashes($row->subject);
			}
			
			$cls  = (isset($row->reward) && $row->reward == 1 && $this->banking) ? ' hasreward' : '';
			$cls .= ($row->state == 1) ? ' answered' : '';
?>
		<li class="reg<?php echo $cls; ?>">
			<div class="ensemble_left">
				<h4><a href="<?php echo $link_on; ?>" title="<?php echo $fulltext; ?>"><?php echo stripslashes($row->subject); ?></a></h4>
				<p class="supplemental"><?php echo JText::sprintf('PLG_PUBLICATION_QUESTIONS_ASKED_BY', $name); ?> - <?php echo JText::sprintf('PLG_PUBLICATION_QUESTIONS_TIME_AGO',$row->when); ?></p>
			</div>
			<div class="ensemble_right">
				<div class="statusupdate">
					<p>
						<?php echo $row->rcount; ?><span class="responses_<?php echo ($row->rcount == 0) ? 'no' : 'yes'; ?>"><a href="<?php echo $link_on; ?>#answers" title="<?php echo $alt_r; ?>">&nbsp;</a></span> 
						<?php echo $row->helpful; ?> <span class="votes_<?php echo ($row->helpful == 0) ? 'no' : 'yes'; ?>"><a href="<?php echo $link_on; ?>?vote=1" title="<?php echo $alt_v; ?>">&nbsp;</a></span>
					</p>
<?php if ($row->state==1) { ?>
					<span class="update_answered"><?php echo JText::_('PLG_PUBLICATION_QUESTIONS_ANSWERED'); ?></span>
<?php } ?>
				</div>
				<div class="rewardarea">
<?php if (isset($row->reward) && $row->reward == 1 && $this->banking) { ?>
					<p>+ <?php echo $row->points; ?> <a href="<?php echo $this->infolink; ?>" title="<?php echo JText::sprintf('PLG_PUBLICATION_QUESTIONS_THERE_IS_A_REWARD',$row->points); ?>">&nbsp;</a></p>
<?php } ?>
				</div>
			</div>
			<div style="clear:left">&nbsp;</div>
		</li>
<?php 	} else if ($row->reports && $i<= $this->limit) { ?>
		<li class="reg under_review">
			<h4 class="review"><?php echo JText::_('PLG_PUBLICATION_QUESTIONS_QUESTION_UNDER_REVIEW'); ?></h4>
			<p class="supplemental"><?php echo JText::sprintf('PLG_PUBLICATION_QUESTIONS_ASKED_BY', $name); ?> - <?php echo JText::sprintf('PLG_PUBLICATION_QUESTIONS_TIME_AGO',$row->when); ?></p>
		</li>
<?php
		}
	}
?>
	</ul>
<?php } else { ?>
	<p class="nocontent"><?php echo JText::_('PLG_PUBLICATION_QUESTIONS_NO_QUESTIONS_FOUND'); ?></p>
<?php } ?>