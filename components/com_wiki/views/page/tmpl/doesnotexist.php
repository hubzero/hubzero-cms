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

$templates = $this->page->getTemplates();

if ($this->sub) {
	$hid = 'sub-content-header';
	$sid = 'sub-section-menu';
} else {
	$hid = 'content-header';
	$sid = 'sub-menu';
}
?>
<div id="<?php echo $hid; ?>" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- /#content-header -->

<div id="<?php echo $sid; ?>">
	<ul>
		<li class="active"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->scope.'&pagename='.$this->page->pagename); ?>"><span>Article</span></a></li>
	</ul>
	<div class="clear"></div>
</div><!-- / #sub-menu -->

<div class="main section">
	<p class="warning">This page does not exist. Would you like to <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->scope.'&pagename='.$this->page->pagename.'&task=new'); ?>">create it?</a></p>
	<p>Or choose a page template to create an already-formatted page:</p>
	<ul>
<?php 
if ($templates) {
	foreach ($templates as $template)
	{
?>
		<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=new&tplate='.stripslashes($template->pagename)); ?>"><?php echo stripslashes($template->pagename); ?></a></li>
<?php
	}
} else {
?>
		<li>No templates available.</li>
<?php
}
?>
	</ul>
	<div class="clear"></div>
</div><!-- / .main section -->
