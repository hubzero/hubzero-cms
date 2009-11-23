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
<?php if ($this->getError()) { ?>
	<h2><?php echo JText::_('COM_XPOLL'); ?></h2>
<?php } else { ?>
	<h2><?php echo JText::_('COM_XPOLL_THANKS'); ?></h2>
<?php } ?>
</div><!-- / #content-header -->

<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } else { ?>
	<p><a href="<?php echo JRoute::_( 'index.php?option='.$this->option.'&task=view&id='.$this->pollid ); ?>"><?php echo JText::_('COM_XPOLL_BUTTON_RESULTS'); ?></a></p>
<?php } ?>
<div class="clear"></div>
</div><!-- / .main section -->