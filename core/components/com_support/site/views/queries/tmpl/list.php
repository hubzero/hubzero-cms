<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */
// No direct access
defined('_HZEXEC_') or die();

if ($this->getError()) { ?>
	<li class="error">Error: <?php echo $this->getError(); ?></li>
<?php }
if (count($this->folders) > 0) { ?>
	<?php foreach ($this->folders as $folder) { ?>
		<li id="folder_<?php echo $this->escape($folder->id); ?>" class="open">
			<span class="icon-folder folder" id="<?php echo $this->escape($folder->id); ?>-title" data-id="<?php echo $this->escape($folder->id); ?>"><?php echo $this->escape($folder->title); ?></span>
			<span class="folder-options">
				<a class="delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=removefolder&id=' . $folder->id . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
					<?php echo Lang::txt('JACTION_DELETE'); ?>
				</a>
				<a class="edit editfolder" data-id="<?php echo $this->escape($folder->id); ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=editfolder&id=' . $folder->id . '&tmpl=component&' . Session::getFormToken() . '=1'); ?>" data-href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=savefolder&' . Session::getFormToken() . '=1&fields[id]=' . $folder->id); ?>" title="<?php echo Lang::txt('JACTION_EDIT'); ?>">
					<?php echo Lang::txt('JACTION_EDIT'); ?>
				</a>
			</span>
			<ul id="queries_<?php echo $this->escape($folder->id); ?>" class="queries">
				<?php
				if (!isset($folder->queries))
				{
					$folder->queries = array();
				}

				foreach ($folder->queries as $query) { ?>
					<li id="query_<?php echo $this->escape($query->id); ?>" <?php if ($this->show == $query->id) { echo ' class="active"'; }?>>
						<a class="aquery" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=tickets&task=display&show=' . $query->id . (intval($this->show) != $query->id ? '&search=' : '')); ?>">
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