<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\App;
use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_EVENTS_MANAGER'), 'event');
Toolbar::preferences('com_events', '550');
Toolbar::spacer();
Toolbar::custom(
    'addpage',
    'new',
    'COM_EVENTS_PAGES_ADD',
    'COM_EVENTS_PAGES_ADD',
    true,
    false
);
Toolbar::custom(
    'respondents',
    'user',
    'COM_EVENTS_VIEW_RESPONDENTS',
    'COM_EVENTS_VIEW_RESPONDENTS',
    true,
    false
);
Toolbar::spacer();
Toolbar::publishList();
Toolbar::unpublishList();
Toolbar::spacer();
Toolbar::addNew();
Toolbar::editList();
Toolbar::deleteList();

Toolbar::spacer();
Toolbar::help('events');

Html::behavior('tooltip');
?>

<?php
$formAction = Route::url('index.php?option=' . $this->option);
$searchPlaceholder = Lang::txt('COM_EVENTS_SEARCH_PLACEHOLDER');
?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <label for="filter_search"><?php echo Lang::txt('COM_EVENTS_SEARCH'); ?>:</label>
        <input type="text"
            name="search"
            id="filter_search"
            class="filter"
            value="<?php echo $this->escape($this->filters['search']); ?>"
            placeholder="<?php echo $searchPlaceholder; ?>"
        />

        <?php echo $this->clist; ?>
        <?php echo $this->glist; ?>

        <input type="submit" name="submitsearch" value="<?php echo Lang::txt('COM_EVENTS_SEARCH_GO'); ?>" />
    </fieldset>

    <table class="adminlist">
        <thead>
            <tr>
                <th scope="col" class="priority-5"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_ID'); ?></th>
                <th scope="col">
                    <input type="checkbox"
                        name="checkall-toggle"
                        id="checkall-toggle"
                        value=""
                        class="checkbox-toggle toggle-all"
                    />
                    <label for="checkall-toggle" class="sr-only visually-hidden">
                        <?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>
                    </label>
                </th>
                <th scope="col"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_TITLE'); ?></th>
                <th scope="col" class="priority-4"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CATEGORY'); ?></th>
                <th scope="col" class="priority-3"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_STATE'); ?></th>
                <th scope="col" class="priority-4"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_TIMESHEET'); ?></th>
                <th scope="col" class="priority-5"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_ACCESS'); ?></th>
                <th scope="col"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_PAGES'); ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="8">
                    <?php
                    // Initiate paging
                    $pageNav = $this->pagination(
                        $this->total,
                        $this->filters['start'],
                        $this->filters['limit']
                    );
                    echo $pageNav->render();
                    ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
<?php
$k = 0;
$database = App::get('db');
$p = new \Components\Events\Tables\Page($database);
for ($i = 0, $n = count($this->rows); $i < $n; $i++) {
    $row = &$this->rows[$i];
    ?>
            <tr class="<?php echo "row$k"; ?>">
                <td class="priority-5">
                    <?php echo $row->id; ?>
                </td>
                <td>
                    <?php if ($row->checked_out && $row->checked_out != User::get('id')) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <input type="checkbox"
                            id="cb<?php echo $i; ?>"
                            name="id[]"
                            value="<?php echo $row->id; ?>"
                            class="checkbox-toggle"
                        />
                        <label for="cb<?php echo $i; ?>" class="sr-only visually-hidden"><?php echo $row->id; ?></label>
                    <?php } ?>
                </td>
                <td>
                    <?php if ($row->checked_out && $row->checked_out != User::get('id')) { ?>
                        <?php $editorName = $this->escape(stripslashes($row->editor ? $row->editor : '')); ?>
                        <span class="checkedout hasTip" title="Checked out::<?php echo $editorName; ?>">
                            <?php echo $this->escape(html_entity_decode(stripslashes($row->title))); ?>
                        </span>
                    <?php } else { ?>
                        <?php
                        $editUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=' . $this->controller
                            . '&task=edit&id=' . $row->id
                        );
                        ?>
                        <a href="<?php echo $editUrl; ?>">
                            <?php echo $this->escape(html_entity_decode(stripslashes($row->title))); ?>
                        </a>
                    <?php } ?>
                </td>
                <td class="priority-4">
                    <span>
                        <?php echo $this->escape($row->category); ?>
                    </span>
                </td>
                <td class="priority-3">
                    <?php
                    $now = Date::toSql();
                    $alt = Lang::txt('COM_EVENTS_EVENT_UNPUBLISHED');
                    if ($now <= $row->publish_up && $row->state == "1") {
                        $alt = Lang::txt('COM_EVENTS_EVENT_PENDING');
                    } elseif (
                        ($now <= $row->publish_down || !$row->publish_down
                            || $row->publish_down == "0000-00-00 00:00:00")
                        && $row->state == "1"
                    ) {
                        $alt = Lang::txt('COM_EVENTS_EVENT_PUBLISHED');
                    } elseif ($now > $row->publish_down && $row->state == "1") {
                        $alt = Lang::txt('COM_EVENTS_EVENT_EXPIRED');
                    } elseif ($row->state == "0") {
                        $alt = Lang::txt('COM_EVENTS_EVENT_UNPUBLISHED');
                    }

                    $times = '';
                    if (isset($row->publish_up)) {
                        if (!$row->publish_up || $row->publish_up == '0000-00-00 00:00:00') {
                            $fromTxt = Lang::txt('COM_EVENTS_CAL_LANG_FROM');
                            $alwaysTxt = Lang::txt('COM_EVENTS_CAL_LANG_ALWAYS');
                            $times .= $fromTxt . ' : ' . $alwaysTxt . '<br />';
                        } else {
                            $times .= Lang::txt('COM_EVENTS_CAL_LANG_FROM')
                                . ' : ' . date('Y-m-d H:i:s', strtotime($row->publish_up)) . '<br />';
                        }
                    }
                    if (isset($row->publish_down)) {
                        if (!$row->publish_down || $row->publish_down == '0000-00-00 00:00:00') {
                            $toTxt = Lang::txt('COM_EVENTS_CAL_LANG_TO');
                            $neverTxt = Lang::txt('COM_EVENTS_CAL_LANG_NEVER');
                            $times .= $toTxt . ' : ' . $neverTxt . '<br />';
                        } else {
                            $times .= Lang::txt('COM_EVENTS_CAL_LANG_FROM')
                                . ' : ' . date('Y-m-d H:i:s', strtotime($row->publish_down)) . '<br />';
                        }
                    }

                    $pages = $p->getCount(array('event_id' => $row->id));

                    if ($times) {
                        ?>
                        <?php
                        $stateClass = $row->state ? 'publish' : 'unpublish';
                        $stateAction = $row->state ? 'unpublish' : 'publish';
                        $pubInfoTxt = Lang::txt('COM_EVENTS_EVENT_PUBLISH_INFO');
                        ?>
                        <a class="state <?php echo $stateClass; ?> hasTip"
                            href="javascript:void(0);"
                            onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $stateAction; ?>')"
                            title="<?php echo $pubInfoTxt; ?>::<?php echo $times; ?>"
                        >
                            <span><?php echo $alt; ?></span>
                        </a>
                    <?php } ?>
                </td>
                <td class="priority-4">
                    <?php echo $times; ?>
                </td>
                <td class="priority-5">
                    <?php if ($row->scope == 'group') : ?>
                        <?php
                            $group = \Hubzero\User\Group::getInstance($row->scope_id);
                        if (is_object($group)) {
                            $groupUrl = Route::url('index.php?option=com_events&group_id=' . $group->get('gidNumber'));
                            echo Lang::txt('COM_EVENTS_EVENT_GROUP', $groupUrl, $group->get('description'));
                        } else {
                            echo Lang::txt('COM_EVENTS_EVENT_GROUP_NOT_FOUND', $row->scope_id);
                        }
                        ?>
                    <?php else : ?>
                        <span>
                            <?php echo $this->escape(stripslashes($row->groupname)); ?>
                        </span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php
                    $pagesUrl = Route::url(
                        'index.php?option=' . $this->option . '&controller=pages&event_id=' . $row->id
                    );
                    ?>
                    <a href="<?php echo $pagesUrl; ?>">
                        <?php echo Lang::txt('COM_EVENTS_EVENT_NUMBER_OF_PAGES', $pages); ?>
                    </a>
                </td>
            </tr>
    <?php
    $k = 1 - $k;
}
?>
        </tbody>
    </table>

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="" autocomplete="" />
    <input type="hidden" name="boxchecked" value="0" />

    <?php echo Html::input('token'); ?>
</form>
