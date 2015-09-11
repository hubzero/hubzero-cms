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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('create.css')
     ->js('create.js');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft'); ?>">
				<?php echo Lang::txt('COM_CONTRIBUTE_NEW_SUBMISSION'); ?>
			</a>
		</p>
	</div><!-- / #content-header -->
</header><!-- / #content-header -->

<section class="main section">
	<?php
		$this->view('steps')
		     ->set('option', $this->option)
		     ->set('step', $this->step)
		     ->set('steps', $this->steps)
		     ->set('id', $this->id)
		     ->set('resource', $this->row)
		     ->set('progress', $this->progress)
		     ->display();
	?>

	<?php if ($this->getError()) { ?>
		<p class="warning"><?php echo $this->getError(); ?></p>
	<?php } ?>

	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft&step=' . $this->next_step . '&id=' . $this->id); ?>" method="post" id="hubForm">
		<div class="explaination">
			<h4><?php echo Lang::txt('COM_CONTRIBUTE_GROUPS_HEADER'); ?></h4>
			<p><?php echo Lang::txt('COM_CONTRIBUTE_GROUPS_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_CONTRIBUTE_GROUPS_OWNERSHIP'); ?></legend>

		<?php if ($this->groups && count($this->groups) > 0) { ?>
			<div class="grid">
				<div class="col span6">
					<label for="group_owner">
						<?php echo Lang::txt('COM_CONTRIBUTE_GROUPS_GROUP'); ?>: <span class="optional"><?php echo Lang::txt('COM_CONTRIBUTE_OPTIONAL'); ?></span>
						<select name="group_owner" id="group_owner">
							<option value=""><?php echo Lang::txt('COM_CONTRIBUTE_SELECT_GROUP'); ?></option>
							<?php
							if ($this->groups && count($this->groups) > 0)
							{
								foreach ($this->groups as $group)
								{
								?>
								<option value="<?php echo $this->escape($group->cn); ?>"<?php if ($this->row->group_owner == $group->cn) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($group->description)); ?></option>
								<?php
								}
							}
							?>
						</select>
					</label>
				</div>
				<div class="col span6 omega">
					<label for="access">
						<?php echo Lang::txt('COM_CONTRIBUTE_GROUPS_ACCESS_LEVEL'); ?>: <span class="optional"><?php echo Lang::txt('COM_CONTRIBUTE_OPTIONAL'); ?></span>
						<select name="access" id="access">
							<option value="0"<?php if ($this->row->access == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_PUBLIC'); ?></option>
							<option value="1"<?php if ($this->row->access == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_REGISTERED'); ?></option>
							<option value="3"<?php if ($this->row->access == 3) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_PROTECTED'); ?></option>
							<option value="4"<?php if ($this->row->access == 4) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_PRIVATE'); ?></option>
						</select>
					</label>
				</div>
			</div>
			<p>
				<strong><?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_PUBLIC'); ?></strong> = <?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_PUBLIC_EXPLANATION'); ?><br />
				<strong><?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_REGISTERED'); ?></strong> = <?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_REGISTERED_EXPLANATION'); ?><br />
				<strong><?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_PROTECTED'); ?></strong> = <?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_PROTECTED_EXPLANATION'); ?><br />
				<strong><?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_PRIVATE'); ?></strong> = <?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_PRIVATE_EXPLANATION'); ?>
			</p>
		<?php } else { ?>
			<p class="information">
				<?php echo Lang::txt('COM_CONTRIBUTE_GROUPS_JOIN'); ?>
			</p>
		<?php } ?>
		</fieldset><div class="clear"></div>

		<div class="explaination">
			<h4><?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_NO_LOGIN'); ?></h4>
			<p><?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_NO_LOGIN_EXPLANATION'); ?></p>

			<h4><?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_NOT_AUTHOR'); ?></h4>
			<p><?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_NOT_AUTHOR_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_AUTHORS'); ?></legend>
			<div class="field-wrap">
				<iframe width="100%" height="400" frameborder="0" name="authors" id="authors" scrolling="auto" src="/index.php?option=<?php echo $this->option; ?>&amp;controller=authors&amp;id=<?php echo $this->id; ?>&amp;tmpl=component"></iframe>
			</div>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
			<input type="hidden" name="step" value="<?php echo $this->next_step; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		</fieldset><div class="clear"></div>

		<div class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('COM_CONTRIBUTE_NEXT'); ?>" />
		</div>
	</form>
</section><!-- / .main section -->
