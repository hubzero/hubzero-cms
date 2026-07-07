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
$queueTxt  = $this->queueSize < 0 ? Lang::txt('COM_MASSMAIL_DASH_QUEUE_UNREADABLE') : number_format($this->queueSize);

$statusColors = array(
	'draft' => '#94a3b8', 'pending_approval' => '#dd6b20', 'preparing' => '#3182ce',
	'sending' => '#2b6cb0', 'paused' => '#b7791f', 'sent' => '#2f855a', 'cancelled' => '#a0aec0'
);

// Reusable single-series bar-chart renderer. Empty data draws just the axis.
$bars = function ($series, $everyN, $color) {
	$vals = array();
	foreach ($series as $h) { $vals[] = $h['value']; }
	$peak  = $vals ? max($vals) : 0;
	$scale = $peak > 0 ? $peak : 1;
	$W = 520; $H = 200; $padL = 34; $padB = 22; $padT = 14;
	$plotW = $W - $padL; $plotH = $H - $padB - $padT;
	$n = count($series); $slot = $plotW / max(1, $n); $bw = max(2, $slot - 3); $base = $padT + $plotH;
	$svg  = '<figure><svg viewBox="0 0 ' . $W . ' ' . $H . '" width="100%" role="img" preserveAspectRatio="xMidYMid meet">';
	$svg .= '<line x1="' . $padL . '" y1="' . $padT . '" x2="' . $W . '" y2="' . $padT . '" stroke="#edf2f7"/>';
	$svg .= '<line x1="' . $padL . '" y1="' . $base . '" x2="' . $W . '" y2="' . $base . '" stroke="#e2e8f0"/>';
	if ($peak > 0) { $svg .= '<text x="' . ($padL - 6) . '" y="' . ($padT + 4) . '" text-anchor="end" font-size="10" fill="#a0aec0">' . $peak . '</text>'; }
	$svg .= '<text x="' . ($padL - 6) . '" y="' . $base . '" text-anchor="end" font-size="10" fill="#a0aec0">0</text>';
	foreach ($series as $i => $h) {
		$x = $padL + $i * $slot + ($slot - $bw) / 2;
		$bh = ($h['value'] / $scale) * $plotH;
		$y = $base - $bh;
		if ($h['value'] > 0) {
			$svg .= '<rect x="' . round($x, 1) . '" y="' . round($y, 1) . '" width="' . round($bw, 1) . '" height="' . round($bh, 1) . '" rx="3" fill="' . $color . '"><title>' . $h['label'] . ' — ' . $h['value'] . '</title></rect>';
		}
		if ($i % $everyN == 0) {
			$svg .= '<text x="' . round($x + $bw / 2, 1) . '" y="' . ($H - 6) . '" text-anchor="middle" font-size="9" fill="#a0aec0">' . $h['label'] . '</text>';
		}
	}
	$svg .= '</svg></figure>';
	return $svg;
};

$sum = function ($s) { return array_sum(array_column($s, 'value')); };
?>
<style>
.mm-dash { max-width: 1100px; }
.mm-dash .mm-alert { padding:10px 14px; border-radius:6px; margin:0 0 10px; font-weight:600; }
.mm-dash .mm-alert.warn { background:#fffaf0; border:1px solid #f6ad55; color:#9c4221; }
.mm-section { font-size:12px; text-transform:uppercase; letter-spacing:.05em; color:#718096; font-weight:700; margin:18px 0 8px; }
.mm-section:first-of-type { margin-top:6px; }
.mm-statusline { display:flex; flex-wrap:wrap; gap:8px 24px; align-items:center; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:11px 16px; margin-bottom:12px; font-size:13px; color:#4a5568; }
.mm-statusline .dot { display:inline-block; width:9px; height:9px; border-radius:50%; margin-right:6px; vertical-align:middle; }
.mm-statusline .ok { background:#2f855a; } .mm-statusline .bad { background:#c53030; }
.mm-statusline strong { color:#1a202c; font-weight:700; }
.mm-cards { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
@media (max-width:800px){ .mm-cards{ grid-template-columns:1fr; } }
.mm-cards-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
@media (max-width:900px){ .mm-cards-3{ grid-template-columns:1fr; } }
.mm-card { background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:16px; }
.mm-card h3 { margin:0 0 10px; font-size:14px; color:#2d3748; }
.mm-card figure { margin:0; }
.mm-legend { font-size:11px; color:#718096; margin-top:6px; }
.mm-pill { display:inline-block; padding:2px 8px; border-radius:10px; color:#fff; font-size:11px; font-weight:600; }
.mm-count { display:inline-block; padding:0 7px; border-radius:5px; background:#2f855a; color:#fff; font-size:11px; font-weight:700; line-height:1.6; margin-left:8px; vertical-align:middle; }
.mm-sbar { display:flex; align-items:center; gap:8px; margin:6px 0; font-size:13px; }
.mm-sbar .bar { height:10px; border-radius:5px; }
.mm-recent { width:100%; border-collapse:collapse; margin-top:6px; }
.mm-recent th, .mm-recent td { text-align:left; padding:6px 8px; border-bottom:1px solid #edf2f7; font-size:13px; }
</style>

<div class="mm-dash">

	<?php if (!$sendingOn) { ?><div class="mm-alert warn"><?php echo Lang::txt('COM_MASSMAIL_DASH_SENDING_DISABLED'); ?></div><?php } ?>
	<?php if (!$cronOn) { ?><div class="mm-alert warn"><?php echo Lang::txt('COM_MASSMAIL_DASH_CRON_WARNING'); ?></div><?php } ?>

	<!-- Mass Mail -->
	<div class="mm-section"><?php echo Lang::txt('COM_MASSMAIL_SECTION_MASSMAIL'); ?></div>
	<div class="mm-statusline">
		<span><span class="dot <?php echo $sendingOn ? 'ok' : 'bad'; ?>"></span><?php echo Lang::txt('COM_MASSMAIL_DASH_SENDING_ENABLED'); ?>: <strong><?php echo $sendingOn ? Lang::txt('JYES') : Lang::txt('JNO'); ?></strong></span>
		<span><?php echo Lang::txt('COM_MASSMAIL_DASH_HOURLY_LIMIT'); ?>: <strong><?php echo number_format($this->hourly); ?>/hr</strong></span>
		<span><?php echo Lang::txt('COM_MASSMAIL_DASH_SENT_LAST_HOUR'); ?>: <strong><?php echo number_format($this->ourLastHour); ?></strong></span>
		<span><?php echo Lang::txt('COM_MASSMAIL_DASH_REMAINING'); ?>: <strong><?php echo number_format($this->hourlyRemaining); ?></strong></span>
		<span><?php echo Lang::txt('COM_MASSMAIL_DASH_MESSAGES_PENDING'); ?>: <strong><?php echo number_format($this->messagesPending); ?></strong></span>
		<span><?php echo Lang::txt('COM_MASSMAIL_FAILED'); ?>: <strong><?php echo number_format($this->messagesFailed); ?></strong></span>
		<span><?php echo Lang::txt('COM_MASSMAIL_DASH_MESSAGES_SENT'); ?>: <strong><?php echo number_format($this->messagesSent); ?></strong></span>
	</div>
	<div class="mm-cards-3" style="margin-bottom:4px;">
		<div class="mm-card">
			<h3><?php echo Lang::txt('COM_MASSMAIL_CHART_SENT_LASTHOUR'); ?><span class="mm-count"><?php echo Lang::txt('COM_MASSMAIL_CHART_TOTAL', number_format($sum($this->mmLastHour))); ?></span></h3>
			<?php echo $bars($this->mmLastHour, 10, '#4299e1'); ?>
		</div>
		<div class="mm-card">
			<h3><?php echo Lang::txt('COM_MASSMAIL_CHART_SENT_HOURLY'); ?><span class="mm-count"><?php echo Lang::txt('COM_MASSMAIL_CHART_TOTAL', number_format($sum($this->mmHourly))); ?></span></h3>
			<?php echo $bars($this->mmHourly, 6, '#3182ce'); ?>
		</div>
		<div class="mm-card">
			<h3><?php echo Lang::txt('COM_MASSMAIL_CHART_SENT_DAILY'); ?><span class="mm-count"><?php echo Lang::txt('COM_MASSMAIL_CHART_TOTAL', number_format($sum($this->mmDaily))); ?></span></h3>
			<?php echo $bars($this->mmDaily, 5, '#2b6cb0'); ?>
		</div>
	</div>

	<!-- Postfix Outbound -->
	<div class="mm-section"><?php echo Lang::txt('COM_MASSMAIL_SECTION_POSTFIX'); ?></div>
	<div class="mm-statusline">
		<span><span class="dot <?php echo $this->pfRunning ? 'ok' : 'bad'; ?>"></span><?php echo Lang::txt('COM_MASSMAIL_PF_SERVICE'); ?>: <strong><?php echo Lang::txt($this->pfRunning ? 'COM_MASSMAIL_PF_RUNNING' : 'COM_MASSMAIL_PF_STOPPED'); ?></strong></span>
		<span><span class="dot <?php echo $this->pfConfig['configured'] ? 'ok' : 'bad'; ?>"></span><strong><?php echo Lang::txt($this->pfConfig['configured'] ? 'COM_MASSMAIL_PF_CONFIGURED' : 'COM_MASSMAIL_PF_NOT_CONFIGURED'); ?></strong><?php if ($this->pfConfig['configured'] && $this->pfConfig['version']) { echo ' <span style="color:#a0aec0;">' . $this->escape($this->pfConfig['version']) . '</span>'; } ?></span>
		<span><?php echo Lang::txt('COM_MASSMAIL_PF_QUEUE'); ?>: <strong><?php echo $queueTxt; ?></strong> <span style="color:#a0aec0;">(<?php echo Lang::txt('COM_MASSMAIL_DASH_WATERMARK'); ?> <?php echo number_format($this->watermark); ?>)</span></span>
		<span><?php echo Lang::txt('COM_MASSMAIL_DASH_LAST_SENT'); ?>: <strong><?php echo $this->lastSent ? $this->escape($this->lastSent) : '&mdash;'; ?></strong></span>
	</div>
	<div class="mm-cards-3" style="margin-bottom:4px;">
		<div class="mm-card">
			<h3><?php echo Lang::txt('COM_MASSMAIL_CHART_OUTBOUND_LASTHOUR'); ?><span class="mm-count"><?php echo Lang::txt('COM_MASSMAIL_CHART_TOTAL', number_format($sum($this->lastHourVolume))); ?></span></h3>
			<?php echo $bars($this->lastHourVolume, 10, '#4299e1'); ?>
		</div>
		<div class="mm-card">
			<h3><?php echo Lang::txt('COM_MASSMAIL_CHART_OUTBOUND_HOURLY'); ?><span class="mm-count"><?php echo Lang::txt('COM_MASSMAIL_CHART_TOTAL', number_format($sum($this->hourlyVolume))); ?></span></h3>
			<?php echo $bars($this->hourlyVolume, 6, '#3182ce'); ?>
		</div>
		<div class="mm-card">
			<h3><?php echo Lang::txt('COM_MASSMAIL_CHART_OUTBOUND_DAILY'); ?><span class="mm-count"><?php echo Lang::txt('COM_MASSMAIL_CHART_TOTAL', number_format($sum($this->dailyVolume))); ?></span></h3>
			<?php echo $bars($this->dailyVolume, 5, '#2b6cb0'); ?>
		</div>
	</div>

	<!-- Queue depth trend + mailings by status -->
	<div class="mm-cards" style="margin-top:14px;">
		<div class="mm-card">
			<h3><?php echo Lang::txt('COM_MASSMAIL_CHART_QUEUE_RECENT'); ?> <span style="font-weight:400;color:#718096;font-size:12px;">&mdash; <?php echo Lang::txt('COM_MASSMAIL_DASH_PEAK_24H', number_format($this->queuePeak24h)); ?></span></h3>
			<?php
			$qs = $this->queueRecent;
			if (count($qs) < 2) { ?>
				<p class="mm-legend"><?php echo Lang::txt('COM_MASSMAIL_CHART_COLLECTING'); ?></p>
			<?php } else {
				$W = 720; $H = 190; $padL = 30; $padB = 22; $padT = 14;
				$plotW = $W - $padL; $plotH = $H - $padB - $padT;
				$base = $padT + $plotH;
				$qvals = array();
				foreach ($qs as $p) { $qvals[] = (int) $p->depth; }
				$qmax = max(1, max($qvals), (int) $this->watermark);
				$n = count($qs); $step = $n > 1 ? $plotW / ($n - 1) : $plotW;
				$pts = array();
				foreach ($qs as $i => $p) {
					$x = $padL + $i * $step;
					$y = $base - ((int) $p->depth / $qmax) * $plotH;
					$pts[] = round($x, 1) . ',' . round($y, 1);
				}
				$line = implode(' ', $pts);
				$area = $padL . ',' . $base . ' ' . $line . ' ' . ($padL + ($n - 1) * $step) . ',' . $base;
				$wy = $base - ((int) $this->watermark / $qmax) * $plotH;
				?>
				<figure><svg viewBox="0 0 <?php echo $W; ?> <?php echo $H; ?>" width="100%" role="img" preserveAspectRatio="xMidYMid meet">
					<line x1="<?php echo $padL; ?>" y1="<?php echo $base; ?>" x2="<?php echo $W; ?>" y2="<?php echo $base; ?>" stroke="#e2e8f0" stroke-width="1" />
					<polygon points="<?php echo $area; ?>" fill="#2b6cb0" fill-opacity="0.12" />
					<polyline points="<?php echo $line; ?>" fill="none" stroke="#2b6cb0" stroke-width="2" stroke-linejoin="round" />
					<line x1="<?php echo $padL; ?>" y1="<?php echo round($wy, 1); ?>" x2="<?php echo $W; ?>" y2="<?php echo round($wy, 1); ?>" stroke="#dd6b20" stroke-width="1" stroke-dasharray="4 3" />
					<text x="<?php echo $W - 4; ?>" y="<?php echo round($wy - 3, 1); ?>" text-anchor="end" font-size="9" fill="#dd6b20"><?php echo Lang::txt('COM_MASSMAIL_DASH_WATERMARK') . ' ' . (int) $this->watermark; ?></text>
					<text x="<?php echo $padL - 6; ?>" y="<?php echo $padT + 4; ?>" text-anchor="end" font-size="10" fill="#a0aec0"><?php echo $qmax; ?></text>
				</svg></figure>
			<?php } ?>
		</div>

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
	</div>

	<!-- Recent mailings -->
	<div class="mm-card" style="margin-top:16px;">
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
						<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=mailings&task=show&id=' . $m->get('id')); ?>"><?php echo $this->escape(stripslashes($m->get('subject'))); ?></a></td>
						<td><span class="mm-pill" style="background:<?php echo $col; ?>;"><?php echo Lang::txt('COM_MASSMAIL_STATUS_' . strtoupper($m->get('status'))); ?></span></td>
						<td><?php echo (int) $m->get('sent_count') . ' / ' . (int) $m->get('total_recipients'); ?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		<?php } ?>
	</div>
</div>
