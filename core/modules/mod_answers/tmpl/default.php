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

defined('_HZEXEC_') or die;

$this->css();

$total = $this->closed + $this->open;

?>
<div class="<?php echo $this->module->module; ?>">
	<table class="stats-overview">
		<tbody>
			<?php if ($total) { ?>
			<tr>
				<td colspan="2">
					<div>
						<div class="graph">
							<strong class="bar" style="width: <?php echo round(($this->closed / $total) * 100, 2); ?>%"><span><?php echo Lang::txt('MOD_ANSWERS_TOTAL_CLOSED', round(($this->closed / $total) * 100, 2)); ?></span></strong>
						</div>
					</div>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td class="closed">
					<a href="<?php echo Route::url('index.php?option=com_answers&state=1'); ?>" title="<?php echo Lang::txt('MOD_ANSWERS_CLOSED_TITLE'); ?>">
						<?php echo $this->escape($this->closed); ?>
						<span><?php echo Lang::txt('MOD_ANSWERS_CLOSED'); ?></span>
					</a>
				</td>
				<td class="asked">
					<a href="<?php echo Route::url('index.php?option=com_answers&state=0'); ?>" title="<?php echo Lang::txt('MOD_ANSWERS_ASKED_TITLE'); ?>">
						<?php echo $this->escape($this->open); ?>
						<span><?php echo Lang::txt('MOD_ANSWERS_ASKED'); ?></span>
					</a>
				</td>
			</tr>
		</tbody>
	</table>
</div>