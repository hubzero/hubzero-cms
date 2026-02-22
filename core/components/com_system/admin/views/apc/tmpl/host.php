<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Menu items
Toolbar::title(Lang::txt('COM_SYSTEM_APC_HOST'), 'config');

$this->css('apc.css')
    ->js();

$time = $this->time;

$baseUrl = 'index.php?option=' . $this->option
    . '&controller=' . $this->controller;
$formAction = Route::url($baseUrl);
$clrCacheUrl = Route::url($baseUrl . '&task=clrcache');
$confirmMsg = Lang::txt('COM_SYSTEM_APC_CONFIRM');

$numHits = $this->cache['num_hits'];
$numMisses = $this->cache['num_misses'];
$totalRequests = $numHits + $numMisses;

$lockingType = isset($this->cache['locking_type'])
    ? $this->cache['locking_type']
    : '[unknown]';
$sharedMemInfo = "{$this->mem['num_seg']} Segment(s)"
    . " with {$this->seg_size}<br />"
    . "({$this->cache['memory_type']} memory, "
    . $lockingType . " locking)";
?>

<?php
    $this->view('_submenu')->display();
?>

<div id="clearcache">
    <a
        class="button"
        data-confirm="<?php echo $confirmMsg; ?>"
        href="<?php echo $clrCacheUrl; ?>"
    >Clear <?php echo $this->cache_mode; ?> cache</a>
</div>

<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="adminForm">
    <div class="grid">
        <div class="col span6">
            <table class="adminlist">
                <thead>
                    <tr>
                        <th colspan="2">
                            General Cache Information
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="row0">
                        <th scope="row">APC Version</th>
                        <td><?php echo $this->apcversion; ?></td>
                    </tr>
                    <tr class="row1">
                        <th scope="row">PHP Version</th>
                        <td><?php echo $this->phpversion; ?></td>
                    </tr>
                    <tr class="row0">
                        <th scope="row">APC Host</th>
                        <td>
                            <?php echo $_SERVER['SERVER_NAME'] . ' ' . $this->host; ?>
                        </td>
                    </tr>
                    <tr class="row1">
                        <th scope="row">Server Software</th>
                        <td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
                    </tr>
                    <tr class="row0">
                        <th scope="row">Shared Memory</th>
                        <td>
                            <?php echo $sharedMemInfo; ?>
                        </td>
                    </tr>
                    <tr class="row1">
                        <th scope="row">Start Time</th>
                        <td>
                            <?php echo date(DATE_FORMAT, $this->cache['start_time']); ?>
                        </td>
                    </tr>
                    <tr class="row0">
                        <th scope="row">Uptime</th>
                        <td><?php echo $this->duration; ?></td>
                    </tr>
                    <tr class="row1">
                        <th scope="row">File Upload Support</th>
                        <td><?php echo $this->cache['file_upload_progress']; ?></td>
                    </tr>
                </tbody>
            </table>

            <table class="adminlist">
                <thead>
                    <tr>
                        <th colspan="2">
                            File Cache Information
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="row0">
                        <th scope="row">Cached Files</th>
                        <td><?php echo "$this->number_files ($this->size_files)"; ?></td>
                    </tr>
                    <tr class="row1">
                        <th scope="row">Hits</th>
                        <td><?php echo "{$this->cache['num_hits']}"; ?></td>
                    </tr>
                    <tr class="row0">
                        <th scope="row">Misses</th>
                        <td><?php echo "{$this->cache['num_misses']}"; ?></td>
                    </tr>
                    <tr class="row1">
                        <th scope="row">Request Rate (hits, misses)</th>
                        <td>
                            <?php echo "$this->req_rate cache requests/second"; ?>
                        </td>
                    </tr>
                    <tr class="row0">
                        <th scope="row">Hit Rate</th>
                        <td>
                            <?php echo "$this->hit_rate cache requests/second"; ?>
                        </td>
                    </tr>
                    <tr class="row1">
                        <th scope="row">Miss Rate</th>
                        <td>
                            <?php echo "$this->miss_rate cache requests/second"; ?>
                        </td>
                    </tr>
                    <tr class="row0">
                        <th scope="row">Insert Rate</th>
                        <td>
                            <?php echo "$this->insert_rate cache requests/second"; ?>
                        </td>
                    </tr>
                    <tr class="row1">
                        <th scope="row">Cache full count</th>
                        <td><?php echo "{$this->cache_user['expunges']}"; ?></td>
                    </tr>
                </tbody>
            </table>

            <table class="adminlist">
                <thead>
                    <tr>
                        <th colspan="2">
                            User Cache Information
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="row0">
                        <th scope="row">Cached Variables</th>
                        <td>
                            <?php echo "$this->number_vars ($this->size_vars)"; ?>
                        </td>
                    </tr>
                    <tr class="row1">
                        <th scope="row">Hits</th>
                        <td><?php echo "{$this->cache_user['num_hits']}"; ?></td>
                    </tr>
                    <tr class="row0">
                        <th scope="row">Misses</th>
                        <td><?php echo "{$this->cache_user['num_misses']}"; ?></td>
                    </tr>
                    <tr class="row1">
                        <th scope="row">Request Rate (hits, misses)</th>
                        <td>
                            <?php echo "$this->req_rate_user cache requests/second"; ?>
                        </td>
                    </tr>
                    <tr class="row0">
                        <th scope="row">Hit Rate</th>
                        <td>
                            <?php echo "$this->hit_rate_user cache requests/second"; ?>
                        </td>
                    </tr>
                    <tr class="row1">
                        <th scope="row">Miss Rate</th>
                        <td>
                            <?php echo "$this->miss_rate_user cache requests/second"; ?>
                        </td>
                    </tr>
                    <tr class="row0">
                        <th scope="row">Insert Rate</th>
                        <td>
                            <?php echo "$this->insert_rate_user cache requests/second"; ?>
                        </td>
                    </tr>
                    <tr class="row1">
                        <th scope="row">Cache full count</th>
                        <td><?php echo "{$this->cache_user['expunges']}"; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col span6">
            <table class="adminlist">
                <thead>
                    <tr>
                        <th colspan="2">
                            Runtime Settings
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $j = 0;
                    foreach (ini_get_all('apc') as $k => $v) {
                        $localVal = str_replace(
                            ',',
                            ',<br />',
                            $v['local_value']
                        );
                        echo "<tr class=\"row$j\"><th>"
                            . $k . "</th><td>"
                            . $localVal . "</td></tr>\n";
                        $j = 1 - $j;
                    }

                    $hasMultipleBlocks = $this->mem['num_seg'] > 1
                        || ($this->mem['num_seg'] == 1
                        && count($this->mem['block_lists'][0]) > 1);
                    if ($hasMultipleBlocks) {
                        $mem_note = 'Memory Usage<br />'
                            . '<span class="smallsub">'
                            . '(multiple slices indicate fragments)'
                            . '</span>';
                    } else {
                        $mem_note = 'Memory Usage';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid">
        <div class="col span6">
            <table class="adminlist">
                <thead>
                    <tr>
                        <th colspan="2">
                            Host Status Diagrams
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="col"><?php echo $mem_note; ?></th>
                        <th scope="col">Hits &amp; Misses</th>
                    </tr>
                <?php
                $size = 'width=' . (GRAPH_SIZE + 50)
                    . ' height=' . (GRAPH_SIZE + 10);
                ?>
                <?php if ($this->graphics_avail) : ?>
                    <?php
                    $img1Url = Route::url(
                        $baseUrl . '&task=mkimage&IMG=1&time=' . $time
                    );
                    $img2Url = Route::url(
                        $baseUrl . '&task=mkimage&IMG=2&time=' . $time
                    );
                    ?>
                    <tr class="row0">
                        <td>
                            <img
                                alt=""
                                <?php echo $size; ?>
                                src="<?php echo $img1Url; ?>"
                            />
                        </td>
                        <td>
                            <img
                                alt=""
                                <?php echo $size; ?>
                                src="<?php echo $img2Url; ?>"
                            />
                        </td>
                    </tr>
                <?php endif; ?>
                    <tr class="row0">
                        <td>
                            <span class="green box">&nbsp;</span>
                            <?php
                            $availPct = sprintf(
                                " (%.1f%%)",
                                $this->mem_avail * 100 / $this->mem_size
                            );
                            echo "Free: $this->bmem_avail " . $availPct;
                            ?>
                        </td>
                        <td>
                            <span class="green box">&nbsp;</span>
                            <?php
                            $hitsPct = $totalRequests > 0
                                ? sprintf(
                                    " (%.1f%%)",
                                    $numHits * 100 / $totalRequests
                                )
                                : '0.0%';
                            echo "Hits: $numHits " . $hitsPct;
                            ?>
                        </td>
                    </tr>
                    <tr class="row1">
                        <td>
                            <span class="red box">&nbsp;</span>
                            <?php
                            $usedPct = sprintf(
                                " (%.1f%%)",
                                $this->mem_used * 100 / $this->mem_size
                            );
                            echo "Used: $this->bmem_used " . $usedPct;
                            ?>
                        </td>
                        <td>
                            <span class="red box">&nbsp;</span>
                            <?php
                            $missPct = $totalRequests > 0
                                ? sprintf(
                                    " (%.1f%%)",
                                    $numMisses * 100 / $totalRequests
                                )
                                : '0.0%';
                            echo "Misses: $numMisses " . $missPct;
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col span6">
            <table class="adminlist">
                <thead>
                    <tr>
                        <?php
                        $colspanAttr = isset($this->mem['adist'])
                            ? ' colspan="2"' : '';
                        ?>
                        <th<?php echo $colspanAttr; ?>>
                            Detailed Memory Usage and Fragmentation
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th<?php echo $colspanAttr; ?>>
                    <?php
                        // Fragementation: (freeseg - 1) / total_seg
                        $nseg = $freeseg = $fragsize = $freetotal = 0;
                    for ($i = 0; $i < $this->mem['num_seg']; $i++) {
                        $ptr = 0;
                        foreach ($this->mem['block_lists'][$i] as $block) {
                            if ($block['offset'] != $ptr) {
                                ++$nseg;
                            }
                            $ptr = $block['offset'] + $block['size'];
                            if ($block['size'] < (5 * 1024 * 1024)) {
                                $fragsize += $block['size'];
                            }
                            $freetotal += $block['size'];
                        }
                        $freeseg += count($this->mem['block_lists'][$i]);
                    }

                    if ($freeseg > 1) {
                        $fragPct = ($fragsize / $freetotal) * 100;
                        $bFrag = \Components\System\Helpers\Html::bsize(
                            $fragsize
                        );
                        $bTotal = \Components\System\Helpers\Html::bsize(
                            $freetotal
                        );
                        $frag = sprintf(
                            "%.2f%% (%s out of %s in %d fragments)",
                            $fragPct,
                            $bFrag,
                            $bTotal,
                            $freeseg
                        );
                    } else {
                        $frag = "0%";
                    }

                    if ($this->graphics_avail) {
                        $size = 'width=' . (2 * GRAPH_SIZE + 150)
                            . ' height=' . (GRAPH_SIZE + 10);
                        echo "<img alt=\"\" $size"
                            . " src=\"index.php?option={$this->option}"
                            . "&amp;controller={$this->controller}"
                            . "&amp;task=mkimage&amp;IMG=3"
                            . "&amp;time=$time\" />";
                    }
                        echo "<br />Fragmentation: $frag";
                        echo "</th>";
                        echo "</tr>";
                    if (isset($this->mem['adist'])) {
                        foreach ($this->mem['adist'] as $i => $v) {
                            $cur = pow(2, $i);
                            $nxt = pow(2, $i + 1) - 1;
                            if ($i == 0) {
                                $range = "1";
                            } else {
                                $range = "$cur - $nxt";
                            }
                            echo "<tr><th>$range</th><td>$v</td></tr>\n";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</form>
