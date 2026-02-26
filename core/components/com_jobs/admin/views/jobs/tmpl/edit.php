<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Jobs\Helpers\Permissions::getActions('job');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_JOBS') . ': ' . $text, 'job');
if ($canDo->get('core.edit')) {
    Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('job');

$now = Date::toSql();

$usonly = $this->config->get('usonly');
$this->row->companyLocationCountry = !$this->isnew
    ? $this->row->companyLocationCountry
    : Lang::txt('COM_JOBS_USA');
$this->row->code = !$this->isnew
    ? $this->row->code
    : Lang::txt('COM_JOBS_ISNEW');

$startdate = ($this->row->startdate && $this->row->startdate != '0000-00-00 00:00:00')
    ? Date::of($this->row->startdate)->toLocal('Y-m-d 00:00:00')
    : '';
$closedate = ($this->row->closedate && $this->row->closedate != '0000-00-00 00:00:00')
    ? Date::of($this->row->closedate)->toLocal('Y-m-d 00:00:00')
    : '';
$opendate = ($this->row->opendate && $this->row->opendate != '0000-00-00 00:00:00')
    ? Date::of($this->row->opendate)->toLocal('Y-m-d 00:00:00')
    : '';
$expiredate = ($this->row->expiredate && $this->row->expiredate != '0000-00-00 00:00:00')
    ? Date::of($this->row->expiredate)->toLocal('Y-m-d 00:00:00')
    : '';

$status = (!$this->isnew) ? $this->row->status : 4; // draft mode

$employerid = ($this->task != 'edit') ? 1 : $this->job->employerid;

$expired = $this->subscription->expires && $this->subscription->expires < $now ? 1 : 0;

// Get the published status
switch ($this->row->status) {
    case 0:
        $alt   = Lang::txt('COM_JOBS_STATUS_PENDING');
        $class = 'post_pending';
        break;
    case 1:
        $alt    = $expired
                ? Lang::txt('COM_JOBS_STATUS_EXPIRED')
                : Lang::txt('COM_JOBS_STATUS_ACTIVE');
        $class  = $expired
                ? 'post_invalidsub'
                : 'post_active';
        break;
    case 2:
        $alt   = Lang::txt('COM_JOBS_STATUS_DELETED');
        $class = 'post_deleted';
        break;
    case 3:
        $alt   = Lang::txt('COM_JOBS_STATUS_INACTIVE');
        $class = 'post_inactive';
        break;
    case 4:
        $alt   = Lang::txt('COM_JOBS_STATUS_DRAFT');
        $class = 'post_draft';
        break;
    default:
        $alt   = '-';
        $class = '';
        break;
}

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

$formAction = Route::url('index.php?option=' . $this->option);
$invalidMsg = $this->escape(
    Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED')
);
$requiredLabel = Lang::txt('JOPTION_REQUIRED');
$companyNameValue = $this->escape(
    stripslashes($this->row->companyName)
);
$companyWebsiteValue = $this->escape(
    stripslashes($this->row->companyWebsite)
);
$companyLocationValue = $this->escape(
    stripslashes($this->row->companyLocation)
);
$locationHint = Lang::txt('COM_JOBS_FIELD_LOCATION_HINT');
$titleValue = $this->escape(stripslashes($this->row->title));
$descHint = Lang::txt('COM_JOBS_FIELD_DESCRIPTION_HINT');
$externalUrlHint = Lang::txt('COM_JOBS_FIELD_EXTERNAL_URL_HINT');
$applyExternalValue = $this->escape(
    stripslashes($this->row->applyExternalUrl)
);
$contactNameValue = $this->escape(
    stripslashes($this->row->contactName)
);
$contactEmailValue = $this->escape(
    stripslashes($this->row->contactEmail)
);
$contactPhoneValue = $this->escape(
    stripslashes($this->row->contactPhone)
);
?>

<form
    action="<?php echo $formAction; ?>"
    method="post"
    id="item-form"
    name="adminForm"
    class="editform form-validate"
    data-invalid-msg="<?php echo $invalidMsg; ?>"
>
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_JOBS_FIELDSET_COMPANY'); ?></span></legend>

                <div class="input-wrap">
                    <label for="companyName">
                        <?php echo Lang::txt('COM_JOBS_FIELD_NAME'); ?>:
                        <span class="required"><?php echo $requiredLabel; ?></span>
                    </label><br />
                    <input
                        type="text"
                        name="companyName"
                        id="companyName"
                        class="required"
                        maxlength="200"
                        value="<?php echo $companyNameValue; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <label for="companyWebsite">
                        <?php echo Lang::txt('COM_JOBS_FIELD_URL'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="companyWebsite"
                        id="companyWebsite"
                        maxlength="200"
                        value="<?php echo $companyWebsiteValue; ?>"
                    />
                </div>
                <div class="input-wrap" data-hint="<?php echo $locationHint; ?>">
                    <label for="companyLocation">
                        <?php echo Lang::txt('COM_JOBS_FIELD_LOCATION'); ?>:
                        <span class="required"><?php echo $requiredLabel; ?></span>
                    </label><br />
                    <input
                        type="text"
                        name="companyLocation"
                        id="companyLocation"
                        class="required"
                        maxlength="200"
                        value="<?php echo $companyLocationValue; ?>"
                    />
                    <span class="hint"><?php echo $locationHint; ?></span>
                </div>
            </fieldset>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_JOBS_FIELDSET_JOB'); ?></span></legend>

                <div class="input-wrap">
                    <label for="cid">
                        <?php echo Lang::txt('COM_JOBS_FIELD_CATEGORY'); ?>:
                    </label><br />
                    <?php
                    echo \Components\Jobs\Helpers\Html::formSelect(
                        'cid',
                        $this->cats,
                        $this->row->cid,
                        '',
                        ''
                    );
                    ?>
                </div>
                <div class="input-wrap">
                    <label for="type">
                        <?php echo Lang::txt('COM_JOBS_FIELD_TYPE'); ?>:
                    </label><br />
                    <?php
                    echo \Components\Jobs\Helpers\Html::formSelect(
                        'type',
                        $this->types,
                        $this->row->type,
                        '',
                        ''
                    );
                    ?>
                </div>
                <div class="input-wrap">
                    <label for="companyLocationCountry">
                        <?php echo Lang::txt('COM_JOBS_FIELD_COUNTRY'); ?>:
                    </label><br />
                    <?php if ($usonly) { ?>
                        <?php echo Lang::txt('COM_JOBS_USA'); ?>
                        <p class="hint"><?php echo Lang::txt('COM_JOBS_USA_HINT'); ?></p>
                        <input type="hidden" id="companyLocationCountry" name="companyLocationCountry" value="us" />
                    <?php } else {
                        $out  = "\t\t\t\t" . '<select name="companyLocationCountry"'
                            . ' id="companyLocationCountry">' . "\n";
                        $selectLabel = Lang::txt('COM_JOBS_SELECT');
                        $out .= "\t\t\t\t" . ' <option value="">' . $selectLabel . '</option>' . "\n";

                        $countries = \Hubzero\Geocode\Geocode::countries();
                        foreach ($countries as $country) {
                            $escapedName = $this->escape($country->name);
                            $out .= "\t\t\t\t" . ' <option value="' . $escapedName . '"';
                            if ($country->name == $this->row->companyLocationCountry) {
                                $out .= ' selected="selected"';
                            }
                            $out .= '>' . $escapedName . '</option>' . "\n";
                        }
                        $out .= "\t\t\t" . '</select>' . "\n";
                        echo $out;
                        ?>
                    <?php } ?>
                </div>
                <div class="input-wrap">
                    <label for="title">
                        <?php echo Lang::txt('COM_JOBS_FIELD_TITLE'); ?>:
                        <span class="required"><?php echo $requiredLabel; ?></span>
                    </label><br />
                    <input
                        type="text"
                        name="title"
                        id="title"
                        class="required"
                        maxlength="200"
                        value="<?php echo $titleValue; ?>"
                    />
                </div>
                <div class="input-wrap" data-hint="<?php echo $descHint; ?>">
                    <label for="description">
                        <?php echo Lang::txt('COM_JOBS_FIELD_DESCRIPTION'); ?>:
                    </label><br />
                    <?php
                    $descValue = $this->escape(
                        stripslashes($this->row->description)
                    );
                    echo $this->editor(
                        'description',
                        $descValue,
                        50,
                        30,
                        'description',
                        array('class' => 'required')
                    );
                    ?>
                    <span class="hint"><?php echo $descHint; ?></span>
                </div>
                <div class="input-wrap">
                    <label for="startdate">
                        <?php echo Lang::txt('COM_JOBS_FIELD_STARTDATE'); ?>:
                    </label><br />
                    <?php echo Html::input('calendar', 'startdate', $startdate); ?>
                </div>
                <div class="input-wrap">
                    <label for="closedate">
                        <?php echo Lang::txt('COM_JOBS_FIELD_DUEDATE'); ?>:
                    </label><br />
                    <?php echo Html::input('calendar', 'closedate', $closedate); ?>
                </div>
                <div class="input-wrap">
                    <label for="expiredate">
                        <?php echo Lang::txt('COM_JOBS_FIELD_EXPIREDATE'); ?>:
                    </label><br />
                    <?php echo Html::input('calendar', 'expiredate', $expiredate); ?>
                </div>
                <div class="input-wrap" data-hint="<?php echo $externalUrlHint; ?>">
                    <label for="applyExternalUrl">
                        <?php echo Lang::txt('COM_JOBS_FIELD_EXTERNAL_URL'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="applyExternalUrl"
                        id="applyExternalUrl"
                        maxlength="100"
                        value="<?php echo $applyExternalValue; ?>"
                    />
                    <span class="hint"><?php echo $externalUrlHint; ?></span>
                </div>
                <div class="input-wrap">
                    <input
                        type="checkbox"
                        class="option"
                        name="applyInternal"
                        id="applyInternal"
                        value="1"
                        <?php if ($this->row->applyInternal) {
                            echo 'checked="checked"';
                        } ?>
                    />
                    <label for="applyInternal">
                        <?php echo Lang::txt('COM_JOBS_FIELD_APPLY_INTERNAL'); ?>
                    </label>
                </div>
            </fieldset>
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_JOBS_FIELDSET_CONTACT_INFO'); ?></span></legend>

                <div class="input-wrap">
                    <label for="contactName">
                        <?php echo Lang::txt('COM_JOBS_FIELD_CONTACT_NAME'); ?>:
                    </label>
                    <input
                        type="text"
                        name="contactName"
                        id="contactName"
                        maxlength="100"
                        value="<?php echo $contactNameValue; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <label for="contactEmail">
                        <?php echo Lang::txt('COM_JOBS_FIELD_CONTACT_EMAIL'); ?>:
                    </label>
                    <input
                        type="text"
                        name="contactEmail"
                        id="contactEmail"
                        maxlength="100"
                        value="<?php echo $contactEmailValue; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <label for="contactPhone">
                        <?php echo Lang::txt('COM_JOBS_FIELD_CONTACT_PHONE'); ?>:
                    </label>
                    <input
                        type="text"
                        name="contactPhone"
                        id="contactPhone"
                        maxlength="100"
                        value="<?php echo $contactPhoneValue; ?>"
                    />
                </div>
            </fieldset>
        </div>
        <div class="col span5">
            <?php if ($this->row->id) { ?>
                <table class="meta">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <?php echo Lang::txt('COM_JOBS_FIELD_CREATED'); ?>:
                            </th>
                            <td><?php echo $this->row->added; ?></td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo Lang::txt('COM_JOBS_FIELD_CREATOR'); ?>:
                            </th>
                            <td>
                                <?php
                                echo $this->row->addedBy;
                                if ($this->job->employerid == 1) {
                                    echo ' ' . Lang::txt('COM_JOBS_ADMIN');
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo Lang::txt('COM_JOBS_FIELD_MODIFIED'); ?>:
                            </th>
                            <td>
                                <?php
                                $edited = $this->job->edited;
                                echo ($edited && $edited != '0000-00-00 00:00:00')
                                    ? $edited
                                    : Lang::txt('COM_JOBS_NOT_APPLICABLE');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo Lang::txt('COM_JOBS_FIELD_MODIFIER'); ?>:
                            </th>
                            <td>
                                <?php
                                echo ($this->job->editedBy)
                                    ? $this->job->editedBy
                                    : Lang::txt('COM_JOBS_NOT_APPLICABLE');
                                ?>
                            </td>
                        </tr>
                    <?php if (isset($this->subscription->id)) { ?>
                        <tr>
                            <th scope="row">
                                <?php echo Lang::txt('COM_JOBS_FIELD_USER_SUBSCRIPTION'); ?>:
                            </th>
                            <td>
                                <?php
                                echo $this->subscription->code;
                                if (!$this->job->inactive) {
                                    $expiresMsg = Lang::txt(
                                        'COM_JOBS_FIELD_USER_SUBSCRIPTION_EXPIRES',
                                        $this->subscription->expires
                                    );
                                    echo ' ' . $expiresMsg;
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                        <tr>
                            <th scope="row">
                                <?php echo Lang::txt('COM_JOBS_FIELD_STATUS'); ?>:
                            </th>
                            <td><?php echo $alt; ?></td>
                        </tr>
                    <?php if ($opendate) { ?>
                        <tr>
                            <th scope="row">
                                <?php echo Lang::txt('COM_JOBS_FIELD_AD_PUBLISHED'); ?>:
                            </th>
                            <td><?php echo $this->row->opendate; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <?php } ?>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_JOBS_FIELDSET_MANAGE'); ?></span></legend>

                <?php if (!$this->isnew) { ?>
                    <fieldset>
                        <legend>
                            <span><?php echo Lang::txt('COM_JOBS_FIELDSET_TAKE_ACTION'); ?>:</span>
                        </legend>

                        <div class="input-wrap">
                            <input type="radio" name="action" value="message" />
                            <?php echo Lang::txt('COM_JOBS_FIELD_ACTION_NONE'); ?><br />
                            <?php if ($this->row->status != 1) { ?>
                                <input type="radio" name="action" value="publish" />
                                <?php echo Lang::txt('COM_JOBS_FIELD_ACTION_PUBLISH'); ?>
                            <?php } else { ?>
                                <input type="radio" name="action" value="unpublish" />
                                <?php echo Lang::txt('COM_JOBS_FIELD_ACTION_UNPUBLISH'); ?>
                            <?php } ?>
                            <br />
                            <input type="radio" name="action" value="delete" />
                            <?php echo Lang::txt('COM_JOBS_FIELD_ACTION_DELETE'); ?><br />
                        </div>
                    </fieldset>

                    <div class="input-wrap">
                        <?php echo Lang::txt('COM_JOBS_FIELD_MESSAGE'); ?>: <br />
                        <textarea name="message" id="message"  cols="30" rows="5"></textarea>
                    </div>
                <?php } else { ?>
                    <p><?php echo Lang::txt('COM_JOBS_WARNING_MUST_SAVE_FIRST'); ?></p>
                <?php } ?>

                <input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
                <input type="hidden" name="isnew" value="<?php echo $this->isnew; ?>" />
                <input type="hidden" name="employerid" value="<?php echo $employerid; ?>" />
                <input type="hidden" name="status" value="<?php echo $status; ?>" />

                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
                <input type="hidden" name="task" value="save" />
            </fieldset>
        </div>
    </div>

    <?php echo Html::input('token'); ?>
</form>
