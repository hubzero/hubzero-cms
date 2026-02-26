<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$params = new \Hubzero\Config\Registry($this->event->params);

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

$detailsUrl = Route::url(
    'index.php?option=' . $this->option
    . '&task=details&id=' . $this->event->id
);
$registerUrl = Route::url(
    'index.php?option=' . $this->option
    . '&task=details&id=' . $this->event->id
    . '&page=register'
);
$overviewTxt = Lang::txt('EVENTS_OVERVIEW');
$registerTxt = Lang::txt('EVENTS_REGISTER');
$reqTxt = Lang::txt('COM_EVENTS_REQUIRED');

// Helper to get register field values
$register = $this->register;
$regVal = function ($key) use ($register) {
    return (isset($register[$key]))
        ? $register[$key] : '';
};
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>

    <?php if ($this->authorized) { ?>
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
    <h3><?php echo stripslashes($this->event->title); ?></h3>
    <?php
        $html  = '<div id="sub-sub-menu">' . "\n";
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
                . '&task=details&id=' . $this->event->id
                . '&page=' . $p->alias
            );
            $html .= "\t" . '<li';
            if ($this->page->alias == $p->alias) {
                $html .= ' class="active"';
            }
            $pageTitle = trim(stripslashes($p->title));
            $html .= '><a class="tab" href="' . $pageUrl . '">'
                . '<span>' . $pageTitle . '</span>'
                . '</a></li>' . "\n";
        }
    }
        $html .= "\t" . '<li';
    if ($this->page->alias == 'register') {
        $html .= ' class="active"';
    }
        $html .= '><a class="tab" href="' . $registerUrl . '">'
            . '<span>' . $registerTxt . '</span>'
            . '</a></li>' . "\n";
        $html .= '</ul>' . "\n";
        $html .= '<div class="clear"></div>' . "\n";
        $html .= '</div>' . "\n";
        echo $html;
    ?>

    <?php if ($this->getError()) { ?>
        <p class="error"><?php echo $this->getError(); ?></p>
    <?php } ?>

    <form method="post" action="index.php" id="hubForm">
        <div class="explaination">
            <p><strong><?php echo Lang::txt('COM_EVENTS_REGISTER_EXPLAINATION'); ?></strong></p>
            <?php
            if (trim($this->event->contact_info)) {
                echo stripslashes($this->event->contact_info);
            } else {
                $noExplTxt = Lang::txt(
                    'COM_EVENTS_REGISTER_EXPLAINATION_NO_EXPLAINATION'
                );
                echo '<p>' . $noExplTxt . '</p>' . "\n";
            }
            ?>
        </div>
        <fieldset>
            <legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_NAME'); ?></legend>
            <div class="grid">
                <div class="col span6">
                    <?php $fnLabel = Lang::txt('COM_EVENTS_REGISTER_FIELD_FIRST_NAME'); ?>
                    <label>
                        <?php echo $fnLabel; ?>
                        <span class="required"><?php echo $reqTxt; ?></span>
                        <input type="text"
                               name="register[firstname]"
                               value="<?php echo $regVal('firstname'); ?>" />
                    </label>
                </div>
                <div class="col span6 omega">
                    <?php $lnLabel = Lang::txt('COM_EVENTS_REGISTER_FIELD_LAST_NAME'); ?>
                    <label>
                        <?php echo $lnLabel; ?>
                        <span class="required"><?php echo $reqTxt; ?></span>
                        <input type="text"
                               name="register[lastname]"
                               value="<?php echo $regVal('lastname'); ?>" />
                    </label>
                </div>
            </div>
            <div class="grid">
                <div class="col span6">
                <?php if ($params->get('show_affiliation')) { ?>
                    <?php $affLabel = Lang::txt('COM_EVENTS_REGISTER_FIELD_AFFILIATION'); ?>
                    <label>
                        <?php echo $affLabel; ?>
                        <span class="required"><?php echo $reqTxt; ?></span>
                        <input type="text"
                               name="register[affiliation]"
                               value="<?php echo $regVal('affiliation'); ?>" />
                    </label>
                <?php } ?>
                </div>
                <div class="col span6 omega">
                <?php if ($params->get('show_title')) { ?>
                    <label>
                        <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_TITLE'); ?>
                        <input type="text"
                               name="register[title]"
                               value="<?php echo $regVal('title'); ?>" />
                    </label>
                <?php } ?>
                </div>
            </div>

            <input type="hidden" name="id" value="<?php echo $this->event->id; ?>" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="task" value="process" />
        </fieldset>
    <?php
    $cityLabel    = Lang::txt('COM_EVENTS_REGISTER_FIELD_CITY');
    $stateLabel   = Lang::txt('COM_EVENTS_REGISTER_FIELD_STATE');
    $zipLabel     = Lang::txt('COM_EVENTS_REGISTER_FIELD_ZIP');
    $countryLabel = Lang::txt('COM_EVENTS_REGISTER_FIELD_COUNTRY');
    $phoneLabel   = Lang::txt('COM_EVENTS_REGISTER_FIELD_PHONE');
    $faxLabel     = Lang::txt('COM_EVENTS_REGISTER_FIELD_FAX');
    $emailLabel   = Lang::txt('COM_EVENTS_REGISTER_FIELD_EMAIL');
    $websiteLabel = Lang::txt('COM_EVENTS_REGISTER_FIELD_WEBSITE');
    ?>
    <?php if (
    $params->get('show_address')
            || $params->get('show_telephone')
            || $params->get('show_fax')
            || $params->get('show_email')
            || $params->get('show_website')
) { ?>
        <fieldset>
            <legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_INFO'); ?></legend>
                                                          <?php if ($params->get('show_address')) { ?>
            <div class="grid">
                <div class="col span6">
                    <label>
                                                                <?php echo $cityLabel; ?>
                        <input type="text"
                               name="register[city]"
                               value="<?php echo $regVal('city'); ?>" />
                    </label>
                </div>
                <div class="col span6 omega">
                    <label>
                                                                <?php echo $stateLabel; ?>
                        <input type="text"
                               name="register[state]"
                               value="<?php echo $regVal('state'); ?>" />
                    </label>
                </div>
            </div>
            <div class="grid">
                <div class="col span6">
                    <label>
                                                                <?php echo $zipLabel; ?>
                        <input type="text"
                               name="register[postalcode]"
                               value="<?php echo $regVal('postalcode'); ?>" />
                    </label>
                </div>
                <div class="col span6 omega">
                    <label>
                                                                <?php echo $countryLabel; ?>
                        <input type="text"
                               name="register[country]"
                               value="<?php echo $regVal('country'); ?>" />
                    </label>
                </div>
            </div>
                                                          <?php } ?>
            <div class="grid">
                <div class="col span6">
                                                          <?php if ($params->get('show_telephone')) { ?>
                    <label>
                                                                <?php echo $phoneLabel; ?>
                        <input type="text"
                               name="register[telephone]"
                               value="<?php echo $regVal('telephone'); ?>" />
                    </label>
                                                          <?php } ?>
                </div>
                <div class="col span6 omega">
                                                          <?php if ($params->get('show_fax')) { ?>
                    <label>
                                                                <?php echo $faxLabel; ?>
                        <input type="text"
                               name="register[fax]"
                               value="<?php echo $regVal('fax'); ?>" />
                    </label>
                                                          <?php } ?>
                </div>
            </div>
            <div class="grid">
                <div class="col span6">
                                                          <?php if ($params->get('show_email')) { ?>
                    <label>
                                                                <?php echo $emailLabel; ?>
                        <span class="required"><?php echo $reqTxt; ?></span>
                        <input type="text"
                               name="register[email]"
                               value="<?php echo $regVal('email'); ?>" />
                    </label>
                                                          <?php } ?>
                </div>
                <div class="col span6 omega">
                                                          <?php if ($params->get('show_website')) { ?>
                    <label>
                                                                <?php echo $websiteLabel; ?>
                        <input type="text"
                               name="register[website]"
                               value="<?php echo $regVal('website'); ?>" />
                    </label>
                                                          <?php } ?>
                </div>
            </div>
        </fieldset>
    <?php } ?>
    <?php if (
    $params->get('show_position')
            || $params->get('show_degree')
            || $params->get('show_gender')
            || $params->get('show_race')
) { ?>
        <fieldset>
            <legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_DEMOGRAPHICS'); ?></legend>

            <?php if ($params->get('show_position')) { ?>
            <label>
                <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_POSITION'); ?>
                <select name="register[position]">
                    <?php
                    $posOpts = array(
                        ''            => 'COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_NULL',
                        'university'  => 'COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_UNIVERSITY',
                        'precollege'  => 'COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_PRECOLLEGE',
                        'nationallab' => 'COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_NATIONALLAB',
                        'industry'    => 'COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_INDUSTRY',
                        'government'  => 'COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_GOVERNMENT',
                        'military'    => 'COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_MILITARY',
                        'unemployed'  => 'COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_UNEMPLOYED',
                    );
                    foreach ($posOpts as $val => $langKey) {
                        $sel = ($val === '') ? ' selected="selected"' : '';
                        ?>
                    <option value="<?php echo $val; ?>"<?php echo $sel; ?>>
                        <?php echo Lang::txt($langKey); ?>
                    </option>
                        <?php
                    }
                    ?>
                </select>
                <input name="register[position_other]"
                       type="text"
                       value="<?php echo $regVal('position_other'); ?>" />
            </label>
            <?php } ?>

            <?php if ($params->get('show_degree')) { ?>
            <fieldset>
                <legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_DEGREE'); ?>:</legend>
                <?php
                $degreeOpts = array(
                    'bachelors'        => 'COM_EVENTS_REGISTER_FIELD_DEGREE_OPTION_BACHELORS',
                    'masters'          => 'COM_EVENTS_REGISTER_FIELD_DEGREE_OPTION_MASTERS',
                    'doctoral'         => 'COM_EVENTS_REGISTER_FIELD_DEGREE_OPTION_DOCTORAL',
                    'none of the above' => 'COM_EVENTS_REGISTER_FIELD_DEGREE_OPTION_NULL',
                );
                foreach ($degreeOpts as $val => $langKey) {
                    $chk = (isset($this->register['degree'])
                        && $this->register['degree'] == $val)
                        ? 'checked="checked"' : '';
                    ?>
                <label>
                    <input type="radio"
                           class="option"
                           name="register[degree]"
                           value="<?php echo $val; ?>"
                           <?php echo $chk; ?> />
                    <?php echo Lang::txt($langKey); ?>
                </label>
                    <?php
                }
                ?>
            </fieldset>
            <?php } ?>

            <?php if ($params->get('show_gender')) { ?>
            <fieldset>
                <legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_GENDER'); ?>:</legend>
                <?php
                $genderOpts = array(
                    'male'    => 'COM_EVENTS_REGISTER_FIELD_GENDER_OPTION_MALE',
                    'female'  => 'COM_EVENTS_REGISTER_FIELD_GENDER_OPTION_FEMALE',
                    'refused' => 'COM_EVENTS_REGISTER_FIELD_GENDER_OPTION_NULL',
                );
                foreach ($genderOpts as $val => $langKey) {
                    $chk = (isset($this->register['sex'])
                        && $this->register['sex'] == $val)
                        ? 'checked="checked"' : '';
                    ?>
                <label>
                    <input type="radio"
                           name="register[sex]"
                           value="<?php echo $val; ?>"
                           class="option"
                           <?php echo $chk; ?> />
                    <?php echo Lang::txt($langKey); ?>
                </label>
                    <?php
                }
                ?>
            </fieldset>
            <?php } ?>

            <?php if ($params->get('show_race')) { ?>
            <fieldset>
                <legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_RACE'); ?>:</legend>
                <p class="hint"><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_RACE_HINT'); ?></p>
                <?php
                $raceAmTxt = Lang::txt('COM_EVENTS_REGISTER_FIELD_RACE_OPTION_AMERICAN');
                ?>
                <label>
                    <input type="checkbox"
                           class="option"
                           name="race[nativeamerican]"
                           id="racenativeamerican"
                           value="nativeamerican" />
                    <?php echo $raceAmTxt; ?>
                </label>
                <?php $affTxt = Lang::txt('COM_EVENTS_REGISTER_FIELD_RACE_AFFILIATIONS'); ?>
                <label class="indent">
                    <?php echo $affTxt; ?>:
                    <input name="race[nativetribe]"
                           id="racenativetribe"
                           type="text"
                           value="" />
                </label>
                <?php
                $raceOpts = array(
                    'asian'    => array('raceasian',    'COM_EVENTS_REGISTER_FIELD_RACE_OPTION_ASIAN'),
                    'black'    => array('raceblack',    'COM_EVENTS_REGISTER_FIELD_RACE_OPTION_BLACK'),
                    'hawaiian' => array('racehawaiian', 'COM_EVENTS_REGISTER_FIELD_RACE_OPTION_HAWAIIAN'),
                    'white'    => array('racewhite',    'COM_EVENTS_REGISTER_FIELD_RACE_OPTION_WHITE'),
                    'hispanic' => array('racehispanic', 'COM_EVENTS_REGISTER_FIELD_RACE_OPTION_HISPANIC'),
                    'refused'  => array('racerefused',  'COM_EVENTS_REGISTER_FIELD_RACE_OPTION_NULL'),
                );
                foreach ($raceOpts as $name => $info) {
                    ?>
                <label>
                    <input type="checkbox"
                           class="option"
                           name="race[<?php echo $name; ?>]"
                           id="<?php echo $info[0]; ?>" />
                    <?php echo Lang::txt($info[1]); ?>
                </label>
                    <?php
                }
                ?>
            </fieldset>
            <?php } ?>
        </fieldset>
    <?php } ?>
    <?php if ($params->get('show_arrival') || $params->get('show_departure')) { ?>
        <fieldset>
            <legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_ARRIVAL_OR_DEPARTURE'); ?></legend>

            <?php if ($params->get('show_arrival')) { ?>
            <fieldset>
                <legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_ARRIVAL'); ?></legend>

                <label>
                    <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_ARRIVAL_DAY'); ?>
                    <?php $arrDay = (isset($this->arrival['day'])) ? $this->arrival['day'] : ''; ?>
                    <input type="text"
                           name="arrival[day]"
                           value="<?php echo $arrDay; ?>" />
                </label>

                <label>
                    <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_ARRIVAL_TIME'); ?>
                    <?php $arrTime = (isset($this->arrival['time'])) ? $this->arrival['time'] : ''; ?>
                    <input type="text"
                           name="arrival[time]"
                           value="<?php echo $arrTime; ?>" />
                </label>
            </fieldset>
            <?php } ?>

            <?php if ($params->get('show_departure')) { ?>
            <fieldset>
                <legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_DEPARTURE'); ?></legend>

                <label>
                    <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_DEPARTURE_DAY'); ?>
                    <?php $depDay = (isset($this->departure['day'])) ? $this->departure['day'] : ''; ?>
                    <input type="text"
                           name="departure[day]"
                           value="<?php echo $depDay; ?>" />
                </label>

                <label>
                    <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_DEPARTURE_TIME'); ?>
                    <?php $depTime = (isset($this->departure['time'])) ? $this->departure['time'] : ''; ?>
                    <input type="text"
                           name="departure[time]"
                           value="<?php echo $depTime; ?>" />
                </label>
            </fieldset>
            <?php } ?>
        </fieldset>
    <?php } ?>
    <?php if ($params->get('show_disability') || $params->get('show_dietary')) { ?>
        <fieldset>
            <legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_DISABILITY'); ?></legend>
            <?php if ($params->get('show_disability')) {
                $disTxt = Lang::txt('COM_EVENTS_REGISTER_FIELD_DISABILTIY');
                ?>
            <label>
                <input type="checkbox"
                       class="option"
                       name="disability"
                       value="yes" />
                <?php echo $disTxt; ?>
            </label>
            <?php } ?>

            <?php if ($params->get('show_dietary')) {
                $dietTxt = Lang::txt('COM_EVENTS_REGISTER_FIELD_DIETARY');
                ?>
            <label>
                <input type="checkbox"
                       class="option"
                       name="dietary[needs]"
                       value="yes" />
                <?php echo $dietTxt; ?>
            </label>
            <label class="indent">
                <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_DIETARY_DETAILS'); ?>
                <input type="text" name="dietary[specific]" />
            </label>
            <?php } ?>
        </fieldset>
    <?php } ?>
    <?php if ($params->get('show_dinner')) {
        $dinnerTxt = Lang::txt('COM_EVENTS_REGISTER_FIELD_DINNER');
        ?>
        <fieldset>
            <legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_DINNER'); ?></legend>

            <label for="filed-dinner">
                <input type="checkbox"
                       class="option"
                       name="dinner"
                       id="filed-dinner"
                       value="yes" />
                <?php echo $dinnerTxt; ?>
            </label>
        </fieldset>
    <?php } ?>

        <?php if ($params->get('show_abstract')) { ?>
        <fieldset>
            <legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_ABSTRACT'); ?></legend>
            <label>
                <?php
                if ($params->get('abstract_text')) {
                    echo stripslashes($params->get('abstract_text'));
                }
                ?>
                <textarea name="register[additional]" rows="16" cols="32"></textarea>
            </label>
        </fieldset>
        <?php } ?>

        <?php if ($params->get('show_comments')) { ?>
        <fieldset>
            <legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_COMMENTS'); ?></legend>
            <label>
                <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_COMMENTS'); ?>:
                <textarea name="register[comments]" rows="4" cols="32"></textarea>
            </label>
        </fieldset>
        <?php } ?>
        <div class="clear"></div>
        <p class="submit">
            <input type="submit" value="<?php echo Lang::txt('EVENTS_SUBMIT'); ?>" />
        </p>
    </form>
</section><!-- / .main section -->
