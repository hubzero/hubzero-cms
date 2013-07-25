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

$file 	= $this->item;	
$me 	= ($file['email'] == $this->juser->get('email') || $file['author'] == $this->juser->get('name'))  ? 1 : 0;
$c 		= $this->c;
$when 	= $file['date'] ? ProjectsHtml::formatTime($file['date']) : 'N/A';

// LaTeX?
$tex = ProjectsCompiler::isTexFile(basename($file['name']));
			
?>
	<tr class="mini faded mline">
		<td><input type="checkbox" value="<?php echo urlencode($file['name']); ?>" name="asset[]" class="checkasset js <?php if($this->publishing && $file['pid']) { echo 'publ'; } ?>" /></td>
		<td class="top_valign nobsp">
			<img src="<?php echo ProjectsHtml::getFileIcon($file['ext']); ?>" alt="<?php echo $file['ext']; ?>" />
			<a href="<?php echo $this->url 
			. '/?' . $this->do . '=download' . a . 'subdir='.urlencode($this->subdir) 
			. a . 'file='.urlencode($file['name']); ?>" 
			class="preview file:<?php echo urlencode($file['name']); ?>" id="edit-c-<?php echo $c; ?>">
			<?php echo ProjectsHtml::shortenFileName($file['name'], 50); ?></a>
			
			<span id="rename-c-<?php echo $c; ?>" class="rename js" title="<?php echo JText::_('COM_PROJECTS_FILES_RENAME_FILE_TOOLTIP'); ?>">&nbsp;</span>
			
		</td>
		<td class="shrinked"></td>
		<td class="shrinked"><?php echo $file['size']; ?></td>
		<td class="shrinked"><a href="<?php echo $this->url . '/?' . $this->do . '=history' . a . 'subdir='.urlencode($this->subdir) . a . 'asset=' . urlencode($file['name']); ?>" title="<?php echo JText::_('COM_PROJECTS_HISTORY_TOOLTIP'); ?>"><?php echo $when; ?></a></td>
		<td class="shrinked pale"><?php if($me) { echo JText::_('COM_PROJECTS_FILES_ME'); } else { echo $file['author']; } ?>
		</td>
		<td class="shrinked nojs"><a href="<?php echo $this->url . '/?' . $this->do . '=delete' . a . 'subdir='.urlencode($this->subdir) 
		. a . 'asset='.urlencode($file['name']); ?>" 
		 title="<?php echo JText::_('COM_PROJECTS_DELETE_TOOLTIP'); ?>" class="i-delete">&nbsp;</a>
		<a href="<?php echo $this->url . '/?' . $this->do . '=move' . a . 'subdir='.urlencode($this->subdir) 
		. a . 'asset='.urlencode($file['name']); ?>" 
		 title="<?php echo JText::_('COM_PROJECTS_MOVE_TOOLTIP'); ?>" class="i-move">&nbsp;</a></td>
		<?php if ($this->publishing) { ?>
		<td class="shrinked"><?php if($file['pid'] && $file['pub_title']) { ?><a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'active=publications' . a . 'alias=' . $this->project->alias . a . 'pid='.$file['pid']).'?section=content'; ?>" title="<?php echo $file['pub_title'] . ' (v.' . $file['pub_version_label'] . ')' ; ?>" class="asset_resource"><?php echo Hubzero_View_Helper_Html::shortenText($file['pub_title'], 20, 0); ?></a><?php } ?></td>
		<?php } ?>
	</tr>