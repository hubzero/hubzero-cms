<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
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

$this->css()
	->js()
	->js('setup')
	->css('jquery.fancybox.css', 'system');

// Display page title
$this->view('_title')
     ->set('model', $this->model)
     ->set('step', $this->step)
     ->set('option', $this->option)
     ->set('title', $this->title)
     ->display();
?>

<section class="main section" id="setup">
	<?php
		// Display status message
		$this->view('_statusmsg', 'projects')
		     ->set('error', $this->getError())
		     ->set('msg', $this->msg)
		     ->display();
	?>
	<?php
		// Display metadata
		$this->view('_metadata')
		     ->set('model', $this->model)
		     ->set('step', $this->step)
		     ->set('option', $this->option)
		     ->display();
	?>
	<?php
	// Display steps
	$this->view('_steps')
	     ->set('model', $this->model)
	     ->set('step', $this->step)
	     ->display();
	?>
	<div class="clear"></div>	
	<div class="setup-wrap">
		<form id="hubForm" method="post" action="index.php" enctype="multipart/form-data">
			<div class="explaination">
				<h4><?php echo Lang::txt('COM_PROJECTS_HOWTO_TITLE_NAME_PROJECT'); ?></h4>
				<p><?php echo Lang::txt('COM_PROJECTS_HOWTO_NAME_PROJECT'); ?></p>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_PROJECTS_PICK_NAME'); ?></legend>
				<?php // Display form fields
				$this->view('_form')
				     ->set('model', $this->model)
				     ->set('step', $this->step)
				     ->set('option', $this->option)
				     ->set('controller', 'setup')
				     ->set('section', $this->section)
				     ->display();
				?>
				<input type="hidden" id="extended" name="extended" value="0" />
				<input type="hidden" name="verified" id="verified" value="0" />
				<label for="field-title"><?php echo Lang::txt('COM_PROJECTS_TITLE'); ?> <span class="required"><?php echo Lang::txt('REQUIRED'); ?></span>
					<span class="verification"></span>
					<input name="title" maxlength="250" id="field-title" type="text" value="<?php echo $this->escape($this->model->get('title')); ?>" class="verifyme" />
				</label>
				<p class="hint"><?php echo Lang::txt('COM_PROJECTS_HINTS_TITLE'); ?></p>
				<label for="field-alias"><?php echo Lang::txt('COM_PROJECTS_ALIAS_NAME'); ?> <span class="required"><?php echo Lang::txt('REQUIRED'); ?></span>
					<span class="verification"></span>
					<input name="name" maxlength="30" id="field-alias" type="text" value="<?php echo $this->model->get('alias'); ?>" <?php echo $this->model->get('id') ? ' disabled="disabled"' : ''; ?> class="verifyme" />
				</label>
				<p class="hint"><?php echo Lang::txt('COM_PROJECTS_HINTS_NAME'); ?></p>
				<div id="moveon" class="nogo">
					<p class="submitarea">
						<input type="submit" value="<?php echo Lang::txt('COM_PROJECTS_SAVE_AND_CONTINUE'); ?>" class="btn disabled" disabled="disabled" />
					</p>
				</div>
				<div id="describe">
					<h2><?php echo Lang::txt('COM_PROJECTS_DESCRIBE_PROJECT'); ?></h2>
					<p class="question"><?php echo Lang::txt('COM_PROJECTS_QUESTION_DESCRIBE_NOW_OR_LATER'); ?></p>	
					<p>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=save&id=' . $this->model->get('id')); ?>" id="next_desc" class="btn btn-success"><?php echo Lang::txt('COM_PROJECTS_QUESTION_DESCRIBE_YES'); ?></a>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=save&id=' . $this->model->get('id')); ?>" id="next_step" class="btn"><?php echo Lang::txt('COM_PROJECTS_QUESTION_DESCRIBE_NO'); ?></a>
					</p>
				</div>
			</fieldset>
			<div class="clear"></div>
			<div id="describearea">
				<div class="explaination">
					<h4><?php echo Lang::txt('COM_PROJECTS_HOWTO_TITLE_DESC'); ?></h4>
					<p><?php echo Lang::txt('COM_PROJECTS_HOWTO_DESC_PROJECT'); ?></p>
				</div>
				<fieldset>
					<legend><?php echo Lang::txt('COM_PROJECTS_DESCRIBE_PROJECT'); ?></legend>
					<label for="field-about"><?php echo Lang::txt('COM_PROJECTS_ABOUT'); ?> <span class="optional"><?php echo Lang::txt('OPTIONAL'); ?></span>
						<?php
							echo $this->editor('about', $this->escape($this->model->about('raw')), 35, 25, 'field-about', array('class' => 'minimal no-footer'));
						?>
					</label>
					<h4><?php echo Lang::txt('COM_PROJECTS_SETTING_APPEAR_IN_SEARCH'); ?></h4>
					<label>
						<input class="option" name="private" type="radio" value="1" <?php echo !$this->model->isPublic() ? 'checked="checked"' : ''; ?> /> <?php echo Lang::txt('COM_PROJECTS_PRIVACY_EDIT_PRIVATE'); ?>
					</label>
					<label>
						<input class="option" name="private" type="radio" value="0" <?php echo $this->model->isPublic() ? 'checked="checked"' : ''; ?> /> <?php echo Lang::txt('COM_PROJECTS_PRIVACY_EDIT_PUBLIC'); ?>
					</label>
				</fieldset>
			<?php if ($this->model->get('id')) { ?>
			<div class="js">
				<div class="clear"></div>
				<div class="explaination">
					<h4><?php echo Lang::txt('COM_PROJECTS_HOWTO_TITLE_THUMB'); ?></h4>
					<p><?php echo Lang::txt('COM_PROJECTS_HOWTO_THUMB'); ?></p>
				</div>
				<fieldset>
					<legend><?php echo Lang::txt('COM_PROJECTS_ADD_PICTURE'); ?></legend>
					<?php
						// Display project image upload
						$this->view('_picture')
						     ->set('model', $this->model)
						     ->set('step', $this->step)
						     ->set('option', $this->option)
						     ->display();
					?>
				</fieldset>
			</div>
			<?php } ?>
				<div class="submitarea">
					<input type="submit" value="<?php echo Lang::txt('COM_PROJECTS_SAVE_AND_CONTINUE'); ?>" class="btn btn-success" id="gonext" />
				</div>
			</div>
		</form>
	</div>
</section>