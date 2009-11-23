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
<?php if ($modtoollist->fav || $modtoollist->no_html) { ?>
	<?php echo $modtoollist->buildList($modtoollist->favtools, 'fav'); ?>
	<p><?php echo JText::_('MOD_MYTOOLS_EXPLANATION'); ?></p>
<?php } else { ?>
<div id="myToolsTabs">
	<ul class="tab_titles">
		<li title="recenttools" class="active"><?php echo JText::_('MOD_MYTOOLS_RECENT'); ?></li>
		<li title="favtools"><?php echo JText::_('MOD_MYTOOLS_FAVORITES'); ?></li>
		<li title="alltools"><?php echo JText::_('MOD_MYTOOLS_ALL_TOOLS'); ?></li>
	</ul>
	
	<div id="recenttools" class="tab_panel active">
		<?php 
		$r = $modtoollist->rectools;
		echo $modtoollist->buildList($r, 'recent'); ?>
		<p><?php echo JText::_('MOD_MYTOOLS_RECENT_EXPLANATION'); ?></p>
	</div>
	
	<div id="favtools" class="tab_panel">
		<?php 
		$f = $modtoollist->favtools;
		echo $modtoollist->buildList($f, 'favs'); ?>
		<p><?php echo JText::_('MOD_MYTOOLS_FAVORITES_EXPLANATION'); ?></p>
	</div>
	
	<div id="alltools" class="tab_panel">
		<?php 
		$a = $modtoollist->alltools;
		echo $modtoollist->buildList($a, 'all'); ?>
		<p><?php echo JText::_('MOD_MYTOOLS_ALL_TOOLS_EXPLANATION'); ?></p>
	</div>
</div>
<?php } ?>