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

$votes = $this->question->get('helpful', 0);
?>
<span class="vote-like">
	<?php if (User::isGuest()) { ?>
		<span class="vote-button <?php echo ($votes > 0) ? 'like' : 'neutral'; ?> tooltips" title="<?php echo Lang::txt('COM_ANSWERS_VOTE_LIKE_LOGIN'); ?>">
			<?php echo $votes; ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_LIKE'); ?></span>
		</span>
	<?php } else { ?>
		<?php if ($this->question->get('created_by') == User::get('id')) { ?>
			<span class="vote-button <?php echo ($votes > 0) ? 'like' : 'neutral'; ?> tooltips" title="<?php echo Lang::txt('COM_ANSWERS_VOTE_CANT_VOTE_FOR_SELF'); ?>">
				<?php echo $votes; ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_LIKE'); ?></span>
			</span>
		<?php } elseif ($this->voted) { ?>
			<span class="vote-button <?php echo ($votes > 0) ? 'like' : 'neutral'; ?> tooltips" title="<?php echo Lang::txt('COM_ANSWERS_VOTE_ALREADY'); ?>">
				<?php echo $votes; ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_LIKE'); ?></span>
			</span>
		<?php } else { ?>
			<a class="vote-button <?php echo ($votes > 0) ? 'like' : 'neutral'; ?> tooltips" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=vote&id=' . $this->question->get('id') . '&vote=1'); ?>" title="<?php echo Lang::txt('COM_ANSWERS_VOTE_LIKE_TITLE', $votes); ?>">
				<?php echo $votes; ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_LIKE'); ?></span>
			</a>
		<?php } ?>
	<?php } ?>
</span>
