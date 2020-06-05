<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;

$this->css();

$total = $this->closed + $this->open;

$percent = $total ? round(($this->closed / $total) * 100, 2) : 100;

$this->css('
	.' . $this->module->module . ' .graph .bar {
		width: ' . $percent . '%;
	}
');
?>
<div class="<?php echo $this->module->module; ?>">
	<table class="stats-overview">
		<tbody>
			<?php if ($total) { ?>
			<tr>
				<td colspan="2">
					<div>
						<div class="graph">
							<strong class="bar"><span><?php echo Lang::txt('MOD_ANSWERS_TOTAL_CLOSED', $percent); ?></span></strong>
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