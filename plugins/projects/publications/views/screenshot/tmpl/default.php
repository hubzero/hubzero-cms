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
?>
	<span class="g-ima"><img src="<?php echo $this->src; ?>" alt="" /></span>
	<span class="g-title"><?php echo $this->title ? $this->title : $this->ima; ?><span class="block faded"><?php echo $this->title == $this->ima  ? '' : $this->ima; ?></span></span>
	<?php if (isset($this->canedit) && $this->canedit && $this->pid) { ?>
	<span class="c-edit"><a href="<?php echo $this->url . '?vid=' . $this->vid . '&amp;ima=' . urlencode($this->ima) . '&amp;move=' . $this->move . '&amp;action=editimage&amp;ajax=1&amp;no_html=1'; ?>" class="showinbox"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_GALLERY_EDIT'); ?></a></span>
<?php } ?>

