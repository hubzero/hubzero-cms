<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2015 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// No direct access
defined('_HZEXEC_') or die();

// Do some text cleanup
$title = $this->escape($this->model->get('title'));

?>
<div id="project-wrap">
	<section class="main section">
		<?php
		$this->view('_header', 'projects')
		     ->set('model', $this->model)
		     ->set('showPic', 0)
		     ->set('showPrivacy', 0)
		     ->set('goBack', 0)
		     ->set('showUnderline', 1)
		     ->set('option', $this->option)
		     ->display();
		?>
		<p class="warning"><?php echo Lang::txt('COM_PROJECTS_INFO_OWNER_DELETED'); ?></p>
		<form method="post" action="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias')); ?>" id="hubForm">
			<fieldset >
				<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
				<input type="hidden" name="task" value="fixownership" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<h4><?php echo Lang::txt('COM_PROJECTS_OWNER_DELETED_OPTIONS'); ?></h4>
			<label><input class="option" name="keep" type="radio" value="1" checked="checked" /> <?php echo Lang::txt('COM_PROJECTS_OWNER_KEEP_PROJECT'); ?></label>
			<label><input class="option" name="keep" type="radio" value="0" /> <?php echo Lang::txt('COM_PROJECTS_OWNER_DELETE_PROJECT'); ?></label>
			<p class="submitarea">
				<input type="submit" class="btn" value="<?php echo Lang::txt('COM_PROJECTS_SAVE_MY_CHOICE'); ?>"  />
			</p>
			</fieldset>
		</form>
	</section><!-- / .main section -->
</div>