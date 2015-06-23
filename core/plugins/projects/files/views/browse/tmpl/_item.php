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
		<input type="checkbox" value="<?php echo urlencode($this->item->get('name')); ?>" name="<?php echo $this->item->get('type') == 'file' ? 'asset[]' : 'folder[]'; ?>" class="checkasset js<?php echo $this->item->get('type') == 'folder' ? ' dirr' : ''; if ($this->item->get('untracked')) { echo ' untracked'; } if ($this->item->get('converted')) { echo ' remote'; } ?>" />
	</td>
	<?php } ?>
	<td class="top_valign nobsp">
		<?php echo $this->item->drawIcon($ext); ?>
		<?php if ($this->item->get('type') == 'file') { ?>
		<a href="<?php echo Route::url($this->model->link('files')
		. '&action=download' . $subdirPath
		. '&asset=' . urlencode($this->item->get('name'))); ?>" class="preview file:<?php echo urlencode($this->item->get('name')); ?>"><?php echo \Components\Projects\Helpers\Html::shortenFileName($name, 60); ?></a>
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