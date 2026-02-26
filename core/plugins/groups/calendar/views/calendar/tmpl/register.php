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

$year  = date("Y", strtotime($this->event->get('publish_up')));
$month = date("m", strtotime($this->event->get('publish_up')));
?>

<?php if ($this->getError()) { ?>
    <p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php
$calendarUrl = Route::url(
    'index.php?option=' . $this->option
    . '&cn=' . $this->group->cn
    . '&active=calendar&year=' . $year
    . '&month=' . $month
);
?>
<ul id="page_options">
    <li>
        <a class="icon-date btn date"
            title=""
            href="<?php echo $calendarUrl; ?>">
            <?php echo Lang::txt('Back to Calendar'); ?>
        </a>
    </li>
</ul>

<?php
$cn = $this->group->get('cn');
$eventId = $this->event->get('id');
$baseUrl = 'index.php?option=' . $this->option
    . '&cn=' . $cn . '&active=calendar';
$deleteUrl = Route::url(
    $baseUrl . '&action=delete&event_id=' . $eventId
);
$editUrl = Route::url(
    $baseUrl . '&action=edit&event_id=' . $eventId
);
$detailsUrl = Route::url(
    $baseUrl . '&action=details&event_id=' . $eventId
);
$registerUrl = Route::url(
    $baseUrl . '&action=register&event_id=' . $eventId
);
$registrantsUrl = Route::url(
    $baseUrl . '&action=registrants&event_id=' . $eventId
);
$isOwnerOrManager = $this->user->get('id') == $this->event->get('created_by')
    || $this->authorized == 'manager';
?>
<div class="event-title-bar">
    <span class="event-title">
        <?php echo $this->event->get('title'); ?>
    </span>
    <?php if ($isOwnerOrManager) : ?>
        <a class="delete"
            href="<?php echo $deleteUrl; ?>">
            Delete
        </a>
        <a class="edit"
            href="<?php echo $editUrl; ?>">
            Edit
        </a>
    <?php endif; ?>
</div>

<div class="event-sub-menu">
    <ul>
        <li>
            <a href="<?php echo $detailsUrl; ?>">
                <span><?php echo Lang::txt('Details'); ?></span>
            </a>
        </li>
        <?php
        $hasRegistration = $this->event->get('registerby')
            && $this->event->get('registerby') != '0000-00-00 00:00:00';
        ?>
        <?php if ($hasRegistration) : ?>
            <li class="active">
                <a href="<?php echo $registerUrl; ?>">
                    <span><?php echo Lang::txt('Register'); ?></span>
                </a>
            </li>
            <?php if ($isOwnerOrManager) : ?>
                <li>
                    <a href="<?php echo $registrantsUrl; ?>">
                        <?php
                        $regLabel = Lang::txt(
                            'Registrants (' . $this->registrants . ')'
                        );
                        ?>
                        <span><?php echo $regLabel; ?></span>
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
    <div class="clear"></div>
</div>

<?php
$firstName = (isset($this->register['first_name']))
    ? $this->register['first_name'] : '';
$lastName = (isset($this->register['last_name']))
    ? $this->register['last_name'] : '';
$affiliation = (isset($this->register['affiliation']))
    ? $this->register['affiliation'] : '';
$telephone = (isset($this->register['telephone']))
    ? $this->register['telephone'] : '';
$positionOther = (isset($this->register['position_other']))
    ? $this->register['position_other'] : '';
?>
<form action="<?php echo $registerUrl; ?>"
    id="hubForm"
    method="post"
    class="full">
    <fieldset>
        <legend><?php echo Lang::txt('Name &amp; Title'); ?></legend>

        <div class="grid">
            <div class="col span6">
                <?php $fnLabel = Lang::txt('First Name:'); ?>
                <label><?php echo $fnLabel; ?> <span class="required">Required</span>
                    <input type="text"
                        name="register[first_name]"
                        value="<?php echo $firstName; ?>" />
                </label>
            </div>
            <div class="col span6 omega">
                <?php $lnLabel = Lang::txt('Last Name:'); ?>
                <label><?php echo $lnLabel; ?> <span class="required">Required</span>
                    <input type="text"
                        name="register[last_name]"
                        value="<?php echo $lastName; ?>"
                        />
                </label>
            </div>
        </div>

        <?php if ($this->params->get('show_affiliation') || $this->params->get('show_title')) : ?>
            <div class="grid">
                <div class="col span6">
                <?php if ($this->params->get('show_affiliation')) : ?>
                    <?php $affLabel = Lang::txt('Affiliation:'); ?>
                    <label><?php echo $affLabel; ?> <span class="required">Required</span>
                        <input type="text"
                            name="register[affiliation]"
                            value="<?php echo $affiliation; ?>" />
                    </label>
                <?php endif; ?>
                </div>
                <div class="col span6 omega">
                <?php if ($this->params->get('show_title')) : ?>
                    <label><?php echo Lang::txt('Title:'); ?> <span class="optional">Optional</span>
                        <input type="text"
                            name="register[title]"
                            value="<?php echo (isset($this->register['title'])) ? $this->register['title'] : ''; ?>"/>
                    </label>
                <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </fieldset>

    <fieldset>
        <legend><?php echo Lang::txt('Contact Information'); ?></legend>
        <?php if ($this->params->get('show_address')) : ?>
            <div class="grid">
                <div class="col span6">
                    <label><?php echo Lang::txt('City:'); ?> <span class="optional">Optional</span>
                        <input type="text"
                            name="register[city]"
                            value="<?php echo (isset($this->register['city'])) ? $this->register['city'] : ''; ?>"/>
                    </label>
                </div>
                <div class="col span6 omega">
                    <label><?php echo Lang::txt('State/Province:'); ?> <span class="optional">Optional</span>
                        <input type="text"
                            name="register[state]"
                            value="<?php echo (isset($this->register['state'])) ? $this->register['state'] : ''; ?>"/>
                    </label>
                </div>
            </div>
            <div class="grid">
                <div class="col span6">
                    <label><?php echo Lang::txt('Zip/Postal code:'); ?> <span class="optional">Optional</span>
                        <input type="text"
                            name="register[zip]"
                            value="<?php echo (isset($this->register['zip'])) ? $this->register['zip'] : ''; ?>"/>
                    </label>
                </div>
                <div class="col span6 omega">
                    <label><?php echo Lang::txt('Country:'); ?> <span class="optional">Optional</span>
                        <input type="text"
                            name="register[country]"
                            value="<?php echo (isset($this->register['country'])) ? $this->register['country'] : ''; ?>"
                            />
                    </label>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($this->params->get('show_telephone') || $this->params->get('show_fax')) : ?>
            <div class="grid">
                <div class="col span6">
                <?php if ($this->params->get('show_telephone')) : ?>
                    <?php $telLabel = Lang::txt('Telephone:'); ?>
                    <label><?php echo $telLabel; ?> <span class="optional">Optional</span>
                        <input type="text"
                            name="register[telephone]"
                            value="<?php echo $telephone; ?>" />
                    </label>
                <?php endif; ?>
                </div>
                <div class="col span6 omega">
                <?php if ($this->params->get('show_fax')) : ?>
                    <label><?php echo Lang::txt('Fax:'); ?> <span class="optional">Optional</span>
                        <input type="text"
                            name="register[fax]"
                            value="<?php echo (isset($this->register['fax'])) ? $this->register['fax'] : ''; ?>"/>
                    </label>
                <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($this->params->get('show_email') || $this->params->get('show_website')) : ?>
            <div class="grid">
                <div class="col span6">
                <?php if ($this->params->get('show_email')) : ?>
                    <label><?php echo Lang::txt('E-mail:'); ?> <span class="required">required</span>
                        <input type="text"
                            name="register[email]"
                            value="<?php echo (isset($this->register['email'])) ? $this->register['email'] : ''; ?>"/>
                    </label>
                <?php endif; ?>
                </div>
                <div class="col span6 omega">
                <?php if ($this->params->get('show_website')) : ?>
                    <label><?php echo Lang::txt('Website:'); ?> <span class="optional">Optional</span>
                        <input type="text"
                            name="register[website]"
                            value="<?php echo (isset($this->register['website'])) ? $this->register['website'] : ''; ?>"
                            />
                    </label>
                <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </fieldset>

    <?php
    $showDemographics = $this->params->get('show_position')
        || $this->params->get('show_degree')
        || $this->params->get('show_gender')
        || $this->params->get('show_race');
    ?>
    <?php if ($showDemographics) : ?>
        <fieldset>
            <legend><?php echo Lang::txt('Demographics'); ?></legend>
            <?php if ($this->params->get('show_position')) : ?>
                <label for="register[position]">
                    <?php
                    $posLabel = Lang::txt(
                        'Which best describes your current position?'
                    );
                    ?>
                    <?php echo $posLabel; ?> <span class="optional">Optional</span>
                    <select name="register[position]">
                        <?php
                        $selectLabel = Lang::txt(
                            '(select from list or enter below)'
                        );
                        ?>
                        <option value=""
                            selected="selected"><?php echo $selectLabel; ?></option>
                        <?php
                        $uniLabel = Lang::txt(
                            'University / College Student or Staff'
                        );
                        $preLabel = Lang::txt(
                            'K-12 (Pre-College) Student or Staff'
                        );
                        ?>
                        <option value="university"><?php echo $uniLabel; ?></option>
                        <option value="precollege"><?php echo $preLabel; ?></option>
                        <option value="nationallab"><?php echo Lang::txt('National Laboratory'); ?></option>
                        <option value="industry"><?php echo Lang::txt('Industry / Private Company'); ?></option>
                        <option value="government"><?php echo Lang::txt('Government Agency'); ?></option>
                        <option value="military"><?php echo Lang::txt('Military'); ?></option>
                        <option value="unemployed"><?php echo Lang::txt('Retired / Unemployed'); ?></option>
                    </select>
                    <input name="register[position_other]"
                        type="text"
                        value="<?php echo $positionOther; ?>" />
                </label>
            <?php endif; ?>

            <?php if ($this->params->get('show_degree')) : ?>
                <fieldset>
                    <?php
                    $degreeLabel = Lang::txt('Highest academic degree earned:');
                    ?>
                    <legend><?php echo $degreeLabel; ?> <span class="optional">Optional</span></legend>
                        <?php
                        $degreeVal = isset($this->register['degree'])
                            ? $this->register['degree'] : '';
                        $bChecked = ($degreeVal == 'Bachelors')
                            ? 'checked="checked"' : '';
                        $mChecked = ($degreeVal == 'Masters')
                            ? 'checked="checked"' : '';
                        $dChecked = ($degreeVal == 'Doctoral')
                            ? 'checked="checked"' : '';
                        $oChecked = ($degreeVal == 'Other')
                            ? 'checked="checked"' : '';
                        ?>
                        <label>
                            <input type="radio"
                                class="option"
                                name="register[degree]"
                                value="Bachelors"
                                <?php echo $bChecked; ?> />
                            <?php echo Lang::txt('Bachelors degree'); ?>
                        </label>
                        <label>
                            <input type="radio"
                                class="option"
                                name="register[degree]"
                                value="Masters"
                                <?php echo $mChecked; ?> />
                            <?php echo Lang::txt('Masters degree'); ?>
                        </label>
                        <label>
                            <input type="radio"
                                class="option"
                                name="register[degree]"
                                value="Doctoral"
                                <?php echo $dChecked; ?> />
                            <?php echo Lang::txt('Doctoral degree'); ?>
                        </label>
                        <label>
                            <input type="radio"
                                class="option"
                                name="register[degree]"
                                value="Other"
                                <?php echo $oChecked; ?> />
                            <?php echo Lang::txt('None of the above'); ?>
                        </label>
                </fieldset>
            <?php endif; ?>

            <?php if ($this->params->get('show_gender')) : ?>
                <?php
                $sexVal = isset($this->register['sex'])
                    ? $this->register['sex'] : '';
                $maleChecked = ($sexVal == 'Male')
                    ? 'checked="checked"' : '';
                $femaleChecked = ($sexVal == 'Female')
                    ? 'checked="checked"' : '';
                $refusedChecked = ($sexVal == 'Refused')
                    ? 'checked="checked"' : '';
                ?>
                <fieldset>
                    <legend><?php echo Lang::txt('Gender:'); ?> <span class="optional">Optional</span></legend>
                    <label>
                        <input type="radio"
                            name="register[sex]"
                            value="Male"
                            class="option"
                            <?php echo $maleChecked; ?> />
                        <?php echo Lang::txt('Male'); ?>
                    </label>
                    <label>
                        <input type="radio"
                            name="register[sex]"
                            value="Female"
                            class="option"
                            <?php echo $femaleChecked; ?> />
                        <?php echo Lang::txt('Female'); ?>
                    </label>
                    <label>
                        <input type="radio"
                            name="register[sex]"
                            value="Refused"
                            class="option"
                            <?php echo $refusedChecked; ?> />
                        <?php echo Lang::txt('Do not wish to reveal'); ?>
                    </label>
                </fieldset>
            <?php endif; ?>

            <?php if ($this->params->get('show_race')) : ?>
                <fieldset>
                    <legend><?php echo Lang::txt('Race:'); ?> <span class="optional">Optional</span></legend>
                    <p class="hint">
                        <?php echo Lang::txt('Select one or more that apply.'); ?>
                    </p>
                    <label>
                        <input type="checkbox"
                            class="option"
                            name="race[nativeamerican]"
                            id="racenativeamerican"
                            value="Native American"/>
                        <?php echo Lang::txt('American Indian or Alaska Native'); ?>
                    </label>
                    <label class="indent"><?php echo Lang::txt('Tribal Affiliation(s):'); ?>
                        <input name="race[nativetribe]" id="racenativetribe" type="text" value="" />
                    </label>
                    <label>
                        <input type="checkbox" class="option" name="race[asian]" id="raceasian" value="Asian" />
                        <?php echo Lang::txt('Asian'); ?>
                    </label>
                    <label>
                        <input type="checkbox"
                            class="option"
                            name="race[black]"
                            id="raceblack"
                            value="African American"/>
                        <?php echo Lang::txt('Black or African American'); ?>
                    </label>
                    <label>
                        <input type="checkbox"
                            class="option"
                            name="race[hawaiian]"
                            id="racehawaiian"
                            value="Hawaiian"/>
                        <?php echo Lang::txt('Native Hawaiian or Other Pacific Islander'); ?>
                    </label>
                    <label>
                        <input type="checkbox" class="option" name="race[white]" id="racewhite" value="White" />
                        <?php echo Lang::txt('White'); ?>
                    </label>
                    <label>
                        <input type="checkbox"
                            class="option"
                            name="race[hispanic]"
                            id="racehispanic"
                            value="Hispanic"/>
                        <?php echo Lang::txt('Hispanic or Latino'); ?>
                    </label>
                    <label>
                        <input type="checkbox" class="option" name="race[refused]" id="racerefused" value="Refused" />
                        <?php echo Lang::txt('Do not wish to reveal'); ?>
                    </label>
                </fieldset>
            <?php endif; ?>
        </fieldset>
    <?php endif; ?>

    <?php if ($this->params->get('show_arrival') || $this->params->get('show_departure')) : ?>
        <fieldset>
            <legend><?php echo Lang::txt('Arrival/Departure'); ?></legend>

            <?php if ($this->params->get('show_arrival')) : ?>
                <fieldset>
                    <?php
                    $arrivalLabel = Lang::txt('Arrival Information:');
                    ?>
                    <legend><?php echo $arrivalLabel; ?> <span class="optional">Optional</span></legend>
                    <label><?php echo Lang::txt('Arrival Day'); ?>
                        <input type="text"
                            name="arrival[day]"
                            value="<?php echo (isset($this->arrival['day'])) ? $this->arrival['day'] : ''; ?>"/>
                    </label>
                    <label><?php echo Lang::txt('Arrival Time'); ?>
                        <input type="text"
                            name="arrival[time]"
                            value="<?php echo (isset($this->arrival['time'])) ? $this->arrival['time'] : ''; ?>"/>
                    </label>
                </fieldset>
            <?php endif ?>

            <?php if ($this->params->get('show_departure')) : ?>
            <fieldset>
                <?php
                $departureLabel = Lang::txt('Departure Information:');
                ?>
                <legend><?php echo $departureLabel; ?> <span class="optional">Optional</span></legend>
                <label><?php echo Lang::txt('Departure Day'); ?>
                    <input type="text"
                        name="departure[day]"
                        value="<?php echo (isset($this->departure['day'])) ? $this->departure['day'] : ''; ?>"/>
                </label>
                <label><?php echo Lang::txt('Departure Time'); ?>
                    <input type="text"
                        name="departure[time]"
                        value="<?php echo (isset($this->departure['time'])) ? $this->departure['time'] : ''; ?>"/>
                </label>
            </fieldset>
            <?php endif; ?>
        </fieldset>
    <?php endif; ?>

    <?php if ($this->params->get('show_disability') || $this->params->get('show_dietary')) : ?>
        <fieldset>
            <legend><?php echo Lang::txt('Disability/Dietary needs'); ?></legend>
            <?php if ($this->params->get('show_disability')) : ?>
                <?php
                $disabilityMsg = Lang::txt(
                    'I have auxiliary aids or services due to a disability. Please contact me.'
                );
                ?>
                <label>
                    <input type="checkbox"
                        class="option"
                        name="disability"
                        value="yes" <?php if (isset($this->disability) && $this->disability == 'yes') {
                            echo 'checked="checked"';
                                    } ?> />
                    <?php echo $disabilityMsg; ?>
                </label>
            <?php endif; ?>

            <?php if ($this->params->get('show_dietary')) : ?>
                <label>
                    <input type="checkbox"
                        class="option"
                        name="dietary[needs]"
                        value="yes" <?php if (isset($this->dietary['needs']) && $this->dietary['needs'] == 'yes') {
                            echo 'checked="checked"';
                                    } ?> />
                    <?php echo Lang::txt('I have specific dietary needs.'); ?>
                </label>
                <label class="indent"><?php echo Lang::txt('Please specify'); ?>
                    <input type="text" name="dietary[specific]" value="<?php echo $this->dietary['specific']; ?>" />
                </label>
            <?php endif; ?>
        </fieldset>
    <?php endif; ?>

    <?php if ($this->params->get('show_dinner')) : ?>
        <fieldset>
            <legend><?php echo Lang::txt('Dinner'); ?></legend>
            <label for="filed-dinner">
                <input type="checkbox"
                    class="option"
                    name="dinner"
                    id="filed-dinner"
                    value="yes" <?php if (isset($this->dinner) && $this->dinner == 'yes') {
                        echo 'checked="checked"';
                                } ?> />
                <?php echo Lang::txt('I plan to attend the dinner.'); ?>
            </label>
        </fieldset>
    <?php endif; ?>

    <?php if ($this->params->get('show_abstract')) : ?>
        <fieldset>
            <legend><?php echo Lang::txt('Abstract'); ?></legend>
            <label>
                <?php
                if ($this->params->get('abstract_text')) {
                    echo stripslashes($this->params->get('abstract_text'));
                }
                ?>
                <textarea name="register[abstract]"
                    rows="16"
                    cols="32"
                    ><?php echo (isset($this->register['abstract'])) ? $this->register['abstract'] : ''; ?></textarea>
            </label>
        </fieldset>
    <?php endif; ?>

    <?php if ($this->params->get('show_comments')) : ?>
        <fieldset>
            <legend><?php echo Lang::txt('Comments'); ?></legend>
            <label>
                <?php echo Lang::txt('Please use the space below to provide any additional comments:'); ?>
                <textarea name="register[comment]"
                    rows="4"
                    cols="32"
                    ><?php echo (isset($this->register['comment'])) ? $this->register['comment'] : ''; ?></textarea>
            </label>
        </fieldset>
    <?php endif; ?>

    <input type="hidden" name="option" value="com_groups" />
    <input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
    <input type="hidden" name="active" value="calendar" />
    <input type="hidden" name="action" value="doregister" />
    <input type="hidden" name="event_id" value="<?php echo $this->event->get('id'); ?>" />

    <p class="submit">
        <input type="submit" name="event_submit" value="Submit" />
    </p>
</form>
