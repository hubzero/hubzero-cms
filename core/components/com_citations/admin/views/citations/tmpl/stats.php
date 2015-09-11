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

Toolbar::title(Lang::txt('CITATION') . ': ' . Lang::txt('CITATION_STATS'), 'citation.png');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('YEAR'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('AFFILIATED'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('NONAFFILIATED'); ?></th>
				<th scope="col"><?php echo Lang::txt('TOTAL'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($this->stats as $year => $amt) { ?>
			<tr>
				<th><?php echo $year; ?></th>
				<td class="priority-2"><?php echo $amt['affiliate']; ?></td>
				<td class="priority-2"><?php echo $amt['non-affiliate']; ?></td>
				<td><?php echo (intval($amt['affiliate']) + intval($amt['non-affiliate'])); ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</form>