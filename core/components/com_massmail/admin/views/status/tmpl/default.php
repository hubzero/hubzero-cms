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
if ($canDo->get('core.admin')) {
    Toolbar::preferences($this->option, '550');
}

$queueTxt  = $this->queueSize < 0 ? Lang::txt('COM_MASSMAIL_DASH_QUEUE_UNREADABLE') : number_format($this->queueSize);

// Reusable single-series bar-chart renderer. Empty data draws just the axis.
// Optionally overlays a line (e.g. queue depth) scaled 0..$lineMax to full
// height, so it reads as "fill vs. threshold" rather than a raw dual axis.
$bars = function ($series, $everyN, $color, $lineVals = null, $lineMax = 0, $lineColor = '#dd6b20') {
    $vals = [];
    foreach ($series as $h) {
        $vals[] = $h['value'];
    }
    $peak  = $vals ? max($vals) : 0;
    $scale = $peak > 0 ? $peak : 1;
    $W = 520;
    $H = 200;
    $padL = 34;
    $padB = 22;
    $padT = 14;
    $plotW = $W - $padL;
    $plotH = $H - $padB - $padT;
    $n = count($series);
    $slot = $plotW / max(1, $n);
    $bw = max(2, $slot - 3);
    $base = $padT + $plotH;
    $svg  = '<figure><svg viewBox="0 0 ' . $W . ' ' . $H
        . '" width="100%" role="img" preserveAspectRatio="xMidYMid meet">';
    $svg .= '<line x1="' . $padL . '" y1="' . $padT . '" x2="' . $W . '" y2="' . $padT . '" stroke="#edf2f7"/>';
    $svg .= '<line x1="' . $padL . '" y1="' . $base . '" x2="' . $W . '" y2="' . $base . '" stroke="#e2e8f0"/>';
    if ($peak > 0) {
        $svg .= '<text x="' . ($padL - 6) . '" y="' . ($padT + 4) . '" text-anchor="end" font-size="10" fill="#a0aec0">'
            . $peak . '</text>';
    }
    $svg .= '<text x="' . ($padL - 6) . '" y="' . $base . '" text-anchor="end" font-size="10" fill="#a0aec0">0</text>';
    foreach ($series as $i => $h) {
        $x = $padL + $i * $slot + ($slot - $bw) / 2;
        $bh = ($h['value'] / $scale) * $plotH;
        $y = $base - $bh;
        if ($h['value'] > 0) {
            $svg .= '<rect x="' . round($x, 1) . '" y="' . round($y, 1) . '" width="' . round($bw, 1) . '" height="'
                . round($bh, 1) . '" rx="3" fill="' . $color . '"><title>' . $h['label'] . ' — ' . $h['value']
                . '</title></rect>';
        }
        if ($i % $everyN == 0) {
            $svg .= '<text x="' . round($x + $bw / 2, 1) . '" y="' . ($H - 6)
                . '" text-anchor="middle" font-size="9" fill="#a0aec0">' . $h['label'] . '</text>';
        }
    }
    if (is_array($lineVals) && $lineMax > 0 && count($lineVals)) {
        $pts = [];
        foreach ($lineVals as $i => $v) {
            $x = $padL + $i * $slot + $slot / 2;
            $yy = $base - min(1, (float) $v / $lineMax) * $plotH;
            $pts[] = round($x, 1) . ',' . round($yy, 1);
        }
        $svg .= '<polyline points="' . implode(' ', $pts) . '" fill="none" stroke="' . $lineColor
            . '" stroke-width="1.5" stroke-linejoin="round" stroke-opacity="0.85" />';
    }
    $svg .= '</svg></figure>';
    return $svg;
};

$sum = function ($s) {
    return array_sum(array_column($s, 'value'));
};

$this->css('status.css', 'com_massmail');
$this->js('status.js', 'com_massmail');
?>

<div class="mm-dash">

    <!-- Mass Mail -->
    <div class="mm-section"><?php echo Lang::txt('COM_MASSMAIL_SECTION_MASSMAIL'); ?></div>
    <div class="mm-statusline">
        <?php // Send-pipeline health (sending toggle + delivery cron) is shown as a
              // single traffic-light status pill on the sub-navigation (see massmail.php).?>
        <?php if ($this->heldMailings > 0) { ?>
            <?php
            $linkUrl = Route::url(
                'index.php?option=com_massmail&controller=mailings&status='
                    . \Components\Massmail\Models\Mailing::STATUS_HELD,
            );
            $linkText = Lang::txt('COM_MASSMAIL_DASH_HELD', number_format($this->heldMailings));
            ?>
            <a class="mm-sending held" href="<?= $linkUrl ?>"><?= $linkText ?></a>
        <?php } ?>
        <?php
        $spanText = Lang::txt('COM_MASSMAIL_DASH_HOURLY_LIMIT');
        ?>
        <span><?= $spanText ?>: <strong><?= number_format($this->hourly) ?>/hr</strong></span>
        <?php
        $runHintText = Lang::txt('COM_MASSMAIL_DASH_PER_RUN_HINT', number_format($this->ticksPerHour));
        ?>
        <span><?= Lang::txt('COM_MASSMAIL_DASH_PER_RUN') ?>: <strong><?= number_format($this->perRun) ?></strong> <span
            class="mm-muted"><?= $runHintText ?></span></span>
        <?php
        $spanText = Lang::txt('COM_MASSMAIL_DASH_SENT_LAST_HOUR');
        ?>
        <span><?= $spanText ?>: <strong><?= number_format($this->ourLastHour) ?></strong></span>
        <?php
        $spanText = Lang::txt('COM_MASSMAIL_DASH_REMAINING');
        ?>
        <span><?= $spanText ?>: <strong><?= number_format($this->hourlyRemaining) ?></strong></span>
        <?php
        $spanText = Lang::txt('COM_MASSMAIL_DASH_MESSAGES_PENDING');
        ?>
        <span><?= $spanText ?>: <strong><?= number_format($this->messagesPending) ?></strong></span>
        <?php
        $strongText = number_format($this->messagesFailed);
        ?>
        <span><?= Lang::txt('COM_MASSMAIL_FAILED') ?>: <strong><?= $strongText ?></strong></span>
        <?php
        $spanText = Lang::txt('COM_MASSMAIL_DASH_MESSAGES_SENT');
        ?>
        <span><?= $spanText ?>: <strong><?= number_format($this->messagesSent) ?></strong></span>
    </div>
    <div class="mm-cards-3">
        <div class="mm-card">
            <h3><?php echo Lang::txt('COM_MASSMAIL_CHART_SENT_LASTHOUR'); ?></h3>
            <?php echo $bars($this->mmLastHour, 10, '#4299e1'); ?>
            <?php
            $mmCountText = Lang::txt('COM_MASSMAIL_CHART_TOTAL', number_format($sum($this->mmLastHour)));
            ?>
            <div class="mm-pills"><span class="mm-count"><?= $mmCountText ?></span></div>
        </div>
        <div class="mm-card">
            <h3><?php echo Lang::txt('COM_MASSMAIL_CHART_SENT_HOURLY'); ?></h3>
            <?php echo $bars($this->mmHourly, 6, '#3182ce'); ?>
            <?php
            $mmCountText = Lang::txt('COM_MASSMAIL_CHART_TOTAL', number_format($sum($this->mmHourly)));
            ?>
            <div class="mm-pills"><span class="mm-count"><?= $mmCountText ?></span></div>
        </div>
        <div class="mm-card">
            <h3><?php echo Lang::txt('COM_MASSMAIL_CHART_SENT_DAILY'); ?></h3>
            <?php echo $bars($this->mmDaily, 5, '#2b6cb0'); ?>
            <?php
            $mmCountText = Lang::txt('COM_MASSMAIL_CHART_TOTAL', number_format($sum($this->mmDaily)));
            ?>
            <div class="mm-pills"><span class="mm-count"><?= $mmCountText ?></span></div>
        </div>
    </div>

    <!-- Postfix Outbound -->
    <div class="mm-section"><?php echo Lang::txt('COM_MASSMAIL_SECTION_POSTFIX'); ?></div>
    <div class="mm-statusline">
        <?php
        $strongText = Lang::txt($this->pfRunning ? 'COM_MASSMAIL_PF_RUNNING' : 'COM_MASSMAIL_PF_STOPPED');
        ?>
        <span><span
            class="dot <?= $this->pfRunning ? 'ok' : 'bad' ?>"></span><?= Lang::txt('COM_MASSMAIL_PF_SERVICE') ?>:
            <strong><?= $strongText ?></strong></span>
        <?php
        $okClass = $this->pfConfig['configured'] ? 'ok' : 'bad';
        $strongText = Lang::txt(
            $this->pfConfig['configured'] ? 'COM_MASSMAIL_PF_CONFIGURED' : 'COM_MASSMAIL_PF_NOT_CONFIGURED',
        );
        $spanText = $this->pfConfig['configured'] && $this->pfConfig['version']
            ? ' <span class="mm-muted">' . $this->escape($this->pfConfig['version']) . '</span>'
            : '';
        ?>
        <span><span class="dot <?= $okClass ?>"></span><strong><?= $strongText ?></strong><?= $spanText ?></span>
        <span><?php echo Lang::txt('COM_MASSMAIL_PF_QUEUE'); ?>: <strong><?php echo $queueTxt; ?></strong></span>
        <?php
        $strongText = $this->lastSent ? $this->escape($this->lastSent) : '&mdash;';
        ?>
        <span><?= Lang::txt('COM_MASSMAIL_DASH_LAST_SENT') ?>: <strong><?= $strongText ?></strong></span>
    </div>
    <div class="mm-cards-3">
        <div class="mm-card">
            <h3><?php echo Lang::txt('COM_MASSMAIL_CHART_OUTBOUND_LASTHOUR'); ?></h3>
            <?php echo $bars($this->lastHourVolume, 10, '#4299e1', $this->qLastHour, (int) $this->hourly); ?>
            <div class="mm-pills">
                <?php
                $mmCountText = Lang::txt('COM_MASSMAIL_CHART_TOTAL', number_format($sum($this->lastHourVolume)));
                ?>
                <span class="mm-count"><?= $mmCountText ?></span>
                <?php
                $mmCountText = Lang::txt('COM_MASSMAIL_CHART_QUEUE_PEAK', number_format($this->qPeakLastHour));
                ?>
                <span class="mm-count mm-count-queue"><?= $mmCountText ?></span>
            </div>
        </div>
        <div class="mm-card">
            <h3><?php echo Lang::txt('COM_MASSMAIL_CHART_OUTBOUND_HOURLY'); ?></h3>
            <?php echo $bars($this->hourlyVolume, 6, '#3182ce', $this->qHourly, (int) $this->hourly); ?>
            <div class="mm-pills">
                <?php
                $mmCountText = Lang::txt('COM_MASSMAIL_CHART_TOTAL', number_format($sum($this->hourlyVolume)));
                ?>
                <span class="mm-count"><?= $mmCountText ?></span>
                <?php
                $mmCountText = Lang::txt('COM_MASSMAIL_CHART_QUEUE_PEAK', number_format($this->qPeak48h));
                ?>
                <span class="mm-count mm-count-queue"><?= $mmCountText ?></span>
            </div>
        </div>
        <div class="mm-card">
            <h3><?php echo Lang::txt('COM_MASSMAIL_CHART_OUTBOUND_DAILY'); ?></h3>
            <?php echo $bars($this->dailyVolume, 5, '#2b6cb0', $this->q30, (int) $this->hourly); ?>
            <div class="mm-pills">
                <?php
                $mmCountText = Lang::txt('COM_MASSMAIL_CHART_TOTAL', number_format($sum($this->dailyVolume)));
                ?>
                <span class="mm-count"><?= $mmCountText ?></span>
                <?php
                $mmCountText = Lang::txt('COM_MASSMAIL_CHART_QUEUE_PEAK', number_format($this->qPeak30d));
                ?>
                <span class="mm-count mm-count-queue"><?= $mmCountText ?></span>
            </div>
        </div>
    </div>
    <?php
    $mmLegendText = Lang::txt('COM_MASSMAIL_CHART_QUEUE_OVERLAY', number_format($this->hourly));
    ?>
    <p class="mm-legend"><span class="mm-legend-swatch"></span><?= $mmLegendText ?></p>
</div>

<?php // data-enlarge-hint, not data-hint: the admin template styles [data-hint] as an
      // inline tooltip anchor, which overrode this modal's display:none.?>
<div class="mm-modal" id="mm-modal" role="dialog" aria-modal="true"
    aria-label="<?= $this->escape(Lang::txt('COM_MASSMAIL_CHART_ENLARGED')) ?>"
    data-enlarge-hint="<?= $this->escape(Lang::txt('COM_MASSMAIL_CLICK_ENLARGE')) ?>">
    <div class="mm-modal-inner">
        <button type="button" class="mm-modal-close"
            aria-label="<?= $this->escape(Lang::txt('COM_MASSMAIL_CLOSE')) ?>">&times;</button>
        <div class="mm-modal-body"></div>
    </div>
</div>
