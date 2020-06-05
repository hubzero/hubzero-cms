<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

Html::behavior('chart');

$this->css()
	->js();
?>
<div class="<?php echo $this->module->module; ?>">
	<div id="container<?php echo $this->module->id; ?>" class="<?php echo $this->module->module; ?>-chart chart" data-datasets="<?php echo $this->module->module; ?>-data<?php echo $this->module->id; ?>"></div>

	<script type="application/json" id="<?php echo $this->module->module; ?>-data<?php echo $this->module->id; ?>">
		<?php
		$top = 0;

		$closeddata = '';
		if ($this->closedmonths)
		{
			$c = array();
			foreach ($this->closedmonths as $year => $data)
			{
				foreach ($data as $k => $v)
				{
					$top = ($v > $top) ? $v : $top;
					$c[] = '[' . Date::of($year . '-' . Hubzero\Utility\Str::pad($k - 1, 2) . '-01')->toUnix() . ',' . $v . ']';
					//$c[] = '[new Date(' . $year . ',  ' . ($k - 1) . ', 1),' . $v . ']';
				}
			}
			$closeddata = implode(',', $c);
		}

		$openeddata = '';
		if ($this->openedmonths)
		{
			$o = array();
			foreach ($this->openedmonths as $year => $data)
			{
				foreach ($data as $k => $v)
				{
					$top = ($v > $top) ? $v : $top;
					$o[] = '[' . Date::of($year . '-' . Hubzero\Utility\Str::pad($k - 1, 2) . '-01')->toUnix() . ',' . $v . ']';
					//$o[] = '[new Date(' . $year . ',  ' . ($k - 1) . ', 1),' . $v . ']'; // - $this->closedmonths[$k];
				}
			}
			$openeddata = implode(',', $o);
		}
		?>
		{
			"datasets": [
				{
					"color": "orange",
					"label": "<?php echo Lang::txt('MOD_SUPPORTTICKETS_OPENED'); ?>",
					"data": [<?php echo $openeddata; ?>]
				},
				{
					"color": "#656565",
					"label": "<?php echo Lang::txt('MOD_SUPPORTTICKETS_CLOSED'); ?>",
					"data": [<?php echo $closeddata; ?>]
				}
			]
		}
	</script>

	<div class="breakdown">
		<table class="support-stats-overview open-tickets">
			<tbody>
				<tr>
					<td class="major">
						<a href="<?php echo Route::url('index.php?option=com_support&controller=tickets'); ?>" title="<?php echo Lang::txt('MOD_SUPPORTTICKETS_OPEN_TITLE'); ?>">
							<?php echo isset($this->topened[0]) ? $this->escape($this->topened[0]->count) : ''; ?>
							<span><?php echo Lang::txt('MOD_SUPPORTTICKETS_OPEN'); ?></span>
						</a>
					</td>
					<td class="critical">
						<a href="<?php echo Route::url('index.php?option=com_support&controller=tickets'); ?>" title="<?php echo Lang::txt('MOD_SUPPORTTICKETS_UNASSIGNED_TITLE'); ?>">
							<?php echo isset($this->topened[2]) ? $this->escape($this->topened[2]->count) : ''; ?>
							<span><?php echo Lang::txt('MOD_SUPPORTTICKETS_UNASSIGNED'); ?></span>
						</a>
					</td>
					<td class="newt">
						<a href="<?php echo Route::url('index.php?option=com_support&controller=tickets'); ?>" title="<?php echo Lang::txt('MOD_SUPPORTTICKETS_NEW_TITLE'); ?>">
							<?php echo isset($this->topened[1]) ? $this->escape($this->topened[1]->count) : ''; ?>
							<span><?php echo Lang::txt('MOD_SUPPORTTICKETS_NEW'); ?></span>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
