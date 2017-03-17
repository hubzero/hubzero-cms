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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

require_once PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'orm' . DS . 'handler.php';

use \Components\Tools\Models\Orm\Handler;

// No direct access
defined('_HZEXEC_') or die();

$handlerBase = DS . trim($this->config->get('handler_base_path','srv' . DS . 'projects'), DS) . DS;
?>

<?php foreach ($this->items as $item) : ?>
<?php //Skip the file if we can verify that it's got a mimetype that we can't handle properly at this point in time ?> 
<?php if ($item->isFile() && strpos($item->getMimeType(), "application/vnd.google") === 0) :  continue; endif;?>
	<tr class="mini faded mline connections">
		<?php if ($this->model->access('content')) : ?>
			<td class="middle_valign">
				<input type="checkbox" value="<?php echo urlencode($item->getPath()); ?>" name="<?php echo $item->isFile() ? 'asset[]' : 'folder[]'; ?>" class="checkasset js<?php echo $item->isDir() ? ' dirr' : ''; ?>" />
			</td>
		<?php endif; ?>
		<?php $subdirPath = $this->subdir ? '&subdir=' . urlencode($this->subdir) : ''; ?>
		<td class="middle_valign nobsp is-relative">
			<?php echo \Components\Projects\Models\File::drawIcon($item->getExtension()); ?>
			<?php if ($item->isFile()) : ?>
				<div class="file-action-dropdown<?php echo ($handlers = Handler::getLaunchUrlsForFile($handlerBase . $this->model->get('alias') . DS . $item->getPath())) ? ' hasMultiple' : ''; ?>">
					<a href="<?php echo Route::url($this->model->link('files') . '&action=download&connection=' . $this->connection->id . $subdirPath . '&asset=' . urlencode($item->getName())); ?>" class="preview file:<?php echo urlencode($item->getName()); ?>">
						<?php echo \Components\Projects\Helpers\Html::shortenFileName($item->getFileName(), 60); ?>
					</a>
					<?php if ($handlers && count($handlers) > 0) : ?>
						<?php foreach ($handlers as $handler) : ?>
						<a href="<?php echo Route::url($handler['url']); ?>">
							<?php echo $handler['prompt']; ?>
						</a>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<a href="<?php echo Route::url($this->model->link('files') . '&action=browse&connection=' . $this->connection->id . '&subdir=' . urlencode($item->getPath())); ?>" class="dir:<?php echo urlencode($item->getName()); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_GO_TO_DIR') . ' ' . $item->getName(); ?>">
					<?php echo \Components\Projects\Helpers\Html::shortenFileName($item->getDisplayName(), 60); ?>
				</a>
			<?php endif; ?>
		</td>
		<td class="shrinked middle_valign"></td>
		<td class="shrinked middle_valign"><?php echo ($item->isFile()) ? $item->getSize() : ''; ?></td>
		<td class="shrinked middle_valign">
			<?php echo $item->getTimestamp() ? \Components\Projects\Helpers\Html::formatTime(Date::of($item->getTimestamp())->toSql()) : 'N/A'; ?>
		</td>
		<td class="shrinked middle_valign">
			<?php echo ($item->getOwner() == User::get('id')) ? Lang::txt('PLG_PROJECTS_FILES_ME') : User::getInstance($item->getOwner())->get('name'); ?>
		</td>
		<td class="shrinked middle_valign nojs">
			<?php if ($this->model->access('content')) : ?>
				<a href="<?php echo Route::url($this->model->link('files') . '&action=delete' . $subdirPath . '&asset=' . urlencode($item->getName())); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE_TOOLTIP'); ?>" class="i-delete">&nbsp;</a>
				<a href="<?php echo Route::url($this->model->link('files') . '&action=move' . $subdirPath . '&asset=' . urlencode($item->getName())); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE_TOOLTIP'); ?>" class="i-move">&nbsp;</a>
			<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
