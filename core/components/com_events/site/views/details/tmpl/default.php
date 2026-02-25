<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

$addUrl = Route::url(
    'index.php?option=' . $this->option . '&task=add'
);
$yearUrl = Route::url(
    'index.php?option=' . $this->option . '&year=' . $this->year
);
$monthUrl = Route::url(
    'index.php?option=' . $this->option
    . '&year=' . $this->year . '&month=' . $this->month
);
$weekUrl = Route::url(
    'index.php?option=' . $this->option
    . '&year=' . $this->year . '&month=' . $this->month
    . '&day=' . $this->day . '&task=week'
);
$dayUrl = Route::url(
    'index.php?option=' . $this->option
    . '&year=' . $this->year . '&month=' . $this->month
    . '&day=' . $this->day
);
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>

    <?php if ($this->auth) { ?>
    <div id="content-header-extra">
        <ul id="useroptions">
            <li class="last">
                <a class="icon-add add btn" href="<?php echo $addUrl; ?>">
                    <?php echo Lang::txt('EVENTS_ADD_EVENT'); ?>
                </a>
            </li>
        </ul>
    </div><!-- / #content-header-extra -->
    <?php } ?>
</header><!-- / #content-header -->

<nav>
    <ul class="sub-menu">
        <li<?php if ($this->task == 'year') {
            echo ' class="active"';
           } ?>>
            <a href="<?php echo $yearUrl; ?>">
                <span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_YEAR'); ?></span>
            </a>
        </li>
        <li<?php if ($this->task == 'month') {
            echo ' class="active"';
           } ?>>
            <a href="<?php echo $monthUrl; ?>">
                <span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_MONTH'); ?></span>
            </a>
        </li>
        <li<?php if ($this->task == 'week') {
            echo ' class="active"';
           } ?>>
            <a href="<?php echo $weekUrl; ?>">
                <span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_WEEK'); ?></span>
            </a>
        </li>
        <li<?php if ($this->task == 'day') {
            echo ' class="active"';
           } ?>>
            <a href="<?php echo $dayUrl; ?>">
                <span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_DAY'); ?></span>
            </a>
        </li>
    </ul>
</nav>

<section class="main section">
    <div class="section-inner hz-layout-with-aside">
        <div class="subject">
        <?php
        if ($this->row) {
            $detailsUrl = Route::url(
                'index.php?option=' . $this->option
                . '&task=details&id=' . $this->row->id
            );
            $editUrl = Route::url(
                'index.php?option=' . $this->option
                . '&task=edit&id=' . $this->row->id
            );
            $deleteUrl = Route::url(
                'index.php?option=' . $this->option
                . '&task=delete&id=' . $this->row->id
            );
            $editTxt = Lang::txt('JACTION_EDIT');
            $deleteTxt = Lang::txt('JACTION_DELETE');
            $overviewTxt = Lang::txt('EVENTS_OVERVIEW');
            $registerTxt = Lang::txt('EVENTS_REGISTER');

            $html = '<h3>'
                . $this->escape(stripslashes($this->row->title));
            if (
                $this->auth
                && $this->row->created_by == User::get('id')
            ) {
                $html .= '&nbsp;&nbsp;';
                $html .= '<a class="edit" href="' . $editUrl
                    . '" title="' . $editTxt . '">'
                    . strtolower($editTxt) . '</a>' . "\n";
                $html .= '&nbsp;&nbsp;' . "\n";
                $html .= '<a class="delete" href="' . $deleteUrl
                    . '" title="' . $deleteTxt . '">'
                    . strtolower($deleteTxt) . '</a>' . "\n";
            }
            $html .= '</h3>' . "\n";

            $html .= '<div id="sub-sub-menu">' . "\n";
            $html .= '<ul>' . "\n";
            $html .= "\t" . '<li';
            if ($this->page->alias == '') {
                $html .= ' class="active"';
            }
            $html .= '><a class="tab" href="' . $detailsUrl . '">'
                . '<span>' . $overviewTxt . '</span>'
                . '</a></li>' . "\n";
            if ($this->pages) {
                foreach ($this->pages as $p) {
                    $pageUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&task=details&id=' . $this->row->id
                        . '&page=' . $p->alias
                    );
                    $html .= "\t" . '<li';
                    if ($this->page->alias == $p->alias) {
                        $html .= ' class="active"';
                    }
                    $pageTitle = trim(stripslashes($p->title));
                    $html .= '><a class="tab" href="'
                        . $pageUrl . '"><span>'
                        . $pageTitle . '</span></a></li>' . "\n";
                }
            }
            $html .= "\t" . '<li';

            if (
                $this->row->registerby
                && $this->row->registerby != '0000-00-00 00:00:00'
                && strtotime($this->row->registerby) >= time()
            ) {
                if ($this->page->alias == 'register') {
                    $html .= ' class="active"';
                }
                $regTabUrl = Route::url(
                    'index.php?option=' . $this->option
                    . '&task=details&id=' . $this->row->id
                    . '&page=register'
                );
                $html .= '><a class="tab" href="' . $regTabUrl
                    . '"><span>' . $registerTxt
                    . '</span></a></li>' . "\n";
            }
            $html .= '</ul>' . "\n";
            $html .= '<div class="clear"></div>' . "\n";
            $html .= '</div>' . "\n";

            if ($this->page->alias != '') {
                $noInfoTxt = Lang::txt('EVENTS_NO_INFO_AVAILABLE');
                $html .= (trim($this->page->pagetext))
                    ? stripslashes($this->page->pagetext)
                    : '<p class="warning">' . $noInfoTxt . '</p>';
            } else {
                $user = User::getInstance($this->row->created_by);

                if (is_object($user)) {
                    $name = $user->get('name');
                } else {
                    $name = Lang::txt('EVENTS_CAL_LANG_UNKNOWN');
                }
                $category = (isset($this->categories[$this->row->catid]))
                    ? $this->categories[$this->row->catid]
                    : 'N/A';
                $catLabel = Lang::txt('EVENTS_CAL_LANG_EVENT_CATEGORY');
                $descLabel = Lang::txt('EVENTS_CAL_LANG_EVENT_DESCRIPTION');
                $whenLabel = Lang::txt('EVENTS_CAL_LANG_EVENT_WHEN');

                $html .= '<table id="event-info">' . "\n";
                $html .= ' <tbody>' . "\n";
                $html .= '  <tr>' . "\n";
                $html .= '   <th scope="row">'
                    . $catLabel . ':</th>' . "\n";
                $html .= '   <td>'
                    . stripslashes($category) . '</td>' . "\n";
                $html .= '  </tr>' . "\n";
                $html .= '  <tr>' . "\n";
                $html .= '   <th scope="row">'
                    . $descLabel . ':</th>' . "\n";
                $html .= '   <td>'
                    . html_entity_decode($this->row->content)
                    . '</td>' . "\n";
                $html .= '  </tr>' . "\n";
                $html .= '  <tr>' . "\n";
                $html .= '   <th scope="row">'
                    . $whenLabel . ':</th>' . "\n";
                $html .= '   <td>' . "\n";

                $ts = explode(':', $this->row->start_time);
                if (intval($ts[0]) > 12) {
                    $ts[0] = ($ts[0] - 12);
                    $this->row->start_time = implode(':', $ts);
                    $this->row->start_time .= ' <small>PM</small>';
                } else {
                    $noonTxt = Lang::txt('EVENTS_NOON');
                    $this->row->start_time .= (intval($ts[0]) == 12)
                        ? ' <small>' . $noonTxt . '</small>'
                        : ' <small>AM</small>';
                }
                $te = explode(':', $this->row->stop_time);
                if (intval($te[0]) > 12) {
                    $te[0] = ($te[0] - 12);
                    $this->row->stop_time = implode(':', $te);
                    $this->row->stop_time .= ' <small>PM</small>';
                } else {
                    $noonTxt = Lang::txt('EVENTS_NOON');
                    $this->row->stop_time .= (intval($te[0]) == 12)
                        ? ' <small>' . $noonTxt . '</small>'
                        : ' <small>AM</small>';
                }

                // get publish up/down & timezone
                $publish_up   = $this->row->publish_up;
                $publish_down = $this->row->publish_down;

                $upDate = date("Y-m-d", strtotime($publish_up));
                $downDate = date("Y-m-d", strtotime($publish_down));
                if ($upDate == $downDate) {
                    $html .= Date::of($publish_up)
                        ->format('l d F, Y') . ', ';
                    $html .= Date::of($publish_up)
                        ->format('g:i a ')
                        . ' - '
                        . Date::of($publish_down)->format('g:i a ');
                    $html .= Date::of(
                        $publish_down,
                        $this->row->time_zone
                    )->format('T', true);
                } else {
                    $tz = $this->row->time_zone;
                    if (!isset($tz) || $tz == '') {
                        // Get the timezone preferred by the USER,
                        // if not use HUB's
                        $event_timezone = \Hubzero\Facades\Config::get('offset');

                        // Case if spanning across two days that are
                        // on different DST or ST
                        $event_timezone_start = Date::of(
                            $publish_up,
                            $event_timezone
                        )->format('T', true);
                        $event_timezone_end = Date::of(
                            $publish_down,
                            $event_timezone
                        )->format('T', true);
                    } else {
                        $event_timezone = Date::of(
                            $publish_down,
                            $tz
                        )->format('T', true);
                        $event_timezone_start = Date::of(
                            $publish_up,
                            $tz
                        )->format('T', true);
                        $event_timezone_end = Date::of(
                            $publish_down,
                            $tz
                        )->format('T', true);
                    }

                    $html .= Date::of(
                        $publish_up,
                        $this->row->time_zone
                    )->toLocal('l d F, Y g:i a ')
                        . $event_timezone_start . ' - ';
                    $html .= Date::of(
                        $publish_down,
                        $this->row->time_zone
                    )->toLocal('l d F, Y g:i a ')
                        . $event_timezone_end;
                }

                $html .= '   </td>' . "\n";
                $html .= '  </tr>' . "\n";
                if (trim($this->row->contact_info)) {
                    $contactLabel = Lang::txt(
                        'EVENTS_CAL_LANG_EVENT_CONTACT'
                    );
                    $html .= '  <tr>' . "\n";
                    $html .= '   <th scope="row">'
                        . $contactLabel . ':</th>' . "\n";
                    $html .= '   <td>'
                        . $this->row->contact_info . '</td>' . "\n";
                    $html .= '  </tr>' . "\n";
                }
                if (trim($this->row->adresse_info)) {
                    $addrLabel = Lang::txt(
                        'EVENTS_CAL_LANG_EVENT_ADRESSE'
                    );
                    $html .= '  <tr>' . "\n";
                    $html .= '   <th scope="row">'
                        . $addrLabel . ':</th>' . "\n";
                    $html .= '   <td>'
                        . $this->row->adresse_info . '</td>' . "\n";
                    $html .= '  </tr>' . "\n";
                }
                if (trim($this->row->extra_info)) {
                    $extraLabel = Lang::txt(
                        'EVENTS_CAL_LANG_EVENT_EXTRA'
                    );
                    $extraUrl = htmlentities($this->row->extra_info);
                    $html .= '  <tr>' . "\n";
                    $html .= '   <th scope="row">'
                        . $extraLabel . ':</th>' . "\n";
                    $html .= '   <td><a href="' . $extraUrl . '">'
                        . $extraUrl . '</a></td>' . "\n";
                    $html .= '  </tr>' . "\n";
                }
                if ($this->fields) {
                    foreach ($this->fields as $field) {
                        if (end($field) != null) {
                            if (end($field) == '1') {
                                $html .= '  <tr>' . "\n";
                                $html .= '   <th scope="row">'
                                    . $field[1] . ':</th>' . "\n";
                                $html .= '   <td>'
                                    . Lang::txt('YES')
                                    . '</td>' . "\n";
                                $html .= '  </tr>' . "\n";
                            } else {
                                $html .= '  <tr>' . "\n";
                                $html .= '   <th scope="row">'
                                    . $field[1] . ':</th>' . "\n";
                                $html .= '   <td>'
                                    . end($field)
                                    . '</td>' . "\n";
                                $html .= '  </tr>' . "\n";
                            }
                        }
                    }
                }
                if ($this->config->getCfg('byview') == 'YES') {
                    $authorLabel = Lang::txt(
                        'EVENTS_CAL_LANG_EVENT_AUTHOR_ALIAS'
                    );
                    $html .= '  <tr>' . "\n";
                    $html .= '   <th scope="row">'
                        . $authorLabel . ':</th>' . "\n";
                    $html .= '   <td>' . $name . '</td>' . "\n";
                    $html .= '  </tr>' . "\n";
                }
                if ($this->tags) {
                    $tagsLabel = Lang::txt(
                        'EVENTS_CAL_LANG_EVENT_TAGS'
                    );
                    $html .= '  <tr>' . "\n";
                    $html .= '   <th scope="row">'
                        . $tagsLabel . ':</th>' . "\n";
                    $html .= '   <td>'
                        . $this->tags . '</td>' . "\n";
                    $html .= '  </tr>' . "\n";
                }
                $html .= ' </tbody>' . "\n";
                $html .= '</table>' . "\n";
            }
            echo $html;
        } else { ?>
            <p class="warning"><?php echo Lang::txt('EVENTS_CAL_LANG_REP_NOEVENTSELECTED'); ?></p>
        <?php } ?>
        </div><!-- / .subject -->
        <div class="aside">
        <div class="calendarwrap">
            <?php
            $this->view('calendar', 'browse')
                 ->set('option', $this->option)
                 ->set('task', $this->task)
                 ->set('year', $this->year)
                 ->set('month', $this->month)
                 ->set('day', $this->day)
                 ->set('offset', $this->offset)
                 ->set('shownav', 1)
                 ->display();
            ?>
        </div><!-- / .calendarwrap -->
    </div><!-- / .aside -->
    </div>
</section><!-- / .main section -->
