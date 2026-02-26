<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// no direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Languages\Helpers\Utilities::getActions();

Toolbar::title(Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_EDIT_TITLE'), 'langmanager');

if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
}

// This component does not support Save as Copy

if ($canDo->get('core.edit') && $canDo->get('core.create')) {
    Toolbar::save2new();
}

if (empty($this->item->key)) {
    Toolbar::cancel();
} else {
    Toolbar::cancel('cancel', 'JTOOLBAR_CLOSE');
}
Toolbar::divider();
Toolbar::help('JHELP_EXTENSIONS_LANGUAGE_MANAGER_OVERRIDES_EDIT');

Html::behavior('framework', true);
Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->css('overrider.css')
    ->js('overrider.js')
    ->js();

$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&task=edit&id=' . $this->item->key
);
$invalidMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));
$cacheExpired = (Request::getString('cache_expired')) ? 'expired' : '';
?>

<form
    action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    class="editform form-validate"
    data-invalid-msg="<?php echo $invalidMsg; ?>"
    data-cache_expired="<?php echo $cacheExpired; ?>"
>
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <?php
                if (empty($this->item->key)) {
                    $legendTxt = Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_EDIT_NEW_OVERRIDE_LEGEND');
                } else {
                    $legendTxt = Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_EDIT_EDIT_OVERRIDE_LEGEND');
                }
                ?>
                <legend><span><?php echo $legendTxt; ?></span></legend>

                <?php $keyHint = Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_KEY_DESC'); ?>
                <div class="input-wrap" data-hint="<?php echo $keyHint; ?>">
                    <?php $keyLabel = Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_KEY_LABEL'); ?>
                    <?php $reqLabel = Lang::txt('JOPTION_REQUIRED'); ?>
                    <label for="field-key">
                        <?php echo $keyLabel; ?>: <span class="required"><?php echo $reqLabel; ?></span>
                    </label>
                    <input
                        type="text"
                        name="fields[key]"
                        id="field-key"
                        class="required"
                        size="60"
                        value="<?php echo $this->escape($this->item->key); ?>"
                    />
                </div>

                <?php $overrideHint = Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_OVERRIDE_DESC'); ?>
                <div class="input-wrap" data-hint="<?php echo $overrideHint; ?>">
                    <?php $overrideLabel = Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_OVERRIDE_LABEL'); ?>
                    <label for="field-override"><?php echo $overrideLabel; ?>:</label>
                    <textarea
                        name="fields[override]"
                        id="field-override"
                        rows="5"
                        cols="50"
                    ><?php echo $this->escape($this->item->override); ?></textarea>
                </div>

                <?php if ($this->get('client') == 'administrator') : ?>
                    <?php $bothHint = Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_BOTH_DESC'); ?>
                    <div class="input-wrap" data-hint="<?php echo $bothHint; ?>">
                        <input type="checkbox" name="fields[both]" id="field-both" value="true" />
                        <?php $bothLabel = Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_BOTH_LABEL'); ?>
                        <label for="field-override"><?php echo $bothLabel; ?>:</label>
                    </div>
                <?php endif; ?>

                <?php $langHint = Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_LANGUAGE_DESC'); ?>
                <div class="input-wrap" data-hint="<?php echo $langHint; ?>">
                    <?php $langLabel = Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_LANGUAGE_LABEL'); ?>
                    <label for="field-language"><?php echo $langLabel; ?>:</label>
                    <input
                        type="text"
                        name="fields[language]"
                        id="field-language"
                        size="50"
                        readonly="readonly"
                        value="<?php echo $this->escape($this->item->language); ?>"
                    />
                </div>

                <?php $clientHint = Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_CLIENT_DESC'); ?>
                <div class="input-wrap" data-hint="<?php echo $clientHint; ?>">
                    <?php $clientLabel = Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_CLIENT_LABEL'); ?>
                    <label for="field-client"><?php echo $clientLabel; ?>:</label>
                    <input
                        type="text"
                        name="fields[client]"
                        id="field-client"
                        size="50"
                        readonly="readonly"
                        value="<?php echo $this->escape($this->item->client); ?>"
                    />
                </div>

                <?php $fileHint = Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_FILE_DESC'); ?>
                <div class="input-wrap" data-hint="<?php echo $fileHint; ?>">
                    <?php $fileLabel = Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_FILE_LABEL'); ?>
                    <label for="field-file"><?php echo $fileLabel; ?>:</label>
                    <input
                        type="text"
                        name="fields[file]"
                        id="field-file"
                        size="80"
                        readonly="readonly"
                        value="<?php echo $this->escape($this->item->file); ?>"
                    />
                </div>
            </fieldset>
        </div>

        <div class="col span5">
            <fieldset class="adminform">
                <?php $searchLegend = Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_SEARCH_LEGEND'); ?>
                <legend><span><?php echo $searchLegend; ?></span></legend>

                <p><?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_SEARCH_TIP'); ?></p>

                <div id="refresh-status" class="overrider-spinner">
                    <?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_REFRESHING'); ?>
                </div>

                <?php $searchTypeHint = Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_SEARCHTYPE_DESC'); ?>
                <div class="input-wrap" data-hint="<?php echo $searchTypeHint; ?>">
                    <?php $searchTypeLabel = Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_SEARCHTYPE_LABEL'); ?>
                    <label for="fields_searchtype"><?php echo $searchTypeLabel; ?>:</label>
                    <fieldset id="fields_searchtype" class="radio inputbox">
                        <ul>
                            <li>
                                <input
                                    type="radio"
                                    id="jform_searchtype0"
                                    name="fields[searchtype]"
                                    value="constant"
                                />
                                <?php $constantLabel = Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_SEARCHTYPE_CONSTANT'); ?>
                                <label for="fields_searchtype0"><?php echo $constantLabel; ?></label>
                            </li>
                            <li>
                                <input
                                    type="radio"
                                    id="jform_searchtype1"
                                    name="fields[searchtype]"
                                    value="value"
                                    checked="checked"
                                />
                                <?php $textLabel = Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_SEARCHTYPE_TEXT'); ?>
                                <label for="fields_searchtype1"><?php echo $textLabel; ?></label>
                            </li>
                        </ul>
                    </fieldset>
                </div>
                <div class="input-wrap">
                    <input type="text" name="fields[searchstring]" id="fields_searchstring" size="50" value="" />
                </div>
                <p>
                    <button type="submit" id="searchstrings">
                        <?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_SEARCH_BUTTON'); ?>
                    </button>
                </p>
            </fieldset>

            <fieldset id="results-container" class="adminform">
                <?php $resultsLegend = Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_RESULTS_LEGEND'); ?>
                <legend><span><?php echo $resultsLegend; ?></span></legend>

                <span id="more-results">
                    <a href="javascript:Hubzero.overrider.searchStrings(Hubzero.overrider.states.more);">
                        <?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_MORE_RESULTS'); ?>
                    </a>
                </span>
            </fieldset>

            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="id" value="<?php echo $this->item->key; ?>" />
            <?php echo Html::input('token'); ?>
        </div>
    </div>
</form>
