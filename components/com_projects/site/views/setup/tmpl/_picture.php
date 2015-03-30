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

if (!$this->model->exists())
{
	return;
}
?>
<div class="grid pictureframe js">
	<div class="col span3">
		<div id="project-image-box" class="project-image-box">
			<img id="project-image-content" src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=media&alias=' . $this->model->get('alias') . '&media=master'); ?>" alt="" />
		</div>
		<?php if ($this->model->get('picture')) { ?>
		<p class="actionlink"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=deleteimg&alias=' . $this->model->get('alias') ); ?>" id="deleteimg">[ <?php echo Lang::txt('COM_PROJECTS_DELETE'); ?> ]</a></p>
		<?php } ?>
	</div>
	<div class="col span9 omega" id="ajax-upload" data-action="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=doajaxupload&no_html=1'); ?>">
		<h5><?php echo Lang::txt('COM_PROJECTS_UPLOAD_NEW_IMAGE'); ?> <span class="hint"><?php echo Lang::txt('COM_PROJECTS_WILL_REPLACE_EXISTING_IMAGE'); ?></span></h5>
		<p id="status-box"></p>
		<label>
			<input name="upload" type="file" class="option uploader" id="uploader" />
		</label>
		<input type="button" value="<?php echo Lang::txt('COM_PROJECTS_UPLOAD'); ?>" class="btn" id="upload-file" />
	</div>
</div>