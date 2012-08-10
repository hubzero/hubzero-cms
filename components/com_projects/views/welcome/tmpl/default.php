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
$suggestions = $this->suggestions;

$max_s = 4;
$actual_s = count($suggestions) >= $max_s ? $max_s : count($suggestions);
switch ($actual_s) 
{
	case 2: 
	default: $numcols = 'two'; break;			
	case 3:	$numcols = 'three'; break;
	case 4:	$numcols = 'four'; break;
}
?>
<?php if($actual_s > 1) { ?>
	<div class="welcome">
		<p class="closethis"><a href="<?php echo JRoute::_('index.php?option=' . $this->option 
		. a . 'alias=' . $this->project->alias . a . 'active=feed') . '?c=1'; ?>"><?php echo JText::_('COM_PROJECTS_PROJECT_CLOSE_THIS'); ?></a></p>
		<h3><?php echo $this->creator ? JText::_('COM_PROJECTS_WELCOME_TO_PROJECT_CREATOR') : JText::_('COM_PROJECTS_WELCOME_TO').' '.stripslashes($this->project->title).' '.JText::_('COM_PROJECTS_PROJECT').'!'; ?> </h3>
		<p><?php echo $this->creator ? JText::_('COM_PROJECTS_WELCOME_SUGGESTIONS_CREATOR') : JText::_('COM_PROJECTS_WELCOME_SUGGESTIONS'); ?></p>
		<div id="suggestions">
			<div class="columns <?php echo $numcols; ?> first">
				<p class="<?php echo $suggestions[0]['class']; ?>"><a href="<?php echo $suggestions[0]['url']; ?>"><?php echo $suggestions[0]['text']; ?></a></p>
			</div>
			<div class="columns <?php echo $numcols; ?> second">
				<p class="<?php echo $suggestions[1]['class']; ?>"><a href="<?php echo $suggestions[1]['url']; ?>"><?php echo $suggestions[1]['text']; ?></a></p>
			</div>
			<?php if($actual_s > 2) { ?>
			<div class="columns <?php echo $numcols; ?> third">
				<p class="<?php echo $suggestions[2]['class']; ?>"><a href="<?php echo $suggestions[2]['url']; ?>"><?php echo $suggestions[2]['text']; ?></a></p>
			</div>
			<?php } ?>
			<?php if($actual_s > 3) { ?>
			<div class="columns <?php echo $numcols; ?> forth">
				<p class="<?php echo $suggestions[3]['class']; ?>"><a href="<?php echo $suggestions[3]['url']; ?>"><?php echo $suggestions[3]['text']; ?></a></p>
			</div>
			<?php } ?>
		</div>
	</div>
<?php  } ?>
