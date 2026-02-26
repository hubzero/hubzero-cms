<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\User;
use Hubzero\Facades\Event;

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

$canDo = \Components\Courses\Helpers\Permissions::getActions();

$tbTitle = Lang::txt('COM_COURSES') . ': '
    . Lang::txt('COM_COURSES_SECTIONS') . ': ' . $text;
Toolbar::title($tbTitle, 'courses.png');
if ($canDo->get('core.edit')) {
    Toolbar::save();
}
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('switcher', 'submenu');
Html::behavior('calendar');

$base = str_replace('/administrator', '', rtrim(Request::base(true), '/'));

$this->js('jquery.fileuploader.js', 'system')
    ->js();
$this->css(); //->css('classic');

$course_id = 0;
?>

<?php if ($this->getError()) { ?>
    <p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<?php
$routeUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
$validMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));
?>
<form
    action="<?php echo $routeUrl; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    enctype="multipart/form-data"
    class="editform form-validate"
    data-invalid-msg="<?php echo $validMsg; ?>">

    <nav role="navigation" class="sub-navigation">
        <div id="submenu-box">
            <div class="submenu-box">
                <div class="submenu-pad">
                    <ul id="submenu" class="coursesection">
                        <?php $txt = Lang::txt('JDETAILS'); ?>
                        <li>
                            <a href="#page-details" id="details" class="active">
                                <?php echo $txt; ?>
                            </a>
                        </li>
                        <?php $txt = Lang::txt('COM_COURSES_FIELDSET_MANAGERS'); ?>
                        <li>
                            <a href="#page-managers" id="managers">
                                <?php echo $txt; ?>
                            </a>
                        </li>
                        <?php $txt = Lang::txt('COM_COURSES_FIELDSET_DATES'); ?>
                        <li>
                            <a href="#page-datetime" id="datetime">
                                <?php echo $txt; ?>
                            </a>
                        </li>
                        <?php $txt = Lang::txt('COM_COURSES_FIELDSET_REWARDS'); ?>
                        <li>
                            <a href="#page-badge" id="badge">
                                <?php echo $txt; ?>
                            </a>
                        </li>
                    </ul>
                    <div class="clr"></div>
                </div>
            </div>
            <div class="clr"></div>
        </div>
    </nav><!-- / .sub-navigation -->

    <div id="section-document">
        <div id="page-details" class="tab">
            <div class="grid">
                <div class="col span6">
                    <fieldset class="adminform">
                        <legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

                        <?php $rowId = $this->row->get('id'); ?>
                        <?php $offeringId = $this->row->get('offering_id'); ?>
                        <input
                            type="hidden"
                            name="fields[id]"
                            value="<?php echo $rowId; ?>" />
                        <input
                            type="hidden"
                            name="offering"
                            value="<?php echo $offeringId; ?>" />
                        <input
                            type="hidden"
                            name="option"
                            value="<?php echo $this->option; ?>" />
                        <input
                            type="hidden"
                            name="controller"
                            value="<?php echo $this->controller; ?>">
                        <input type="hidden" name="task" value="save" />

                        <div class="input-wrap">
                            <?php $offeringTxt = Lang::txt('COM_COURSES_OFFERING'); ?>
                            <label for="offering_id">
                                <?php echo $offeringTxt; ?>:
                            </label><br />
                            <select name="fields[offering_id]" id="offering_id">
                                <?php $selectTxt = Lang::txt('COM_COURSES_SELECT'); ?>
                                <option value="-1">
                                    <?php echo $selectTxt; ?>
                                </option>
                                <?php
                                    $model = \Components\Courses\Models\Courses::getInstance();
                                if ($model->courses()->total() > 0) {
                                    foreach ($model->courses() as $course) {
                                        $courseAlias = $this->escape(
                                            stripslashes($course->get('alias'))
                                        );
                                        ?>
                                            <optgroup label="<?php echo $courseAlias; ?>">
                                            <?php
                                            $j = 0;
                                            foreach ($course->offerings() as $ii => $offering) {
                                                if ($offering->get('id') == $this->row->get('offering_id')) {
                                                    $course_id = $offering->get('course_id');
                                                }
                                                $offeringVal = $this->escape(
                                                    stripslashes($offering->get('id'))
                                                );
                                                $sel = ($offering->get('id') == $this->row->get('offering_id'))
                                                    ? ' selected="selected"' : '';
                                                $offeringAlias = $this->escape(
                                                    stripslashes($offering->get('alias'))
                                                );
                                                ?>
                                            <option
                                                value="<?php echo $offeringVal; ?>"
                                                <?php echo $sel; ?>
                                            ><?php echo $offeringAlias; ?></option>
                                                <?php
                                            }
                                            ?>
                                            </optgroup>
                                            <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <?php
                        $aliasHint = Lang::txt('COM_COURSES_FIELD_ALIAS_HINT');
                        $aliasTxt = Lang::txt('COM_COURSES_FIELD_ALIAS');
                        $aliasVal = $this->escape(
                            stripslashes($this->row->get('alias'))
                        );
                        ?>
                        <div class="input-wrap" data-hint="<?php echo $aliasHint; ?>">
                            <label for="field-alias">
                                <?php echo $aliasTxt; ?>:
                            </label><br />
                            <input
                                type="text"
                                name="fields[alias]"
                                id="field-alias"
                                value="<?php echo $aliasVal; ?>" />
                            <span class="hint"><?php echo $aliasHint; ?></span>
                        </div>

                        <div class="input-wrap">
                            <?php $titleTxt = Lang::txt('COM_COURSES_FIELD_TITLE'); ?>
                            <?php $reqTxt = Lang::txt('JOPTION_REQUIRED'); ?>
                            <label for="field-title">
                                <?php echo $titleTxt; ?>:
                                <span class="required"><?php echo $reqTxt; ?></span>
                            </label><br />
                            <?php
                            $titleVal = $this->escape(
                                stripslashes($this->row->get('title'))
                            );
                            ?>
                            <input
                                type="text"
                                name="fields[title]"
                                id="field-title"
                                class="required"
                                value="<?php echo $titleVal; ?>" />
                        </div>

                        <fieldset>
                            <?php $defTxt = Lang::txt('COM_COURSES_FIELD_DEFAULT_SECTION'); ?>
                            <legend><?php echo $defTxt; ?>:</legend>
                            <div class="input-wrap">
                                <?php
                                $isDefault = $this->row->get('is_default', 0);
                                $yesChecked = ($isDefault == 1) ? ' checked="checked"' : '';
                                $noChecked = ($isDefault == 0) ? ' checked="checked"' : '';
                                $yesTxt = Lang::txt('JYES');
                                $noTxt = Lang::txt('JNO');
                                ?>
                                <label for="field-is_default-yes">
                                    <input
                                        type="radio"
                                        name="fields[is_default]"
                                        id="field-is_default-yes"
                                        value="1"
                                        <?php echo $yesChecked; ?> />
                                    <?php echo $yesTxt; ?>
                                </label>
                                <label for="field-is_default-no">
                                    <input
                                        type="radio"
                                        name="fields[is_default]"
                                        id="field-is_default-no"
                                        value="0"
                                        <?php echo $noChecked; ?> />
                                    <?php echo $noTxt; ?>
                                </label>
                            </div>
                        </fieldset>

                        <div class="input-wrap">
                            <?php $enrollTxt = Lang::txt('COM_COURSES_FIELD_ENROLLMENT'); ?>
                            <label for="field-enrollment">
                                <?php echo $enrollTxt; ?>:
                            </label><br />
                            <select name="fields[enrollment]" id="field-enrollment">
                                <?php
                                $enrollVal = $this->row->get(
                                    'enrollment',
                                    $this->row->config('default_enrollment', 0)
                                );
                                $sel = ($enrollVal == 0) ? ' selected="selected"' : '';
                                ?>
                            <option
                                value="0"
                                <?php echo $sel; ?>
                            ><?php echo Lang::txt('COM_COURSES_FIELD_ENROLLMENT_OPEN'); ?></option>
                                <?php
                                $sel = ($enrollVal == 1) ? ' selected="selected"' : '';
                                ?>
                            <option
                                value="1"
                                <?php echo $sel; ?>
                            ><?php echo Lang::txt('COM_COURSES_FIELD_ENROLLMENT_RESTRICTED'); ?></option>
                                <?php
                                $sel = ($enrollVal == 2) ? ' selected="selected"' : '';
                                ?>
                            <option
                                value="2"
                                <?php echo $sel; ?>
                            ><?php echo Lang::txt('COM_COURSES_FIELD_ENROLLMENT_CLOSED'); ?></option>
                            </select>
                        </div>

                        <div class="input-wrap">
                            <?php $stateTxt = Lang::txt('COM_COURSES_FIELD_STATE'); ?>
                            <label for="field-state">
                                <?php echo $stateTxt; ?>:
                            </label><br />
                            <select name="fields[state]" id="field-state">
                                <?php $sel = ($this->row->get('state') == 0) ? ' selected="selected"' : ''; ?>
                            <option value="0"<?php echo $sel; ?>>
                                <?php echo Lang::txt('COM_COURSES_UNPUBLISHED'); ?>
                            </option>
                                <?php $sel = ($this->row->get('state') == 3) ? ' selected="selected"' : ''; ?>
                            <option value="3"<?php echo $sel; ?>>
                                <?php echo Lang::txt('COM_COURSES_DRAFT'); ?>
                            </option>
                                <?php $sel = ($this->row->get('state') == 1) ? ' selected="selected"' : ''; ?>
                            <option value="1"<?php echo $sel; ?>>
                                <?php echo Lang::txt('COM_COURSES_PUBLISHED'); ?>
                            </option>
                                <?php $sel = ($this->row->get('state') == 2) ? ' selected="selected"' : ''; ?>
                            <option value="2"<?php echo $sel; ?>>
                                <?php echo Lang::txt('COM_COURSES_TRASHED'); ?>
                            </option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="adminform">
                        <?php $pubTxt = Lang::txt('COM_COURSES_FIELDSET_PUBLISHING'); ?>
                        <legend><span><?php echo $pubTxt; ?></span></legend>

                        <?php $hintTxt = Lang::txt('COM_COURSES_FIELD_PUBLISH_UP_HINT'); ?>
                        <div class="input-wrap" data-hint="<?php echo $hintTxt; ?>">
                            <?php $pubUpTxt = Lang::txt('COM_COURSES_FIELD_PUBLISH_UP'); ?>
                            <label for="field-publish_up">
                                <?php echo $pubUpTxt; ?>:
                            </label><br />
                            <?php
                                $pubUp = $this->row->get('publish_up');
                                $dateVal = ($pubUp && $pubUp != '0000-00-00 00:00:00')
                                    ? $pubUp : '';
                                echo Html::input(
                                    'calendar',
                                    'fields[publish_up]',
                                    $dateVal,
                                    array('id' => 'field-publish_up')
                                );
                                ?>
                            <?php $hintPubUp = Lang::txt('COM_COURSES_FIELD_PUBLISH_UP_HINT'); ?>
                            <span class="hint"><?php echo $hintPubUp; ?></span>
                        </div>

                        <?php $hintTxt = Lang::txt('COM_COURSES_FIELD_SECTION_STARTS_HINT'); ?>
                        <div class="input-wrap" data-hint="<?php echo $hintTxt; ?>">
                            <?php $startTxt = Lang::txt('COM_COURSES_FIELD_SECTION_STARTS'); ?>
                            <label for="field-start_date">
                                <?php echo $startTxt; ?>:
                            </label><br />
                            <?php
                                $startDate = $this->row->get('start_date');
                                $dateVal = ($startDate && $startDate != '0000-00-00 00:00:00')
                                    ? $startDate : '';
                                echo Html::input(
                                    'calendar',
                                    'fields[start_date]',
                                    $dateVal,
                                    array('id' => 'field-start_date')
                                );
                                ?>
                            <?php $hintStart = Lang::txt('COM_COURSES_FIELD_SECTION_STARTS_HINT'); ?>
                            <span class="hint"><?php echo $hintStart; ?></span>
                        </div>

                        <?php $finishHint = Lang::txt('COM_COURSES_FIELD_FINISHES_HINT'); ?>
                        <?php $finishTxt = Lang::txt('COM_COURSES_FIELD_FINISHES'); ?>
                        <div class="input-wrap" data-hint="<?php echo $finishHint; ?>">
                            <label for="field-end date">
                                <?php echo $finishTxt; ?>:
                            </label><br />
                            <?php
                                $endDate = $this->row->get('end_date');
                                $dateVal = ($endDate && $endDate != '0000-00-00 00:00:00')
                                    ? $endDate : '';
                                echo Html::input(
                                    'calendar',
                                    'fields[end_date]',
                                    $dateVal,
                                    array('id' => 'field-end_date')
                                );
                                ?>
                            <?php $finishHintTxt = Lang::txt('COM_COURSES_FIELD_FINISHES_HINT'); ?>
                            <span class="hint"><?php echo $finishHintTxt; ?></span>
                        </div>

                        <?php $hintTxt = Lang::txt('COM_COURSES_FIELD_PUBLISH_DOWN_HINT'); ?>
                        <div class="input-wrap" data-hint="<?php echo $hintTxt; ?>">
                            <?php $pubDownTxt = Lang::txt('COM_COURSES_FIELD_PUBLISH_DOWN'); ?>
                            <label for="field-publish_down">
                                <?php echo $pubDownTxt; ?>:
                            </label><br />
                            <?php
                                $pubDown = $this->row->get('publish_down');
                                $dateVal = ($pubDown && $pubDown != '0000-00-00 00:00:00')
                                    ? $pubDown : '';
                                echo Html::input(
                                    'calendar',
                                    'fields[publish_down]',
                                    $dateVal,
                                    array('id' => 'field-publish_down')
                                );
                                ?>
                            <?php $hintPubDown = Lang::txt('COM_COURSES_FIELD_PUBLISH_DOWN_HINT'); ?>
                            <span class="hint"><?php echo $hintPubDown; ?></span>
                        </div>
                    </fieldset>
                </div>
                <div class="col span6">
                    <table class="meta">
                        <tbody>
                            <tr>
                                <?php $cidTxt = Lang::txt('COM_COURSES_FIELD_COURSE_ID'); ?>
                                <th><?php echo $cidTxt; ?></th>
                                <td colspan="3">
                                    <?php echo $this->escape($course_id); ?>
                                </td>
                            </tr>
                            <tr>
                                <?php $oidTxt = Lang::txt('COM_COURSES_FIELD_OFFERING_ID'); ?>
                                <th><?php echo $oidTxt; ?></th>
                                <td colspan="3">
                                    <?php echo $this->escape($this->row->get('offering_id')); ?>
                                </td>
                            </tr>
                            <tr>
                                <?php $sidTxt = Lang::txt('COM_COURSES_FIELD_SECTION_ID'); ?>
                                <th><?php echo $sidTxt; ?></th>
                                <td colspan="3">
                                    <?php echo $this->escape($this->row->get('id')); ?>
                                </td>
                            </tr>
                            <?php if ($this->row->get('created')) { ?>
                                <tr>
                                    <?php $createdTxt = Lang::txt('COM_COURSES_FIELD_CREATED'); ?>
                                    <th><?php echo $createdTxt; ?></th>
                                    <td>
                                        <?php echo $this->escape($this->row->get('created')); ?>
                                    </td>
                                </tr>
                                <?php if ($this->row->get('created_by')) { ?>
                                    <tr>
                                        <?php $creatorTxt = Lang::txt('COM_COURSES_FIELD_CREATOR'); ?>
                                        <th><?php echo $creatorTxt; ?></th>
                                        <td><?php
                                            $creator = User::getInstance($this->row->get('created_by'));
                                            echo $this->escape(stripslashes($creator->get('name'))); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>

                    <fieldset class="adminform">
                        <?php $logoTxt = Lang::txt('COM_COURSES_LOGO'); ?>
                        <legend><span><?php echo $logoTxt; ?></span></legend>

                        <?php
                        if ($this->row->exists()) {
                            $logo = $this->row->params('logo');
                            ?>
                            <div class="uploader-wrap">
                                <?php
                                $uploadUrl = Route::url(
                                    'index.php?option=' . $this->option
                                    . '&controller=logo&task=upload&type=section&id='
                                    . $this->row->get('id') . '&no_html=1&'
                                    . Session::getFormToken() . '=1'
                                );
                                $uploadTxt = Lang::txt('COM_COURSES_UPLOAD_CLICK_OR_DROP');
                                ?>
                            <div
                                id="ajax-uploader"
                                data-action="<?php echo $uploadUrl; ?>"
                                data-instructions="<?php echo $uploadTxt; ?>">
                                    <noscript>
                                        <?php
                                        $iframeSrc = Route::url(
                                            'index.php?option=' . $this->option
                                            . '&controller=logo&tmpl=component&file='
                                            . $logo . '&type=section&id='
                                            . $this->row->get('id')
                                        );
                                        ?>
                                    <iframe
                                        width="100%"
                                        height="350"
                                        name="filer"
                                        id="filer"
                                        frameborder="0"
                                        src="<?php echo $iframeSrc; ?>"></iframe>
                                    </noscript>
                                </div>
                            </div>
                                <?php
                                $width  = 0;
                                $height = 0;
                                $this_size = 0;
                                if ($logo) {
                                    $path = $this->row->logo('path');

                                    $this_size = filesize(PATH_APP . $path . DS . $logo);
                                    list($width, $height, $type, $attr) = getimagesize(
                                        PATH_APP . $path . DS . $logo
                                    );
                                    $pic = $logo;
                                } else {
                                    $pic  = 'blank.png';
                                    $path = '/core/components/com_courses/admin/assets/img';
                                }
                                $imgSrc = '..' . $path . DS . $pic;
                                $logoAlt = Lang::txt('COM_COURSES_LOGO');
                                ?>
                                <div id="img-container">
                                    <img
                                        id="img-display"
                                        src="<?php echo $imgSrc; ?>"
                                        alt="<?php echo $logoAlt; ?>" />
                                    <input
                                        type="hidden"
                                        name="currentfile"
                                        id="currentfile"
                                        value="<?php echo $this->escape($logo); ?>" />
                                </div>
                                <table class="formed">
                                    <tbody>
                                        <tr>
                                            <?php $fileTxt = Lang::txt('COM_COURSES_FILE'); ?>
                                            <th><?php echo $fileTxt; ?>:</th>
                                            <td>
                                                <?php
                                                $picName = $this->row->params(
                                                    'logo',
                                                    Lang::txt('COM_COURSES_NONE')
                                                );
                                                ?>
                                                <span id="img-name">
                                                    <?php echo $picName; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $delCls = $logo ? '' : 'class="hide" ';
                                                $delUrl = Route::url(
                                                    'index.php?option=' . $this->option
                                                    . '&controller=logo&tmpl=component'
                                                    . '&task=remove&currentfile=' . $logo
                                                    . '&type=section&id='
                                                    . $this->row->get('id') . '&'
                                                    . Session::getFormToken() . '=1'
                                                );
                                                $delTitle = Lang::txt('COM_COURSES_DELETE');
                                                $defImg = '../core/components/com_courses/admin/assets/img/blank.png';
                                                ?>
                                                <a
                                                    id="img-delete"
                                                    <?php echo $delCls; ?>
                                                    href="<?php echo $delUrl; ?>"
                                                    title="<?php echo $delTitle; ?>"
                                                    data-defaultimg="<?php echo $defImg; ?>"
                                                >[ x ]</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <?php $sizeTxt = Lang::txt('COM_COURSES_PICTURE_SIZE'); ?>
                                            <th><?php echo $sizeTxt; ?>:</th>
                                            <td>
                                                <span id="img-size">
                                                    <?php echo \Hubzero\Utility\Number::formatBytes($this_size); ?>
                                                </span>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <?php $widthTxt = Lang::txt('COM_COURSES_PICTURE_WIDTH'); ?>
                                            <th><?php echo $widthTxt; ?>:</th>
                                            <td>
                                                <span id="img-width"><?php echo $width; ?></span> px
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <?php $heightTxt = Lang::txt('COM_COURSES_PICTURE_HEIGHT'); ?>
                                            <th><?php echo $heightTxt; ?>:</th>
                                            <td>
                                                <span id="img-height"><?php echo $height; ?></span> px
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php
                        } else {
                            $laterTxt = Lang::txt('COM_COURSES_UPLOAD_ADDED_LATER');
                            echo '<p class="warning">' . $laterTxt . '</p>';
                        }
                        ?>
                    </fieldset>

                    <?php $params = new \Hubzero\Config\Registry($this->row->get('params')); ?>

                    <fieldset class="adminform sectionparams">
                        <?php $paramsTxt = Lang::txt('COM_COURSES_FIELDSET_PARAMS'); ?>
                        <legend><?php echo $paramsTxt; ?></legend>
                        <div class="input-wrap">
                            <?php $pcTxt = Lang::txt('COM_COURSES_PROGRESS_CALCULATION'); ?>
                            <label for="params-progress-calculation">
                                <?php echo $pcTxt; ?>:
                            </label><br />
                            <?php $pc = $params->get('progress_calculation', ''); ?>
                            <select
                                name="params[progress_calculation]"
                                id="params-progress-calculation">
                                <?php $sel = ($pc == '') ? 'selected="selected"' : ''; ?>
                                <option value=""<?php echo $sel; ?>>
                                    <?php echo Lang::txt('COM_COURSES_PROGRESS_CALCULATION_INHERIT_FROM_OFFERING'); ?>
                                </option>
                                <?php $sel = ($pc == 'all') ? 'selected="selected"' : ''; ?>
                                <option value="all"<?php echo $sel; ?>>
                                    <?php echo Lang::txt('COM_COURSES_PROGRESS_CALCULATION_ALL'); ?>
                                </option>
                                <?php $sel = ($pc == 'graded') ? 'selected="selected"' : ''; ?>
                                <option value="graded"<?php echo $sel; ?>>
                                    <?php echo Lang::txt('COM_COURSES_PROGRESS_CALCULATION_GRADED'); ?>
                                </option>
                                <?php $sel = ($pc == 'videos') ? 'selected="selected"' : ''; ?>
                                <option value="videos"<?php echo $sel; ?>>
                                    <?php echo Lang::txt('COM_COURSES_PROGRESS_CALCULATION_VIDEOS'); ?>
                                </option>
                                <?php $sel = ($pc == 'manual') ? 'selected="selected"' : ''; ?>
                                <option value="manual"<?php echo $sel; ?>>
                                    <?php echo Lang::txt('COM_COURSES_PROGRESS_CALCULATION_MANUAL'); ?>
                                </option>
                            </select>
                        </div>
                        <div class="input-wrap">
                            <?php $prevTxt = Lang::txt('COM_COURSES_PREVIEW_MODE'); ?>
                            <label for="params-progress-calculation">
                                <?php echo $prevTxt; ?>:
                            </label><br />
                            <select name="params[preview]" id="params-preview">
                                <?php $pv = $params->get('preview', ''); ?>
                                <?php $sel = ($pv == '0') ? 'selected="selected"' : ''; ?>
                                <option value="0"<?php echo $sel; ?>>
                                    <?php echo Lang::txt('COM_COURSES_PREVIEW_NO'); ?>
                                </option>
                                <?php $sel = ($pv == '1') ? 'selected="selected"' : ''; ?>
                                <option value="1"<?php echo $sel; ?>>
                                    <?php echo Lang::txt('COM_COURSES_PREVIEW_YES_FULL'); ?>
                                </option>
                                <?php $sel = ($pv == '2') ? 'selected="selected"' : ''; ?>
                                <option value="2"<?php echo $sel; ?>>
                                    <?php echo Lang::txt('COM_COURSES_PREVIEW_YES_FIRST_UNIT'); ?>
                                </option>
                            </select>
                        </div>
                    </fieldset>

                    <?php
                    if ($plugins = Event::trigger('courses.onSectionEdit')) {
                        $data = $this->row->get('params');

                        foreach ($plugins as $plugin) {
                            $param = new \Hubzero\Html\Parameter(
                                (is_object($data) ? $data->toString() : $data),
                                PATH_CORE . DS . 'plugins' . DS . 'courses'
                                . DS . $plugin['name'] . DS . $plugin['name'] . '.xml'
                            );
                            $out = $param->render('params', 'onSectionEdit');
                            if (!$out) {
                                continue;
                            }
                            ?>
                                <fieldset
                                    class="adminform eventparams"
                                    id="params-<?php echo $plugin['name']; ?>">
                                <?php
                                $paramTxt = Lang::txt(
                                    'COM_COURSES_FIELDSET_PARAMETERS',
                                    $plugin['title']
                                );
                                ?>
                            <legend><?php echo $paramTxt; ?></legend>
                                    <div class="input-wrap">
                                    <?php echo $out; ?>
                                    </div>
                                </fieldset>
                                <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <div id="page-managers" class="tab">
            <fieldset class="adminform">
                <?php $mgrTxt = Lang::txt('COM_COURSES_FIELDSET_MANAGERS'); ?>
                <legend><span><?php echo $mgrTxt; ?></span></legend>
                <?php if ($this->row->get('id')) { ?>
                        <?php
                        $mgrUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=supervisors&tmpl=component&offering='
                            . $this->row->get('offering_id')
                            . '&section=' . $this->row->get('id')
                        );
                        ?>
                    <iframe
                        width="100%"
                        height="500"
                        name="managers"
                        id="managers"
                        frameborder="0"
                        src="<?php echo $mgrUrl; ?>"></iframe>
                <?php } else { ?>
                    <?php $warnTxt = Lang::txt('COM_COURSES_FIELDSET_MANAGERS_WARNING'); ?>
                    <p class="warning"><?php echo $warnTxt; ?></p>
                <?php } ?>
            </fieldset>
        </div>

        <div id="page-datetime" class="tab">
        <?php if ($this->offering->units()->total() > 0) { ?>
            <div class="col span12">
                <?php if (!$this->row->exists() && !$this->row->get('is_default')) { ?>
                    <?php $helpTxt = Lang::txt('COM_COURSES_SECTION_DATES_HELP'); ?>
                <p class="info"><?php echo $helpTxt; ?></p>
                <?php } ?>

                <?php
                echo Html::sliders('start', 'content-pane');

                $nullDate = '0000-00-00 00:00:00';

                $this->offering->section($this->row->get('alias', '!!default!!'));

                    $i = 0;
                foreach ($this->offering->units(array(), true) as $unit) {
                    $unitTitle = stripslashes($unit->get('title'));
                    $unitAlias = stripslashes($unit->get('alias'));
                    echo Html::sliders('panel', $unitTitle, $unitAlias);
                    $unitDateId = $this->row->date('unit', $unit->get('id'))->get('id');
                    $unitId = $unit->get('id');
                    ?>
                            <input
                                type="hidden"
                                name="dates[<?php echo $i; ?>][id]"
                                value="<?php echo $unitDateId; ?>" />
                            <input
                                type="hidden"
                                name="dates[<?php echo $i; ?>][scope]"
                                value="unit" />
                            <input
                                type="hidden"
                                name="dates[<?php echo $i; ?>][scope_id]"
                                value="<?php echo $unitId; ?>" />

                            <table
                                class="admintable section-dates"
                                id="dates_<?php echo $i; ?>">
                                <tbody>
                                    <tr>
                                    <?php $fromTxt = Lang::txt('COM_COURSES_FROM'); ?>
                                        <th class="key">
                                            <label for="dates_<?php echo $i; ?>_publish_up">
                                            <?php echo $fromTxt; ?>
                                            </label>
                                        </th>
                                        <td>
                                        <?php
                                        $unitPubUp = $unit->get('publish_up');
                                        $unitDatePubUp = $this->row->date('unit', $unitId)->get('publish_up');
                                        $tm = ($unitPubUp && $unitPubUp != $nullDate)
                                            ? $unitPubUp : $unitDatePubUp;
                                        $tmVal = (!$tm || $tm == $nullDate)
                                            ? '' : Date::of($tm)->toLocal('Y-m-d H:i:s');
                                        ?>
                                            <input
                                                type="text"
                                                name="dates[<?php echo $i; ?>][publish_up]"
                                                id="dates_<?php echo $i; ?>_publish_up"
                                                class="datetime-field"
                                                value="<?php echo $tmVal; ?>" />
                                        </td>
                                        <?php $toTxt = Lang::txt('COM_COURSES_TO'); ?>
                                        <th class="key">
                                            <label for="dates_<?php echo $i; ?>_publish_up">
                                            <?php echo $toTxt; ?>
                                            </label>
                                        </th>
                                        <td>
                                            <?php
                                            $unitPubDown = $unit->get('publish_down');
                                            $unitDatePubDown = $this->row->date('unit', $unitId)->get('publish_down');
                                            $tm = ($unitPubDown && $unitPubDown != $nullDate)
                                            ? $unitPubDown : $unitDatePubDown;
                                            $tmVal = (!$tm || $tm == $nullDate)
                                            ? '' : Date::of($tm)->toLocal('Y-m-d H:i:s');
                                            ?>
                                            <input
                                                type="text"
                                                name="dates[<?php echo $i; ?>][publish_down]"
                                                id="dates_<?php echo $i; ?>_publish_down"
                                                class="datetime-field"
                                                value="<?php echo $tmVal; ?>" />
                                        </td>
                                        <td>
                                            <?php $inhTxt = Lang::txt('COM_COURSES_SECTION_DATES_INHERITED'); ?>
                                            <?php echo $inhTxt; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table
                                class="admintable section-dates"
                                id="dates_<?php echo $i; ?>">
                                <tbody>
                            <?php
                            // Loop through the asset group types
                            $z = 0;
                            foreach ($unit->assetgroups() as $agt) {
                                $agtId = $agt->get('id');
                                $agtPubUp = $this->row->date('asset_group', $agtId)->get('publish_up');
                                $agtPubDown = $this->row->date('asset_group', $agtId)->get('publish_down');
                                $agt->set('publish_up', $agtPubUp);
                                $agt->set('publish_down', $agtPubDown);

                                if ($agt->get('publish_up') == $nullDate) {
                                    $agt->set('publish_up', $unit->get('publish_up'));
                                }
                                if ($agt->get('publish_down') == $nullDate) {
                                    $agt->set('publish_down', $unit->get('publish_down'));
                                }
                                $agtTitle = $this->escape(stripslashes($agt->get('title')));
                                ?>

                                    <tr>
                                        <th class="key">
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <span class="treenode">&#8970;</span> &nbsp;
                                            <?php echo $agtTitle; ?>
                                        </th>
                                        <td>
                                            <?php $agtLabelUp = 'dates_' . $i . '_assetgroup_' . $z . '_publish_up'; ?>
                                            <label for="<?php echo $agtLabelUp; ?>">
                                                <?php echo Lang::txt('COM_COURSES_FROM'); ?>
                                            </label>
                                        </th>
                                        <td>
                                            <?php
                                            $agtDateId = $this->row->date('asset_group', $agtId)->get('id');
                                            $namePrefix = 'dates[' . $i . '][asset_group][' . $z . ']';
                                            ?>
                                            <input
                                                type="hidden"
                                                name="<?php echo $namePrefix; ?>[id]"
                                                value="<?php echo $agtDateId; ?>" />
                                            <input
                                                type="hidden"
                                                name="<?php echo $namePrefix; ?>[scope]"
                                                value="asset_group" />
                                            <input
                                                type="hidden"
                                                name="<?php echo $namePrefix; ?>[scope_id]"
                                                value="<?php echo $agtId; ?>" />
                                            <?php
                                            $agtUp = $agt->get('publish_up');
                                            $unitUp = $unit->get('publish_up');
                                            $pubUpVal = (!$agtUp || $agtUp == $unitUp || $agtUp == $nullDate)
                                                ? '' : Date::of($agtUp)->toLocal('Y-m-d H:i:s');
                                            $fieldId = 'dates_' . $i . '_assetgroup_' . $z . '_publish_up';
                                            ?>
                                            <input
                                                type="text"
                                                name="<?php echo $namePrefix; ?>[publish_up]"
                                                id="<?php echo $fieldId; ?>"
                                                class="datetime-field"
                                                value="<?php echo $pubUpVal; ?>" />
                                        </td>
                                        <td>
                                            <label for="<?php echo $agtLabelUp; ?>">
                                                <?php echo Lang::txt('COM_COURSES_TO'); ?>
                                            </label>
                                        </th>
                                        <td>
                                            <?php
                                            $agtDown = $agt->get('publish_down');
                                            $unitDown = $unit->get('publish_down');
                                            $pubDownVal = (!$agtDown || $agtDown == $unitDown || $agtDown == $nullDate)
                                                ? '' : Date::of($agtDown)->toLocal('Y-m-d H:i:s');
                                            $fieldId = 'dates_' . $i . '_assetgroup_' . $z . '_publish_down';
                                            ?>
                                            <input
                                                type="text"
                                                name="<?php echo $namePrefix; ?>[publish_down]"
                                                id="<?php echo $fieldId; ?>"
                                                class="datetime-field"
                                                value="<?php echo $pubDownVal; ?>" />
                                        </td>
                                    </tr>

                                <?php
                                $j = 0;
                                foreach ($agt->children() as $ag) {
                                    $agId = $ag->get('id');
                                    $agPubUp = $this->row->date('asset_group', $agId)->get('publish_up');
                                    $agPubDown = $this->row->date('asset_group', $agId)->get('publish_down');
                                    $ag->set('publish_up', $agPubUp);
                                    $ag->set('publish_down', $agPubDown);

                                    if ($ag->get('publish_up') == $nullDate) {
                                        $ag->set('publish_up', $agt->get('publish_up'));
                                    }
                                    if ($ag->get('publish_down') == $nullDate) {
                                        $ag->set('publish_down', $agt->get('publish_down'));
                                    }
                                    $agTitle = $this->escape(stripslashes($ag->get('title')));
                                    $agNamePrefix = $namePrefix . '[asset_group][' . $j . ']';
                                    $agDateId = $this->row->date('asset_group', $agId)->get('id');
                                    ?>
                                            <tr>
                                                <th class="key">
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <span class="treenode">&#8970;</span> &nbsp;
                                                    <?php echo $agTitle; ?>
                                                </th>
                                                <td>
                                                    <?php
                                                    $labelId = 'dates_' . $i . '_' . $j
                                                        . '_assetgroup_' . $z
                                                        . '_assetgroup_' . $j . '_publish_up';
                                                    ?>
                                                    <label for="<?php echo $labelId; ?>">
                                                        <?php echo Lang::txt('COM_COURSES_FROM'); ?>
                                                    </label>
                                                </th>
                                                <td>
                                                    <input
                                                        type="hidden"
                                                        name="<?php echo $agNamePrefix; ?>[id]"
                                                        value="<?php echo $agDateId; ?>" />
                                                    <input
                                                        type="hidden"
                                                        name="<?php echo $agNamePrefix; ?>[scope]"
                                                        value="asset_group" />
                                                    <input
                                                        type="hidden"
                                                        name="<?php echo $agNamePrefix; ?>[scope_id]"
                                                        value="<?php echo $agId; ?>" />
                                                    <?php
                                                    $agUp = $ag->get('publish_up');
                                                    $agtUp = $agt->get('publish_up');
                                                    $agUpVal = (!$agUp || $agUp == $agtUp || $agUp == $nullDate)
                                                        ? '' : Date::of($agUp)->toLocal('Y-m-d H:i:s');
                                                    $fieldId = 'dates_' . $i . '_assetgroup_' . $z
                                                        . '_assetgroup_' . $j . '_publish_up';
                                                    ?>
                                                    <input
                                                        type="text"
                                                        name="<?php echo $agNamePrefix; ?>[publish_up]"
                                                        id="<?php echo $fieldId; ?>"
                                                        class="datetime-field"
                                                        value="<?php echo $agUpVal; ?>" />
                                                </td>
                                                <td>
                                                    <?php
                                                    $labelId = 'dates_' . $i . '_' . $j
                                                        . '_assetgroup_' . $z
                                                        . '_assetgroup_' . $j . '_publish_up';
                                                    ?>
                                                    <label for="<?php echo $labelId; ?>">
                                                        <?php echo Lang::txt('COM_COURSES_TO'); ?>
                                                    </label>
                                                </th>
                                                <td>
                                                    <?php
                                                    $agDown = $ag->get('publish_down');
                                                    $agtDown = $agt->get('publish_down');
                                                    $noDown = (!$agDown || $agDown == $agtDown || $agDown == $nullDate);
                                                    $agDownVal = $noDown
                                                        ? '' : Date::of($agDown)->toLocal('Y-m-d H:i:s');
                                                    $fieldId = 'dates_' . $i . '_assetgroup_' . $z
                                                        . '_assetgroup_' . $j . '_publish_down';
                                                    ?>
                                                    <input
                                                        type="text"
                                                        name="<?php echo $agNamePrefix; ?>[publish_down]"
                                                        id="<?php echo $fieldId; ?>"
                                                        class="datetime-field"
                                                        value="<?php echo $agDownVal; ?>" />
                                                </td>
                                            </tr>

                                    <?php

                                    if ($ag->assets()->total()) {
                                        $k = 0;
                                        foreach ($ag->assets() as $a) {
                                            $aId = $a->get('id');
                                            $aPubUp = $this->row->date('asset', $aId)->get('publish_up');
                                            $aPubDown = $this->row->date('asset', $aId)->get('publish_down');
                                            $a->set('publish_up', $aPubUp);
                                            $a->set('publish_down', $aPubDown);

                                            if ($a->get('publish_up') == $nullDate) {
                                                $a->set('publish_up', $ag->get('publish_up'));
                                            }
                                            if ($a->get('publish_down') == $nullDate) {
                                                $a->set('publish_down', $ag->get('publish_down'));
                                            }
                                            $aTitle = $this->escape(stripslashes($a->get('title')));
                                            $aNamePrefix = $agNamePrefix . '[asset][' . $k . ']';
                                            $aDateId = $this->row->date('asset', $aId)->get('id');
                                            $aLabelId = 'dates_' . $i
                                                . '_assetgroup_' . $z
                                                . '_assetgroup_' . $j
                                                . 'asset_' . $k . '_publish_up';
                                            ?>
                                                    <?php
                                                    $sp = str_repeat('&nbsp;', 15);
                                                    ?>
                                                    <tr>
                                                        <th class="key">
                                                            <?php echo $sp; ?>
                                                            <span class="treenode">&#8970;</span> &nbsp;
                                                            <?php echo $aTitle; ?>
                                                        </th>
                                                        <td>
                                                            <label for="<?php echo $aLabelId; ?>">
                                                                <?php echo Lang::txt('COM_COURSES_FROM'); ?>
                                                            </label>
                                                        </th>
                                                        <td>
                                                            <input
                                                                type="hidden"
                                                                name="<?php echo $aNamePrefix; ?>[id]"
                                                                value="<?php echo $aDateId; ?>" />
                                                            <input
                                                                type="hidden"
                                                                name="<?php echo $aNamePrefix; ?>[scope]"
                                                                value="asset" />
                                                            <input
                                                                type="hidden"
                                                                name="<?php echo $aNamePrefix; ?>[scope_id]"
                                                                value="<?php echo $aId; ?>" />
                                                            <?php
                                                            $aUp = $a->get('publish_up');
                                                            $agUp2 = $ag->get('publish_up');
                                                            $aUpVal = (!$aUp || $aUp == $agUp2 || $aUp == $nullDate)
                                                                ? '' : Date::of($aUp)->toLocal('Y-m-d H:i:s');
                                                            $aFieldUpId = 'dates_' . $i
                                                                . '_assetgroup_' . $z
                                                                . '_assetgroup_' . $j
                                                                . 'asset_' . $k . '_publish_up';
                                                            ?>
                                                            <input
                                                                type="text"
                                                                name="<?php echo $aNamePrefix; ?>[publish_up]"
                                                                id="<?php echo $aFieldUpId; ?>"
                                                                class="datetime-field"
                                                                value="<?php echo $aUpVal; ?>" />
                                                        </td>
                                                        <td>
                                                            <label for="<?php echo $aLabelId; ?>">
                                                                <?php echo Lang::txt('COM_COURSES_TO'); ?>
                                                            </label>
                                                        </th>
                                                        <td>
                                                            <?php
                                                            $aDown = $a->get('publish_down');
                                                            $agDown2 = $ag->get('publish_down');
                                                            $noDown = (!$aDown
                                                                || $aDown == $agDown2
                                                                || $aDown == $nullDate);
                                                            $aDownVal = $noDown
                                                                ? '' : Date::of($aDown)->toLocal('Y-m-d H:i:s');
                                                            $aFieldDownId = 'dates_' . $i
                                                                . '_assetgroup_' . $z
                                                                . '_assetgroup_' . $j
                                                                . 'asset_' . $k . '_publish_down';
                                                            ?>
                                                            <input
                                                                type="text"
                                                                name="<?php echo $aNamePrefix; ?>[publish_down]"
                                                                id="<?php echo $aFieldDownId; ?>"
                                                                class="datetime-field"
                                                                value="<?php echo $aDownVal; ?>" />
                                                        </td>
                                                    </tr>

                                            <?php
                                            $k++;
                                        }
                                    }
                                    $j++;
                                }
                                if ($agt->assets()->total()) {
                                    $k = 0;
                                    foreach ($agt->assets() as $a) {
                                        $aId = $a->get('id');
                                        $aPubUp = $this->row->date('asset', $aId)->get('publish_up');
                                        $aPubDown = $this->row->date('asset', $aId)->get('publish_down');
                                        $a->set('publish_up', $aPubUp);
                                        $a->set('publish_down', $aPubDown);

                                        if ($a->get('publish_up') == $nullDate) {
                                            $a->set('publish_up', $agt->get('publish_up'));
                                        }
                                        if ($a->get('publish_down') == $nullDate) {
                                            $a->set('publish_down', $agt->get('publish_down'));
                                        }
                                        $aTitle = $this->escape(stripslashes($a->get('title')));
                                        $aNamePrefix = $namePrefix . '[asset][' . $k . ']';
                                        $aDateId = $this->row->date('asset', $aId)->get('id');
                                        $aLabelId = 'dates_' . $i
                                            . '_assetgroup_' . $z
                                            . '_asset_' . $k . '_publish_up';
                                        ?>
                                                <tr>
                                                    <th class="key">
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <span class="treenode">&#8970;</span> &nbsp;
                                                        <?php echo $aTitle; ?>
                                                    </th>
                                                    <td>
                                                        <label for="<?php echo $aLabelId; ?>">
                                                            <?php echo Lang::txt('COM_COURSES_FROM'); ?>
                                                        </label>
                                                    </th>
                                                    <td>
                                                        <input
                                                            type="hidden"
                                                            name="<?php echo $aNamePrefix; ?>[id]"
                                                            value="<?php echo $aDateId; ?>" />
                                                        <input
                                                            type="hidden"
                                                            name="<?php echo $aNamePrefix; ?>[scope]"
                                                            value="asset" />
                                                        <input
                                                            type="hidden"
                                                            name="<?php echo $aNamePrefix; ?>[scope_id]"
                                                            value="<?php echo $aId; ?>" />
                                                        <?php
                                                        $aUp = $a->get('publish_up');
                                                        $agtUp2 = $agt->get('publish_up');
                                                        $aUpVal = (!$aUp || $aUp == $agtUp2 || $aUp == $nullDate)
                                                            ? '' : Date::of($aUp)->toLocal('Y-m-d H:i:s');
                                                        $aFieldUpId = 'dates_' . $i
                                                            . '_assetgroup_' . $z
                                                            . '_asset_' . $k . '_publish_up';
                                                        ?>
                                                        <input
                                                            type="text"
                                                            name="<?php echo $aNamePrefix; ?>[publish_up]"
                                                            id="<?php echo $aFieldUpId; ?>"
                                                            class="datetime-field"
                                                            value="<?php echo $aUpVal; ?>" />
                                                    </td>
                                                    <td>
                                                        <label for="<?php echo $aLabelId; ?>">
                                                            <?php echo Lang::txt('COM_COURSES_TO'); ?>
                                                        </label>
                                                    </th>
                                                    <td>
                                                        <?php
                                                        $aDown = $a->get('publish_down');
                                                        $agtDown2 = $agt->get('publish_down');
                                                        $noDown = (!$aDown
                                                            || $aDown == $agtDown2
                                                            || $aDown == $nullDate);
                                                        $aDownVal = $noDown
                                                            ? '' : Date::of($aDown)->toLocal('Y-m-d H:i:s');
                                                        $aFieldDownId = 'dates_' . $i
                                                            . '_assetgroup_' . $z
                                                            . '_asset_' . $k . '_publish_down';
                                                        ?>
                                                        <input
                                                            type="text"
                                                            name="<?php echo $aNamePrefix; ?>[publish_down]"
                                                            id="<?php echo $aFieldDownId; ?>"
                                                            class="datetime-field"
                                                            value="<?php echo $aDownVal; ?>" />
                                                    </td>
                                                </tr>

                                        <?php
                                        $k++;
                                    }
                                }
                                $z++;
                            }
                            if ($unit->assets()->total()) {
                                $k = 0;
                                foreach ($unit->assets() as $a) {
                                    $aId = $a->get('id');
                                    $aPubUp = $this->row->date('asset', $aId)->get('publish_up');
                                    $aPubDown = $this->row->date('asset', $aId)->get('publish_down');
                                    $a->set('publish_up', $aPubUp);
                                    $a->set('publish_down', $aPubDown);

                                    if ($a->get('publish_up') == $nullDate) {
                                        $a->set('publish_up', $unit->get('publish_up'));
                                    }
                                    if ($a->get('publish_down') == $nullDate) {
                                        $a->set('publish_down', $unit->get('publish_down'));
                                    }
                                    $aTitle = $this->escape(stripslashes($a->get('title')));
                                    $aNamePrefix = 'dates[' . $i . '][asset][' . $k . ']';
                                    $aDateId = $this->row->date('asset', $aId)->get('id');
                                    $aLabelId = 'dates_' . $i . '_asset_' . $k . '_publish_up';
                                    ?>
                                            <tr>
                                                <th class="key">
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <span class="treenode">&#8970;</span> &nbsp;
                                                    <?php echo $aTitle; ?>
                                                </th>
                                                <td>
                                                    <label for="<?php echo $aLabelId; ?>">
                                                        <?php echo Lang::txt('COM_COURSES_FROM'); ?>
                                                    </label>
                                                </th>
                                                <td>
                                                    <input
                                                        type="hidden"
                                                        name="<?php echo $aNamePrefix; ?>[id]"
                                                        value="<?php echo $aDateId; ?>" />
                                                    <input
                                                        type="hidden"
                                                        name="<?php echo $aNamePrefix; ?>[scope]"
                                                        value="asset" />
                                                    <input
                                                        type="hidden"
                                                        name="<?php echo $aNamePrefix; ?>[scope_id]"
                                                        value="<?php echo $aId; ?>" />
                                                    <?php
                                                    $aUp = $a->get('publish_up');
                                                    $unitUp2 = $unit->get('publish_up');
                                                    $aUpVal = (!$aUp || $aUp == $unitUp2 || $aUp == $nullDate)
                                                        ? '' : Date::of($aUp)->toLocal('Y-m-d H:i:s');
                                                    $aFieldUpId = 'dates_' . $i . '_asset_' . $k . '_publish_up';
                                                    ?>
                                                    <input
                                                        type="text"
                                                        name="<?php echo $aNamePrefix; ?>[publish_up]"
                                                        id="<?php echo $aFieldUpId; ?>"
                                                        class="datetime-field"
                                                        value="<?php echo $aUpVal; ?>" />
                                                </td>
                                                <td>
                                                    <label for="<?php echo $aLabelId; ?>">
                                                        <?php echo Lang::txt('COM_COURSES_TO'); ?>
                                                    </label>
                                                </th>
                                                <td>
                                                    <?php
                                                    $aDown = $a->get('publish_down');
                                                    $unitDown2 = $unit->get('publish_down');
                                                    $aDownVal = (!$aDown || $aDown == $unitDown2 || $aDown == $nullDate)
                                                        ? '' : Date::of($aDown)->toLocal('Y-m-d H:i:s');
                                                    $aFieldDownId = 'dates_' . $i . '_asset_' . $k . '_publish_down';
                                                    ?>
                                                    <input
                                                        type="text"
                                                        name="<?php echo $aNamePrefix; ?>[publish_down]"
                                                        id="<?php echo $aFieldDownId; ?>"
                                                        class="datetime-field"
                                                        value="<?php echo $aDownVal; ?>" />
                                                </td>
                                            </tr>

                                    <?php
                                    $k++;
                                }
                            }
                            ?>
                                </tbody>
                            </table>
                        <?php
                        $i++;
                }

                echo Html::sliders('end');
                ?>
                <!-- </fieldset> -->
            </div>
            <div class="clr"></div>
        <?php } else { ?>
            <?php $noDates = Lang::txt('COM_COURSES_NO_DATES_FOUND'); ?>
            <p class="warning"><?php echo $noDates; ?></p>
        <?php } ?>
        </div>
        <div id="page-badge" class="tab">
            <?php $certificate = $this->course->certificate();
            if ($certificate->exists() && $certificate->hasFile()) { ?>
                <fieldset class="adminform">
                    <?php $certFieldTxt = Lang::txt('COM_COURSES_FIELDSET_CERTIFICATE'); ?>
                    <legend><span><?php echo $certFieldTxt; ?></span></legend>

                    <?php $certHint = Lang::txt('COM_COURSES_CERTIFICATE_AVAILABLE_EXPLANATION'); ?>
                    <div class="input-wrap" data-hint="<?php echo $certHint; ?>">
                        <?php $certTxt = Lang::txt('COM_COURSES_CERTIFICATE_AVAILABLE'); ?>
                        <label for="params-certificate">
                            <?php echo $certTxt; ?>:
                        </label><br />
                        <select name="params[certificate]" id="params-certificate">
                            <?php $cert = $params->get('certificate', 0); ?>
                            <?php $sel = ($cert == 0) ? 'selected="selected"' : ''; ?>
                            <option value="0"<?php echo $sel; ?>>
                                <?php echo Lang::txt('COM_COURSES_CERTIFICATE_AVAILABLE_NO'); ?>
                            </option>
                            <?php $sel = ($cert == 1) ? 'selected="selected"' : ''; ?>
                            <option value="1"<?php echo $sel; ?>>
                                <?php echo Lang::txt('COM_COURSES_CERTIFICATE_AVAILABLE_YES'); ?>
                            </option>
                        </select>
                        <span class="hint"><?php echo $certHint; ?></span>
                    </div>
                </fieldset>
            <?php } else { ?>
                <input
                    type="hidden"
                    name="params[certificate]"
                    value="0" />
            <?php } ?>

            <fieldset class="adminform">
                <?php $badgeTxt = Lang::txt('COM_COURSES_FIELDSET_BADGE'); ?>
                <legend><span><?php echo $badgeTxt; ?></span></legend>
                <?php if (!$this->badge->get('id') || !$this->badge->get('provider_badge_id')) : ?>
                    <?php $badgeId = $this->badge->get('id'); ?>
                    <input
                        type="hidden"
                        name="badge[id]"
                        value="<?php echo $badgeId; ?>" />
                    <table class="admintable">
                        <tbody>
                            <tr>
                                <?php $enabledTxt = Lang::txt('COM_COURSES_FIELD_BADGE_ENABLED'); ?>
                                <th class="key" width="250">
                                    <label for="badge-published">
                                        <?php echo $enabledTxt; ?>:
                                    </label>
                                </th>
                                <td>
                                    <?php $pubChecked = ($this->badge->get('published')) ? 'checked="checked"' : ''; ?>
                                    <input
                                        type="checkbox"
                                        name="badge[published]"
                                        id="badge-published"
                                        value="1"
                                        <?php echo $pubChecked; ?> />
                                </td>
                            </tr>
                            <tr class="badge-field-toggle">
                                <?php $imgTxt = Lang::txt('COM_COURSES_FIELD_BADGE_IMAGE'); ?>
                                <th class="key">
                                    <label for="badge-image">
                                        <?php echo $imgTxt; ?>:
                                    </label>
                                </th>
                                <td>
                                    <?php if ($this->badge->get('img_url')) : ?>
                                        <?php echo $this->escape(stripslashes($this->badge->get('img_url'))); ?>
                                        <input
                                            type="file"
                                            name="badge_image"
                                            id="badge-image" />
                                    <?php else : ?>
                                        <input
                                            type="file"
                                            name="badge_image"
                                            id="badge-image" />
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr class="badge-field-toggle">
                                <?php $provTxt = Lang::txt('COM_COURSES_FIELD_BADGE_PROVIDER'); ?>
                                <th class="key">
                                    <label for="badge-provider">
                                        <?php echo $provTxt; ?>:
                                    </label>
                                </th>
                                <td>
                                    <select name="badge[provider_name]" id="badge-provider">
                                        <?php
                                        $sel = ($this->badge->get('provider_name', 'passport') == 'passport')
                                        ? ' selected="selected"' : '';
                                        ?>
                                <option
                                    value="passport"
                                    <?php echo $sel; ?>
                                >Passport</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="badge-field-toggle">
                                <?php $critTxt = Lang::txt('COM_COURSES_FIELD_BADGE_CRITERIA'); ?>
                                <th class="key">
                                    <label for="badge-criteria">
                                        <?php echo $critTxt; ?>:
                                    </label>
                                </th>
                                <td>
                                    <?php
                                        $criteriaVal = $this->escape(
                                            stripslashes($this->badge->get('criteria_text'))
                                        );
                                    echo $this->editor(
                                        'badge[criteria]',
                                        $criteriaVal,
                                        50,
                                        10,
                                        'badge-criteria'
                                    );
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php else : ?>
                    <?php $badgeId = $this->badge->get('id'); ?>
                    <input
                        type="hidden"
                        name="badge[id]"
                        value="<?php echo $badgeId; ?>" />
                    <table class="admintable">
                        <tbody>
                            <tr>
                                <?php $enabledTxt = Lang::txt('COM_COURSES_FIELD_BADGE_ENABLED'); ?>
                                <th class="key" width="250">
                                    <label for="badge-published">
                                        <?php echo $enabledTxt; ?>:
                                    </label>
                                </th>
                                <td>
                                    <?php $pubChecked = ($this->badge->get('published')) ? 'checked="checked"' : ''; ?>
                                    <input
                                        type="checkbox"
                                        name="badge[published]"
                                        id="badge-published"
                                        value="1"
                                        <?php echo $pubChecked; ?> />
                                </td>
                            </tr>
                            <tr class="badge-field-toggle">
                                <?php $imgTxt = Lang::txt('COM_COURSES_FIELD_BADGE_IMAGE'); ?>
                                <th class="key">
                                    <label for="badge-image">
                                        <?php echo $imgTxt; ?>:
                                    </label>
                                </th>
                                <td>
                                    <?php $imgUrl = $this->badge->get('img_url'); ?>
                                    <img
                                        src="<?php echo $imgUrl; ?>"
                                        width="125" />
                                    <label for="badge-image" class="label clearfix">
                                        Image File
                                    </label>
                                    <input
                                        type="file"
                                        name="badge_image"
                                        id="badge-image" />
                                    <p class="note clearfix">
                                        <strong>NOTE:</strong>
                                        Selecting a new image file will overwrite
                                        the image above when you save your changes.
                                    </p>
                                </td>
                            </tr>
                            <tr class="badge-field-toggle">
                                <?php $provTxt = Lang::txt('COM_COURSES_FIELD_BADGE_PROVIDER'); ?>
                                <th class="key">
                                    <label for="badge-provider">
                                        <?php echo $provTxt; ?>:
                                    </label>
                                </th>
                                <td>
                                    <?php
                                    $provName = $this->escape(
                                        stripslashes($this->badge->get('provider_name'))
                                    );
                                    echo $provName;
                                    ?>
                                </td>
                            </tr>
                            <tr class="badge-field-toggle">
                                <?php $critTxt = Lang::txt('COM_COURSES_FIELD_BADGE_CRITERIA'); ?>
                                <th class="key">
                                    <label for="badge-criteria">
                                        <?php echo $critTxt; ?>:
                                    </label>
                                </th>
                                <td>
                                    <?php
                                        $criteriaVal = $this->escape(
                                            stripslashes($this->badge->get('criteria_text'))
                                        );
                                    echo $this->editor(
                                        'badge[criteria]',
                                        $criteriaVal,
                                        35,
                                        5,
                                        'badge-criteria'
                                    );
                                    ?>
                                    <?php
                                    $criteriaUrl = Request::base(true) . '/courses/badge/'
                                    . $this->badge->get('id') . '/criteria';
                                    $criteriaTxt = Lang::txt('COM_COURSES_FIELD_BADGE_CRITERIA');
                                    ?>
                            <a
                                rel="noopener"
                                target="_blank"
                                href="<?php echo $criteriaUrl; ?>"
                            ><?php echo $criteriaTxt; ?></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>
            </fieldset>
        </div>
    </div>

    <?php echo Html::input('token'); ?>
</form>
