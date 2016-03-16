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

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_AUDIT'));

$this->css('audit.css');

Html::behavior('tooltip');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=check'); ?>" method="post" name="adminForm" id="item-form">
	<?php foreach ($this->tests as $key => $test) { ?>
		<div class="test">
			<div class="test-overview grid">
				<div class="test-chart col span6">
					<h3 class="test-header">
						<span class="test-title"><?php echo $this->escape($test['name']); ?></span> <span class="test-total"><?php echo Lang::txt('COM_RESOURCES_NUM_TOTAL', '<strong>' . $test['total'] . '</strong>'); ?></span>
					</h3>
					<div class="bars">
						<?php
						$passed = $test['total'] <= 0 ? 0 : round(($test['totals']['passed'] / $test['total']) * 100, 2);
						$failed = $test['total'] <= 0 ? 0 : round(($test['totals']['failed'] / $test['total']) * 100, 2);
						$passed = $passed + $failed;
						?>
						<span class="bar skipped" style="width: 100%"></span>
						<span class="bar passed" style="width: <?php echo $passed; ?>%"></span>
						<span class="bar failed" style="width: <?php echo $failed; ?>%"></span>
					</div>
				</div>
				<div class="test-key col span2">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=check&status=failed&test=' . $key); ?>" class="test-value failed hasTip" title="<?php echo Lang::txt('COM_RESOURCES_NUM_FAILED_TITLE'); ?>"><?php echo Lang::txt('COM_RESOURCES_NUM_FAILED', $test['totals']['failed']); ?></a>
				</div>
				<div class="test-key col span2">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=check&status=passed&test=' . $key); ?>" class="test-value passed hasTip" title="<?php echo Lang::txt('COM_RESOURCES_NUM_PASSED_TITLE'); ?>"><?php echo Lang::txt('COM_RESOURCES_NUM_FAILED', $test['totals']['passed']); ?></a>
				</div>
				<div class="test-key col span2">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=check&status=skipped&test=' . $key); ?>" class="test-value skipped hasTip" title="<?php echo Lang::txt('COM_RESOURCES_NUM_SKIPPED_TITLE'); ?>"><?php echo Lang::txt('COM_RESOURCES_NUM_FAILED', $test['totals']['skipped']); ?></a>
				</div>
			</div>
			<?php if ($this->test == $key && $this->status) { ?>
				<div class="test-data">
					<table>
						<thead>
							<tr>
								<th><?php echo Lang::txt('COM_RESOURCES_AUDIT_ID'); ?></th>
								<th><?php echo Lang::txt('COM_RESOURCES_AUDIT_ENTRY'); ?></th>
								<th><?php echo Lang::txt('COM_RESOURCES_AUDIT_STATUS'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							switch ($this->status)
							{
								case 'failed': $status = -1; break;
								case 'skipped': $status = 0; break;
								case 'passed': $status = 1; break;
							}
							$results = \Hubzero\Content\Auditor\Result::all()
								->whereEquals('scope', 'resource')
								->whereEquals('test_id', $this->test)
								->whereEquals('status', $status)
								->ordered()
								->rows();
							foreach ($results as $result) { ?>
								<tr>
									<th><?php echo $result->get('scope_id'); ?></th>
									<td><a href="<?php echo Route::url('index.php?option=com_resources&task=edit&id=' . $result->get('scope_id')); ?>">
										<?php
										if ($notes = $result->get('notes'))
										{
											$notes = json_decode($notes);
											if (isset($notes->field))
											{
												$result->set('title', $notes->field);
											}
										}
										echo $result->get('title', Lang::txt('COM_RESOURCES_UNKNOWN')); ?>
									</a></td>
									<td><?php echo '<span class="test-status ' . $result->status() . '">' . $result->status() . '</span>'; ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			<?php } ?>
		</div>
	<?php } ?>
</form>