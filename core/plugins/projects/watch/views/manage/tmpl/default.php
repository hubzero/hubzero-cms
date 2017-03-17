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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

?>

<div id="abox-content">
	<h3><?php echo $this->watch->get('id') ? Lang::txt('PLG_PROJECTS_WATCH_MANAGE') : Lang::txt('PLG_PROJECTS_WATCH_SUBSCRIBE'); ?></h3>
	<form id="hubForm-ajax" method="post" action="<?php echo Route::url($this->project->link() . '&active=watch&action=save'); ?>">
		<fieldset >
			<input type="hidden" name="id" value="<?php echo $this->project->get('id'); ?>" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="task" value="view" />
			<input type="hidden" name="active" value="watch" />
			<input type="hidden" name="ajax" value="1" />
			<input type="hidden" name="option" value="com_projects" />
			<h5><?php echo Lang::txt('PLG_PROJECTS_WATCH_SUBSCRIBE_CATEGORIES'); ?>:</h5>

			<ul class="cat-list">
			<?php foreach ($this->cats as $name => $checked) { ?>
				<li>
					<input type="checkbox" name="category[<?php echo $name; ?>]" value="1" <?php if ($checked == 1) { echo 'checked="checked"'; } ?> /> <span class="cat-icon"><span class="<?php echo $name; ?>"></span></span> <span><?php echo Lang::txt('PLG_PROJECTS_WATCH_' . strtoupper($name)); ?></span>
				</li>
			<?php  } ?>
			</ul>
			<?php /*
			<div class="delivery">
				<p><?php echo Lang::txt('PLG_PROJECTS_WATCH_UPDATES_DELIVERED_TO_EMAIL', User::get('email')); ?></p>
			<h5><?php echo Lang::txt('PLG_PROJECTS_WATCH_UPDATES_FREQUENCY'); ?>:</h5>
			<ul class="cat-list">
				<li>
					<input type="radio" name="frequency" value="immediate" checked="checked" /> <span><?php echo Lang::txt('PLG_PROJECTS_WATCH_FREQUENCY_IMMEDIATE'); ?></span>
				</li>
			</ul>
			</div>
			*/?>
			<input type="hidden" name="frequency" value="immediate" />

			<p class="submitarea">
				<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PROJECTS_WATCH_SAVE'); ?>" />
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('PLG_PROJECTS_WATCH_CANCEL'); ?>" />
			</p>
			<?php echo Html::input('token'); ?>
		</fieldset>
	</form>
</div>