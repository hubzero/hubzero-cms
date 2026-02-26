<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

?>
<div id="event" class="modal">
<?php if ($this->row) {
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
    $detailsUrl = Route::url(
        'index.php?option=' . $this->option
        . '&task=details&id=' . $this->row->id
        . '&no_html=1'
    );
    ?>
    <h2 class="entry-title">
        <?php echo $this->escape(stripslashes($this->row->title)); ?>
        <?php if ($this->authorized || $this->row->created_by == User::get('id')) { ?>
            <a class="edit"
               href="<?php echo $editUrl; ?>"
               title="<?php echo $editTxt; ?>">
                <?php echo strtolower($editTxt); ?>
            </a>
            <a class="delete"
               href="<?php echo $deleteUrl; ?>"
               title="<?php echo $deleteTxt; ?>">
                <?php echo strtolower($deleteTxt); ?>
            </a>
        <?php } ?>
    </h2>

    <?php
    $hasRegisterby = $this->row->registerby
        && $this->row->registerby != '0000-00-00 00:00:00';
    if ($this->pages || $hasRegisterby) {
        ?>
        <div id="sub-sub-menu">
            <ul>
                <li<?php if ($this->page->alias == '') {
                    echo ' class="active"';
                   } ?>>
                    <a class="tab" href="<?php echo $detailsUrl; ?>">
                        <span><?php echo Lang::txt('EVENTS_OVERVIEW'); ?></span>
                    </a>
                </li>
            <?php
            if ($this->pages) {
                foreach ($this->pages as $p) {
                    $pageUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&task=details&id=' . $this->row->id
                        . '&no_html=1&page=' . $p->alias
                    );
                    ?>
                <li<?php if ($this->page->alias == $p->alias) {
                    echo ' class="active"';
                   } ?>>
                    <a class="tab" href="<?php echo $pageUrl; ?>">
                        <span><?php echo trim(stripslashes($p->title)); ?></span>
                    </a>
                </li>
                    <?php
                }
            }
            ?>
            <?php if ($hasRegisterby) {
                $regUrl = Route::url(
                    'index.php?option=' . $this->option
                    . '&task=details&id=' . $this->row->id
                    . '&no_html=1&page=register'
                );
                ?>
                <li<?php if ($this->page->alias == 'register') {
                    echo ' class="active"';
                   } ?>>
                    <a class="tab" href="<?php echo $regUrl; ?>">
                        <span><?php echo Lang::txt('EVENTS_REGISTER'); ?></span>
                    </a>
                </li>
            <?php } ?>
            </ul>
            <div class="clear"></div>
        </div>
    <?php } ?>

    <div class="entry-details">
    <?php if ($this->page->alias != '') {
        $noInfoTxt = Lang::txt('EVENTS_NO_INFO_AVAILABLE');
        echo (trim($this->page->pagetext))
            ? stripslashes($this->page->pagetext)
            : '<p class="warning">' . $noInfoTxt . '</p>';
    } else { ?>
        <div class="col span6">
            <div class="container">
                <h3><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_DESCRIPTION'); ?></h3>
                <p class="entry-description">
                    <?php echo stripslashes($this->row->content); ?>
                </p>
                <?php
                if ($this->fields) {
                    foreach ($this->fields as $field) {
                        if (end($field) != null) {
                            if (end($field) == '1') {
                                ?>
                    <h3><?php echo $this->escape(stripslashes($field[1])); ?></h3>
                    p><?php echo Lang::txt('YES'); ?></p>
                            <?php } else { ?>
                    <h3><?php echo $this->escape(stripslashes($field[1])); ?></h3>
                    <p><?php echo end($field); ?></p>
                                <?php
                            }
                        }
                    }
                }
                ?>
            </div>
            <?php if ($this->tags) { ?>
                <div class="container">
                    <h3><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_TAGS'); ?></h3>
                    <?php echo $this->tags; ?>
                </div>
            <?php } ?>
        </div>

        <div class="col span6 omega">
            <div class="container">
                <h3><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_CATEGORY'); ?></h3>
                <p class="entry-category">
                    <?php echo $this->escape(stripslashes($this->categories[$this->row->catid])); ?>
                </p>
            </div>

            <div class="container">
                <h3><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_WHEN'); ?></h3>
                <p class="entry-datetime">
                <?php
                $ts = explode(':', $this->row->start_time);
                //$ts[0] = intval($ts[0]);
                if (intval($ts[0]) > 12) {
                    $ts[0] = ($ts[0] - 12);
                    $ts[0] = (substr($ts[0], 0, 1) == '0')
                        ? substr($ts[0], 1) : $ts[0];
                    $this->row->start_time = implode(':', $ts);
                    $this->row->start_time .= ' <abbr title="Post Meridian">am</abbr>';
                } else {
                    $st = $this->row->start_time;
                    $this->row->start_time = (substr($st, 0, 1) == '0')
                        ? substr($st, 1) : $st;
                    $noonTxt = Lang::txt('EVENTS_NOON');
                    if (intval($ts[0]) == 12) {
                        $this->row->start_time .= ' <small>'
                            . $noonTxt . '</small>';
                    } else {
                        $this->row->start_time .= ' <abbr title="Ante Meridian">am</abbr>';
                    }
                }
                $te = explode(':', $this->row->stop_time);
                //$te[0] = intval($te[0]);
                if (intval($te[0]) > 12) {
                    $te[0] = ($te[0] - 12);
                    $te[0] = (substr($te[0], 0, 1) == '0')
                        ? substr($te[0], 1) : $te[0];
                    $this->row->stop_time = implode(':', $te);
                    $this->row->stop_time .= ' <abbr title="Post Meridian">pm</abbr>';
                } else {
                    $et = $this->row->stop_time;
                    $this->row->stop_time = (substr($et, 0, 1) == '0')
                        ? substr($et, 1) : $et;
                    $noonTxt = Lang::txt('EVENTS_NOON');
                    if (intval($te[0]) == 12) {
                        $this->row->stop_time .= ' <small>'
                            . $noonTxt . '</small>';
                    } else {
                        $this->row->stop_time .= ' <abbr title="Ante Meridian">pm</abbr>';
                    }
                }
                if ($this->row->start_date == $this->row->stop_date) {
                    echo $this->row->start_date . ',<br />'
                        . $this->row->start_time
                        . '&nbsp;-&nbsp;'
                        . $this->row->stop_time . '<br />';
                } else {
                    $fromTxt = Lang::txt('EVENTS_CAL_LANG_FROM');
                    $toTxt = Lang::txt('EVENTS_CAL_LANG_TO');
                    echo $fromTxt . ' '
                        . $this->row->start_date
                        . '&nbsp;-&nbsp;'
                        . $this->row->start_time . '<br />'
                        . $toTxt . ' '
                        . $this->row->stop_date
                        . '&nbsp;-&nbsp;'
                        . $this->row->stop_time . '<br />';
                }
                ?>
                </p>
            </div>

            <?php if (trim($this->row->adresse_info)) { ?>
                <div class="container">
                    <h3><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_ADRESSE'); ?></h3>
                    <p class="entry-location">
                        <?php echo $this->escape(stripslashes($this->row->adresse_info)); ?>
                    </p>
                </div>
            <?php } ?>

            <?php if (trim($this->row->extra_info)) { ?>
                <div class="container">
                    <h3><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_EXTRA'); ?></h3>
                    <?php
                    $extraUrl = stripslashes($this->row->extra_info);
                    $extraEsc = $this->escape($extraUrl);
                    ?>
                    <p class="entry-link">
                        <a href="<?php echo $extraUrl; ?>"><?php echo $extraEsc; ?></a>
                    </p>
                </div>
            <?php } ?>

            <?php if (trim($this->row->contact_info)) { ?>
                <div class="container">
                    <h3><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_CONTACT'); ?></h3>
                    <p class="entry-contact">
                        <?php echo $this->escape(stripslashes($this->row->contact_info)); ?>
                    </p>
                </div>
            <?php } ?>

            <?php if ($this->config->getCfg('byview') == 'YES') {
                $user = User::getInstance($this->row->created_by);

                if (is_object($user)) {
                    $name = $user->get('name');
                } else {
                    $name = Lang::txt('EVENTS_CAL_LANG_UNKNOWN');
                }
                ?>
                <div class="container">
                    <h3><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_AUTHOR_ALIAS'); ?></h3>
                    <p class="entry-author">
                        <?php echo $this->escape(stripslashes($name)); ?>
                    </p>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    </div><!-- / .entry-details -->
<?php } else { ?>
    <p class="warning"><?php echo Lang::txt('EVENTS_CAL_LANG_REP_NOEVENTSELECTED'); ?></p>
<?php } ?>
</div><!-- / .modal -->
