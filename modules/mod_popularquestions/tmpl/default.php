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

$rows = $modpopularquestions->rows;
if (count($rows) > 0) {
?>
	<ul class="questions">
<?php 
	require_once( JPATH_ROOT.DS.'components'.DS.'com_answers'.DS.'answers.tags.php' );
	$database =& JFactory::getDBO();
	$tagging = new AnswersTags( $database );

	ximport('Hubzero_View_Helper_Html');

	foreach ($rows as $row) 
	{
		$name = JText::_('MOD_POPULARQUESTIONS_ANONYMOUS');
		if ($row->anonymous == 0) {
			$xuser =& XUser::getInstance( $row->created_by );
			if (is_object($xuser)) {
				$name = $xuser->get('name');
			}
		}
		
		$when = $modpopularquestions->timeAgo( $modpopularquestions->mkt($row->created) );
		
		$tags = $tagging->get_tags_on_object($row->id, 0, 0, 0);
?>
		<li>
<?php if ($modpopularquestions->style == 'compact') { ?>
			<a href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id='.$row->id); ?>"><?php echo $row->subject; ?></a>
<?php } else { ?>
			<h4><a href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id='.$row->id); ?>"><?php echo $row->subject; ?></a></h4>
			<p class="snippet">';
				<?php echo Hubzero_View_Helper_Html::shortenText($row->question, 100, 0); ?>
			</p>
			<p><?php echo JText::sprintf('MOD_POPULARQUESTIONS_ASKED_BY', $name).' - '.$when.' '.JText::_('MOD_POPULARQUESTIONS_AGO'); ?></p>
			<p><?php echo JText::_('MOD_POPULARQUESTIONS_TAGS'); ?>:</p> 
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