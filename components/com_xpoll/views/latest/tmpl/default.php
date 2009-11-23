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

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div id="content-header" class="full">
	<h2><?php echo JText::_('COM_XPOLL').': '.JText::_('COM_XPOLL_LATEST'); ?></h2>
</div>

<div class="main section">
<?php if (count($this->options) > 0) { ?>
	<form id="pollform" method="post" action="<?php echo JRoute::_('index.php?option='.$this->option); ?>">
		<h3><?php echo stripslashes($this->poll->title); ?></h3>
		<ul class="poll">
<?php
		for ($i=0, $n=count( $this->options ); $i < $n; $i++) 
		{ 
?>
			<li>
				<input type="radio" name="voteid" id="voteid<?php echo $this->options[$i]->id; ?>" value="<?php echo $this->options[$i]->id; ?>" alt="<?php echo $this->options[$i]->id; ?>" />
				<label for="voteid<?php echo $this->options[$i]->id; ?>"><?php echo $this->options[$i]->text; ?></label>
			</li>
<?php
		}
?>
		</ul>
		<p>
			<input type="submit" name="task_button" value="<?php echo JText::_('COM_XPOLL_BUTTON_VOTE'); ?>" />&nbsp;&nbsp;
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=view&id='.$this->poll->id); ?>"><?php echo JText::_('COM_XPOLL_BUTTON_RESULTS'); ?></a>
		</p>
		<input type="hidden" name="id" value="<?php echo $this->poll->id; ?>" />
		<input type="hidden" name="task" value="vote" />
	</form>
<?php } else { ?>
	<p><?php echo JText::_('COM_XPOLL_NO_POLL'); ?></p>
<?php } ?>
</div><!-- / .main section -->