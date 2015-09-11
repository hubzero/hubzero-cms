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

defined('_HZEXEC_') or die(); ?>

<form id="<?php echo ($this->params->get('moduleclass_sfx')) ? $this->params->get('moduleclass_sfx') : 'poll' . rand(); ?>" method="post" action="<?php echo Route::url('index.php?option=com_poll'); ?>">
	<fieldset>
		<h4><?php echo $this->escape($poll->title); ?></h4>
		<ul class="poll">
	<?php for ($i = 0, $n = count($options); $i < $n; $i ++) : ?>
			<li class="<?php echo $this->escape($tabclass_arr[$tabcnt]); ?><?php echo $this->params->get('moduleclass_sfx'); ?>">
				<input type="radio" name="voteid" id="voteid<?php echo $options[$i]->id;?>" value="<?php echo $this->escape($options[$i]->id);?>" />
				<label for="voteid<?php echo $options[$i]->id; ?>" class="<?php echo $this->escape($tabclass_arr[$tabcnt]); ?><?php echo $this->params->get('moduleclass_sfx'); ?>">
					<?php echo $this->escape(str_replace('&#039;', "'", $options[$i]->text)); ?>
				</label>
			</li>
			<?php
				$tabcnt = 1 - $tabcnt;
			?>
	<?php endfor; ?>
		</ul>
		<p>
			<input type="submit" name="task_button" class="button" value="<?php echo Lang::txt('MOD_POLL_VOTE'); ?>" />
			 &nbsp;
			<a href="<?php echo Route::url('index.php?option=com_poll&view=poll&id=' . $this->escape($poll->slug)); ?>"><?php echo Lang::txt('MOD_POLL_RESULTS'); ?></a>
		</p>

		<input type="hidden" name="option" value="com_poll" />
		<input type="hidden" name="task" value="vote" />
		<input type="hidden" name="id" value="<?php echo $this->escape($poll->id); ?>" />
		<?php echo Html::input('token'); ?>
	</fieldset>
</form>