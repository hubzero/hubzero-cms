<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Massmail\Admin\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_MASSMAIL') . ': ' . Lang::txt('COM_MASSMAIL_DASHBOARD'), 'massmail');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
}

$sendingOn = (int) $this->params->get('sending_enabled', 0);
$cronOn    = ($this->cron && (int) $this->cron->state == 1);
$sys       = $this->systemLastHour;    // -1 if log unreadable
$queueTxt  = $this->queueSize < 0 ? Lang::txt('COM_MASSMAIL_DASH_QUEUE_UNREADABLE') : number_format($this->queueSize);

// ---- helpers for tiny inline SVG charts --------------------------------------
$Mailing = '\Components\Massmail\Models\Mailing';
$statusColors = array(
	'draft' => '#94a3b8', 'pending_approval' => '#dd6b20', 'preparing' => '#3182ce',
	'sending' => '#2b6cb0', 'paused' => '#b7791f', 'sent' => '#2f855a', 'cancelled' => '#a0aec0'
);

// Reusable bar-chart renderer (single series). Returns an SVG string.
$bars = function ($series, $everyN, $color) {
	$vals = array();
	foreach ($series as $h) { $vals[] = $h['value']; }
	$peak = $vals ? max($vals) : 0;
	if ($peak <= 0)
	{
		return '<p class="mm-legend">' . Lang::txt('COM_MASSMAIL_CHART_NO_DATA') . '</p>';
	}
	$W = 900; $H = 200; $padL = 34; $padB = 22; $padT = 14;
	$plotW = $W - $padL; $plotH = $H - $padB - $padT;
	$n = count($series); $slot = $plotW / $n; $bw = max(2, $slot - 3); $base = $padT + $plotH;
	$svg  = '<figure><svg viewBox="0 0 ' . $W . ' ' . $H . '" width="100%" role="img" preserveAspectRatio="xMidYMid meet">';
	$svg .= '<line x1="' . $padL . '" y1="' . $padT . '" x2="' . $W . '" y2="' . $padT . '" stroke="#edf2f7"/>';
	$svg .= '<line x1="' . $padL . '" y1="' . $base . '" x2="' . $W . '" y2="' . $base . '" stroke="#e2e8f0"/>';
	$svg .= '<text x="' . ($padL - 6) . '" y="' . ($padT + 4) . '" text-anchor="end" font-size="10" fill="#a0aec0">' . $peak . '</text>';
	$svg .= '<text x="' . ($padL - 6) . '" y="' . $base . '" text-anchor="end" font-size="10" fill="#a0aec0">0</text>';
	foreach ($series as $i => $h)
	{
		$x = $padL + $i * $slot + ($slot - $bw) / 2;
		$bh = ($h['value'] / $peak) * $plotH;
		$y = $base - $bh;
		if ($h['value'] > 0)
		{
			$svg .= '<rect x="' . round($x, 1) . '" y="' . round($y, 1) . '" width="' . round($bw, 1) . '" height="' . round($bh, 1) . '" rx="3" fill="' . $color . '"><title>' . $h['label'] . ' — ' . $h['value'] . '</title></rect>';
		}
		if ($i % $everyN == 0)
		{
			$svg .= '<text x="' . round($x + $bw / 2, 1) . '" y="' . ($H - 6) . '" text-anchor="middle" font-size="9" fill="#a0aec0">' . $h['label'] . '</text>';
		}
	}
	$svg .= '</svg></figure>';
	return $svg;
};
?>
<style>
.mm-dash { max-width: 1100px; }
.mm-dash .mm-alert { padding:10px 14px; border-radius:6px; margin:0 0 10px; font-weight:600; }
.mm-dash .mm-alert.warn { background:#fffaf0; border:1px solid #f6ad55; color:#9c4221; }
.mm-tiles { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:12px; margin:14px 0 22px; }
.mm-tile { background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:14px 16px; }
.mm-tile .n { font-size:26px; font-weight:700; line-height:1.1; color:#1a202c; }
.mm-tile .l { font-size:12px; text-transform:uppercase; letter-spacing:.03em; color:#718096; margin-top:4px; }
.mm-tile .s { font-size:12px; color:#a0aec0; margin-top:2px; }
.mm-tile.on  .n { color:#2f855a; } .mm-tile.off .n { color:#c53030; }
.mm-cards { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
@media (max-width:800px){ .mm-cards{ grid-template-columns:1fr; } }
.mm-card { background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:16px; }
.mm-card h3 { margin:0 0 10px; font-size:14px; color:#2d3748; }
.mm-card figure { margin:0; }
.mm-legend { font-size:11px; color:#718096; margin-top:6px; }
.mm-pill { display:inline-block; padding:2px 8px; border-radius:10px; color:#fff; font-size:11px; font-weight:600; }
.mm-sbar { display:flex; align-items:center; gap:8px; margin:6px 0; font-size:13px; }
.mm-sbar .bar { height:10px; border-radius:5px; }
.mm-recent { width:100%; border-collapse:collapse; margin-top:6px; }
.mm-recent th, .mm-recent td { text-align:left; padding:6px 8px; border-bottom:1px solid #edf2f7; font-size:13px; }
</style>

<div class="mm-dash">

	<?php if (!$sendingOn) { ?><div class="mm-alert warn"><?php echo Lang::txt('COM_MASSMAIL_DASH_SENDING_DISABLED'); ?></div><?php } ?>
	<?php if (!$cronOn) { ?><div class="mm-alert warn"><?php echo Lang::txt('COM_MASSMAIL_DASH_CRON_WARNING'); ?></div><?php } ?>

	<div class="mm-tiles">
		<div class="mm-tile <?php echo $sendingOn ? 'on' : 'off'; ?>">
			<div class="n"><?php echo $sendingOn ? Lang::txt('JYES') : Lang::txt('JNO'); ?></div>
			<div class="l"><?php echo Lang::txt('COM_MASSMAIL_DASH_SENDING_ENABLED'); ?></div>
		</div>
		<div class="mm-tile">
			<div class="n"><?php echo number_format($this->hourly); ?></div>
			<div class="l"><?php echo Lang::txt('COM_MASSMAIL_DASH_HOURLY_LIMIT'); ?></div>
		</div>
		<div class="mm-tile">
			<div class="n"><?php echo $sys < 0 ? number_format($this->ourLastHour) : number_format($sys); ?></div>
			<div class="l"><?php echo Lang::txt('COM_MASSMAIL_DASH_SENT_LAST_HOUR'); ?></div>
			<div class="s"><?php echo $sys < 0 ? Lang::txt('COM_MASSMAIL_DASH_OURS_ONLY') : Lang::txt('COM_MASSMAIL_DASH_INCL_ALL', number_format($this->ourLastHour)); ?></div>
		</div>
		<div class="mm-tile">
			<div class="n"><?php echo number_format($this->hourlyRemaining); ?></div>
			<div class="l"><?php echo Lang::txt('COM_MASSMAIL_DASH_REMAINING'); ?></div>
			<div class="s"><?php echo Lang::txt('COM_MASSMAIL_DASH_NEXT_RUN', number_format($this->nextRunBudget)); ?></div>
		</div>
		<div class="mm-tile">
			<div class="n"><?php echo $queueTxt; ?></div>
			<div class="l"><?php echo Lang::txt('COM_MASSMAIL_DASH_QUEUE_SIZE'); ?></div>
			<div class="s"><?php echo Lang::txt('COM_MASSMAIL_DASH_WATERMARK') . ': ' . number_format($this->watermark); ?></div>
		</div>
		<div class="mm-tile">
			<div class="n"><?php echo number_format($this->messagesPending); ?></div>
			<div class="l"><?php echo Lang::txt('COM_MASSMAIL_DASH_MESSAGES_PENDING'); ?></div>
		</div>
		<div class="mm-tile">
			<div class="n"><?php echo number_format($this->messagesSent); ?></div>
			<div class="l"><?php echo Lang::txt('COM_MASSMAIL_DASH_MESSAGES_SENT'); ?></div>
		</div>
		<div class="mm-tile">
			<div class="n"><?php echo number_format($this->messagesFailed); ?></div>
			<div class="l"><?php echo Lang::txt('COM_MASSMAIL_FAILED'); ?></div>
		</div>
	</div>

	<div class="mm-card" style="margin-bottom:16px;">
		<h3>
			<?php echo Lang::txt('COM_MASSMAIL_CHART_OUTBOUND_DAILY'); ?>
			<span style="font-weight:400;color:#718096;font-size:12px;">
				&mdash; <?php echo Lang::txt('COM_MASSMAIL_DASH_TODAY', number_format($this->sentToday)); ?>
				&middot; <?php echo Lang::txt('COM_MASSMAIL_DASH_7DAYS', number_format($this->sent7d)); ?>
			</span>
		</h3>
		<?php echo $bars($this->dailyVolume, 5, '#2b6cb0'); ?>
		<p class="mm-legend"><?php echo Lang::txt('COM_MASSMAIL_CHART_OUTBOUND_LEGEND'); ?></p>
	</div>

	<div class="mm-card" style="margin-bottom:16px;">
		<h3><?php echo Lang::txt('COM_MASSMAIL_CHART_OUTBOUND_HOURLY'); ?></h3>
		<?php echo $bars($this->hourlyVolume, 6, '#3182ce'); ?>
		<p class="mm-legend"><?php echo Lang::txt('COM_MASSMAIL_CHART_HOUR_LOCAL'); ?></p>
	</div>

	<div class="mm-cards">
		<div class="mm-card">
			<h3><?php echo Lang::txt('COM_MASSMAIL_CHART_SENDS_24H'); ?></h3>
			<?php
			$series = $this->hourlySeries;
			$vals = array();
			foreach ($series as $h) { $vals[] = $h['value']; }
			$peak = $vals ? max($vals) : 0;
			if ($peak <= 0) { ?>
				<p class="mm-legend"><?php echo Lang::txt('COM_MASSMAIL_CHART_NO_SENDS'); ?></p>
			<?php } else {
				$W = 720; $H = 190; $padL = 30; $padB = 22; $padT = 14;
				$plotW = $W - $padL; $plotH = $H - $padB - $padT;
				$n = count($series); $slot = $plotW / $n; $bw = max(3, $slot - 3);
				$base = $padT + $plotH;
				?>
				<figure><svg viewBox="0 0 <?php echo $W; ?> <?php echo $H; ?>" width="100%" role="img" aria-label="<?php echo Lang::txt('COM_MASSMAIL_CHART_SENDS_24H'); ?>" preserveAspectRatio="xMidYMid meet">
					<line x1="<?php echo $padL; ?>" y1="<?php echo $padT; ?>" x2="<?php echo $W; ?>" y2="<?php echo $padT; ?>" stroke="#edf2f7" stroke-width="1" />
					<line x1="<?php echo $padL; ?>" y1="<?php echo $base; ?>" x2="<?php echo $W; ?>" y2="<?php echo $base; ?>" stroke="#e2e8f0" stroke-width="1" />
					<text x="<?php echo $padL - 6; ?>" y="<?php echo $padT + 4; ?>" text-anchor="end" font-size="10" fill="#a0aec0"><?php echo $peak; ?></text>
					<text x="<?php echo $padL - 6; ?>" y="<?php echo $base; ?>" text-anchor="end" font-size="10" fill="#a0aec0">0</text>
					<?php foreach ($series as $i => $h) {
						$x = $padL + $i * $slot + ($slot - $bw) / 2;
						$bh = $peak > 0 ? ($h['value'] / $peak) * $plotH : 0;
						$y = $base - $bh;
						if ($h['value'] > 0) { ?>
							<rect x="<?php echo round($x, 1); ?>" y="<?php echo round($y, 1); ?>" width="<?php echo round($bw, 1); ?>" height="<?php echo round($bh, 1); ?>" rx="3" fill="#2b6cb0"><title><?php echo $h['label'] . ':00 — ' . $h['value']; ?></title></rect>
						<?php }
						if ($i % 4 == 0) { ?>
							<text x="<?php echo round($x + $bw / 2, 1); ?>" y="<?php echo $H - 6; ?>" text-anchor="middle" font-size="9" fill="#a0aec0"><?php echo $h['label']; ?></text>
						<?php }
					} ?>
				</svg></figure>
				<p class="mm-legend"><?php echo Lang::txt('COM_MASSMAIL_CHART_HOUR_LOCAL'); ?></p>
			<?php } ?>
		</div>

		<div class="mm-card">
			<h3><?php echo Lang::txt('COM_MASSMAIL_CHART_QUEUE_24H'); ?></h3>
			<?php
			$qs = $this->queueSeries;
			if (count($qs) < 2) { ?>
				<p class="mm-legend"><?php echo Lang::txt('COM_MASSMAIL_CHART_COLLECTING'); ?></p>
			<?php } else {
				$W = 720; $H = 190; $padL = 30; $padB = 22; $padT = 14;
				$plotW = $W - $padL; $plotH = $H - $padB - $padT;
				$base = $padT + $plotH;
				$qvals = array();
				foreach ($qs as $p) { $qvals[] = (int) $p->queue_size; }
				$qmax = max(1, max($qvals), (int) $this->watermark);
				$n = count($qs); $step = $n > 1 ? $plotW / ($n - 1) : $plotW;
				$pts = array();
				foreach ($qs as $i => $p) {
					$x = $padL + $i * $step;
					$y = $base - ((int) $p->queue_size / $qmax) * $plotH;
					$pts[] = round($x, 1) . ',' . round($y, 1);
				}
				$line = implode(' ', $pts);
				$area = $padL . ',' . $base . ' ' . $line . ' ' . ($padL + ($n - 1) * $step) . ',' . $base;
				$wy = $base - ((int) $this->watermark / $qmax) * $plotH;
				?>
				<figure><svg viewBox="0 0 <?php echo $W; ?> <?php echo $H; ?>" width="100%" role="img" aria-label="<?php echo Lang::txt('COM_MASSMAIL_CHART_QUEUE_24H'); ?>" preserveAspectRatio="xMidYMid meet">
					<line x1="<?php echo $padL; ?>" y1="<?php echo $base; ?>" x2="<?php echo $W; ?>" y2="<?php echo $base; ?>" stroke="#e2e8f0" stroke-width="1" />
					<polygon points="<?php echo $area; ?>" fill="#2b6cb0" fill-opacity="0.12" />
					<polyline points="<?php echo $line; ?>" fill="none" stroke="#2b6cb0" stroke-width="2" stroke-linejoin="round" />
					<line x1="<?php echo $padL; ?>" y1="<?php echo round($wy, 1); ?>" x2="<?php echo $W; ?>" y2="<?php echo round($wy, 1); ?>" stroke="#dd6b20" stroke-width="1" stroke-dasharray="4 3" />
					<text x="<?php echo $W - 4; ?>" y="<?php echo round($wy - 3, 1); ?>" text-anchor="end" font-size="9" fill="#dd6b20"><?php echo Lang::txt('COM_MASSMAIL_DASH_WATERMARK') . ' ' . (int) $this->watermark; ?></text>
					<text x="<?php echo $padL - 6; ?>" y="<?php echo $padT + 4; ?>" text-anchor="end" font-size="10" fill="#a0aec0"><?php echo $qmax; ?></text>
				</svg></figure>
				<p class="mm-legend"><?php echo Lang::txt('COM_MASSMAIL_CHART_QUEUE_LEGEND'); ?></p>
			<?php } ?>
		</div>
	</div>

	<div class="mm-cards" style="margin-top:16px;">
		<div class="mm-card">
			<h3><?php echo Lang::txt('COM_MASSMAIL_DASH_BY_STATUS'); ?></h3>
			<?php
			$maxc = 1;
			foreach ($this->byStatus as $c) { $maxc = max($maxc, $c); }
			if (!$this->byStatus) { ?>
				<p class="mm-legend"><?php echo Lang::txt('COM_MASSMAIL_NO_MAILINGS'); ?></p>
			<?php } else {
				foreach (\Components\Massmail\Models\Mailing::statuses() as $st) {
					if (!isset($this->byStatus[$st])) { continue; }
					$c = $this->byStatus[$st];
					$col = isset($statusColors[$st]) ? $statusColors[$st] : '#718096';
					$w = round(($c / $maxc) * 220);
					?>
					<div class="mm-sbar">
						<span class="mm-pill" style="background:<?php echo $col; ?>;"><?php echo Lang::txt('COM_MASSMAIL_STATUS_' . strtoupper($st)); ?></span>
						<span class="bar" style="width:<?php echo $w; ?>px;background:<?php echo $col; ?>;opacity:.35;"></span>
						<strong><?php echo $c; ?></strong>
					</div>
				<?php }
			} ?>
		</div>

		<div class="mm-card">
			<h3><?php echo Lang::txt('COM_MASSMAIL_DASH_RECENT'); ?></h3>
			<?php if (!$this->recent->count()) { ?>
				<p class="mm-legend"><?php echo Lang::txt('COM_MASSMAIL_NO_MAILINGS'); ?></p>
			<?php } else { ?>
				<table class="mm-recent">
					<tbody>
					<?php foreach ($this->recent as $m) {
						$col = isset($statusColors[$m->get('status')]) ? $statusColors[$m->get('status')] : '#718096';
						?>
						<tr>
							<td>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=mailings&task=show&id=' . $m->get('id')); ?>"><?php echo $this->escape(stripslashes($m->get('subject'))); ?></a>
							</td>
							<td><span class="mm-pill" style="background:<?php echo $col; ?>;"><?php echo Lang::txt('COM_MASSMAIL_STATUS_' . strtoupper($m->get('status'))); ?></span></td>
							<td><?php echo (int) $m->get('sent_count') . ' / ' . (int) $m->get('total_recipients'); ?></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			<?php } ?>
		</div>
	</div>
</div>
