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

if ($this->publication->alias) {
	$url = 'index.php?option='.$this->option.'&alias='.$this->publication->alias.'&active=usage';
} else {
	$url = 'index.php?option='.$this->option.'&id='.$this->publication->id.'&active=usage';
}

$database = JFactory::getDBO();

?>
<h3>
	<a name="usage"></a>
	<?php echo JText::_('PLG_PUBLICATION_USAGE'); ?>
</h3>
<div id="sub-sub-menu">
	<ul>
		<li<?php if ($this->period == '14') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_($url.'&period=14&dthis='.$this->dthis); ?>"><span><?php echo JText::_('PLG_PUBLICATION_USAGE_PERIOD_OVERALL'); ?></span></a></li>
		<li<?php if ($this->period == 'prior12' || $this->period == '12') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_($url.'&period=12&dthis='.$this->dthis); ?>"><span><?php echo JText::_('PLG_PUBLICATION_USAGE_PERIOD_PRIOR12'); ?></span></a></li>
		<li<?php if ($this->period == 'month' || $this->period == '1') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_($url.'&period=1&dthis='.$this->dthis); ?>"><span><?php echo JText::_('PLG_PUBLICATION_USAGE_PERIOD_MONTH'); ?></span></a></li>
		<li<?php if ($this->period == 'qtr' || $this->period == '3') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_($url.'&period=3&dthis='.$this->dthis); ?>"><span><?php echo JText::_('PLG_PUBLICATION_USAGE_PERIOD_QTR'); ?></span></a></li>
		<li<?php if ($this->period == 'year' || $this->period == '0') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_($url.'&period=0&dthis='.$this->dthis); ?>"><span><?php echo JText::_('PLG_PUBLICATION_USAGE_PERIOD_YEAR'); ?></span></a></li>
		<li<?php if ($this->period == 'fiscal' || $this->period == '13') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_($url.'&period=13&dthis='.$this->dthis); ?>"><span><?php echo JText::_('PLG_PUBLICATION_USAGE_PERIOD_FISCAL'); ?></span></a></li>
	</ul>
</div>
<form method="get" action="<?php echo JRoute::_($url); ?>">

</form>
