<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

// Get blocks		
$blocks = $this->pub->_curationModel->_blocks;

$history = $this->pub->_curationModel->getHistory($this->pub, 1);

//print_r($history);

?>
<div id="abox-content" class="history-wrap">
	<h3><?php echo JText::_('COM_PUBLICATIONS_CURATION_HISTORY_VIEW'); ?></h3>
	
	<div class="curation-history">
		<div class="pubtitle">
			<p><span class="restype indlist"><?php echo $typetitle; ?></span> <?php echo \Hubzero\Utility\String::truncate($this->pub->title, 65); ?> | <?php echo JText::_('COM_PUBLICATIONS_CURATION_VERSION') 
			. ' ' . $this->pub->version_label; ?>
			</p>
		</div>	
	<?php if ($history) { $i = 1; ?>
		<h5><?php echo JText::_('COM_PUBLICATIONS_CURATION_HISTORY_EVENTS'); ?></h5>
		<div class="history-blocks">
	<?php 
		foreach ($history as $event)
		{ 
			$author  = JUser::getInstance($event->created_by);
			$trClass = $i % 2 == 0 ? ' even' : ' odd';
			$i++;
			?>
			<div class="history-block <?php echo $trClass; ?>">
				<div class="changelog-time columns four first">
					<?php echo $event->created; ?>
					<span class="block"><?php echo $this->escape(stripslashes($author->get('name'))); ?></span>
					<span class="block">(
					<?php echo ($event->curator) 
						? JText::_('COM_PUBLICATIONS_CURATION_CURATOR') 
						: JText::_('COM_PUBLICATIONS_CURATION_AUTHOR');  ?>
					)</span>
				</div>
				<div class="changelog-text columns four second third fourth"><?php echo $event->changelog; ?></div>
				<div class="clear"></div>
			</div>
		<?php } ?>
		</div>
	<?php  } else { ?>
		<p class="warning"><?php echo JText::_('COM_PUBLICATIONS_CURATION_HISTORY_NOTHING'); ?></p>
	<?php } ?>
	</div>
</div>
