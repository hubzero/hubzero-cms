<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css();

$total = $this->confirmed + $this->unconfirmed;

$percent = round(($this->confirmed / $total) * 100, 2);

$this->css('
	.' . $this->module->module . ' .graph .bar {
		width: ' . $percent . '%;
	}
');
?>
<div class="<?php echo $this->module->module; ?>">
	<table class="stats-overview">
		<tbody>
			<tr>
				<td colspan="3">
					<div>
						<div class="graph">
							<strong class="bar"><span><?php echo $percent; ?>%</span></strong>
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