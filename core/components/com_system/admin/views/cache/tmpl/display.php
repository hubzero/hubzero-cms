<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('cache');

Toolbar::title(Lang::txt('COM_SYSTEM_CACHE_OVERVIEW'), 'config');

$opcache = $this->opcache;
$opcacheConfig = $this->opcacheConfig;
$apcu = $this->apcu;
$apcuMem = $this->apcuMem;
?>

<?php echo $this->loadTemplate('_submenu'); ?>

<div class="grid">
    <div class="col span6">
        <fieldset class="adminform">
            <legend><?php echo Lang::txt('OPcache'); ?></legend>

            <?php if (!$opcache) : ?>
                <p class="warning">
                    <?php echo Lang::txt('COM_SYSTEM_CACHE_OPCACHE_NOT_AVAILABLE'); ?>
                </p>
            <?php else : ?>
                <?php
                    $mem = $opcache['memory_usage'];
                    $stats = $opcache['opcache_statistics'];
                    $total = $mem['used_memory'] + $mem['free_memory'];
                    $usedPct = $total > 0 ? round($mem['used_memory'] / $total * 100, 1) : 0;
                    $hitRate = round($stats['opcache_hit_rate'], 1);
                ?>
                <table class="adminlist">
                    <tbody>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('Status'); ?></th>
                            <td>
                                <?php if ($opcache['opcache_enabled']) : ?>
                                    <span class="state publish"><span>Enabled</span></span>
                                <?php else : ?>
                                    <span class="state unpublish"><span>Disabled</span></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_SYSTEM_CACHE_MEMORY'); ?></th>
                            <td>
                                <?php echo \Hubzero\Utility\Number::formatBytes($mem['used_memory']); ?>
                                / <?php echo \Hubzero\Utility\Number::formatBytes($total); ?>
                                (<?php echo $usedPct; ?>%)
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_SYSTEM_CACHE_HIT_RATE'); ?></th>
                            <td><?php echo $hitRate; ?>%</td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_SYSTEM_CACHE_HITS'); ?></th>
                            <td><?php echo number_format($stats['hits']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_SYSTEM_CACHE_MISSES'); ?></th>
                            <td><?php echo number_format($stats['misses']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_SYSTEM_CACHE_ENTRIES'); ?></th>
                            <td><?php echo number_format($stats['num_cached_scripts']); ?></td>
                        </tr>
                        <?php if (isset($stats['oom_restarts'])) : ?>
                        <tr>
                            <th scope="row">OOM Restarts</th>
                            <td><?php echo number_format($stats['oom_restarts']); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if ($opcacheConfig) : ?>
                <h4>Configuration</h4>
                <table class="adminlist">
                    <tbody>
                        <?php
                            $directives = $opcacheConfig['directives'];
                            $show = [
                                'opcache.memory_consumption',
                                'opcache.max_accelerated_files',
                                'opcache.interned_strings_buffer',
                                'opcache.validate_timestamps',
                                'opcache.revalidate_freq',
                                'opcache.jit',
                                'opcache.jit_buffer_size',
                            ];
                            ?>
                        <?php foreach ($show as $key) : ?>
                            <?php if (isset($directives[$key])) : ?>
                            <tr>
                                <th scope="row"><?php echo htmlspecialchars($key); ?></th>
                                <td>
                                    <?php
                                        $val = $directives[$key];
                                    if (is_bool($val)) {
                                        echo $val ? 'true' : 'false';
                                    } elseif (is_numeric($val) && $val > 1048576) {
                                        echo \Hubzero\Utility\Number::formatBytes($val);
                                    } else {
                                        echo htmlspecialchars((string) $val);
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            <?php endif; ?>
        </fieldset>
    </div>

    <div class="col span6">
        <fieldset class="adminform">
            <legend><?php echo Lang::txt('APCu'); ?></legend>

            <?php if (!$apcu) : ?>
                <p class="warning">
                    <?php echo Lang::txt('COM_SYSTEM_CACHE_APCU_NOT_AVAILABLE'); ?>
                </p>
            <?php else : ?>
                <?php
                    $totalMem = $apcuMem['seg_size'] * $apcuMem['num_seg'];
                    $availMem = $apcuMem['avail_mem'];
                    $usedMem = $totalMem - $availMem;
                    $usedPct = $totalMem > 0
                        ? round($usedMem / $totalMem * 100, 1)
                        : 0;
                    $hits = $apcu['num_hits'] ?? 0;
                    $misses = $apcu['num_misses'] ?? 0;
                    $totalReq = $hits + $misses;
                    $hitRate = $totalReq > 0
                        ? round($hits / $totalReq * 100, 1)
                        : 0;
                ?>
                <table class="adminlist">
                    <tbody>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_SYSTEM_CACHE_MEMORY'); ?></th>
                            <td>
                                <?php echo \Hubzero\Utility\Number::formatBytes($usedMem); ?>
                                / <?php echo \Hubzero\Utility\Number::formatBytes($totalMem); ?>
                                (<?php echo $usedPct; ?>%)
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_SYSTEM_CACHE_HIT_RATE'); ?></th>
                            <td><?php echo $hitRate; ?>%</td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_SYSTEM_CACHE_HITS'); ?></th>
                            <td><?php echo number_format($hits); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_SYSTEM_CACHE_MISSES'); ?></th>
                            <td><?php echo number_format($misses); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_SYSTEM_CACHE_ENTRIES'); ?></th>
                            <td><?php echo number_format($apcu['num_entries'] ?? 0); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_SYSTEM_CACHE_UPTIME'); ?></th>
                            <td>
                                <?php
                                if (isset($apcu['start_time'])) {
                                    $uptime = time() - $apcu['start_time'];
                                    $days = floor($uptime / 86400);
                                    $hours = floor(($uptime % 86400) / 3600);
                                    $mins = floor(($uptime % 3600) / 60);
                                    echo "{$days}d {$hours}h {$mins}m";
                                } else {
                                    echo 'n/a';
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php endif; ?>
        </fieldset>
    </div>
</div>
