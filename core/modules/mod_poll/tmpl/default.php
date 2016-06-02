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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die(); ?>

<form id="<?php echo ($this->params->get('moduleclass_sfx')) ? $this->params->get('moduleclass_sfx') : 'poll' . rand(); ?>" method="post" action="<?php echo Route::url('index.php?option=com_poll'); ?>">
	<fieldset>
		<h4><?php echo $this->escape($poll->title); ?></h4>
		<ul class="poll">
			<?php foreach ($poll->options()->where('text', '!=', '')->order('id', 'asc')->rows() as $option) : ?>
				<li class="poll-option <?php echo $this->params->get('moduleclass_sfx'); ?>">
					<input type="radio" name="voteid" id="voteid<?php echo $option->id; ?>" value="<?php echo $this->escape($option->id); ?>" />
					<label for="voteid<?php echo $option->id; ?>" class="poll-option-text <?php echo $this->params->get('moduleclass_sfx'); ?>">
						<?php echo $this->escape(str_replace('&#039;', "'", $option->text)); ?>
					</label>
				</li>
			<?php endforeach; ?>
		</ul>
		<p>
			<input type="submit" name="task_button" class="button" value="<?php echo Lang::txt('MOD_POLL_VOTE'); ?>" />
			 &nbsp;
			<a href="<?php echo Route::url('index.php?option=com_poll&view=poll&id=' . $this->escape($poll->id . ':' . $poll->alias)); ?>"><?php echo Lang::txt('MOD_POLL_RESULTS'); ?></a>
		</p>

		<input type="hidden" name="option" value="com_poll" />
		<input type="hidden" name="task" value="vote" />
		<input type="hidden" name="id" value="<?php echo $this->escape($poll->id); ?>" />
		<?php echo Html::input('token'); ?>
	</fieldset>
</form>