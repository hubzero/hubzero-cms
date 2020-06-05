<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
				<?php foreach ($folder->queries()->order('ordering', 'asc')->rows() as $query) { ?>
					<li id="query_<?php echo $this->escape($query->id); ?>" <?php if ($this->show == $query->id) { echo ' class="active"'; }?>>
						<a class="query" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=tickets&show=' . $query->id . (intval($this->show) != $query->id ? '&search=' : '')); ?>">
							<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo Components\Support\Models\Ticket::countWithQuery($query, array()); ?></span>
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