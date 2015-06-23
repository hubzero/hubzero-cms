<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

?>
<div class="sidebox<?php if (count($this->files) == 0) { echo ' suggestions'; } ?>">
		<h4><a href="<?php echo Route::url($this->model->link('files')); ?>" class="hlink"><?php echo (count($this->files) == 0) ? Lang::txt('COM_PROJECTS_FILES') : Lang::txt('PLG_PROJECTS_FILES_RECENTLY_ADDED'); ?></a>
</h4>
<?php if (count($this->files) == 0) { ?>
	<p class="s-files">
		<a href="<?php echo Route::url($this->model->link('files')); ?>"><?php echo Lang::txt('COM_PROJECTS_WELCOME_UPLOAD_FILES'); ?></a>
	</p>
<?php } else { ?>
	<ul>
		<?php foreach ($this->files as $file) {
			$ext = $file->get('type') == 'file' ? $file->get('ext') : 'folder';
		?>
			<li>
				<a href="<?php echo Route::url($this->model->link('files')
				. '&action=download&asset=' . urlencode($file->get('localPath'))); ?>" title="<?php echo $this->escape($file->get('name')); ?>"><?php echo $file->drawIcon($ext); ?> <?php echo \Components\Projects\Helpers\Html::shortenFileName($file->get('name')); ?></a>
				<span class="block faded mini">
					<?php echo $file->getSize('formatted'); ?> &middot; <?php echo Date::of($file->get('date'))->toLocal('M d, Y'); ?> &middot; <?php echo \Components\Projects\Helpers\Html::shortenName($file->get('author')); ?>
				</span>
			</li>
		<?php } ?>
	</ul><?php } ?>
</div>
