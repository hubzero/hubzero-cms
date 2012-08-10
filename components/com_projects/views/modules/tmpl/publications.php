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
<div class="sidebox">
		<h4 class="publications"><a href="<?php echo JRoute::_('index.php?option='.$this->option . a . 'alias=' . $this->project->alias . a . 'active=publications'); ?>" class="hlink" title="<?php echo JText::_('COM_PROJECTS_VIEW') . ' ' . strtolower(JText::_('COM_PROJECTS_PROJECT')) . ' ' . strtolower(JText::_('COM_PROJECTS_TAB_PUBLICATIONS')); ?>"><?php echo ucfirst(JText::_('COM_PROJECTS_TAB_PUBLICATIONS')); ?></a>
<?php if (count($this->items) > 0) { ?>
	<span><a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'alias=' . $this->project->alias . a . 'active=publications'); ?>"><?php echo ucfirst(JText::_('COM_PROJECTS_SEE_ALL')); ?> </a></span>
<?php } ?>
</h4>
<?php if (count($this->items) == 0) { ?>
	<div class="noitems">
		<p><?php echo JText::_('COM_PROJECTS_NO_PUBLICATIONS'); ?></p>
		<p class="addnew"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'alias=' . $this->project->alias . a . 'active=publications') . '?action=start'; ?>"><?php echo JText::_('COM_PROJECTS_ADD_PUBLICATION'); ?></a></p>
	</div>
<?php } else { ?>
	<ul>
		<?php foreach($this->items as $pub) { 
			$status = PublicationHelper::getPubStateProperty($pub, 'status', 0);					
		?>
	<li>
		 <a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'alias=' . $this->project->alias . a . 'active=publications') . '/?pid=' . $pub->id; ?>" title="<?php echo $pub->title; ?>">
		<?php echo Hubzero_View_Helper_Html::shortenText($pub->title, 50, 0); ?></a>
		 <span class="block faded mini">
			<span>v. <?php echo $pub->version_label; ?> (<?php echo $status; ?>)</span>
		</span>
	   </li><?php } ?>
	</ul><?php } ?>
</div>