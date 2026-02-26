<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Plugins\Members\Usage\Usage as PlgMembersUsage;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

$cls = 'even';

$this->css('usage', 'com_usage');
?>
<h3 class="section-header"><?php echo Lang::txt('PLG_MEMBERS_USAGE'); ?></h3>

<p class="info"><?php echo Lang::txt('PLG_MEMBERS_USAGE_EXPLANATION'); ?></p>

<div id="statistics">
    <table class="data">
        <caption><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_CAPTION_OVERVIEW'); ?></caption>
        <thead>
            <tr>
                <th scope="col" class="textual-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_ITEM'); ?></th>
                <th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_VALUE'); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr class="<?php

            $cls = ($cls == 'even') ? 'odd' : 'even';
            echo $cls; ?>">
                <th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS'); ?>:</th>
                <td><?php echo $this->contribution['contribs']; ?></td>
            </tr>
            <?php /*if ($this->total_tool_users) { ?>
            <tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
            <th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_USERS_SERVED_TOOLS'); ?>:</th>
            <td><?php echo number_format($this->total_tool_users); ?></td>
            </tr>
            <?php } ?>
            <?php if ($this->total_andmore_users) { ?>
            <tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
            <th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_USERS_SERVED_ANDMORE'); ?>:</th>
            <td><?php echo number_format($this->total_andmore_users); ?></td>
            </tr>
            <?php }*/ ?>
            <tr class="<?php

            $cls = ($cls == 'even') ? 'odd' : 'even';
            echo $cls; ?>">
                <th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS_RANK'); ?>:</th>
                <td><?php echo $this->rank; ?></td>
            </tr>
            <tr class="<?php

            $cls = ($cls == 'even') ? 'odd' : 'even';
            echo $cls; ?>">
                <th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS_FIRST'); ?>:</th>
                <td><?php echo Date::of($this->contribution['first'])->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
            </tr>
            <tr class="<?php

            $cls = ($cls == 'even') ? 'odd' : 'even';
            echo $cls; ?>">
                <th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS_LAST'); ?>:</th>
                <td><?php echo Date::of($this->contribution['last'])->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
            </tr>
            <tr class="<?php

            $cls = ($cls == 'even') ? 'odd' : 'even';
            echo $cls; ?>">
                <th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_CITATIONS'); ?>:</th>
                <td><?php echo $this->citation_count; ?></td>
            </tr>
            <?php if ($this->cluster_users) { ?>
                <tr class="<?php

                $cls = ($cls == 'even') ? 'odd' : 'even';
                echo $cls; ?>">
                    <th scope="row"><?php echo Lang::txt('PLG_MEMBERS_USAGE_CLUSTERS'); ?>:</th>
                    <td>
                        <?php
                        echo Lang::txt(
                            'PLG_MEMBERS_USAGE_USERS_IN_COURSES_SERVED',
                            number_format($this->cluster_users),
                            number_format($this->cluster_classes),
                            number_format($this->cluster_schools)
                        );
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <table class="data">
        <caption><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_CAPTION_TOOLS'); ?></caption>
        <thead>
            <tr>
                <th scope="col"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_NUMBER'); ?></th>
                <th scope="col"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_TOOL_TITLE'); ?></th>
                <th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_USERS_YEAR'); ?>
                </th>
                <th scope="col" class="numerical-data">
                    <?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_SIM_RUNS_YEAR'); ?></th>
                <th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_USERS_TOTAL'); ?>
                </th>
                <th scope="col" class="numerical-data">
                    <?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_SIM_RUNS_TOTAL'); ?></th>
                <th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CITATIONS'); ?>
                </th>
                <th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_PUBLISHED'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php

            if ($this->tool_stats) {
                $count = 0;
                $cls = 'even';
                $sum_usercount_12 = 0;
                $sum_usercount_14 = 0;
                $sum_simcount_12 = 0;
                $sum_simcount_14 = 0;
                foreach ($this->tool_stats as $row) {
                    $user_count_12 = PlgMembersUsage::getUsercount($row->id, 12, 7);
                    $user_count_14 = PlgMembersUsage::getUsercount($row->id, 14, 7);
                    $sim_count_12 = PlgMembersUsage::getSimcount($row->id, 12);
                    $sim_count_14 = PlgMembersUsage::getSimcount($row->id, 14);

                    $sum_usercount_12 += intval($user_count_12);
                    $sum_usercount_14 += intval($user_count_14);
                    $sum_simcount_12 += intval($sim_count_12);
                    $sum_simcount_14 += intval($sim_count_14);
                    ?>
                    <tr class="<?php

                    $cls = ($cls == 'even') ? 'odd' : 'even';
                    echo $cls; ?>">
                        <td><?php echo ($count + 1); ?></td>
                        <?php
                        $resourceUrl = Route::url(
                            'index.php?option=com_resources&id=' . $row->id
                        );
                        $usageUrl12 = Route::url(
                            'index.php?option=com_usage&task=tools&id='
                            . $row->id . '&period=12'
                        );
                        $usageUrl14 = Route::url(
                            'index.php?option=com_usage&task=tools&id='
                            . $row->id . '&period=14'
                        );
                        $fmtUser12 = is_numeric($user_count_12)
                            ? number_format($user_count_12)
                            : $user_count_12;
                        $fmtSim12 = is_numeric($sim_count_12)
                            ? number_format($sim_count_12)
                            : $sim_count_12;
                        $fmtUser14 = is_numeric($user_count_14)
                            ? number_format($user_count_14)
                            : $user_count_14;
                        $fmtSim14 = is_numeric($sim_count_14)
                            ? number_format($sim_count_14)
                            : $sim_count_14;
                        ?>
                        <td class="textual-data">
                            <a href="<?php echo $resourceUrl; ?>">
                                <?php echo $row->title; ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?php echo $usageUrl12; ?>">
                                <?php echo $fmtUser12; ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?php echo $usageUrl12; ?>">
                                <?php echo $fmtSim12; ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?php echo $usageUrl14; ?>">
                                <?php echo $fmtUser14; ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?php echo $usageUrl14; ?>">
                                <?php echo $fmtSim14; ?>
                            </a>
                        </td>
                        <td><?php echo PlgMembersUsage::getCitationcount($row->id, 0); ?></td>
                        <td><?php echo Date::of($row->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
                    </tr>
                    <?php

                    $count++;
                }
                if ($this->tool_total_14 && $this->tool_total_12) {
                    ?>
                    <tr class="summary">
                        <td class="group"></td>
                        <td class="group textual-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TOTAL'); ?></td>
                        <td class="group"><?php echo number_format($this->tool_total_12); ?></td>
                        <td class="group"><?php echo number_format($sum_simcount_12); ?></td>
                        <td class="group"><?php echo number_format($this->tool_total_14); ?></td>
                        <td class="group"><?php echo number_format($sum_simcount_14); ?></td>
                        <td class="group"></td>
                        <td class="group"></td>
                    </tr>
                    <?php
                }
            } else { ?>
                <tr class="odd">
                    <td colspan="8" class="textual-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_NO_RESULTS'); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <table class="data">
        <caption><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_CAPTION_RESOURCES'); ?></caption>
        <thead>
            <tr>
                <th scope="col"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_NUMBER'); ?></th>
                <th scope="col"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_RESOURCE_TITLE'); ?></th>
                <th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_USERS_YEAR'); ?>
                </th>
                <th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_USERS_TOTAL'); ?>
                </th>
                <th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CITATIONS'); ?>
                </th>
                <th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_PUBLISHED'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php

            if ($this->andmore_stats) {
                $cls = 'even';
                $count = 0;
                $total = array(
                    'usercount12' => 0,
                    'usercount14' => 0,
                    'citations' => 0
                );

                $serials = (array) PlgMembersUsage::getSerialResourceTypes();

                // First pass
                // See if any of the resource types are lists of other resources
                $children = array();
                foreach ($this->andmore_stats as $row) {
                    if (in_array($row->type_id, $serials)) {
                        $children[$row->id] = (array) PlgMembersUsage::getSerialResourceChildren($row->id);
                    } else {
                        $children[$row->id] = array();
                    }
                }

                // Second pass
                $andmore = array(
                    0 => array()
                );
                foreach ($this->andmore_stats as $row) {
                    $hasParent = false;
                    foreach ($children as $parent => $childs) {
                        if (in_array($row->id, $childs)) {
                            if (!isset($andmore[$parent])) {
                                $andmore[$parent] = array();
                            }
                            $andmore[$parent][] = $row;
                            $hasParent = true;
                            break;
                        }
                    }
                    if (!$hasParent) {
                        $andmore[0][] = $row;
                    }
                }

                foreach ($andmore[0] as $row) {
                    $result = PlgMembersUsage::getUsercount($row->id, 12);
                    $usercount12 = (is_numeric($result)) ? number_format($result) : $result;

                    $result = PlgMembersUsage::getUsercount($row->id, 14);
                    $usercount14 = (is_numeric($result)) ? number_format($result) : $result;

                    $cites = PlgMembersUsage::getCitationcount($row->id, 0);

                    if (!in_array($row->type_id, $serials)) {
                        $total['usercount12'] += (int) str_replace(',', '', $usercount12);
                        $total['usercount14'] += (int) str_replace(',', '', $usercount14);
                    }
                    $total['citations'] += (int) $cites;
                    ?>
                    <tr class="<?php

                    $cls = ($cls == 'even') ? 'odd' : 'even';
                    echo $cls; ?>">
                        <td><?php echo ($count + 1); ?></td>
                        <?php
                        $resUrl = Route::url(
                            'index.php?option=com_resources&id=' . $row->id
                        );
                        ?>
                        <td class="textual-data">
                            <a href="<?php echo $resUrl; ?>">
                                <?php echo $row->title; ?>
                            </a>
                            <span class="small">
                                <?php echo $row->type; ?>
                            </span>
                        </td>
                        <td><?php echo $usercount12; ?></td>
                        <td><?php echo $usercount14; ?></td>
                        <td><?php echo $cites ?></td>
                        <td><?php echo Date::of($row->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
                    </tr>
                    <?php

                    $count++;

                    if (isset($andmore[$row->id])) {
                        foreach ($andmore[$row->id] as $rw) {
                            $result = PlgMembersUsage::getUsercount($rw->id, 12);
                            $usercount12 = (is_numeric($result)) ? number_format($result) : $result;

                            $result = PlgMembersUsage::getUsercount($rw->id, 14);
                            $usercount14 = (is_numeric($result)) ? number_format($result) : $result;

                            $cites = PlgMembersUsage::getCitationcount($rw->id, 0);

                            $total['usercount12'] += (int) str_replace(',', '', $usercount12);
                            $total['usercount14'] += (int) str_replace(',', '', $usercount14);
                            $total['citations'] += (int) $cites;
                            ?>
                            <tr class="child <?php

                            $cls = ($cls == 'even') ? 'odd' : 'even';
                            echo $cls; ?>">
                                <?php
                                $childUrl = Route::url(
                                    'index.php?option=com_resources&id=' . $rw->id
                                );
                                $childDate = Date::of($rw->publish_up)
                                    ->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                                ?>
                                <td class="highlight">
                                    <?php echo ($count + 1); ?>
                                </td>
                                <td class="highlight textual-data">
                                    <span class="child-connector">|-</span>
                                    <a href="<?php echo $childUrl; ?>">
                                        <?php echo $rw->title; ?>
                                    </a>
                                    <span class="small">
                                        <?php echo $rw->type; ?>
                                    </span>
                                </td>
                                <td class="highlight">
                                    <?php echo $usercount12; ?>
                                </td>
                                <td class="highlight">
                                    <?php echo $usercount14; ?>
                                </td>
                                <td class="highlight">
                                    <?php echo $cites ?>
                                </td>
                                <td class="highlight">
                                    <?php echo $childDate; ?>
                                </td>
                            </tr>
                            <?php

                            $count++;
                        }
                    }
                }

                if ($count) {
                    ?>
                    <tr class="summary">
                        <td class="group"></td>
                        <td class="group"><?php echo Lang::txt('TOTAL'); ?></td>
                        <td class="group"><?php echo number_format($total['usercount12']); ?></td>
                        <td class="group"><?php echo number_format($total['usercount14']); ?></td>
                        <td class="group"><?php echo number_format($total['citations']); ?></td>
                        <td class="group"></td>
                    </tr>
                    <?php
                }
            } else { ?>
                <tr class="odd">
                    <td colspan="6" class="textual-data"><?php echo Lang::txt('PLG_MEMBERS_USAGE_NO_RESULTS'); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <p><?php echo Lang::txt('PLG_MEMBERS_USAGE_FOOTNOTE'); ?></p>
</div>
