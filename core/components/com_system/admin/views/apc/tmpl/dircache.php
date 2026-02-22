<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Menu items
Toolbar::title(Lang::txt('COM_SYSTEM_APC_DIR'), 'config.png');

$this->css('apc.css');

//$this->MYREQUEST = $this->MYREQUEST;
$this->MY_SELF_WO_SORT = str_replace(
    '&amp;',
    '&',
    $this->MY_SELF_WO_SORT
);
$this->MY_SELF_WO_SORT = str_replace(
    '&',
    '&amp;',
    $this->MY_SELF_WO_SORT
);
$MY_SELF = str_replace('&amp;', '&', $this->MY_SELF);
$MY_SELF = str_replace('&', '&amp;', $MY_SELF);

$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
$obParam = $this->MYREQUEST['OB'];

$scopeA = $this->MYREQUEST['SCOPE'] == 'A'
    ? ' selected="selected"' : '';
$scopeD = $this->MYREQUEST['SCOPE'] == 'D'
    ? ' selected="selected"' : '';

$sort1H = $this->MYREQUEST['SORT1'] == 'H'
    ? ' selected="selected"' : '';
$sort1Z = $this->MYREQUEST['SORT1'] == 'Z'
    ? ' selected="selected"' : '';
$sort1T = $this->MYREQUEST['SORT1'] == 'T'
    ? ' selected="selected"' : '';
$sort1S = $this->MYREQUEST['SORT1'] == 'S'
    ? ' selected="selected"' : '';
$sort1A = $this->MYREQUEST['SORT1'] == 'A'
    ? ' selected="selected"' : '';
$sort1C = $this->MYREQUEST['SORT1'] == 'C'
    ? ' selected="selected"' : '';

$sort2D = $this->MYREQUEST['SORT2'] == 'D'
    ? ' selected="selected"' : '';
$sort2A = $this->MYREQUEST['SORT2'] == 'A'
    ? ' selected="selected"' : '';

$obSuffix = "&amp;OB=" . $obParam;
$helperClass = \Components\System\Helpers\Html::class;
?>

<?php
    $this->view('_submenu')->display();
?>

<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
        <input type="hidden" name="task" value="<?php echo $this->task; ?>" />
        <input type="hidden" name="OB" value="<?php echo $obParam; ?>" />

        <div class="grid">
            <div class="col span7">
                <label for="filter-scope">Scope:</label>
                <select name="SCOPE" id="filter-scope">
                    <option value="A"<?php echo $scopeA; ?>>Active</option>
                    <option value="D"<?php echo $scopeD; ?>>Deleted</option>
                </select>

                <label for="filter-sort1">Sorting:</label>
                <select name="SORT1" id="filter-sort1">
                    <option value="H"<?php echo $sort1H; ?>>Total Hits</option>
                    <option value="Z"<?php echo $sort1Z; ?>>Total Size</option>
                    <option value="T"<?php echo $sort1T; ?>>Number of Files</option>
                    <option value="S"<?php echo $sort1S; ?>>Directory Name</option>
                    <option value="A"<?php echo $sort1A; ?>>Avg. Size</option>
                    <option value="C"<?php echo $sort1C; ?>>Avg. Hits</option>
                </select>

                <select name="SORT2">
                    <option value="D"<?php echo $sort2D; ?>>DESC</option>
                    <option value="A"<?php echo $sort2A; ?>>ASC</option>
                </select>

                <select name="COUNT" onChange="form.submit()">
                    <?php
                    $countOptions = array(
                        '10' => 'Top 10',
                        '20' => 'Top 20',
                        '50' => 'Top 50',
                        '100' => 'Top 100',
                        '150' => 'Top 150',
                        '200' => 'Top 200',
                        '500' => 'Top 500',
                        '0' => 'All',
                    );
                    foreach ($countOptions as $val => $label) :
                        $sel = $this->MYREQUEST['COUNT'] == $val
                            ? ' selected="selected"' : '';
                        ?>
                    <option value="<?php echo $val; ?>"<?php echo $sel; ?>>
                        <?php echo $label; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col span5">
                <label for="AGGR">Group By Dir Level:</label>
                <select name="AGGR" id="AGGR">
                    <option value="" selected="selected">None</option>
                <?php for ($i = 1; $i < 10; $i++) :
                    $aggrSel = $this->MYREQUEST['AGGR'] == $i
                        ? ' selected="selected"' : '';
                    ?>
                    <option value="<?php echo $i; ?>"<?php echo $aggrSel; ?>>
                        <?php echo $i; ?>
                    </option>
                <?php endfor; ?>
                </select>
                &nbsp;<input type="submit" value="GO!" />
            </div>
        </div>
    </fieldset>

    <table class="adminlist">
        <thead>
            <tr>
                <th>
                    <?php echo $helperClass::sortheader(
                        $this->MYREQUEST,
                        $this->MY_SELF_WO_SORT,
                        'S',
                        'Directory Name',
                        $obSuffix
                    ); ?>
                </th>
                <th>
                    <?php echo $helperClass::sortheader(
                        $this->MYREQUEST,
                        $this->MY_SELF_WO_SORT,
                        'T',
                        'Number of Files',
                        $obSuffix
                    ); ?>
                </th>
                <th>
                    <?php echo $helperClass::sortheader(
                        $this->MYREQUEST,
                        $this->MY_SELF_WO_SORT,
                        'H',
                        'Total Hits',
                        $obSuffix
                    ); ?>
                </th>
                <th>
                    <?php echo $helperClass::sortheader(
                        $this->MYREQUEST,
                        $this->MY_SELF_WO_SORT,
                        'Z',
                        'Total Size',
                        $obSuffix
                    ); ?>
                </th>
                <th>
                    <?php echo $helperClass::sortheader(
                        $this->MYREQUEST,
                        $this->MY_SELF_WO_SORT,
                        'C',
                        'Avg. Hits',
                        $obSuffix
                    ); ?>
                </th>
                <th>
                    <?php echo $helperClass::sortheader(
                        $this->MYREQUEST,
                        $this->MY_SELF_WO_SORT,
                        'A',
                        'Avg. Size',
                        $obSuffix
                    ); ?>
                </th>
            </tr>
        </thead>
        <tbody>
<?php
    // builds list with alpha numeric sortable keys
    $tmp = $list = array();
$scope = $this->scope_list[$this->MYREQUEST['SCOPE']];
foreach ($this->cache[$scope] as $entry) {
    $n = dirname($entry['filename']);
    if ($this->MYREQUEST['AGGR'] > 0) {
        $aggrPattern = "!^(/?(?:[^/\\\\]+[/\\\\]){"
            . ($this->MYREQUEST['AGGR'] - 1)
            . "}[^/\\\\]*).*!";
        $n = preg_replace($aggrPattern, "$1", $n);
    }
    if (!isset($tmp[$n])) {
        $tmp[$n] = array('hits' => 0,'size' => 0,'ents' => 0);
    }
    $tmp[$n]['hits'] += $entry['num_hits'];
    $tmp[$n]['size'] += $entry['mem_size'];
    ++$tmp[$n]['ents'];
}

foreach ($tmp as $k => $v) {
    switch ($this->MYREQUEST['SORT1']) {
        case 'A':
            $kn = sprintf('%015d-', $v['size'] / $v['ents']);
            break;
        case 'T':
            $kn = sprintf('%015d-', $v['ents']);
            break;
        case 'H':
            $kn = sprintf('%015d-', $v['hits']);
            break;
        case 'Z':
            $kn = sprintf('%015d-', $v['size']);
            break;
        case 'C':
            $kn = sprintf('%015d-', $v['hits'] / $v['ents']);
            break;
        case 'S':
            $kn = $k;
            break;
    }
    $list[$kn . $k] = array($k, $v['ents'], $v['hits'], $v['size']);
}

if ($list) {
    // sort list
    switch ($this->MYREQUEST['SORT2']) {
        case "A":
            krsort($list);
            break;
        case "D":
            ksort($list);
            break;
    }
    // output list
    $i = 0;
    foreach ($list as $entry) {
        echo
            '<tr class="row' . $i % 2 . '">'
            . '<td class="td-0">' . $entry[0] . '</td>'
            . '<td class="td-n center">' . $entry[1] . '</td>'
            . '<td class="td-n center">' . $entry[2] . '</td>'
            . '<td class="td-n center">' . $entry[3] . '</td>'
            . '<td class="td-n center">'
            . round($entry[2] / $entry[1]) . '</td>'
            . '<td class="td-n center">'
            . round($entry[3] / $entry[1]) . '</td>'
            . '</tr>';

        if (++$i == $this->MYREQUEST['COUNT']) {
            break;
        }
    }
} else {
    echo '<tr class="row0">'
        . '<td class="center" colspan="6">'
        . '<i>No data</i></td></tr>';
}

    echo "</tbody></table>";

if ($list && $i < count($list)) {
    echo '<a href="' . $MY_SELF . '&amp;OB=' . $obParam
        . '&amp;COUNT=0"><i>'
        . (count($list) - $i) . ' more available...</i></a>';
}
?>
</form>
