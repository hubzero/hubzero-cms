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

// Build url
$route = $this->project->provisioned
	? 'index.php?option=com_publications' . a . 'task=submit'
	: 'index.php?option=com_projects' . a . 'alias=' . $this->project->alias;
$p_url = JRoute::_($route . a . 'active=files');

$shown = array();
$skipped = 0;

// Filter URL
$filterUrl   = $this->project->provisioned == 1
		? JRoute::_( $route) . '?active=files&action=browser&'
		: JRoute::_( $route . '&active=files&action=browser') .'/?';

$filterUrl .= 'pid=' . $this->pid
				. '&versionid=' . $this->versionid . '&amp;ajax=1&amp;no_html=1';
$filterUrl .= $this->primary ? '&primary=1' : '';
$filterUrl .= $this->images ? '&images=1' : '';

?>
<form action="<?php echo JRoute::_($route).'?active=publications'; ?>" method="post" id="select-form" class="myselector">
	<input type="hidden" id="filterUrl" name="filterUrl" value="<?php echo $filterUrl; ?>" />
	<p class="prominent"><?php echo JText::_('Search within your file repository:'); ?></p>
	<div id="search-filter" class="search-filter">
		<label>
			<input type="text" value="<?php echo $this->filter; ?>" placeholder="<?php echo JText::_('Type a search term'); ?>" name="filter" id="item-search" /></label>
	</div>

	<ul id="c-browser">
		<?php
			// Show filtered files
			$i = 0;
			foreach ($this->files as $file) {
				if ($this->images)
				{
					// Skip non-image/video files
					if (!in_array(strtolower($file['ext']), $this->image_ext) && !in_array(strtolower($file['ext']), $this->video_ext)) {
						continue;
					}
				}
				// Skip files attached in another role
				if (in_array($file['fpath'], $this->exclude)) {
					continue;
				}

				// Ignore hidden files
				if (substr(basename($file['fpath']), 0, 1) == '.')
				{
					continue;
				}
				$shown[] = $file['fpath'];

				 ?>
			<li class="c-click" id="file::<?php echo urlencode($file['fpath']); ?>" title="<?php echo $file['fpath']; ?>"><img src="<?php echo ProjectsHtml::getFileIcon($file['ext']); ?>" alt="<?php echo $file['ext']; ?>" /><?php echo ProjectsHtml::shortenFileName($file['fpath'], 50); ?></li>
		<?php
			$i++;
		}

			// Show selected files
			$missing = array();

			// Primary content / Supporting docs
			if (isset($this->attachments)) {
				if (count($this->attachments) > 0) {
					foreach ($this->attachments as $attachment) {
						if (!in_array($attachment->path, $shown)) {
							// Found missing
							$miss = array();
							$miss['fpath'] = $attachment->path;
							$miss['ext'] = ProjectsHtml::getFileAttribs( $attachment->path, '', 'ext' );
							$missing[] = $miss;
						}
					}
				}
			}

			// Screenshots
			if ($this->images) {
				if (count($this->shots) > 0) {
					foreach ($this->shots as $shot) {
						if (!in_array($shot->filename, $shown)) {
							// Found missing
							$miss = array();
							$miss['fpath'] = $shot->filename;
							$miss['ext'] = ProjectsHtml::getFileAttribs( $shot->filename, '', 'ext' );
							$missing[] = $miss;
						}
					}
				}
			}

			// Add missing items
			if (count($missing) > 0) {
				foreach ($missing as $miss) { ?>
					<li class="c-click" id="file::<?php echo urlencode($miss['fpath']); ?>" title="<?php echo $miss['fpath']; ?>"><img src="<?php echo ProjectsHtml::getFileIcon($miss['ext']); ?>" alt="<?php echo $miss['ext']; ?>" /><?php echo ProjectsHtml::shortenFileName($miss['fpath'], 50); ?></li>
			<?php	}
			}
		?>
	</ul>
</form>