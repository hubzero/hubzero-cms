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

if ($this->type == 'folder')
{
	$ext  = 'folder';
	$name = $this->item;
}
else
{
	$ext = explode('.', $this->item);
	$ext = count($ext) > 1 ? end($ext) : '';
	$name = $this->item;
}

// Is this a duplicate remote?
if ($this->remote && $this->item != $this->remote['title'])
{
	$append = ProjectsHtml::getAppendedNumber($this->item);

	if ($append > 0)
	{
		$ext = explode('.', $this->item);
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

if ($this->remote && $this->remote['converted'] == 1)
{
	$slabel = $this->type == 'folder' ? JText::_('COM_PROJECTS_FILES_REMOTE_FOLDER') : JText::_('COM_PROJECTS_FILES_REMOTE_FILE');
	if ($this->remote['service'] == 'google')
	{
		$slabel = $this->type == 'folder' ? JText::_('COM_PROJECTS_FILES_REMOTE_FOLDER_GOOGLE') : JText::_('COM_PROJECTS_FILES_REMOTE_FILE_GOOGLE');
	}
}

$img = $this->remote && $this->remote['converted'] == 1 ? ProjectsHtml::getGoogleIcon($this->remote['mimeType']) : ProjectsHtml::getFileIcon($ext);

$multi = isset($this->multi) && $this->multi ? '[]' : '';

$fpath = isset($this->subdir) && $this->subdir ? $this->subdir. DS . urldecode($name) : urldecode($name);

?>
<li><img src="<?php echo $img; ?>" alt="<?php echo $name; ?>" />
<?php echo $fpath; ?>
<?php if ($this->remote && $this->remote['converted'] == 1) { echo '<span class="remote-file">' . $slabel . '</span>'; } ?>
<?php if ($this->remote && $this->remote['original_path'] && $this->remote['converted'] == 1) { echo '<span class="remote-file faded">' . JText::_('COM_PROJECTS_FILES_CONVERTED_FROM_ORIGINAL'). ' ' . basename($this->remote['original_path']); if ($this->remote['original_format']) { echo ' (' . $this->remote['original_format']. ')'; } echo '</span>'; } ?>

<?php if (isset($this->skip) && $this->skip == true) { echo '<span class="file-skipped">' . JText::_('COM_PROJECTS_FILES_SKIPPED') . '</span>'; } ?>
<?php echo $this->type == 'folder'
	? '<input type="hidden" name="folder' . $multi . '" value="'.$this->item.'" />'
	: '<input type="hidden" name="asset' . $multi . '" value="'.$this->item.'" />'; ?>

<?php if (isset($this->extras)) { echo $this->extras; } ?>
</li>