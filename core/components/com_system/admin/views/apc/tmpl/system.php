<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Menu items
Toolbar::title(Lang::txt('COM_SYSTEM_APC_SYSTEM'), 'config.png');

$this->css('apc.css');

//$this->MYREQUEST = $this->MYREQUEST;
$this->MYREQUEST = str_replace('&amp;', '&', $this->MYREQUEST);
$this->MYREQUEST = str_replace('&', '&amp;', $this->MYREQUEST);
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
$cache = $this->cache;

$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
$obParam = $this->MYREQUEST['OB'];
$obSuffix = "&amp;OB=" . $obParam;
$helperClass = \Components\System\Helpers\Html::class;
?>

<?php
    $this->view('_submenu')->display();
?>

<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="adminForm">
<?php
if (!isset($fieldname)) {
    $fieldname = 'filename';
    $fieldheading = 'Script Filename';
    if (ini_get("apc.stat")) {
        $fieldkey = 'inode';
    } else {
        $fieldkey = 'filename';
    }
}
if (!empty($this->MYREQUEST['SH'])) {
    echo '<table class="adminlist"><thead>';
    echo '<tr><th>Attribute</th><th>Value</th></tr>';
    echo '</thead><tbody>';

    $m = 0;
    foreach ($this->scope_list as $j => $list) {
        foreach ($cache[$list] as $i => $entry) {
            if (md5($entry[$fieldkey]) != $this->MYREQUEST['SH']) {
                continue;
            }
            foreach ($entry as $k => $value) {
                if ($k == "num_hits") {
                    $value = sprintf(
                        "%s (%.2f%%)",
                        $value,
                        $value * 100 / $cache['num_hits']
                    );
                }
                if ($k == 'deletion_time') {
                    if (!$entry['deletion_time']) {
                        $value = "None";
                    }
                }
                $label = ucwords(preg_replace("/_/", " ", $k));
                $isTime = preg_match("/time/", $k)
                    && $value != 'None';
                $display = $isTime
                    ? date(DATE_FORMAT, $value)
                    : $value;
                echo "<tr class=\"tr-$m\">"
                    . "<td class=\"td-0\">$label</td>"
                    . "<td class=\"td-last\">$display</td>"
                    . "</tr>";
                $m = 1 - $m;
            }
            if ($fieldkey == 'info') {
                echo "<tr class=\"tr-$m\">"
                    . '<td class="td-0">Stored Value</td>'
                    . '<td class="td-last"><pre>';
                $output = var_export(
                    apc_fetch($entry[$fieldkey]),
                    true
                );
                echo htmlspecialchars($output);
                echo "</pre></td></tr>\n";
            }
            break;
        }
    }

    echo '</tbody></table>';
    echo '</div>';
} else {
    $cols = 7;

    $scopeA = $this->MYREQUEST['SCOPE'] == 'A'
        ? ' selected="selected"' : '';
    $scopeD = $this->MYREQUEST['SCOPE'] == 'D'
        ? ' selected="selected"' : '';

    $sort1H = $this->MYREQUEST['SORT1'] == 'H'
        ? ' selected="selected"' : '';
    $sort1Z = $this->MYREQUEST['SORT1'] == 'Z'
        ? ' selected="selected"' : '';
    $sort1S = $this->MYREQUEST['SORT1'] == 'S'
        ? ' selected="selected"' : '';
    $sort1A = $this->MYREQUEST['SORT1'] == 'A'
        ? ' selected="selected"' : '';
    $sort1M = $this->MYREQUEST['SORT1'] == 'M'
        ? ' selected="selected"' : '';
    $sort1C = $this->MYREQUEST['SORT1'] == 'C'
        ? ' selected="selected"' : '';
    $sort1D = $this->MYREQUEST['SORT1'] == 'D'
        ? ' selected="selected"' : '';
    $sort1T = $this->MYREQUEST['SORT1'] == 'T'
        ? ' selected="selected"' : '';

    $sort2D = $this->MYREQUEST['SORT2'] == 'D'
        ? ' selected="selected"' : '';
    $sort2A = $this->MYREQUEST['SORT2'] == 'A'
        ? ' selected="selected"' : '';
    ?>
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
                    <option value="H"<?php echo $sort1H; ?>>Hits</option>
                    <option value="Z"<?php echo $sort1Z; ?>>Size</option>
                    <option value="S"<?php echo $sort1S; ?>>
                        <?php echo $fieldheading; ?>
                    </option>
                    <option value="A"<?php echo $sort1A; ?>>Last accessed</option>
                    <option value="M"<?php echo $sort1M; ?>>Last modified</option>
                    <option value="C"<?php echo $sort1C; ?>>Created at</option>
                    <option value="D"<?php echo $sort1D; ?>>Deleted at</option>
            <?php if ($fieldname == 'info') { ?>
                    <option value="D"<?php echo $sort1T; ?>>Timeout</option>
            <?php } ?>
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
                <label for="filter_search">Search:</label>
                <input
                    name="SEARCH"
                    id="filter_search"
                    value="<?php echo $this->MYREQUEST['SEARCH']; ?>"
                    type="text"
                    size="25"
                />

                &nbsp;<input type="submit" value="GO!" />
            </div>
        </div>
    </fieldset>

    <?php
    if (isset($this->MYREQUEST['SEARCH'])) {
        // Don't use preg_quote because we want the user to be able
        // to specify a regular expression subpattern.
        $this->MYREQUEST['SEARCH'] = '/'
            . str_replace('/', '\\/', $this->MYREQUEST['SEARCH'])
            . '/i';
        if (preg_match($this->MYREQUEST['SEARCH'], 'test') === false) {
            echo '<p class="error">'
                . 'Error: enter a valid regular expression'
                . ' as a search query.</p>';
        }
    }
    ?>
    <table class="adminlist">
        <thead>
            <tr>
                <th>
                    <?php echo $helperClass::sortheader(
                        $this->MYREQUEST,
                        $this->MY_SELF_WO_SORT,
                        'S',
                        $fieldheading,
                        $obSuffix
                    ); ?>
                </th>
                <th>
                    <?php echo $helperClass::sortheader(
                        $this->MYREQUEST,
                        $this->MY_SELF_WO_SORT,
                        'H',
                        'Hits',
                        $obSuffix
                    ); ?>
                </th>
                <th>
                    <?php echo $helperClass::sortheader(
                        $this->MYREQUEST,
                        $this->MY_SELF_WO_SORT,
                        'Z',
                        'Size',
                        $obSuffix
                    ); ?>
                </th>
                <th>
                    <?php echo $helperClass::sortheader(
                        $this->MYREQUEST,
                        $this->MY_SELF_WO_SORT,
                        'A',
                        'Last accessed',
                        $obSuffix
                    ); ?>
                </th>
                <th>
                    <?php echo $helperClass::sortheader(
                        $this->MYREQUEST,
                        $this->MY_SELF_WO_SORT,
                        'M',
                        'Last modified',
                        $obSuffix
                    ); ?>
                </th>
                <th>
                    <?php echo $helperClass::sortheader(
                        $this->MYREQUEST,
                        $this->MY_SELF_WO_SORT,
                        'C',
                        'Created at',
                        $obSuffix
                    ); ?>
                </th>
            <?php
            if ($fieldname == 'info') {
                $cols += 1;
                ?>
                    <th>
                        <?php echo $helperClass::sortheader(
                            $this->MYREQUEST,
                            $this->MY_SELF_WO_SORT,
                            'T',
                            'Timeout',
                            $obSuffix
                        ); ?>
                    </th>
                    <?php
            }
            ?>
                <th>
                    <?php echo $helperClass::sortheader(
                        $this->MYREQUEST,
                        $this->MY_SELF_WO_SORT,
                        'D',
                        'Deleted at',
                        $obSuffix
                    ); ?>
                </th>
            </tr>
        </thead>
        <tbody>
    <?php
    // builds list with alpha numeric sortable keys
    //
    $list = array();
    $scope = $this->scope_list[$this->MYREQUEST['SCOPE']];
    foreach ($this->cache[$scope] as $i => $entry) {
        switch ($this->MYREQUEST['SORT1']) {
            case 'A':
                $k = sprintf('%015d-', $entry['access_time']);
                break;
            case 'H':
                $k = sprintf('%015d-', $entry['num_hits']);
                break;
            case 'Z':
                $k = sprintf('%015d-', $entry['mem_size']);
                break;
            case 'M':
                $k = sprintf('%015d-', $entry['mtime']);
                break;
            case 'C':
                $k = sprintf('%015d-', $entry['creation_time']);
                break;
            case 'T':
                $k = sprintf('%015d-', $entry['ttl']);
                break;
            case 'D':
                $k = sprintf('%015d-', $entry['deletion_time']);
                break;
            case 'S':
                $k = '';
                break;
        }
        $list[$k . $entry[$fieldname]] = $entry;
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
        foreach ($list as $k => $entry) {
            $searchMatch = !$this->MYREQUEST['SEARCH']
                || preg_match(
                    $this->MYREQUEST['SEARCH'],
                    $entry[$fieldname]
                ) != 0;
            if ($searchMatch) {
                $field_value = $this->escape(
                    strip_tags($entry[$fieldname], '')
                );
                $detailUrl = $MY_SELF . '&amp;OB=' . $obParam
                    . '&amp;SH=' . md5($entry[$fieldkey]);
                $accessDate = date(
                    DATE_FORMAT,
                    $entry['access_time']
                );
                $mtimeDate = date(DATE_FORMAT, $entry['mtime']);
                $createDate = date(
                    DATE_FORMAT,
                    $entry['creation_time']
                );
                echo
                    '<tr class="tr-' . ($i % 2) . '">'
                    . '<td class="td-0">'
                    . '<a href="' . $detailUrl . '">'
                    . $field_value . '</a></td>'
                    . '<td class="td-n center">'
                    . $entry['num_hits'] . '</td>'
                    . '<td class="td-n right">'
                    . $entry['mem_size'] . '</td>'
                    . '<td class="td-n center">'
                    . $accessDate . '</td>'
                    . '<td class="td-n center">'
                    . $mtimeDate . '</td>'
                    . '<td class="td-n center">'
                    . $createDate . '</td>';

                if ($fieldname == 'info') {
                    if ($entry['ttl']) {
                        echo '<td class="td-n center">'
                            . $entry['ttl'] . ' seconds</td>';
                    } else {
                        echo '<td class="td-n center">None</td>';
                    }
                }
                if ($entry['deletion_time']) {
                    $delDate = date(
                        DATE_FORMAT,
                        $entry['deletion_time']
                    );
                    echo '<td class="td-last center">'
                        . $delDate . '</td>';
                } elseif ($this->MYREQUEST['OB'] == OB_USER_CACHE) {
                    $delUrl = $MY_SELF . '&amp;OB=' . $obParam
                        . '&amp;DU='
                        . urlencode($entry[$fieldkey]);
                    echo '<td class="td-last center">';
                    echo '[<a href="' . $delUrl
                        . '">Delete Now</a>]';
                    echo '</td>';
                } else {
                    echo '<td class="td-last center"> &nbsp; </td>';
                }
                echo '</tr>';
                $i++;
                if ($i == $this->MYREQUEST['COUNT']) {
                    break;
                }
            }
        }
    } else {
        echo '<tr class="tr-0">'
            . '<td class="center" colspan="' . $cols . '">'
            . '<i>No data</i></td></tr>';
    }
    echo '</tbody></table>';

    if ($list && $i < count($list)) {
        $remaining = count($list) - $i;
        echo '<a href="' . $MY_SELF . '&amp;OB=' . $obParam
            . '&amp;COUNT=0"><i>'
            . $remaining . ' more available...</i></a>';
    }
}
?>
</form>
