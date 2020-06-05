<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_AUDIT'), 'resources');

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

						$this->css('
							.bars .skipped' . $key . ' { width: 100%; }
							.bars .passed' . $key . ' { width: ' . $passed . '%; }
							.bars .failed' . $key . ' { width: ' . $failed . '%; }
						');
						?>
						<span class="bar skipped skipped<?php echo $key; ?>"></span>
						<span class="bar passed passed<?php echo $key; ?>"></span>
						<span class="bar failed failed<?php echo $key; ?>"></span>
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
								<th scope="col"><?php echo Lang::txt('COM_RESOURCES_AUDIT_ID'); ?></th>
								<th scope="col"><?php echo Lang::txt('COM_RESOURCES_AUDIT_PARENT_ID'); ?></th>
								<th scope="col"><?php echo Lang::txt('COM_RESOURCES_AUDIT_ENTRY'); ?></th>
								<th scope="col"><?php echo Lang::txt('COM_RESOURCES_AUDIT_STATUS'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							switch ($this->status)
							{
								case 'failed':
									$status = -1;
									break;
								case 'skipped':
									$status = 0;
									break;
								case 'passed':
									$status = 1;
									break;
							}
							$results = \Hubzero\Content\Auditor\Result::all()
								->whereEquals('scope', 'resource')
								->whereEquals('test_id', $this->test)
								->whereEquals('status', $status)
								->ordered()
								->rows();
							foreach ($results as $result) { ?>
								<tr>
									<th>
										<?php echo $result->get('scope_id'); ?>
									</th>
									<td>
										<?php
										$parents = Components\Resources\Models\Association::all()
											->whereEquals('child_id', $result->get('scope_id'))
											->rows()
											->fieldsByKey('parent_id');

										if (!empty($parents))
										{
											echo implode(', ', $parents);
										}
										?>
									</td>
									<td>
										<a href="<?php echo Route::url('index.php?option=com_resources&task=edit&id=' . $result->get('scope_id')); ?>">
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
										</a>
									</td>
									<td>
										<?php echo '<span class="test-status ' . $result->status() . '">' . $result->status() . '</span>'; ?>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			<?php } ?>
		</div>
	<?php } ?>
</form>