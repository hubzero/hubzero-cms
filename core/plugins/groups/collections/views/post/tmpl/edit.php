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

// No direct access
defined('_HZEXEC_') or die();

$this->js('jquery.fileuploader.js', 'system');
$this->js('fileupload.js');
$this->js();

$item = $this->entry->item();

if (!$this->entry->exists()) {
    $this->entry->set('original', 1);
}

$type = 'file'; //strtolower(Request::getWord('type', $item->get('type')));
if (!$type) {
    $type = 'file';
}
if ($type && !in_array($type, array('file', 'image', 'text', 'link'))) {
    $type = 'link';
}

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name;

$dir = $item->get('id');
if (!$dir) {
    $dir = 'tmp' . time(); // . rand(0, 100);
}

$jbase = rtrim(Request::base(true), '/');
?>
<?php if ($this->getError()) { ?>
    <p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php
$formAction = Route::url(
    $base . '&scope=post/save'
    . ($this->no_html ? '&no_html=' . $this->no_html : '')
);
?>
<form
    action="<?php echo $formAction; ?>"
    method="post"
    id="hubForm"
    class="full"
    enctype="multipart/form-data"
>
    <fieldset>
        <?php
        if ($item->get('id')) {
            $legendTxt = $this->entry->get('original')
                ? Lang::txt('Edit post')
                : Lang::txt('Edit repost');
        } else {
            $legendTxt = Lang::txt('New post');
        }
        ?>
        <legend><?php echo $legendTxt; ?></legend>

        <?php if ($this->entry->get('original')) { ?>
            <div class="field-wrap">
                <div class="asset-uploader">
                    <div class="grid">
                        <div class="col span-half">
                            <?php
                            $uploaderInstructions = Lang::txt(
                                'Click or drop file'
                            );
                            $uploaderAction = $jbase
                                . '/index.php?option=com_collections'
                                . '&amp;no_html=1'
                                . '&amp;controller=media'
                                . '&amp;task=upload';
                            $uploaderList = '/index.php'
                                . '?option=com_collections'
                                . '&amp;no_html=1'
                                . '&amp;controller=media'
                                . '&amp;task=list'
                                . '&amp;dir=';
                            ?>
                            <div
                                id="ajax-uploader"
                                data-txt-instructions="<?php echo $uploaderInstructions; ?>"
                                data-action="<?php echo $uploaderAction; ?>"
                                data-list="<?php echo $uploaderList; ?>"
                            >
                                <noscript>
                                    <div class="form-group">
                                        <label for="upload">
                                            <?php echo Lang::txt('File:'); ?>
                                            <input type="file" name="upload" id="upload" class="form-control-file" />
                                        </label>
                                    </div>
                                </noscript>
                            </div>
                        </div><!-- / .col span-half -->
                        <div class="col span-half omega">
                            <?php
                            $linkAdderBase = rtrim(
                                Request::base(true),
                                '/'
                            );
                            $linkAdderDelete = Lang::txt(
                                'JACTION_DELETE'
                            );
                            $linkAdderInstr = Lang::txt(
                                'Click to add link'
                            );
                            $linkAdderAction = $jbase
                                . '/index.php'
                                . '?option=com_collections'
                                . '&amp;no_html=1'
                                . '&amp;controller=media'
                                . '&amp;task=create'
                                . '&amp;dir=';
                            $linkAdderList = '/index.php'
                                . '?option=com_collections'
                                . '&amp;no_html=1'
                                . '&amp;controller=media'
                                . '&amp;task=list'
                                . '&amp;dir=';
                            ?>
                            <div
                                id="link-adder"
                                data-base="<?php echo $linkAdderBase; ?>"
                                data-txt-delete="<?php echo $linkAdderDelete; ?>"
                                data-txt-instructions="<?php echo $linkAdderInstr; ?>"
                                data-action="<?php echo $linkAdderAction; ?>"
                                data-list="<?php echo $linkAdderList; ?>"
                            >
                                <noscript>
                                    <div class="form-group">
                                        <label for="add-link">
                                            <?php echo Lang::txt('Add a link:'); ?>
                                            <input type="text"
                                                name="assets[-1][filename]"
                                                id="add-link"
                                                class="form-control"
                                                value="http://"/>
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
                                <input type="text"
                                    name="assets[<?php echo $i; ?>][filename]"
                                    size="35"
                                    value="<?php echo $this->escape(stripslashes($asset->get('filename'))); ?>"
                                    placeholder="http://"/>
                                <?php } else { ?>
                                    <?php echo $this->escape(stripslashes($asset->get('filename'))); ?>
                                <input type="hidden"
                                    name="assets[<?php echo $i; ?>][filename]"
                                    value="<?php echo $this->escape(stripslashes($asset->get('filename'))); ?>"/>
                                <?php } ?>
                            </span>
                            <span class="asset-description">
                                <input type="hidden"
                                    name="assets[<?php echo $i; ?>][type]"
                                    value="<?php echo $this->escape(stripslashes($asset->get('type'))); ?>"/>
                                <input type="hidden"
                                    name="assets[<?php echo $i; ?>][id]"
                                    value="<?php echo $this->escape($asset->get('id')); ?>"/>
                                <?php
                                $deleteDataId = $this->escape(
                                    $asset->get('id')
                                );
                                $deleteHref = Route::url(
                                    $base
                                    . '&scope=post/'
                                    . $this->entry->get('id')
                                    . '/edit&remove='
                                    . $asset->get('id')
                                );
                                $deleteTitle = Lang::txt(
                                    'Delete this asset'
                                );
                                ?>
                                <a
                                    class="delete"
                                    data-id="<?php echo $deleteDataId; ?>"
                                    href="<?php echo $deleteHref; ?>"
                                    title="<?php echo $deleteTitle; ?>"
                                >
                                <?php echo Lang::txt('delete'); ?>
                                </a>
                                <!--
                                <input type="text"
                                    name="assets[<?php echo $i; ?>][description]"
                                    size="35"
                                    value="<?php echo $this->escape(stripslashes($asset->get('description'))); ?>"
                                    placeholder="Brief description" />
                                -->
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
                            <?php echo Lang::txt('Title'); ?>
                            <input type="text"
                                name="fields[title]"
                                id="field-title"
                                class="form-control"
                                size="35"
                                value="<?php echo $this->escape(stripslashes($item->get('title', ''))); ?>"/>
                        </label>
                    </div>
                    <input type="hidden" name="fields[type]" value="file" />
                <?php } else { ?>
                    <div class="form-group">
                        <label for="field-title">
                            <?php echo Lang::txt('Title'); ?>
                            <input type="text"
                                name="fieldstitle"
                                id="field-title"
                                class="form-control disabled"
                                disabled="disabled"
                                value="<?php echo $this->escape(stripslashes($item->get('title'))); ?>"/>
                        </label>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <label for="field_description">
                        <?php echo Lang::txt('Description'); ?>
                        <?php if ($this->entry->get('original')) { ?>
                            <?php
                            $editorContent = $this->escape(
                                stripslashes(
                                    $item->description('raw')
                                )
                            );
                            $editorOptions = array(
                                'class' => 'form-control minimal no-footer'
                            );
                            echo $this->editor(
                                'fields[description]',
                                $editorContent,
                                35,
                                5,
                                'field_description',
                                $editorOptions
                            );
                            ?>
                        <?php } else { ?>
                            <?php
                            $editorContent = $this->escape(
                                stripslashes(
                                    $this->entry->description('raw')
                                )
                            );
                            $editorOptions = array(
                                'class' => 'form-control minimal no-footer'
                            );
                            echo $this->editor(
                                'post[description]',
                                $editorContent,
                                35,
                                5,
                                'field_description',
                                $editorOptions
                            );
                            ?>
                        <?php } ?>
                    </label>
                </div>

                <?php if ($this->task == 'save' && !$item->get('description')) { ?>
                    <?php
                    $errorKey = 'PLG_GROUPS_'
                        . strtoupper($this->name)
                        . '_ERROR_PROVIDE_CONTENT';
                    ?>
                    <p class="error"><?php echo Lang::txt($errorKey); ?></p>
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
                    <?php
                    $selectLabel = Lang::txt(
                        'PLG_GROUPS_COLLECTIONS_SELECT_COLLECTION'
                    );
                    $requiredTxt = Lang::txt('JREQUIRED');
                    ?>
                    <?php echo $selectLabel; ?> <span class="required"><?php echo $requiredTxt; ?></span>
                    <select
                        name="post[collection_id]"
                        id="post-collection_id"
                        class="form-control"
                    >
                    <?php foreach ($this->collections as $collection) { ?>
                        <?php
                        $optionVal = $this->escape(
                            $collection->get('id')
                        );
                        $isSelected = ($this->collection->get('id') == $collection->get('id'))
                            ? ' selected="selected"'
                            : '';
                        $optionTitle = $this->escape(
                            stripslashes(
                                $collection->get('title')
                            )
                        );
                        ?>
                        <option
                            value="<?php echo $optionVal; ?>"<?php echo $isSelected; ?>
                        ><?php echo $optionTitle; ?></option>
                    <?php } ?>
                    </select>
                    <?php
                    $selectHint = Lang::txt(
                        'PLG_GROUPS_COLLECTIONS_SELECT_COLLECTION_HINT'
                    );
                    ?>
                    <span class="hint"><?php echo $selectHint; ?></span>
                </label>
            </div>
        <?php } else { ?>
            <div class="form-group">
                <label for="post-collection_title">
                    <?php
                    $newCollLabel = Lang::txt(
                        'PLG_GROUPS_COLLECTIONS_NEW_COLLECTION'
                    );
                    $requiredTxt = Lang::txt('JREQUIRED');
                    ?>
                    <?php echo $newCollLabel; ?> <span class="required"><?php echo $requiredTxt; ?></span>
                    <input type="text"
                        name="collection_title"
                        id="post-collection_title"
                        class="form-control"
                        value=""/>
                    <?php
                    $newCollHint = Lang::txt(
                        'PLG_GROUPS_COLLECTIONS_NEW_COLLECTION_HINT'
                    );
                    ?>
                    <span class="hint"><?php echo $newCollHint; ?></span>
                </label>
            </div>
        <?php } ?>

    <?php if ($this->entry->get('original')) { ?>
            </div>
            <div class="col span6 omega">
                <div class="form-group">
                    <label for="actags">
                        <?php
                        $tagsKey = 'PLG_GROUPS_'
                            . strtoupper($this->name)
                            . '_FIELD_TAGS';
                        $tagsHintKey = 'PLG_GROUPS_'
                            . strtoupper($this->name)
                            . '_FIELD_TAGS_HINT';
                        $tagsValue = $this->escape(
                            $item->tags('string')
                        );
                        ?>
                        <?php echo Lang::txt($tagsKey); ?>
                        <?php echo $this->autocompleter('tags', 'tags', $tagsValue, 'actags'); ?>
                        <span class="hint"><?php echo Lang::txt($tagsHintKey); ?></span>
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
    <input type="hidden"
        name="fields[access]"
        id="field-access"
        value="<?php echo $this->escape($item->get('access', 0)); ?>"/>

    <input type="hidden" name="post[id]" value="<?php echo $this->escape($this->entry->get('id')); ?>" />
    <input type="hidden"
        name="post[item_id]"
        id="post-item_id"
        value="<?php echo $this->escape($this->entry->get('item_id')); ?>"/>

    <input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="active" value="<?php echo $this->name; ?>" />
    <input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />
    <input type="hidden" name="action" value="save" />

    <?php echo Html::input('token'); ?>

    <p class="submit">
        <input class="btn btn-success"
            type="submit"
            value="<?php echo Lang::txt('PLG_GROUPS_' . strtoupper($this->name) . '_SAVE'); ?>"/>

        <?php if ($item->get('id')) { ?>
            <?php
            $cancelScope = $item->get('id')
                ? '&scope=' . $this->collection->get('alias')
                : '';
            $cancelHref = Route::url($base . $cancelScope);
            ?>
            <a
                class="btn btn-secondary"
                href="<?php echo $cancelHref; ?>"
            >
                <?php echo Lang::txt('Cancel'); ?>
            </a>
        <?php } ?>
    </p>
</form>
