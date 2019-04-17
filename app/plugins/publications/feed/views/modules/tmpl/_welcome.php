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

// No direct access
defined('_HZEXEC_') or die();

if (!$this->model->access('content'))
{
	return;
}

$max_s = 3;
$actual_s = count($this->suggestions) >= $max_s ? $max_s : count($this->suggestions);

if ($actual_s <= 1)
{
	return;
}
$i = 0;
?>
<?php if ($actual_s > 1) { ?>
	<div class="welcome">
		<p class="closethis"><a href="<?php echo Route::url('index.php?option=' . $this->option
		. '&alias=' . $this->model->get('alias') . '&active=feed') . '?c=1'; ?>"><?php echo Lang::txt('COM_PROJECTS_PROJECT_CLOSE_THIS'); ?></a></p>

		<h3><?php echo $this->model->access('owner') ? Lang::txt('COM_PROJECTS_WELCOME_TO_PROJECT_CREATOR') : Lang::txt('COM_PROJECTS_WELCOME_TO').' '.stripslashes($this->model->get('title')).' '.Lang::txt('COM_PROJECTS_PROJECT').'!'; ?> </h3>
		<p><?php echo $this->model->access('owner') ? Lang::txt('COM_PROJECTS_WELCOME_SUGGESTIONS_CREATOR') : Lang::txt('COM_PROJECTS_WELCOME_SUGGESTIONS'); ?></p>
		<div id="suggestions" class="suggestions">
			<?php foreach ($this->suggestions as $suggestion)
				{ $i++;
				  if ($i <= $max_s)
					{ ?>
				<div class="<?php echo $suggestion['class']; ?>">
					<p><a href="<?php echo $suggestion['url']; ?>"><?php echo $suggestion['text']; ?></a></p>
				</div>
			<?php }
			} ?>
			<div class="clear"></div>
		</div>
	</div>
<?php } ?>
