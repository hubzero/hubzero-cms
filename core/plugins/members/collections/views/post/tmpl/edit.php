<?php

// @phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$item = $this->entry->item();

if (!$this->entry->exists()) {
    $this->entry->set('original', 1);
}

//tag editor
$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $item->tags('string'))));

$type = 'file'; //strtolower(Request::getWord('type', $item->get('type')));
if (!$type) {
    $type = 'file';
}
if ($type && !in_array($type, array('file', 'image', 'text', 'link'))) {
    $type = 'link';
}

$base = $this->member->link() . '&active=' . $this->name;

$dir = $item->get('id');
if (!$dir) {
    $dir = 'tmp' . time(); // . rand(0, 100);
}

$jbase = rtrim(Request::base(true), '/');

$this->css()
    ->js('jquery.fileuploader.js', 'system')
    ->js('fileupload.js')
    ->js();
?>

<?php if ($this->getError()) { ?>
    <p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php
$saveUrl = Route::url(
    $base . '&task=post/save'
    . ($this->no_html ? '&no_html=' . $this->no_html : '')
);
?>
<form action="<?php echo $saveUrl; ?>"
    method="post"
    id="hubForm"
    class="full"
    enctype="multipart/form-data">
    <fieldset>
        <?php
        if ($item->get('id')) {
            $legendText = $this->entry->get('original')
                ? Lang::txt('PLG_MEMBERS_COLLECTIONS_EDIT_POST')
                : Lang::txt('PLG_MEMBERS_COLLECTIONS_EDIT_REPOST');
        } else {
            $legendText = Lang::txt('PLG_MEMBERS_COLLECTIONS_NEW_POST');
        }
        ?>
        <legend><?php echo $legendText; ?></legend>

        <?php if ($this->entry->get('original')) { ?>
            <div class="field-wrap">
                <div class="asset-uploader">
                    <div class="grid">
                        <div class="col span-half">
                            <?php
                            $uploadInstructions = Lang::txt(
                                'PLG_MEMBERS_COLLECTIONS_CLICK_OR_DROP_FILE'
                            );
                            $uploadAction = $jbase
                                . '/index.php?option=com_collections'
                                . '&amp;no_html=1&amp;controller=media'
                                . '&amp;task=upload';
                            $uploadList = $jbase
                                . '/index.php?option=com_collections'
                                . '&amp;no_html=1&amp;controller=media'
                                . '&amp;task=list&amp;dir=';
                            ?>
                            <div id="ajax-uploader"
                                data-txt-instructions="<?php echo $uploadInstructions; ?>"
                                data-action="<?php echo $uploadAction; ?>"
                                data-list="<?php echo $uploadList; ?>">
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
                            $linkBase = rtrim(Request::base(true), '/');
                            $linkDeleteTxt = Lang::txt('JACTION_DELETE');
                            $linkInstructions = Lang::txt(
                                'PLG_MEMBERS_COLLECTIONS_CLICK_TO_ADD_LINK'
                            );
                            $linkAction = $jbase
                                . '/index.php?option=com_collections'
                                . '&amp;no_html=1&amp;controller=media'
                                . '&amp;task=create&amp;dir=';
                            $linkList = $jbase
                                . '/index.php?option=com_collections'
                                . '&amp;no_html=1&amp;controller=media'
                                . '&amp;task=list&amp;dir=';
                            ?>
                            <div id="link-adder"
                                data-base="<?php echo $linkBase; ?>"
                                data-txt-delete="<?php echo $linkDeleteTxt; ?>"
                                data-txt-instructions="<?php echo $linkInstructions; ?>"
                                data-action="<?php echo $linkAction; ?>"
                                data-list="<?php echo $linkList; ?>">
                                <noscript>
                                    <div class="form-group">
                                        <label for="add-link">
                                            <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_ADD_LINK'); ?>
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
                                $assetId = $this->escape($asset->get('id'));
                                $deleteUrl = Route::url(
                                    $base . '&task=post/'
                                    . $this->entry->get('id')
                                    . '/edit&remove=' . $asset->get('id')
                                );
                                $deleteTxt = Lang::txt(
                                    'PLG_MEMBERS_COLLECTIONS_DELETE'
                                );
                                ?>
                                <a class="delete"
                                    data-id="<?php echo $assetId; ?>"
                                    href="<?php echo $deleteUrl; ?>"
                                    title="<?php echo $deleteTxt; ?>">
                                <?php echo $deleteTxt; ?>
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
                            <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_TITLE'); ?>
                            <input type="text"
                                name="fields[title]"
                                id="field-title"
                                class="form-control"
                                value="<?php echo $this->escape(stripslashes($item->get('title', ''))); ?>"/>
                        </label>
                    </div>
                    <input type="hidden" name="fields[type]" value="file" />
                <?php } else { ?>
                    <div class="form-group">
                        <label for="field-title">
                            <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_TITLE'); ?>
                            <input type="text"
                                name="fieldstitle"
                                id="field-title"
                                class="form-control disabled"
                                disabled="disabled"
                                value="<?php echo $this->escape(stripslashes($item->get('title', ''))); ?>"/>
                        </label>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <label for="field_description">
                        <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_DESCRIPTION'); ?>
                        <?php
                        $editorOptions = array(
                            'class' => 'form-control minimal no-footer'
                        );
                        ?>
                        <?php if ($this->entry->get('original')) { ?>
                            <?php
                            $editorContent = $this->escape(
                                stripslashes($item->description('raw'))
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
                                stripslashes($this->entry->description('raw'))
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
                    $errorKey = 'PLG_MEMBERS_'
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
                    <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_SELECT_COLLECTION'); ?>
                    <select name="post[collection_id]" id="post-collection_id" class="form-control">
                    <?php foreach ($this->collections as $collection) { ?>
                        <?php
                        $collId = $this->escape($collection->get('id'));
                        $selected = ($this->collection->get('id') == $collection->get('id'))
                            ? ' selected="selected"'
                            : '';
                        $collTitle = $this->escape(
                            stripslashes($collection->get('title'))
                        );
                        ?>
                        <option value="<?php echo $collId; ?>"<?php
                            echo $selected;
                        ?>><?php echo $collTitle; ?></option>
                    <?php } ?>
                    </select>
                    <?php
                    $selectHint = Lang::txt(
                        'PLG_MEMBERS_COLLECTIONS_FIELD_SELECT_COLLECTION_HINT'
                    );
                    ?>
                    <span class="hint"><?php echo $selectHint; ?></span>
                </label>
            </div>
        <?php } else { ?>
            <div class="form-group">
                <label for="post-collection_title">
                    <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_CREATE_COLLECTION'); ?>
                    <input type="text"
                        name="collection_title"
                        id="post-collection_title"
                        class="form-control"
                        value=""/>
                    <?php
                    $createHint = Lang::txt(
                        'PLG_MEMBERS_COLLECTIONS_FIELD_CREATE_COLLECTION_HINT'
                    );
                    ?>
                    <span class="hint"><?php echo $createHint; ?></span>
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
                            <input type="text"
                                name="tags"
                                id="actags"
                                class="form-control"
                                value="<?php echo $this->escape($item->tags('string')); ?>"/>
                        <?php } ?>
                        <?php
                        $tagsHintKey = 'PLG_MEMBERS_'
                            . strtoupper($this->name)
                            . '_FIELD_TAGS_HINT';
                        ?>
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

    <input type="hidden" name="id" value="<?php echo $this->member->get('id'); ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="active" value="<?php echo $this->name; ?>" />
    <input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />
    <input type="hidden" name="action" value="save" />

    <?php echo Html::input('token'); ?>

    <p class="submit">
        <input class="btn btn-success"
            type="submit"
            value="<?php echo Lang::txt('PLG_MEMBERS_' . strtoupper($this->name) . '_SAVE'); ?>"/>

        <?php if ($item->get('id')) { ?>
            <?php
            $cancelUrl = Route::url(
                $base . ($item->get('id')
                    ? '&task=' . $this->collection->get('alias')
                    : '')
            );
            ?>
            <a class="btn btn-secondary"
                href="<?php echo $cancelUrl; ?>">
                <?php echo Lang::txt('JCANCEL'); ?>
            </a>
        <?php } ?>
    </p>
</form>
