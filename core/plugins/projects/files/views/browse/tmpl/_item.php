<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$me = ($this->item->get('email') == User::get('email')
	|| $this->item->get('author') == User::get('name'))  ? 1 : 0;
$when = $this->item->get('date') ? \Components\Projects\Helpers\Html::formatTime($this->item->get('date')) : 'N/A';
$subdirPath = $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

// Do not display Google native extension
$name = $this->item->get('name');
if ($this->item->get('remote'))
{
	$native = \Components\Projects\Helpers\Google::getGoogleNativeExts();
	if (in_array($this->item->get('ext'), $native))
	{
		$name = preg_replace("/." . $this->item->get('ext') . "\z/", "", $this->item->get('name'));
	}
}
$ext = $this->item->get('type') == 'file' ? $this->item->get('ext') : 'folder';

?>
<tr class="mini faded mline">
	<?php if ($this->model->access('content')) { ?>
	<td>
		<input type="checkbox" value="<?php echo urlencode($this->item->get('name')); ?>" name="<?php echo $this->item->get('type') == 'file' ? 'asset[]' : 'folder[]'; ?>" class="checkasset js<?php echo $this->item->get('type') == 'folder' ? ' dirr' : ''; if ($this->item->get('untracked')) { echo ' untracked'; } if ($this->item->get('converted')) { echo ' remote service-google'; } ?>" />
	</td>
	<?php } ?>
	<td class="top_valign nobsp">
		<?php echo $this->item->drawIcon($ext); ?>
		<?php if ($this->item->get('type') == 'file') { ?>
		<a href="<?php echo Route::url($this->model->link('files')
		. '&action=' . (($this->item->get('converted')) ? 'open' : 'download') . $subdirPath
		. '&asset=' . urlencode($this->item->get('name'))); ?>" class="preview file:<?php echo urlencode($this->item->get('name')); ?>"<?php echo $this->item->get('converted') ? ' target="_blank"' : ''; ?>>
			<?php echo \Components\Projects\Helpers\Html::shortenFileName($name, 60); ?>
		</a>
		<?php } else { ?>
			<a href="<?php echo Route::url($this->model->link('files') . '/&action=browse&subdir=' . urlencode($this->item->get('localPath'))); ?>" class="dir:<?php echo urlencode($this->item->get('name')); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_GO_TO_DIR') . ' ' . $this->item->get('name'); ?>"><?php echo \Components\Projects\Helpers\Html::shortenFileName($this->item->get('name'), 60); ?></a>
		<?php } ?>
	</td>
	<td class="shrinked"></td>
	<td class="shrinked"><?php echo $this->item->getSize(true); ?></td>
	<td class="shrinked">
	<?php if (!$this->item->get('untracked') && $this->item->get('type') == 'file') { ?>
		<a href="<?php echo Route::url($this->model->link('files') . '&action=history' . $subdirPath . '&asset=' . urlencode($this->item->get('name'))); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_HISTORY_TOOLTIP'); ?>"><?php echo $when; ?></a>
	<?php } elseif ($this->item->get('untracked')) { echo Lang::txt('PLG_PROJECTS_FILES_UNTRACKED'); } ?>
	</td>
	<td class="shrinked"><?php echo $me ? Lang::txt('PLG_PROJECTS_FILES_ME') : $this->item->get('author'); ?></td>
	<td class="shrinked nojs">
		<?php if ($this->model->access('content')) { ?>
		<a href="<?php echo Route::url($this->model->link('files') . '&action=delete' . $subdirPath
	. '&asset=' . urlencode($this->item->get('name'))); ?>"
	 title="<?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE_TOOLTIP'); ?>" class="i-delete">&nbsp;</a>
		<a href="<?php echo Route::url($this->model->link('files') . '&action=move' . $subdirPath
	. '&asset=' . urlencode($this->item->get('name'))); ?>"
	 title="<?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE_TOOLTIP'); ?>" class="i-move">&nbsp;</a>
	<?php } ?>
	</td>
	<?php if ($this->publishing) { ?>
	<td class="shrinked"></td>
	<?php } ?>
</tr>