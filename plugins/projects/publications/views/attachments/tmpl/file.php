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
	$data  = $this->data;

	$viewer 	 = $this->data->viewer;
	$allowRename = $this->data->allowRename;

	if ($viewer == 'freeze')
	{
		$title 	 = $data->title;
		$details = $data->path;
		$details.= $data->size ? ' | ' . ProjectsHtml::formatSize($data->size) : '';
	}
	else
	{
		$title 	 = $data->title;
		$details = $data->path;
		$details.= $data->size ? ' | ' . ProjectsHtml::formatSize($data->size) : '';
		$details.= $data->gitStatus ? ' | ' . $data->gitStatus : '';
	}
?>
	<li>
		<span class="item-options">
			<?php if ($viewer == 'edit') { ?>
			<span>
				<?php if (!$data->gone) { ?>
				<a href="<?php echo $data->downloadUrl; ?>" class="item-download" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_DOWNLOAD'); ?>">&nbsp;</a>
				<?php } ?>
				<a href="<?php echo $data->editUrl . '/?action=edititem' . a . 'aid=' . $data->id . a .'p=' . $data->props; ?>" class="showinbox item-edit" title="<?php echo ($data->gone || $allowRename == false) ? JText::_('PLG_PROJECTS_PUBLICATIONS_RELABEL') : JText::_('PLG_PROJECTS_PUBLICATIONS_RENAME'); ?>">&nbsp;</a>
				<a href="<?php echo $data->editUrl . '/?action=deleteitem&version=' . $data->version . '&aid=' . $data->id . '&p=' . $data->props; ?>" class="item-remove" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_REMOVE'); ?>">&nbsp;</a>
			</span>
			<?php } else { ?>
				<span><a href="<?php echo $data->downloadUrl; ?>" class="item-download" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_DOWNLOAD'); ?>">&nbsp;</a></span>
			<?php } ?>
		</span>
		<span class="item-title">
			<img alt="" src="<?php echo ProjectsHtml::getFileIcon($data->ext); ?>" /> <?php echo $title; ?>
		</span>
		<span class="item-details"><?php echo $details; ?></span>
	</li>