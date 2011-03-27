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

$rows = $modpopularquestions->rows;
if (count($rows) > 0) {
?>
	<ul class="questions">
<?php 
	require_once( JPATH_ROOT.DS.'components'.DS.'com_answers'.DS.'helpers'.DS.'tags.php' );
	$database =& JFactory::getDBO();
	$tagging = new AnswersTags( $database );

	ximport('Hubzero_View_Helper_Html');

	foreach ($rows as $row) 
	{
		$name = JText::_('MOD_POPULARQUESTIONS_ANONYMOUS');
		if ($row->anonymous == 0) {
			$juser =& JUser::getInstance( $row->created_by );
			if (is_object($juser)) {
				$name = $juser->get('name');
			}
		}
		
		//$when = $modpopularquestions->timeAgo( $modpopularquestions->mkt($row->created) );
		
		$tags = $tagging->get_tags_on_object($row->id, 0, 0, 0);
?>
		<li>
<?php if ($modpopularquestions->style == 'compact') { ?>
			<a href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id='.$row->id); ?>"><?php echo $row->subject; ?></a>
<?php } else { ?>
			<h4><a href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id='.$row->id); ?>"><?php echo $row->subject; ?></a></h4>
<?php /*			<p class="snippet">
				<?php echo Hubzero_View_Helper_Html::shortenText($row->question, 100, 0); ?>
			</p> */ ?>
			<p class="entry-details">
				<?php echo JText::sprintf('MOD_POPULARQUESTIONS_ASKED_BY', $name); ?> @ 
				<span class="entry-time"><?php echo JHTML::_('date',$row->created, '%I:%M %p', 0); ?></span> on 
				<span class="entry-date"><?php echo JHTML::_('date',$row->created, '%d %b %Y', 0); ?></span>
				<span class="entry-details-divider">&bull;</span>
				<span class="entry-comments">
					<a href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id='.$row->id.'#answers'); ?>" title="<?php echo JText::sprintf('MOD_RECENTQUESTIONS_RESPONSES', $row->rcount); ?>">
						<?php echo $row->rcount; ?>
					</a>
				</span>
			</p>
			<p class="entry-tags"><?php echo JText::_('MOD_POPULARQUESTIONS_TAGS'); ?>:</p> 
			<?php
			if (count($tags) > 0) {
				$tagarray = array();
				$tagarray[] = '<ol class="tags">';
				foreach ($tags as $tag)
				{
					$tag->raw_tag = str_replace( '&amp;', '&', $tag['raw_tag'] );
					$tag->raw_tag = str_replace( '&', '&amp;', $tag['raw_tag'] );
					$tagarray[] = ' <li><a href="'.JRoute::_('index.php?option=com_answers&task=tag&tag='.$tag['tag']).'" rel="tag">'.$tag['raw_tag'].'</a></li>';
				}
				$tagarray[] = '</ol>';

				echo implode( "\n", $tagarray );
			} else {
				echo '&nbsp;';
			}
			?>
<?php } ?>
		</li>
<?php
	}
?>
	</ul>
<?php } else { ?>
	<p><?php echo JText::_('MOD_POPULARQUESTIONS_NO_RESULTS'); ?></p>
<?php } ?>