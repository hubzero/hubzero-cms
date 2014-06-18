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
	<?php if (count($this->items) > 0) {

		$parents = array();
		$openParents = array();
		$a = 1;

		// Get all parents
		foreach ($this->items as $item)
		{
			if ($item->type == 'folder')
			{
				$tempId = strtolower(ProjectsHtml::generateCode(5, 5, 0, 1, 1));
				$parents[$item->localPath] = $tempId;
			}

			// Selected item?
			if (!empty($this->selected) && in_array($item->localPath, $this->selected))
			{
				if ($item->parents)
				{
					foreach ($item->parents as $i => $parent)
					{
						$openParents[] = $parent;
					}
				}
			}
		}

	?>
		<ul class="file-selector" id="file-selector">
			<?php foreach ($this->items as $item)
			{
					$icon = $item->type == 'folder'
							? "/plugins/projects/files/images/folder.gif"
							: ProjectsHtml::getFileIcon($item->ext);

					$level =  $item->dirname ? count(explode('/', $item->dirname)) : 0;

					// Get element ID
					$liId  = ($item->type == 'folder' && isset($parents[$item->localPath]))
							? 'dir-' . $parents[$item->localPath]
							: 'item-' . $a;

					// Assign parent classes (for collapsing)
					$parentCss = '';
					if ($item->parents)
					{
						foreach ($item->parents as $i => $parent)
						{
							if (isset($parents[$parent]))
							{
								$parentCss .= ' parent-' . $parents[$parent];
							}
						}
					}

					$levelCss = $this->showLevels == true ? 'level-' . $level : 'flatlist';
					$a++;

					// Is file already attached?
					$selected = !empty($this->selected) && in_array($item->localPath, $this->selected) ? 1 : 0;

					// Is file type allowed?
					$allowed = $item->type == 'file' && !empty($this->allowed)
							&& !in_array($item->ext, $this->allowed)
							? ' notallowed' : ' allowed';

					$used = !empty($this->used)
							&& in_array($item->localPath, $this->used) ? true : false;

					// Do not allow files used in other elements
					$allowed = $used ? ' notallowed' : $allowed;

					// No selection for folders
					$allowed = $item->type == 'folder' ? ' freeze' : $allowed;

					// Do not allow to delete previously selected items
					$allowed = $selected ? ' freeze' : $allowed;

					// Is selection within folder? Then open this folder
					$opened = !empty($openParents) && $item->type == 'folder'
							&& in_array($item->localPath, $openParents) ? 1 : 0;

				?>
				<li class="<?php echo $item->type == 'folder' ? 'type-folder' : 'type-file'; ?><?php echo $parentCss; ?><?php if ($selected) { echo ' selectedfilter preselected'; } ?><?php echo $allowed; ?><?php echo $opened ? ' opened' : ''; ?>" id="<?php echo $liId; ?>">
					<span class="item-info"><?php echo $item->type == 'file' ? $item->formattedSize : ''; ?></span>
					<span class="item-wrap <?php echo $levelCss; ?>" id="<?php echo urlencode($item->localPath); ?>">
						<?php if($item->type == 'folder') { ?><span class="collapsor">&nbsp;</span><?php } ?>
						<img src="<?php echo $icon; ?>" alt="" /> <span title="<?php echo $item->localPath; echo $item->type == 'file' ? ' [' . $item->mimeType . ']' : '' ?>"><?php echo ProjectsHtml::shortenFileName($item->name, 50); ?></span>
					</span>

				</li>
			<?php } ?>
		</ul>
	<?php } else {  ?>
		<p class="noresults"><?php echo $this->project->provisioned == 1 ? JText::_('PLG_PROJECTS_FILES_SELECTOR_NO_FILES_FOUND_PROV') : JText::_('PLG_PROJECTS_FILES_SELECTOR_NO_FILES_FOUND'); ?></p>
	<?php } ?>
