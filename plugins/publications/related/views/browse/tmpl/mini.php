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

// Add stylesheet
$document = JFactory::getDocument();
$document->addStyleSheet('plugins' . DS . 'publications' . DS
	. 'related' . DS . 'assets' . DS . 'css' . DS . 'related.css');

?>
<div id="whatsrelated">
	<h3><?php echo JText::_('PLG_PUBLICATION_RELATED_HEADER'); ?></h3>
<?php if ($this->related) { ?>
	<ul>
<?php
	foreach ($this->related as $line)
	{
		if ($line->section != 'Topic') {
			// Get the SEF for the resource
			if ($line->alias) {
				$sef = JRoute::_('index.php?option='.$this->option.'&alias='. $line->alias);
			} else {
				$sef = JRoute::_('index.php?option='.$this->option.'&id='. $line->id);
			}
		} else {
			if ($line->group != '' && $line->scope != '') {
				$sef = JRoute::_('index.php?option=com_groups&scope='.$line->scope.'&pagename='.$line->alias);
			} else {
				$sef = JRoute::_('index.php?option=com_topics&scope='.$line->scope.'&pagename='.$line->alias);
			}
		}
?>
		<li class="<?php echo $line->class; ?>">
		 	<?php echo ($line->section == 'Series') ? JText::_('PLG_PUBLICATION_RELATED_PART_OF').' ' : ''; ?>
			<a href="<?php echo $sef; ?>"><?php echo stripslashes($line->title); ?></a>
		</li>
<?php } ?>
	</ul>
<?php } else { ?>
	<p><?php echo JText::_('PLG_PUBLICATION_RELATED_NO_RESULTS_FOUND'); ?></p>
<?php } ?>
</div>