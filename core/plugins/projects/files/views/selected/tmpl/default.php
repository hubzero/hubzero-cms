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

// No direct access
defined('_HZEXEC_') or die();

if ($this->file->get('converted'))
{
	$slabel = $this->file->get('type') == 'folder' ? Lang::txt('PLG_PROJECTS_FILES_REMOTE_FOLDER') : Lang::txt('PLG_PROJECTS_FILES_REMOTE_FILE');
}

$multi = isset($this->multi) && $this->multi ? '[]' : '';

?>
<li><img src="<?php echo $this->file->getIcon(); ?>" alt="<?php echo $this->file->get('name'); ?>" />
<?php echo $this->file->get('name'); ?>
<?php if ($this->file->get('converted')) { echo '<span class="remote-file">' . $slabel . '</span>'; } ?>
<?php if ($this->file->get('converted') && $this->file->get('originalPath')) { echo '<span class="remote-file faded">' . Lang::txt('PLG_PROJECTS_FILES_CONVERTED_FROM_ORIGINAL'). ' ' . basename($this->file->get('originalPath')); if ($this->file->get('originalFormat')) { echo ' (' . $this->file->get('originalPath') . ')'; } echo '</span>'; } ?>

<?php if (isset($this->skip) && $this->skip == true) { echo '<span class="file-skipped">' . Lang::txt('PLG_PROJECTS_FILES_SKIPPED') . '</span>'; } ?>
<?php echo $this->file->get('type') == 'folder'
	? '<input type="hidden" name="folder' . $multi . '" value="' . urlencode($this->file->get('name')) . '" />'
	: '<input type="hidden" name="asset' . $multi . '" value="' . urlencode($this->file->get('name')) . '" />'; ?>

<?php if (isset($this->extras)) { echo $this->extras; } ?>
</li>