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

	$data  		 = $this->data;
	$ih    		 = $this->ih;
	$allowRename = $this->data->allowRename;

	// Get settings
	$suffix = isset($this->config->params->thumbSuffix) && $this->config->params->thumbSuffix
			? $this->config->params->thumbSuffix : '-tn';

	$format = isset($this->config->params->thumbFormat) && $this->config->params->thumbFormat
			? $this->config->params->thumbFormat : 'png';

	$width = isset($this->config->params->thumbWidth) && $this->config->params->thumbWidth
			? $this->config->params->thumbWidth : 100;

	$height = isset($this->config->params->thumbHeight) && $this->config->params->thumbHeight
			? $this->config->params->thumbHeight : 60;

	$dirHierarchy = isset($this->params->dirHierarchy) ? $this->params->dirHierarchy : 1;

	if ($dirHierarchy)
	{
		$file = $this->data->path;
	}
	else
	{
		$file 	= ProjectsHtml::fixFileName(basename($data->path), '-' . $data->id);
	}

	$filePath  = $data->pubPath . DS . $file;
	$thumbName = $ih->createThumbName($file, $suffix, $format);
	$thumbPath = $data->pubPath . DS . $thumbName;

	// No file found
	if (!is_file($filePath))
	{
		return;
	}

	$md5 = hash_file('sha256', $filePath);

	// Create/update thumb if doesn't exist or file changed
	if (!is_file($thumbPath) || $md5 != $data->md5)
	{
		JFile::copy($filePath, $thumbPath);
		$ih->set('image', basename($thumbName));
		$ih->set('overwrite', true);
		$ih->set('path', $data->pubPath . DS);
		$ih->set('maxWidth', $width);
		$ih->set('maxHeight', $height);
		if (!$ih->process())
		{
			return false;
		}
	}

	// Image src
	if (is_file($thumbPath))
	{
		$thumbSrc = str_replace(JPATH_ROOT, '', $thumbPath);
	}
	else
	{
		$thumbSrc = $this->configs->defaultThumb;
	}

	$filePath = str_replace(JPATH_ROOT, '', $filePath);

	// Is this image used for publication thumbail?
	$class = $data->pubThumb == 1 ? ' starred' : '';
	$over  = $data->pubThumb == 1 ? ' title="' . JText::_('PLG_PROJECTS_PUBLICATIONS_IMAGE_DEFAULT') . '"' : '';

	$viewer = $this->data->viewer;

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
	<li class="image-container">
		<span class="item-options">
			<?php if ($viewer == 'edit') { ?>
			<span>
				<?php if (!$data->pubThumb) { ?>
				<a href="<?php echo $data->editUrl . '/?action=saveitem&aid=' . $data->id . '&p=' . $data->props . '&makedefault=1'; ?>" class="item-default" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_IMAGE_MAKE_DEFAULT'); ?>">&nbsp;</a>
				<?php } ?>
				<a href="<?php echo $data->editUrl . '/?action=edititem&aid=' . $data->id . '&p=' . $data->props; ?>" class="showinbox item-edit" title="<?php echo ($data->gone || $allowRename == false) ? JText::_('PLG_PROJECTS_PUBLICATIONS_RELABEL') : JText::_('PLG_PROJECTS_PUBLICATIONS_RENAME'); ?>">&nbsp;</a>
				<a href="<?php echo $data->editUrl . '/?action=deleteitem&aid=' . $data->id . '&p=' . $data->props; ?>" class="item-remove" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_REMOVE'); ?>">&nbsp;</a>
			</span>
			<?php } ?>
		</span>
		<span class="item-image<?php echo $class; ?>" <?php echo $over; ?>><a class="more-content" href="<?php echo $filePath; ?>"><img alt="" src="<?php echo $thumbSrc; ?>" /></a></span>
		<span class="item-title">
			<?php echo $title; ?></span>
		<span class="item-details"><?php echo $details; ?></span>
	</li>