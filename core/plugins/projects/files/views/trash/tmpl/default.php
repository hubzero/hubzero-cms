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
<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_FILES_DELETED_FILES'); ?></h3>

<form id="hubForm-ajax" method="post" action="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->model->get('id')); ?>">
	<fieldset >
<?php if (empty($this->files)) { ?>
	<p class="warning"><?php echo Lang::txt('PLG_PROJECTS_FILES_TRASH_EMPTY'); ?></p>
<?php } else { ?>
	<div class="wrapper">
		<table id="filelist" class="listing">
			<thead>
				<tr>
					<th><?php echo Lang::txt('PLG_PROJECTS_FILES_FILE'); ?></th>
					<th><?php echo Lang::txt('PLG_PROJECTS_FILES_DELETED'); ?></th>
					<th><?php echo Lang::txt('PLG_PROJECTS_FILES_OPTIONS'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->files as $filename => $file) {

					$dirname = dirname($filename);
				?>
				<tr class="mini">
					<td>
						<span class="icon-image"></span>
						<span><?php echo basename($filename); ?></span>
						<?php if ($dirname != '.') { ?>
						<span class="faded block ipadded">
							<span class="icon-folder"></span>
							<?php echo $dirname; ?></span>
						<?php } ?>
					</td>
					<td class="faded">
						<?php echo \Components\Projects\Helpers\Html::formatTime($file['date'], true, true); ?>
						<span class="block"><?php echo $file['author']; ?></span>
					</td>
					<td><a href="<?php echo Route::url($this->url . '&action=restore&asset=' . urlencode($filename) . '&hash=' . $file['hash']);  ?>"><?php echo Lang::txt('PLG_PROJECTS_FILES_RESTORE'); ?></a></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
		<p class="submitarea">
			<?php if ($this->ajax) { ?>
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_CLOSE'); ?>" />
			<?php } else {  ?>
			<a id="cancel-action" class="btn btn-cancel" href="<?php echo $this->url . '?a=1' . $subdirlink; ?>"><?php echo Lang::txt('PLG_PROJECTS_FILES_GO_BACK'); ?></a>
			<?php } ?>
		</p>

<?php } ?>
	</fieldset>
</form>
</div>