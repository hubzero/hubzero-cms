<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

if (User::isGuest()) { ?>
	<p class="warning"><?php echo Lang::txt('MOD_MYSUBMISSIONS_WARNING'); ?></p>
<?php } else {
	$steps = $this->steps;

	if ($this->rows->count())
	{
		$stepchecks = array();
		$laststep = (count($steps) - 1);

		foreach ($this->rows as $row)
		{
			?>
			<div class="submission">
				<h4>
					<?php echo $this->escape(stripslashes($row->title)); ?>
					<a class="edit" href="<?php echo Route::url('index.php?option=com_resources&task=draft&step=1&id=' . $row->id); ?>">
						<?php echo Lang::txt('JACTION_EDIT'); ?>
					</a>
				</h4>
				<table>
					<tbody>
						<tr>
							<th><?php echo Lang::txt('MOD_MYSUBMISSIONS_TYPE'); ?></th>
							<td colspan="2"><?php echo $this->escape($row->type->get('type')); ?></td>
						</tr>
						<?php
						for ($i=1, $n=count($steps); $i < $n; $i++)
						{
							if ($i != $laststep)
							{
								$check = 'step_' . $steps[$i] . '_check';
								$stepchecks[$steps[$i]] = $this->$check($row);

								if ($stepchecks[$steps[$i]])
								{
									$completed = '<span class="yes">' . Lang::txt('MOD_MYSUBMISSIONS_COMPLETED') . '</span>';
								}
								else
								{
									$completed = '<span class="no">' . Lang::txt('MOD_MYSUBMISSIONS_NOT_COMPLETED') . '</span>';
								}
								?>
								<tr>
									<th><?php echo $steps[$i]; ?></th>
									<td><?php echo $completed; ?></td>
									<td>
										<a href="<?php echo Route::url('index.php?option=com_resources&task=draft&step=' . $i . '&id=' . $row->id); ?>">
											<?php echo Lang::txt('JACTION_EDIT'); ?>
										</a>
									</td>
								</tr>
								<?php
							}
						}
						?>
				</table>
				<p class="discrd">
					<a href="<?php echo Route::url('index.php?option=com_resources&task=discard&id=' . $row->id); ?>">
						<?php echo Lang::txt('MOD_MYSUBMISSIONS_DELETE'); ?>
					</a>
				</p>
				<p class="review">
					<a href="<?php echo Route::url('index.php?option=com_com_resources&task=draft&step=' . $laststep . '&id=' . $row->id); ?>">
						<?php echo Lang::txt('MOD_MYSUBMISSIONS_REVIEW_SUBMIT'); ?>
					</a>
				</p>
				<div class="clear"></div>
			</div>
			<?php
		}
	}
	else
	{
		?>
		<p><?php echo Lang::txt('MOD_MYSUBMISSIONS_NONE'); ?></p>
		<?php
	}
}
