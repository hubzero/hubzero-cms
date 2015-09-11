<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
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

?>
	<?php if (count($this->items) > 0) {

		$parents = array();
		$openParents = array();
		$a = 1;

		// Get all parents
		foreach ($this->items as $item)
		{
			if ($item->get('type') == 'folder')
			{
				$tempId = strtolower(\Components\Projects\Helpers\Html::generateCode(5, 5, 0, 1, 1));
				$parents[$item->get('localPath')] = $tempId;
			}

			// Selected item?
			if (!empty($this->selected) && in_array($item->get('localPath'), $this->selected))
			{
				if ($item->get('parents'))
				{
					foreach ($item->get('parents') as $i => $parent)
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
				$level =  $item->getDirLevel($item->get('dirname'));

				// Get element ID
				$liId  = ($item->get('type') == 'folder' && isset($parents[$item->get('localPath')]))
						? 'dir-' . $parents[$item->get('localPath')]
						: 'item-' . $a;

				// Assign parent classes (for collapsing)
				$parentCss = '';
				if ($item->get('parents'))
				{
					foreach ($item->get('parents') as $i => $parent)
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
				$selected = !empty($this->selected) && in_array($item->get('localPath'), $this->selected) ? 1 : 0;

				// Is file type allowed?
				$allowed = $item->get('type') == 'file' && !empty($this->allowed)
						&& !in_array($item->get('ext'), $this->allowed)
						? ' notallowed' : ' allowed';

				$used = !empty($this->used)
						&& in_array($item->get('localPath'), $this->used) ? true : false;

				// Do not allow files used in other elements
				$allowed = $used ? ' notallowed' : $allowed;

				// No selection for folders
				$allowed = $item->get('type') == 'folder' ? ' freeze' : $allowed;

				// Do not allow to delete previously selected items
				$allowed = $selected ? ' freeze' : $allowed;

				// Is selection within folder? Then open this folder
				$opened = !empty($openParents) && $item->get('type') == 'folder'
						&& in_array($item->get('localPath'), $openParents) ? 1 : 0;

				?>
				<li class="<?php echo $item->get('type') == 'folder' ? 'type-folder' : 'type-file'; ?><?php echo $parentCss; ?><?php if ($selected) { echo ' selectedfilter preselected'; } ?><?php echo $allowed; ?><?php echo $opened ? ' opened' : ''; ?>" id="<?php echo $liId; ?>">
					<span class="item-info"><?php echo $item->get('type') == 'file' ? $item->getSize('formatted') : ''; ?></span>
					<span class="item-wrap <?php echo $levelCss; ?>" id="<?php echo urlencode($item->get('localPath')); ?>">
						<?php if ($item->get('type') == 'folder') { ?><span class="collapsor">&nbsp;</span><?php } ?>
						<img src="<?php echo $item->get('icon'); ?>" alt="" /> <span title="<?php echo $item->get('localPath'); ?>"><?php echo \Components\Projects\Helpers\Html::shortenFileName($item->get('name'), 50); ?></span>
					</span>

				</li>
			<?php } ?>
		</ul>
	<?php } else {  ?>
		<p class="noresults"><?php echo $this->model->isProvisioned() ? Lang::txt('PLG_PROJECTS_FILES_SELECTOR_NO_FILES_FOUND_PROV') : Lang::txt('PLG_PROJECTS_FILES_SELECTOR_NO_FILES_FOUND'); ?></p>
	<?php } ?>
