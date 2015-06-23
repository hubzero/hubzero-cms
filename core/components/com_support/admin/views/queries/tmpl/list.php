<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */
// No direct access
defined('_HZEXEC_') or die();

if (count($this->folders) > 0) { ?>
	<?php foreach ($this->folders as $folder) { ?>
		<li id="folder_<?php echo $this->escape($folder->id); ?>" class="open">
			<span class="icon-folder folder" id="<?php echo $this->escape($folder->id); ?>-title" data-id="<?php echo $this->escape($folder->id); ?>"><?php echo $this->escape($folder->title); ?></span>
			<span class="folder-options">
				<a class="delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=removefolder&id=' . $folder->id . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
					<?php echo Lang::txt('COM_SUPPORT_DELETE'); ?>
				</a>
				<a class="edit editfolder" data-id="<?php echo $this->escape($folder->id); ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=editfolder&id=' . $folder->id . '&tmpl=component&' . Session::getFormToken() . '=1'); ?>" data-href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=savefolder&' . Session::getFormToken() . '=1&fields[id]=' . $folder->id); ?>" title="<?php echo Lang::txt('JACTION_EDIT'); ?>">
					<?php echo Lang::txt('COM_SUPPORT_EDIT'); ?>
				</a>
			</span>
			<ul id="queries_<?php echo $this->escape($folder->id); ?>" class="queries">
				<?php foreach ($folder->queries as $query) { ?>
					<li id="query_<?php echo $this->escape($query->id); ?>" <?php if ($this->show == $query->id) { echo ' class="active"'; }?>>
						<a class="query" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=tickets&show=' . $query->id . (intval($this->show) != $query->id ? '&search=' : '')); ?>">
							<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo $query->count; ?></span>
						</a>
						<span class="query-options">
							<a class="delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=remove&id=' . $query->id . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
								<?php echo Lang::txt('JACTION_DELETE'); ?>
							</a>
							<a class="modal edit" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=edit&id=' . $query->id . '&tmpl=component&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('JACTION_EDIT'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}">
								<?php echo Lang::txt('JACTION_EDIT'); ?>
							</a>
						</span>
					</li>
				<?php } ?>
			</ul>
		</li>
	<?php } ?>
<?php }