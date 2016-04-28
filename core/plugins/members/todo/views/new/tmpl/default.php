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

// No direct access
defined('_HZEXEC_') or die();

$url = 'index.php?option=com_members&id=' . $this->member->get('id') . '&active=todo';

?>

<div id="abox-content">
	<h3><?php echo Lang::txt('PLG_MEMBERS_TODO_ADD_TODO'); ?>
</h3>

<div class="pinboard">
	<form action="<?php echo Route::url($url . '&action=save'); ?>" method="post" id="plg-form" >
		<fieldset>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="uid" id="uid" value="<?php echo $this->member->get('id'); ?>" />
			<input type="hidden" name="active" value="todo" />
			<input type="hidden" name="action" value="save" />
			<?php echo Html::input('token'); ?>
		</fieldset>
		<section class="section intropage">
			<div id="td-item">
				<span class="pin">&nbsp;</span>
				<div class="todo-content">
					<textarea name="content" rows="10" cols="25" placeholder="<?php echo Lang::txt('PLG_MEMBERS_TODO_TYPEIT'); ?>"></textarea>
					<div class="todo-edits">
						<?php if (count($this->projects) > 0 ) { ?>
						<label><?php echo ucfirst(Lang::txt('PLG_MEMBERS_TODO_CHOOSE_PROJECT')); ?>:
							<select name="projectid">
							<?php foreach ($this->projects as $project) {
							?>
								<option value="<?php echo $project->get('id'); ?>"><?php echo stripslashes($project->get('title')) . '(' . $project->get('alias') . ')'; ?></option>
							<?php } ?>
							</select>
						</label>
						<?php } ?>

						<label><?php echo ucfirst(Lang::txt('PLG_MEMBERS_TODO_DUE')); ?>
							<input type="text" name="due" id="dued" class="duebox" placeholder="mm/dd/yyyy" value="" />
						</label>
						<p class="submitarea">
							<input type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_TODO_SAVE'); ?>" class="btn" />
						</p>
					</div>
				</div>
			</div>
		</section>
	</form>
</div>
</div>