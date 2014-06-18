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

$img = $this->remote && $this->remote['converted'] == 1
	? ProjectsHtml::getGoogleIcon($this->remote['mimeType'])
	: ProjectsHtml::getFileIcon($this->ext);

$alt = $this->ext;

$filesize = $this->filesize;

$name = $this->title;

$ext = explode('.', $this->title);
$ext = count($ext) > 1 ? end($ext) : '';

// Is this a duplicate remote?
if ($this->remote && $this->title != $this->remote['title'])
{
	$append = ProjectsHtml::getAppendedNumber($this->title);

	if ($append > 0)
	{
		$ext = explode('.', $this->title);
		$ext = count($ext) > 1 ? end($ext) : '';

		$name = ProjectsHtml::fixFileName($this->remote['title'], ' (' . $append . ')', $ext );
	}
}

// Do not display Google native extension
$native = ProjectsGoogleHelper::getGoogleNativeExts();
if (in_array($ext, $native))
{
	$name = preg_replace("/.".$ext."\z/", "", $name);
}

?>
	<h4><img src="<?php echo $img; ?>" alt="<?php echo $alt; ?>" /> <?php echo $name; ?></h4>
	<ul class="filedata">
		<?php echo $this->ext && $this->remote['converted'] == 0 ? '<li>' . strtoupper($this->ext) . '</li>' : ''; ?>
		<?php echo $this->remote && $this->remote['converted'] == 1 ? '<li>' . JText::_('COM_PROJECTS_FILES_REMOTE_FILE_GOOGLE') . '</li>' : ''; ?>
		<?php if ($this->remote['converted'] == 1 && $this->remote['original_path']) { echo '<li>From ' . basename($this->remote['original_path']); if ($this->remote['original_format']) { echo ' (' . $this->remote['original_format']. ')'; } echo '</li>'; } ?>
		<?php echo $this->filesize ? '<li>' . strtoupper($filesize) . '</li>' : ''; ?>
	</ul>

	<?php if ($this->image && is_file(JPATH_ROOT.$this->image)) { ?>
		<div id="preview-image"><img src="<?php echo $this->image; ?>" alt="<?php echo JText::_('COM_PROJECTS_FILES_LOADING_PREVIEW'); ?>" /></div>
	<?php }
	elseif ($this->content) { ?>
	<pre><?php echo $this->content; ?></pre>
	<?php } ?>

