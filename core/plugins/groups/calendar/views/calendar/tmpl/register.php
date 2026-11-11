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

<ul id="page_options">
    <li>
        <?php
        $linkUrl = Route::url(
            'index.php?option=' . $this->option . '&cn=' . $this->group->cn . '&active=calendar&year=' . $year
                . '&month=' . $month,
        );
        ?>
        <a class="icon-date btn date" title="" href="<?= $linkUrl ?>">
            <?php echo Lang::txt('Back to Calendar'); ?>
        </a>
    </li>
</ul>

<div class="event-title-bar">
    <span class="event-title">
        <?php echo $this->escape($this->event->get('title')); ?>
    </span>
    <?php if ($this->user->get('id') == $this->event->get('created_by') || $this->authorized == 'manager') : ?>
        <?php
        $deleteUrl = Route::url(
            'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
                . '&active=calendar&action=delete&event_id=' . $this->event->get('id'),
        );
        ?>
        <button type="submit" form="hz-post-action" formaction="<?= $deleteUrl ?>" class="delete">Delete</button>
        <?php
        $editUrl = Route::url(
            'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
                . '&active=calendar&action=edit&event_id=' . $this->event->get('id'),
        );
        ?>
        <a class="edit" href="<?= $editUrl ?>">
            Edit
        </a>
    <?php endif; ?>
</div>

<div class="event-sub-menu">
    <ul>
        <li>
            <?php
            $detailsUrl = Route::url(
                'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
                    . '&active=calendar&action=details&event_id=' . $this->event->get('id'),
            );
            ?>
            <a href="<?= $detailsUrl ?>">
                <span><?php echo Lang::txt('Details'); ?></span>
            </a>
        </li>
        <?php if ($this->event->get('registerby') && $this->event->get('registerby') != '0000-00-00 00:00:00') : ?>
            <li class="active">
                <?php
                $registerUrl = Route::url(
                    'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
                        . '&active=calendar&action=register&event_id=' . $this->event->get('id'),
                );
                ?>
                <a href="<?= $registerUrl ?>">
                    <span><?php echo Lang::txt('Register'); ?></span>
                </a>
            </li>
            <?php if ($this->user->get('id') == $this->event->get('created_by') || $this->authorized == 'manager') : ?>
                <li>
                    <?php
                    $registrantsUrl = Route::url(
                        'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
                            . '&active=calendar&action=registrants&event_id=' . $this->event->get('id'),
                    );
                    ?>
                    <a href="<?= $registrantsUrl ?>">
                        <span><?php echo Lang::txt('Registrants (' . $this->registrants . ')'); ?></span>
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
    <div class="clear"></div>
</div>

<?php
$registerUrl = Route::url(
    'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
        . '&active=calendar&action=register&event_id=' . $this->event->get('id'),
);
?>
<form action="<?= $registerUrl ?>" id="hubForm" method="post" class="full">
    <fieldset>
        <legend><?php echo Lang::txt('Name &amp; Title'); ?></legend>

        <div class="grid">
            <div class="col span6">
                <label><?php echo Lang::txt('First Name:'); ?> <span class="required">Required</span>
                    <input type="text" name="register[first_name]" value="<?= (isset($this->register['first_name'])) ? $this->escape($this->register['first_name']) : '' ?>" />
                </label>
            </div>
            <div class="col span6 omega">
                <label><?php echo Lang::txt('Last Name:'); ?> <span class="required">Required</span>
                    <input type="text" name="register[last_name]" value="<?= (isset($this->register['last_name'])) ? $this->escape($this->register['last_name']) : '' ?>" />
                </label>
            </div>
        </div>

        <?php if ($this->params->get('show_affiliation') || $this->params->get('show_title')) : ?>
            <div class="grid">
                <div class="col span6">
                <?php if ($this->params->get('show_affiliation')) : ?>
                    <label><?php echo Lang::txt('Affiliation:'); ?> <span class="required">Required</span>
                        <input type="text" name="register[affiliation]" value="<?= (isset($this->register['affiliation'])) ? $this->escape($this->register['affiliation']) : '' ?>" />
                    </label>
                <?php endif; ?>
                </div>
                <div class="col span6 omega">
                <?php if ($this->params->get('show_title')) : ?>
                    <label><?php echo Lang::txt('Title:'); ?> <span class="optional">Optional</span>
                        <input type="text" name="register[title]" value="<?= (isset($this->register['title'])) ? $this->escape($this->register['title']) : '' ?>" />
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
                        <input type="text" name="register[city]" value="<?= (isset($this->register['city'])) ? $this->escape($this->register['city']) : '' ?>" />
                    </label>
                </div>
                <div class="col span6 omega">
                    <label><?php echo Lang::txt('State/Province:'); ?> <span class="optional">Optional</span>
                        <input type="text" name="register[state]" value="<?= (isset($this->register['state'])) ? $this->escape($this->register['state']) : '' ?>" />
                    </label>
                </div>
            </div>
            <div class="grid">
                <div class="col span6">
                    <label><?php echo Lang::txt('Zip/Postal code:'); ?> <span class="optional">Optional</span>
                        <input type="text" name="register[zip]" value="<?= (isset($this->register['zip'])) ? $this->escape($this->register['zip']) : '' ?>" />
                    </label>
                </div>
                <div class="col span6 omega">
                    <label><?php echo Lang::txt('Country:'); ?> <span class="optional">Optional</span>
                        <input type="text" name="register[country]" value="<?= (isset($this->register['country'])) ? $this->escape($this->register['country']) : '' ?>" />
                    </label>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($this->params->get('show_telephone') || $this->params->get('show_fax')) : ?>
            <div class="grid">
                <div class="col span6">
                <?php if ($this->params->get('show_telephone')) : ?>
                    <label><?php echo Lang::txt('Telephone:'); ?> <span class="optional">Optional</span>
                        <input type="text" name="register[telephone]" value="<?= (isset($this->register['telephone'])) ? $this->escape($this->register['telephone']) : '' ?>" />
                    </label>
                <?php endif; ?>
                </div>
                <div class="col span6 omega">
                <?php if ($this->params->get('show_fax')) : ?>
                    <label><?php echo Lang::txt('Fax:'); ?> <span class="optional">Optional</span>
                        <input type="text" name="register[fax]" value="<?= (isset($this->register['fax'])) ? $this->escape($this->register['fax']) : '' ?>" />
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
                        <input type="text" name="register[email]" value="<?= (isset($this->register['email'])) ? $this->escape($this->register['email']) : '' ?>" />
                    </label>
                <?php endif; ?>
                </div>
                <div class="col span6 omega">
                <?php if ($this->params->get('show_website')) : ?>
                    <label><?php echo Lang::txt('Website:'); ?> <span class="optional">Optional</span>
                        <input type="text" name="register[website]" value="<?= (isset($this->register['website'])) ? $this->escape($this->register['website']) : '' ?>" />
                    </label>
                <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </fieldset>

    <?php
    if (
        $this->params->get('show_position')
        || $this->params->get('show_degree')
        || $this->params->get('show_gender')
        || $this->params->get('show_race')
    ) : ?>
        <fieldset>
            <legend><?php echo Lang::txt('Demographics'); ?></legend>
            <?php if ($this->params->get('show_position')) : ?>
                <label for="register[position]">
                    <?= Lang::txt('Which best describes your current position?') ?> <span
                        class="optional">Optional</span>
                    <select name="register[position]">
                        <option value=""
                            selected="selected"><?= Lang::txt('(select from list or enter below)') ?></option>
                        <option value="university"><?= Lang::txt('University / College Student or Staff') ?></option>
                        <option value="precollege"><?= Lang::txt('K-12 (Pre-College) Student or Staff') ?></option>
                        <option value="nationallab"><?php echo Lang::txt('National Laboratory'); ?></option>
                        <option value="industry"><?php echo Lang::txt('Industry / Private Company'); ?></option>
                        <option value="government"><?php echo Lang::txt('Government Agency'); ?></option>
                        <option value="military"><?php echo Lang::txt('Military'); ?></option>
                        <option value="unemployed"><?php echo Lang::txt('Retired / Unemployed'); ?></option>
                    </select>
                    <input name="register[position_other]" type="text" value="<?= (isset($this->register['position_other'])) ? $this->escape($this->register['position_other']) : '' ?>" />
                </label>
            <?php endif; ?>

            <?php if ($this->params->get('show_degree')) : ?>
                <fieldset>
                    <legend><?= Lang::txt('Highest academic degree earned:') ?> <span
                        class="optional">Optional</span></legend>
                        <label>
                            <input type="radio" class="option" name="register[degree]" value="Bachelors" <?= (isset($this->register['degree']) && $this->register['degree'] == 'Bachelors') ? 'checked="checked"' : '' ?> />
                            <?php echo Lang::txt('Bachelors degree'); ?>
                        </label>
                        <label>
                            <input type="radio" class="option" name="register[degree]" value="Masters" <?= (isset($this->register['degree']) && $this->register['degree'] == 'Masters') ? 'checked="checked"' : '' ?> />
                            <?php echo Lang::txt('Masters degree'); ?>
                        </label>
                        <label>
                            <input type="radio" class="option" name="register[degree]" value="Doctoral" <?= (isset($this->register['degree']) && $this->register['degree'] == 'Doctoral') ? 'checked="checked"' : '' ?> />
                            <?php echo Lang::txt('Doctoral degree'); ?>
                        </label>
                        <label>
                            <input type="radio" class="option" name="register[degree]" value="Other" <?= (isset($this->register['degree']) && $this->register['degree'] == 'Other') ? 'checked="checked"' : '' ?> />
                            <?php echo Lang::txt('None of the above'); ?>
                        </label>
                </fieldset>
            <?php endif; ?>

            <?php if ($this->params->get('show_gender')) : ?>
                <fieldset>
                    <legend><?php echo Lang::txt('Gender:'); ?> <span class="optional">Optional</span></legend>
                    <label>
                        <input type="radio" name="register[sex]" value="Male" class="option" <?= (isset($this->register['sex']) && $this->register['sex'] == 'Male') ? 'checked="checked"' : '' ?> />
                        <?php echo Lang::txt('Male'); ?>
                    </label>
                    <label>
                        <input type="radio" name="register[sex]" value="Female" class="option" <?= (isset($this->register['sex']) && $this->register['sex'] == 'Female') ? 'checked="checked"' : '' ?> />
                        <?php echo Lang::txt('Female'); ?>
                    </label>
                    <label>
                        <input type="radio" name="register[sex]" value="Refused" class="option" <?= (isset($this->register['sex']) && $this->register['sex'] == 'Refused') ? 'checked="checked"' : '' ?> />
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
                        <input type="checkbox" class="option" name="race[nativeamerican]" id="racenativeamerican"
                            value="Native American" />
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
                        <input type="checkbox" class="option" name="race[black]" id="raceblack"
                            value="African American" />
                        <?php echo Lang::txt('Black or African American'); ?>
                    </label>
                    <label>
                        <input type="checkbox" class="option" name="race[hawaiian]" id="racehawaiian"
                            value="Hawaiian" />
                        <?php echo Lang::txt('Native Hawaiian or Other Pacific Islander'); ?>
                    </label>
                    <label>
                        <input type="checkbox" class="option" name="race[white]" id="racewhite" value="White" />
                        <?php echo Lang::txt('White'); ?>
                    </label>
                    <label>
                        <input type="checkbox" class="option" name="race[hispanic]" id="racehispanic"
                            value="Hispanic" />
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
                    <legend><?= Lang::txt('Arrival Information:') ?> <span class="optional">Optional</span></legend>
                    <label><?php echo Lang::txt('Arrival Day'); ?>
                        <input type="text" name="arrival[day]"
                            value="<?= (isset($this->arrival['day'])) ? $this->arrival['day'] : '' ?>" />
                    </label>
                    <label><?php echo Lang::txt('Arrival Time'); ?>
                        <input type="text" name="arrival[time]"
                            value="<?= (isset($this->arrival['time'])) ? $this->arrival['time'] : '' ?>" />
                    </label>
                </fieldset>
            <?php endif ?>

            <?php if ($this->params->get('show_departure')) : ?>
            <fieldset>
                <legend><?= Lang::txt('Departure Information:') ?> <span class="optional">Optional</span></legend>
                <label><?php echo Lang::txt('Departure Day'); ?>
                    <input type="text" name="departure[day]"
                        value="<?= (isset($this->departure['day'])) ? $this->departure['day'] : '' ?>" />
                </label>
                <label><?php echo Lang::txt('Departure Time'); ?>
                    <input type="text" name="departure[time]"
                        value="<?= (isset($this->departure['time'])) ? $this->departure['time'] : '' ?>" />
                </label>
            </fieldset>
            <?php endif; ?>
        </fieldset>
    <?php endif; ?>

    <?php if ($this->params->get('show_disability') || $this->params->get('show_dietary')) : ?>
        <fieldset>
            <legend><?php echo Lang::txt('Disability/Dietary needs'); ?></legend>
            <?php if ($this->params->get('show_disability')) : ?>
                <label>
                    <input type="checkbox" class="option" name="disability" value="yes" <?= isset($this->disability) && $this->disability == 'yes' ? 'checked="checked"' : '' ?> />
                    <?= Lang::txt('I have auxiliary aids or services due to a disability. Please contact me.') ?>
                </label>
            <?php endif; ?>

            <?php if ($this->params->get('show_dietary')) : ?>
                <label>
                    <input type="checkbox" class="option" name="dietary[needs]" value="yes" <?= isset($this->dietary['needs']) && $this->dietary['needs'] == 'yes' ? 'checked="checked"' : '' ?> />
                    <?php echo Lang::txt('I have specific dietary needs.'); ?>
                </label>
                <label class="indent"><?php echo Lang::txt('Please specify'); ?>
                    <input type="text" name="dietary[specific]" value="<?= isset($this->dietary['specific']) ? $this->escape($this->dietary['specific']) : '' ?>" />
                </label>
            <?php endif; ?>
        </fieldset>
    <?php endif; ?>

    <?php if ($this->params->get('show_dinner')) : ?>
        <fieldset>
            <legend><?php echo Lang::txt('Dinner'); ?></legend>
            <label for="filed-dinner">
                <input type="checkbox" class="option" name="dinner" id="filed-dinner"
                    value="yes" <?= isset($this->dinner) && $this->dinner == 'yes' ? 'checked="checked"' : '' ?> />
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
                <textarea name="register[abstract]" rows="16" cols="32"><?= (isset($this->register['abstract'])) ? $this->escape($this->register['abstract']) : '' ?></textarea>
            </label>
        </fieldset>
    <?php endif; ?>

    <?php if ($this->params->get('show_comments')) : ?>
        <fieldset>
            <legend><?php echo Lang::txt('Comments'); ?></legend>
            <label>
                <?php echo Lang::txt('Please use the space below to provide any additional comments:'); ?>
                <textarea name="register[comment]" rows="4" cols="32"><?= (isset($this->register['comment'])) ? $this->escape($this->register['comment']) : '' ?></textarea>
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