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
$dirpath = $this->subdir ? $this->subdir . DS . $this->item : $this->item;

?>

<tr class="mini faded mline">
	<td><input type="checkbox" value="<?php echo urlencode($this->item); ?>" name="folder[]" class="checkasset js dirr" /></td>
	<td class="top_valign nobsp"><img src="/plugins/projects/files/images/folder.gif" alt="<?php echo $this->item; ?>" />
		<a href="<?php echo $this->url. '/?' . $this->do . '=browse' . a . 'subdir=' . urlencode($dirpath); ?>" class="dir:<?php echo urlencode($this->item); ?>" title="<?php echo JText::_('COM_PROJECTS_FILES_GO_TO_DIR') . ' ' . $this->item; ?>" id="edit-c-<?php echo $this->c; ?>"><?php echo ProjectsHtml::shortenFileName($this->item, 50); ?></a>
		<span id="rename-c-<?php echo $this->c; ?>" class="rename js" title="<?php echo JText::_('COM_PROJECTS_FILES_RENAME_DIR_TOOLTIP'); ?>">&nbsp;</span>
	</td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td class="shrinked nojs"><a href="<?php echo $this->url . '/?' . $this->do . '=delete' . a . 'subdir='.urlencode($this->subdir)
	. a . 'folder[]='.urlencode($this->item); ?>"
	 title="<?php echo JText::_('COM_PROJECTS_DELETE_TOOLTIP'); ?>" class="i-delete">&nbsp;</a></td>
	<?php if ($this->publishing) { ?>
	<td></td>
	<?php } ?>
</tr>
