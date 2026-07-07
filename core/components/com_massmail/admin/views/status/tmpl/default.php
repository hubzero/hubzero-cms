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

// Reusable single-series bar-chart renderer. Empty data draws just the axis.
// Optionally overlays a line (e.g. queue depth) scaled 0..$lineMax to full
// height, so it reads as "fill vs. threshold" rather than a raw dual axis.
$bars = function ($series, $everyN, $color, $lineVals = null, $lineMax = 0, $lineColor = '#dd6b20') {
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
	if (is_array($lineVals) && $lineMax > 0 && count($lineVals)) {
		$pts = array();
		foreach ($lineVals as $i => $v) {
			$x = $padL + $i * $slot + $slot / 2;
			$yy = $base - min(1, (float) $v / $lineMax) * $plotH;
			$pts[] = round($x, 1) . ',' . round($yy, 1);
		}
		$svg .= '<polyline points="' . implode(' ', $pts) . '" fill="none" stroke="' . $lineColor . '" stroke-width="1.5" stroke-linejoin="round" stroke-opacity="0.85" />';
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
.mm-sending { padding:3px 12px; border-radius:6px; font-weight:700; font-size:13px; color:#fff; }
.mm-sending.on { background:#2f855a; } .mm-sending.off { background:#c53030; }
.mm-cards { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
@media (max-width:800px){ .mm-cards{ grid-template-columns:1fr; } }
.mm-cards-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
@media (max-width:900px){ .mm-cards-3{ grid-template-columns:1fr; } }
.mm-card { background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:16px; }
.mm-card h3 { margin:0 0 10px; font-size:14px; color:#2d3748; }
.mm-card figure { margin:0; }
.mm-legend { font-size:11px; color:#718096; margin-top:6px; }
.mm-pill { display:inline-block; padding:2px 8px; border-radius:10px; color:#fff; font-size:11px; font-weight:600; }
.mm-pills { display:flex; flex-wrap:wrap; gap:6px; margin-top:8px; }
.mm-count { display:inline-block; padding:1px 8px; border-radius:5px; background:#2f855a; color:#fff; font-size:11px; font-weight:700; }
.mm-count-queue { background:#dd6b20; }
.mm-sbar { display:flex; align-items:center; gap:8px; margin:6px 0; font-size:13px; }
.mm-sbar .bar { height:10px; border-radius:5px; }
.mm-recent { width:100%; border-collapse:collapse; margin-top:6px; }
.mm-recent th, .mm-recent td { text-align:left; padding:6px 8px; border-bottom:1px solid #edf2f7; font-size:13px; }
</style>

<div class="mm-dash">

	<!-- Mass Mail -->
	<div class="mm-section"><?php echo Lang::txt('COM_MASSMAIL_SECTION_MASSMAIL'); ?></div>
	<div class="mm-statusline">
		<span class="mm-sending <?php echo $sendingOn ? 'on' : 'off'; ?>"><?php echo $sendingOn ? Lang::txt('COM_MASSMAIL_SENDING_ON') : Lang::txt('COM_MASSMAIL_SENDING_OFF'); ?></span>
		<span class="mm-sending <?php echo $cronOn ? 'on' : 'off'; ?>"><?php echo $cronOn ? Lang::txt('COM_MASSMAIL_CRON_ON') : Lang::txt('COM_MASSMAIL_CRON_OFF'); ?></span>
		<span><?php echo Lang::txt('COM_MASSMAIL_DASH_HOURLY_LIMIT'); ?>: <strong><?php echo number_format($this->hourly); ?>/hr</strong></span>
		<span><?php echo Lang::txt('COM_MASSMAIL_DASH_SENT_LAST_HOUR'); ?>: <strong><?php echo number_format($this->ourLastHour); ?></strong></span>
		<span><?php echo Lang::txt('COM_MASSMAIL_DASH_REMAINING'); ?>: <strong><?php echo number_format($this->hourlyRemaining); ?></strong></span>
		<span><?php echo Lang::txt('COM_MASSMAIL_DASH_MESSAGES_PENDING'); ?>: <strong><?php echo number_format($this->messagesPending); ?></strong></span>
		<span><?php echo Lang::txt('COM_MASSMAIL_FAILED'); ?>: <strong><?php echo number_format($this->messagesFailed); ?></strong></span>
		<span><?php echo Lang::txt('COM_MASSMAIL_DASH_MESSAGES_SENT'); ?>: <strong><?php echo number_format($this->messagesSent); ?></strong></span>
	</div>
	<div class="mm-cards-3">
		<div class="mm-card">
			<h3><?php echo Lang::txt('COM_MASSMAIL_CHART_SENT_LASTHOUR'); ?></h3>
			<?php echo $bars($this->mmLastHour, 10, '#4299e1'); ?>
			<div class="mm-pills"><span class="mm-count"><?php echo Lang::txt('COM_MASSMAIL_CHART_TOTAL', number_format($sum($this->mmLastHour))); ?></span></div>
		</div>
		<div class="mm-card">
			<h3><?php echo Lang::txt('COM_MASSMAIL_CHART_SENT_HOURLY'); ?></h3>
			<?php echo $bars($this->mmHourly, 6, '#3182ce'); ?>
			<div class="mm-pills"><span class="mm-count"><?php echo Lang::txt('COM_MASSMAIL_CHART_TOTAL', number_format($sum($this->mmHourly))); ?></span></div>
		</div>
		<div class="mm-card">
			<h3><?php echo Lang::txt('COM_MASSMAIL_CHART_SENT_DAILY'); ?></h3>
			<?php echo $bars($this->mmDaily, 5, '#2b6cb0'); ?>
			<div class="mm-pills"><span class="mm-count"><?php echo Lang::txt('COM_MASSMAIL_CHART_TOTAL', number_format($sum($this->mmDaily))); ?></span></div>
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
	<div class="mm-cards-3">
		<div class="mm-card">
			<h3><?php echo Lang::txt('COM_MASSMAIL_CHART_OUTBOUND_LASTHOUR'); ?></h3>
			<?php echo $bars($this->lastHourVolume, 10, '#4299e1', $this->qLastHour, (int) $this->watermark); ?>
			<div class="mm-pills">
				<span class="mm-count"><?php echo Lang::txt('COM_MASSMAIL_CHART_TOTAL', number_format($sum($this->lastHourVolume))); ?></span>
				<span class="mm-count mm-count-queue"><?php echo Lang::txt('COM_MASSMAIL_CHART_QUEUE_PEAK', number_format($this->qPeakLastHour)); ?></span>
			</div>
		</div>
		<div class="mm-card">
			<h3><?php echo Lang::txt('COM_MASSMAIL_CHART_OUTBOUND_HOURLY'); ?></h3>
			<?php echo $bars($this->hourlyVolume, 6, '#3182ce', $this->qHourly, (int) $this->watermark); ?>
			<div class="mm-pills">
				<span class="mm-count"><?php echo Lang::txt('COM_MASSMAIL_CHART_TOTAL', number_format($sum($this->hourlyVolume))); ?></span>
				<span class="mm-count mm-count-queue"><?php echo Lang::txt('COM_MASSMAIL_CHART_QUEUE_PEAK', number_format($this->qPeak48h)); ?></span>
			</div>
		</div>
		<div class="mm-card">
			<h3><?php echo Lang::txt('COM_MASSMAIL_CHART_OUTBOUND_DAILY'); ?></h3>
			<?php echo $bars($this->dailyVolume, 5, '#2b6cb0', $this->q30, (int) $this->watermark); ?>
			<div class="mm-pills">
				<span class="mm-count"><?php echo Lang::txt('COM_MASSMAIL_CHART_TOTAL', number_format($sum($this->dailyVolume))); ?></span>
				<span class="mm-count mm-count-queue"><?php echo Lang::txt('COM_MASSMAIL_CHART_QUEUE_PEAK', number_format($this->qPeak30d)); ?></span>
			</div>
		</div>
	</div>
	<p class="mm-legend"><span style="display:inline-block;width:14px;height:0;border-top:2px solid #dd6b20;vertical-align:middle;margin-right:5px;"></span><?php echo Lang::txt('COM_MASSMAIL_CHART_QUEUE_OVERLAY', number_format($this->watermark)); ?></p>
</div>
