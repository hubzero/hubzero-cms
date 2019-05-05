<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css();
?>
<div class="<?php echo $this->module->module; ?>">
	<?php
	if (count($this->domains) > 0):
		$this->js();

		$colors = array(
			'#56e2cf', // aqua
			'#56aee2', // light blue
			'#5668e2', // blue
			'#8a56e2', // dark purple
			'#cf56e2', // light purple
			'#e256ae', // pink
			'#e25668', // red
			'#e28956', // orange
			'#e2cf56', // yellow
			'#aee256', // lime
			'#68e256', // light green
			'#56e289'  // green-blue
		);

		$total = 0;
		$other = new stdClass;
		$other->domain = Lang::txt('MOD_MEMBERS_OTHER');
		$other->email_count = 0;
		foreach ($this->domains as $i => $domain):
			$total += $domain->email_count;
			if ($domain->domain == 'invalid'):
				$this->domains[$i]->domain = Lang::txt('MOD_MEMBERS_INCOMPLETE');
			endif;
			if ($domain->email_count == 1):
				$other->email_count++;
				unset($this->domains[$i]);
			endif;
		endforeach;

		if ($other->email_count):
			$this->domains[] = $other;
		endif;

		$k = 0;
		$css = '';
		?>
		<div class="overview-container">
			<div id="user-domains-container<?php echo $this->module->id; ?>" class="<?php echo $this->module->module; ?>-chart chrt" data-datasets="<?php echo $this->module->module; ?>-data<?php echo $this->module->id; ?>"></div>

			<script type="application/json" id="<?php echo $this->module->module; ?>-data<?php echo $this->module->id; ?>">
				{
					"datasets": [
					<?php
					foreach ($this->domains as $i => $domain):
						if ($k > 12):
							$k = 0;
						endif;

						$this->domains[$i]->color = $colors[$k];

						$css .= '.' . preg_replace('/[^a-zA-Z0-9]/', '', $domain->domain) . ' { background-color: ' . $colors[$k] . '; }';

						$data  = '{';
						$data .= '"label": "' . $domain->domain . '",';
						$data .= '"data": ' . round(($domain->email_count / $total)*100, 2) . ',';
						$data .= '"color": "' . $colors[$k] . '"';
						$data .= '}';

						$d[] = $data;

						$k++;
					endforeach;

					echo implode(',', $d);
					?>
					]
				}
			</script>

			<p class="members-total"><?php echo $total; ?></p>
		</div>
		<div class="overview-container">
			<?php
			$total = $this->confirmed + $this->unconfirmed;

			$percent = round(($this->confirmed / $total) * 100, 2);

			$css .= '
				.' . $this->module->module . ' .confirmed-graph .bar {
					width: ' . $percent . '%;
				}
			';
			?>
			<table class="stats-overview">
				<tbody>
					<tr>
						<td colspan="3">
							<div>
								<div class="graph confirmed-graph">
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

			<?php
			$total = $this->approved + $this->unapproved;

			$percent = round(($this->approved / $total) * 100, 2);

			$css .= '
				.' . $this->module->module . ' .approved-graph .bar {
					width: ' . $percent . '%;
				}
			';

			$this->css($css);
			?>
			<table class="stats-overview">
				<tbody>
					<tr>
						<td colspan="3">
							<div>
								<div class="graph approved-graph">
									<strong class="bar"><span><?php echo $percent; ?>%</span></strong>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td class="approved">
							<a href="<?php echo Route::url('index.php?option=com_members&approved=1'); ?>" title="<?php echo Lang::txt('MOD_MEMBERS_APPROVED_TITLE'); ?>">
								<?php echo $this->escape($this->approved); ?>
								<span><?php echo Lang::txt('MOD_MEMBERS_APPROVED'); ?></span>
							</a>
						</td>
						<td class="unapproved">
							<a href="<?php echo Route::url('index.php?option=com_members&approved=0'); ?>" title="<?php echo Lang::txt('MOD_MEMBERS_UNAPPROVED_TITLE'); ?>">
								<?php echo $this->escape($this->unapproved); ?>
								<span><?php echo Lang::txt('MOD_MEMBERS_UNAPPROVED'); ?></span>
							</a>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="member-stats-overview">
			<table>
				<thead>
					<tr>
						<th scope="col"><?php echo Lang::txt('MOD_MEMBERS_DOMAIN'); ?></th>
						<th scope="col" class="val"><?php echo Lang::txt('MOD_MEMBERS_REGISTERED'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($this->domains as $domain):
						?>
						<tr>
							<th scope="row">
								<span class="pie-key <?php echo preg_replace('/[^a-zA-Z0-9]/', '', $domain->domain); ?>"></span><?php echo $domain->domain; ?>
							</th>
							<td class="val">
								<?php echo $domain->email_count; ?>
							</td>
						</tr>
						<?php
					endforeach;
					?>
				</tbody>
			</table>
		</div>
		<p class="note"><?php echo Lang::txt('MOD_MEMBERS_NOTE'); ?></p>
	<?php else: ?>
		<div class="none"><?php echo Lang::txt('MOD_MEMBERS_NONE'); ?></div>
	<?php endif; ?>
</div>