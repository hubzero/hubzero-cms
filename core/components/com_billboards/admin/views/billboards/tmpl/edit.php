<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;
use Components\Billboards\Models\Collection;
use Components\Billboards\Models\Billboard;

// No direct access
defined('_HZEXEC_') or die();

// Change title depending on whether or not we're editing or creating a new billboard
$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

// Menu items
Toolbar::title(Lang::txt('COM_BILLBOARDS_MANAGER') . ': ' . $text, 'billboards');
Toolbar::save();
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('billboard');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<?php $formAction = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>
<?php $invalidMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED')); ?>
<form action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    enctype="multipart/form-data"
    class="editform form-validate"
    data-invalid-msg="<?php echo $invalidMsg; ?>"
>
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_BILLBOARDS_CONTENT'); ?></span></legend>

                <div class="input-wrap">
                    <label for="billboardname">
                        <?php echo Lang::txt('COM_BILLBOARDS_FIELD_NAME'); ?>:
                        <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                    </label><br />
                    <?php $nameVal = $this->escape(stripslashes(
                        $this->row->name == null ? '' : $this->row->name
                    )); ?>
                    <input type="text"
                        name="billboard[name]"
                        id="billboardname"
                        class="required"
                        value="<?php echo $nameVal; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <label for="billboardcollection">
                        <?php echo Lang::txt('COM_BILLBOARDS_FIELD_COLLECTION'); ?>:
                    </label><br />
                    <select name="billboard[collection_id]" id="billboardcollection">
                        <?php $collections = Collection::all()->rows(); ?>
                        <?php if ($collections->count() > 0) : ?>
                            <?php foreach ($collections as $collection) : ?>
                                <?php $selected = ($collection->id == $this->row->collection_id)
                                    ? ' selected="selected"'
                                    : ''; ?>
                                <option value="<?php echo $collection->id; ?>"<?php echo $selected; ?>>
                                    <?php echo $collection->name; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <option value="0"><?php echo Lang::txt('Default Collection'); ?></option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="input-wrap">
                    <label for="ordering"><?php echo Lang::txt('COM_BILLBOARDS_FIELD_ORDERING'); ?>:</label><br />
                    <?php if ($this->row->id) : ?>
                        <?php $query = Billboard::select('ordering', 'value')
                            ->select('name', 'text')
                            ->whereEquals('collection_id', $this->row->collection_id)
                            ->toString(); ?>
                        <?php echo Html::select('ordering', 'billboard[ordering]', $query, null, $this->row->id); ?>
                    <?php else : ?>
                        <input type="hidden" name="billboard[ordering]" id="ordering" value="" />
                        <span class="readonly"><?php echo Lang::txt('COM_BILLBOARDS_ASC'); ?></span>
                    <?php endif; ?>
                </div>
                <div class="input-wrap">
                    <label for="billboardheader"><?php echo Lang::txt('COM_BILLBOARDS_FIELD_HEADER'); ?>:</label><br />
                    <?php $headerVal = $this->escape(stripslashes(
                        $this->row->header == null ? '' : $this->row->header
                    )); ?>
                    <input type="text"
                        name="billboard[header]"
                        id="billboardheader"
                        value="<?php echo $headerVal; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <label for="billboard-image">
                        <?php echo Lang::txt('COM_BILLBOARDS_FIELD_BACKGROUND_IMG'); ?>:
                    </label><br />
                    <input type="file" name="billboard-image" id="billboard-image" />
                </div>
                <div class="input-wrap">
                    <label for="billboard[text]"><?php echo Lang::txt('COM_BILLBOARDS_FIELD_TEXT'); ?>:</label><br />
                    <?php $textVal = $this->escape(stripslashes(
                        $this->row->text == null ? '' : $this->row->text
                    )); ?>
                    <?php echo $this->editor(
                        'billboard[text]',
                        $textVal,
                        45,
                        13,
                        'billboard-text',
                        ['buttons' => false]
                    ); ?>
                </div>
            </fieldset>
        </div>
        <div class="col span5">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_BILLBOARDS_LEARN_MORE'); ?></span></legend>
                <div class="input-wrap">
                    <label for="billboardlearnmoretext">
                        <?php echo Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_TEXT'); ?>:
                    </label><br />
                    <?php $learnMoreTextVal = $this->escape(stripslashes(
                        $this->row->learn_more_text == null
                        ? '' : $this->row->learn_more_text
                    )); ?>
                    <input type="text"
                        name="billboard[learn_more_text]"
                        id="billboardlearnmoretext"
                        value="<?php echo $learnMoreTextVal; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <label for="billboardlearnmoretarget">
                        <?php echo Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_TARGET'); ?>:
                    </label><br />
                    <?php $learnMoreTargetVal = $this->escape(stripslashes(
                        $this->row->learn_more_target == null
                        ? '' : $this->row->learn_more_target
                    )); ?>
                    <input type="text"
                        name="billboard[learn_more_target]"
                        id="billboardlearnmoretarget"
                        value="<?php echo $learnMoreTargetVal; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <label for="billboardlearnmoreclass">
                        <?php echo Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_CLASS'); ?>:
                    </label><br />
                    <?php $learnMoreClassVal = $this->escape(stripslashes(
                        $this->row->learn_more_class == null
                        ? '' : $this->row->learn_more_class
                    )); ?>
                    <input type="text"
                        name="billboard[learn_more_class]"
                        id="billboardlearnmoreclass"
                        value="<?php echo $learnMoreClassVal; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <label for="billboardlearnmorelocation">
                        <?php echo Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION'); ?>:
                    </label><br />
                    <?php $loc = $this->row->learn_more_location; ?>
                    <select name="billboard[learn_more_location]"
                        id="billboardlearnmorelocation"
                    >
                        <option value="topleft"
                            <?php echo ($loc == 'topleft')
                                ? 'selected="selected"' : ''; ?>
                        >
                            <?php echo Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_TOP_LEFT'); ?>
                        </option>
                        <option value="topright"
                            <?php echo ($loc == 'topright')
                                ? 'selected="selected"' : ''; ?>
                        >
                            <?php echo Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_TOP_RIGHT'); ?>
                        </option>
                        <option value="bottomleft"
                            <?php echo ($loc == 'bottomleft')
                                ? 'selected="selected"' : ''; ?>
                        >
                            <?php echo Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_BOTTOM_LEFT'); ?>
                        </option>
                        <option value="bottomright"
                            <?php echo ($loc == 'bottomright')
                                ? 'selected="selected"' : ''; ?>
                        >
                            <?php echo Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_BOTTOM_RIGHT'); ?>
                        </option>
                        <option value="relative"
                            <?php echo ($loc == 'relative')
                                ? 'selected="selected"' : ''; ?>
                        >
                            <?php echo Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_RELATIVE'); ?>
                        </option>
                    </select>
                </div>
            </fieldset>
            <?php if ($this->row->get('background_img', false)) : ?>
                <fieldset class="adminform">
                    <legend><span><?php echo Lang::txt('COM_BILLBOARDS_CURRENT_IMG'); ?></span></legend>
                    <?php $imgPath = PATH_ROOT . DS . ltrim($this->row->background_img, DS); ?>
                    <?php $image = new \Hubzero\Image\Processor($imgPath); ?>
                    <?php if (count($image->getErrors()) == 0) : ?>
                        <?php $image->resize(500); ?>
                        <div class="billboard-img">
                            <img src="<?php echo $image->inline(); ?>"
                                alt="billboard image"
                            />
                        </div>
                    <?php endif; ?>
                </fieldset>
            <?php endif; ?>
            <fieldset class="adminform">
                <legend id="styling"><?php echo Lang::txt('COM_BILLBOARDS_STYLING'); ?></legend>
                <br />

                <div id="styling_table">
                    <div class="input-wrap">
                        <label for="billboardalias">
                            <?php echo Lang::txt('COM_BILLBOARDS_FIELD_ALIAS'); ?>:
                        </label><br />
                        <?php $aliasVal = $this->escape(stripslashes(
                            $this->row->alias == null ? '' : $this->row->alias
                        )); ?>
                        <input type="text"
                            name="billboard[alias]"
                            id="billboardalias"
                            value="<?php echo $aliasVal; ?>"
                        />
                    </div>
                    <div class="input-wrap">
                        <label for="billboardpadding">
                            <?php echo Lang::txt('COM_BILLBOARDS_FIELD_PADDING'); ?>:
                        </label><br />
                        <?php $paddingVal = $this->escape(stripslashes(
                            $this->row->padding == null
                            ? '' : $this->row->padding
                        )); ?>
                        <input type="text"
                            name="billboard[padding]"
                            id="billboardpadding"
                            value="<?php echo $paddingVal; ?>"
                        />
                    </div>
                    <div class="input-wrap">
                        <label for="billboardcss">
                            <?php echo Lang::txt('COM_BILLBOARDS_FIELD_CSS'); ?>:
                        </label><br />
                        <?php $cssVal = $this->escape(stripslashes(
                            $this->row->css == null ? '' : $this->row->css
                        )); ?>
                        <textarea name="billboard[css]"
                            id="billboardcss"
                            cols="45"
                            rows="13"
                        ><?php echo $cssVal; ?></textarea>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="billboard[id]" value="<?php echo $this->row->id; ?>" />

    <?php echo Html::input('token'); ?>
</form>
