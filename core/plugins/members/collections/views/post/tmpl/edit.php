<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Event;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

$item = $this->entry->item();

if (!$this->entry->exists()) {
    $this->entry->set('original', 1);
}

//tag editor
$tf = Event::trigger('hubzero.onGetMultiEntry', [['tags', 'tags', 'actags', '', $item->tags('string')]]);

$type = 'file'; //strtolower(Request::getWord('type', $item->get('type')));
if (!$type) {
    $type = 'file';
}
if ($type && !in_array($type, ['file', 'image', 'text', 'link'])) {
    $type = 'link';
}

$base = $this->member->link() . '&active=' . $this->name;

$dir = $item->get('id');
if (!$dir) {
    $dir = 'tmp' . time(); // . rand(0, 100);
}

$jbase = rtrim(Request::base(true), '/');

$this
    ->css()
    ->js('jquery.fileuploader.js', 'system')
    ->js('fileupload.js')
    ->js();

$view = $this;

// The attribute that marks the current value in the form fields below
$idSelected = function ($value) use ($view) {
    if ($view->collection->get('id') == $value) {
        return ' selected="selected"';
    }

    return '';
};
?>

<?php if ($this->getError()) { ?>
    <p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<form action="<?= Route::url($base . '&task=post/save' . ($this->no_html ? '&no_html=' . $this->no_html : '')) ?>"
    method="post" id="hubForm" class="full" enctype="multipart/form-data">
    <fieldset>
        <legend>
            <?= $item->get('id')
                ? (
                    $this->entry->get('original')
                        ? Lang::txt('PLG_MEMBERS_COLLECTIONS_EDIT_POST')
                        : Lang::txt('PLG_MEMBERS_COLLECTIONS_EDIT_REPOST')
                )
                : Lang::txt('PLG_MEMBERS_COLLECTIONS_NEW_POST') ?>
        </legend>

        <?php if ($this->entry->get('original')) { ?>
            <div class="field-wrap">
                <div class="asset-uploader">
                    <div class="grid">
                        <div class="col span-half">
                            <?php
                            // echo &amp;dir=$dir;
                            // echo $dir;
                            ?>
                            <?php
                            $dataActionUrl = $jbase . '/index.php?option=com_collections&amp;no_html=1'
                                . '&amp;controller=media&amp;task=upload';
                            $dataList = $jbase . '/index.php?option=com_collections&amp;no_html=1&amp;controller=media'
                                . '&amp;task=list&amp;dir=';
                            ?>
                            <div id="ajax-uploader" data-txt-instructions="<?= Lang::txt('PLG_MEMBERS_COLLECTIONS_CLICK_OR_DROP_FILE') ?>" data-action="<?= $dataActionUrl ?>" data-list="<?= $dataList ?>">
                                <noscript>
                                    <div class="form-group">
                                        <label for="upload">
                                            <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_ADD_FILE'); ?>
                                            <input type="file" name="upload" id="upload" class="form-control-file" />
                                        </label>
                                    </div>
                                </noscript>
                            </div>
                        </div><!-- / .col span-half -->
                        <div class="col span-half omega">
                            <?php
                            // echo $dir;
                            // echo $dir;
                            ?>
                            <?php
                            $dataActionUrl = $jbase . '/index.php?option=com_collections&amp;no_html=1'
                                . '&amp;controller=media&amp;task=create&amp;dir=';
                            $dataList = $jbase . '/index.php?option=com_collections&amp;no_html=1&amp;controller=media'
                                . '&amp;task=list&amp;dir=';
                            ?>
                            <div id="link-adder" data-base="<?= rtrim(Request::base(true), '/') ?>" data-txt-delete="<?= Lang::txt('JACTION_DELETE') ?>" data-txt-instructions="<?= Lang::txt('PLG_MEMBERS_COLLECTIONS_CLICK_TO_ADD_LINK') ?>" data-action="<?= $dataActionUrl ?>" data-list="<?= $dataList ?>">
                                <noscript>
                                    <div class="form-group">
                                        <label for="add-link">
                                            <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_ADD_LINK'); ?>
                                            <input type="text" name="assets[-1][filename]" id="add-link"
                                                class="form-control" value="http://" />
                                            <input type="hidden" name="assets[-1][id]" value="0" />
                                            <input type="hidden" name="assets[-1][type]" value="link" />
                                        </label>
                                    </div>
                                </noscript>
                            </div>
                        </div><!-- / .col span-half -->
                    </div>
                </div><!-- / .asset-uploader -->
            </div><!-- / .field-wrap -->
        <?php } ?>

        <div id="post-type-form">
            <div id="post-file" class="fieldset">

                <?php if ($this->entry->get('original')) { ?>
                    <div class="field-wrap" id="ajax-uploader-list">
                    <?php
                        $assets = $item->assets();
                    if ($assets->total() > 0) {
                        $i = 0;
                        foreach ($assets as $asset) {
                            ?>
                        <p class="item-asset">
                            <span class="asset-handle">
                            </span>
                            <span class="asset-file">
                            <?php if ($asset->get('type') == 'link') { ?>
                                <input type="text" name="assets[<?= $i ?>][filename]" size="35"
                                    value="<?= $this->escape(stripslashes($asset->get('filename'))) ?>"
                                    placeholder="http://" />
                            <?php } else { ?>
                                <?php echo $this->escape(stripslashes($asset->get('filename'))); ?>
                                <input type="hidden" name="assets[<?= $i ?>][filename]"
                                    value="<?= $this->escape(stripslashes($asset->get('filename'))) ?>" />
                            <?php } ?>
                            </span>
                            <span class="asset-description">
                                <input type="hidden" name="assets[<?= $i ?>][type]"
                                    value="<?= $this->escape(stripslashes($asset->get('type'))) ?>" />
                                <input type="hidden" name="assets[<?= $i ?>][id]"
                                    value="<?= $this->escape($asset->get('id')) ?>" />
                            <?php // The asset hangs off the item, so removing it removes it from
                                  // every repost too. The controller allows that only to the item's
                                  // owner; offering the link to anyone else is a 403 waiting to happen.
                            if ($item->get('created_by') == User::get('id')) { ?>
                                <?php
                                $postUrl = Route::url(
                                    $base . '&task=post/' . $this->entry->get('id') . '/edit&remove='
                                        . $asset->get('id'),
                                );
                                ?>
                                <button type="submit" form="hz-post-action" formaction="<?= $postUrl ?>" class="delete"
                                    data-id="<?= $this->escape($asset->get('id')) ?>"
                                    title="<?= Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE') ?>">
                                    <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE'); ?>
                                </button>
                            <?php } ?>
                                <!-- <input type="text" name="assets[<?= $i ?>][description]" size="35"
                                    value="<?= $this->escape(stripslashes($asset->get('description'))) ?>"
                                    placeholder="Brief description" /> -->
                            </span>
                        </p>
                            <?php
                            $i++;
                        }
                    }
                    ?>
                    </div><!-- / .field-wrap -->

                    <div class="form-group">
                        <label for="field-title">
                            <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_TITLE'); ?>
                            <input type="text" name="fields[title]" id="field-title" class="form-control"
                                value="<?= $this->escape(stripslashes($item->get('title', ''))) ?>" />
                        </label>
                    </div>
                    <input type="hidden" name="fields[type]" value="file" />
                <?php } else { ?>
                    <div class="form-group">
                        <label for="field-title">
                            <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_TITLE'); ?>
                            <input type="text" name="fieldstitle" id="field-title" class="form-control disabled"
                                disabled="disabled"
                                value="<?= $this->escape(stripslashes($item->get('title', ''))) ?>" />
                        </label>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <label for="field_description">
                        <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_DESCRIPTION'); ?>
                        <?php if ($this->entry->get('original')) { ?>
                            <?= $this->editor(
                                'fields[description]',
                                $this->escape(stripslashes($item->description('raw'))),
                                35,
                                5,
                                'field_description',
                                ['class' => 'form-control minimal no-footer'],
                            ) ?>
                        <?php } else { ?>
                            <?= $this->editor(
                                'post[description]',
                                $this->escape(stripslashes($this->entry->description('raw'))),
                                35,
                                5,
                                'field_description',
                                ['class' => 'form-control minimal no-footer'],
                            ) ?>
                        <?php } ?>
                    </label>
                </div>

                <?php if ($this->task == 'save' && !$item->get('description')) { ?>
                    <p class="error">
                        <?= Lang::txt('PLG_MEMBERS_' . strtoupper($this->name) . '_ERROR_PROVIDE_CONTENT') ?>
                    </p>
                <?php } ?>
            </div><!-- / #post-file -->
        </div><!-- / #post-type-form -->

    <?php if ($this->entry->get('original')) { ?>
        <div class="grid">
            <div class="col span6">
    <?php } ?>

        <?php if ($this->collections->total() > 0) { ?>
            <div class="form-group">
                <label for="post-collection_id">
                    <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_SELECT_COLLECTION'); ?>
                    <select name="post[collection_id]" id="post-collection_id" class="form-control">
                    <?php foreach ($this->collections as $collection) { ?>
                        <?php
                        $fieldValue = $this->escape($collection->get('id'));
                        $selectedAttr = $idSelected($collection->get('id'));
                        $optionText = $this->escape(stripslashes($collection->get('title')));
                        ?>
                        <option value="<?= $fieldValue ?>"<?= $selectedAttr ?>><?= $optionText ?></option>
                    <?php } ?>
                    </select>
                    <span class="hint"><?= Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_SELECT_COLLECTION_HINT') ?></span>
                </label>
            </div>
        <?php } else { ?>
            <div class="form-group">
                <label for="post-collection_title">
                    <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_CREATE_COLLECTION'); ?>
                    <input type="text" name="collection_title" id="post-collection_title" class="form-control"
                        value="" />
                    <span class="hint"><?= Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_CREATE_COLLECTION_HINT') ?></span>
                </label>
            </div>
        <?php } ?>

    <?php if ($this->entry->get('original')) { ?>
            </div>
            <div class="col span6 omega">
                <div class="form-group">
                    <label for="actags">
                        <?php echo Lang::txt('PLG_MEMBERS_' . strtoupper($this->name) . '_FIELD_TAGS'); ?>
                        <?php
                        if (count($tf) > 0) {
                            echo $tf[0];
                        } else { ?>
                            <input type="text" name="tags" id="actags" class="form-control"
                                value="<?= $this->escape($item->tags('string')) ?>" />
                        <?php } ?>
                        <?php
                        $hintText = Lang::txt('PLG_MEMBERS_' . strtoupper($this->name) . '_FIELD_TAGS_HINT');
                        ?>
                        <span class="hint"><?= $hintText ?></span>
                    </label>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <input type="hidden" name="tags" value="<?php echo $this->escape($item->tags('string')); ?>" />
    <?php } ?>
    </fieldset>

    <input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->escape($item->get('id')); ?>" />
    <input type="hidden" name="fields[created]" value="<?php echo $this->escape($item->get('created')); ?>" />
    <input type="hidden" name="fields[created_by]" value="<?php echo $this->escape($item->get('created_by')); ?>" />
    <input type="hidden" name="fields[dir]" id="field-dir" value="<?php echo $this->escape($dir); ?>" />
    <input type="hidden" name="fields[access]" id="field-access"
        value="<?= $this->escape($item->get('access', 0)) ?>" />

    <input type="hidden" name="post[id]" value="<?php echo $this->escape($this->entry->get('id')); ?>" />
    <input type="hidden" name="post[item_id]" id="post-item_id"
        value="<?= $this->escape($this->entry->get('item_id')) ?>" />

    <input type="hidden" name="id" value="<?php echo $this->member->get('id'); ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="active" value="<?php echo $this->name; ?>" />
    <input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />
    <input type="hidden" name="action" value="save" />

    <?php echo Html::input('token'); ?>

    <p class="submit">
        <input class="btn btn-success" type="submit"
            value="<?= Lang::txt('PLG_MEMBERS_' . strtoupper($this->name) . '_SAVE') ?>" />

        <?php if ($item->get('id')) { ?>
            <a class="btn btn-secondary"
                href="<?= Route::url($base . ($item->get('id') ? '&task=' . $this->collection->get('alias') : '')) ?>">
                <?php echo Lang::txt('JCANCEL'); ?>
            </a>
        <?php } ?>
    </p>
</form>
