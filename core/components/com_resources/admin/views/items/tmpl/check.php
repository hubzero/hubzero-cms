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

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_PATH_CHECKER'), 'resources.png');

$total   = number_format(count($this->good+$this->warning+$this->missing));
$missing = number_format(count($this->missing));
?>

<form action="" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<tbody>
			<tr>
				<td>
					<h3><?php echo Lang::txt('COM_RESOURCES_PATH_CHECKER_RESULTS'); ?></h3>
					<p><?php echo Lang::txt('COM_RESOURCES_PATH_CHECKER_RESULTS_SUMMARY', $total, $missing); ?> </p>
					<?php if (count($this->missing) > 0) : ?>
						<hr / >
						<?php echo implode($this->missing, '<br />'); ?>
					<?php endif; ?>

					<?php if (count($this->warning) > 0) : ?>
						<br /><br /><hr />
						<?php echo implode($this->warning, '<br />'); ?>
					<?php endif; ?>

					<?php if (count($this->good) > 0) : ?>
						<br /><br /><hr />
						<?php echo implode($this->good, '<br />'); ?>
					<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>
</form>