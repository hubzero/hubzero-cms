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

defined('_HZEXEC_') or die();

$this->css();

$total = $this->confirmed + $this->unconfirmed;
?>
<div class="<?php echo $this->module->module; ?>">
	<table class="stats-overview">
		<tbody>
			<tr>
				<td colspan="3">
					<div>
						<div class="graph">
							<strong class="bar" style="width: <?php echo ($total ? round(($this->confirmed / $total) * 100, 2) : 0); ?>%"><span><?php echo ($total ? round(($this->confirmed / $total) * 100, 2) : 0); ?>%</span></strong>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="confirmed">
					<a href="<?php echo Route::url('index.php?option=com_members&activation=1&registerDate='); ?>" title="<?php echo Lang::txt('MOD_MEMBERS_CONFIRMED_TITLE'); ?>">
						<?php echo $this->escape($this->confirmed); ?>
						<span><?php echo Lang::txt('MOD_MEMBERS_CONFIRMED'); ?></span>
					</a>
				</td>
				<td class="unconfirmed">
					<a href="<?php echo Route::url('index.php?option=com_members&activation=-1&registerDate='); ?>" title="<?php echo Lang::txt('MOD_MEMBERS_UNCONFIRMED_TITLE'); ?>">
						<?php echo $this->escape($this->unconfirmed); ?>
						<span><?php echo Lang::txt('MOD_MEMBERS_UNCONFIRMED'); ?></span>
					</a>
				</td>
				<td class="newest">
					<a href="<?php echo Route::url('index.php?option=com_members&activation=0&registerDate=' . gmdate("Y-m-d H:i:s", strtotime('-1 day'))); ?>" title="<?php echo Lang::txt('MOD_MEMBERS_NEW_TITLE'); ?>">
						<?php echo $this->escape($this->pastDay); ?>
						<span><?php echo Lang::txt('MOD_MEMBERS_NEW'); ?></span>
					</a>
				</td>
			</tr>
		</tbody>
	</table>
</div>