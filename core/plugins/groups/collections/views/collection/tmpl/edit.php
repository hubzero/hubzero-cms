<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Event;

// No direct access
defined('_HZEXEC_') or die();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name;

if (!$this->entry->exists()) {
    $legend = 'PLG_GROUPS_COLLECTIONS_NEW_COLLECTION';
} else {
    $legend = 'PLG_GROUPS_COLLECTIONS_EDIT_COLLECTION';
}
$default = $this->params->get('access-plugin');

$accessValue = $this->entry->get('access', $default);
$accessPublicSelected = ($accessValue == 0) ? ' selected="selected"' : '';
$accessRegisteredSelected = ($accessValue == 1) ? ' selected="selected"' : '';
$accessPrivateSelected = ($accessValue == 4) ? ' selected="selected"' : '';

$privacyPublicLabel = Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_PRIVACY_PUBLIC');
$privacyRegisteredLabel = Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_PRIVACY_REGISTERED');
$privacyPrivateLabel = Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_PRIVACY_PRIVATE');

$titleFieldError = ($this->task == 'save' && !$this->entry->get('title'))
    ? ' class="fieldWithErrors"'
    : '';
$titleLabel = Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_TITLE');
$requiredLabel = Lang::txt('JREQUIRED');

$editorArgs = array('class' => 'form-control minimal no-footer');
$descriptionEditor = $this->editor(
    'fields[description]',
    $this->escape(stripslashes($this->entry->description('raw'))),
    35,
    5,
    'field-description',
    $editorArgs
);

$layoutValue = $this->entry->get('layout');
$gridSelected = ($layoutValue == 'grid') ? ' selected="selected"' : '';
$listSelected = ($layoutValue == 'list') ? ' selected="selected"' : '';
$gridLabel = Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_LAYOUT_GRID');
$listLabel = Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_LAYOUT_LIST');

$sortValue = $this->entry->get('sort');
$createdSelected = ($sortValue == 'created') ? ' selected="selected"' : '';
$orderingSelected = ($sortValue == 'ordering') ? ' selected="selected"' : '';
$createdLabel = Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_SORT_CREATED');
$orderingLabel = Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_SORT_ORDERING');
?>

<?php if ($this->getError()) { ?>
    <p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<form action="<?php echo Route::url($base . '&scope=save'); ?>"
    method="post"
    id="hubForm"
    class="full"
    enctype="multipart/form-data">
    <fieldset>
        <legend><?php echo Lang::txt($legend); ?></legend>

        <div class="form-group">
            <label for="field-access">
                <?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_PRIVACY'); ?>
                <select name="fields[access]" id="field-access" class="form-control">
                    <option value="0"<?php echo $accessPublicSelected; ?>><?php
                        echo $privacyPublicLabel;
                    ?></option>
                    <option value="1"<?php echo $accessRegisteredSelected; ?>><?php
                        echo $privacyRegisteredLabel;
                    ?></option>
                    <option value="4"<?php echo $accessPrivateSelected; ?>><?php
                        echo $privacyPrivateLabel;
                    ?></option>
                </select>
            </label>
        </div>

        <div class="form-group">
            <label for="field-title"<?php echo $titleFieldError; ?>>
                <?php echo $titleLabel; ?>
                <span class="required"><?php echo $requiredLabel; ?></span>
                <input type="text"
                    name="fields[title]"
                    id="field-title"
                    size="35"
                    class="form-control"
                    value="<?php echo $this->escape(stripslashes($this->entry->get('title', ''))); ?>"/>
            </label>
        </div>

        <div class="form-group">
            <label for="field-description">
                <?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_DESCRIPTION'); ?>
                <?php echo $descriptionEditor; ?>
            </label>
        </div>

        <div class="form-group">
            <label for="actags">
                <?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_TAGS'); ?>
                <?php
                $tags = ($this->entry->get('id') ? $this->entry->item()->tags('string') : '');
                $tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $tags)));
                $tf = implode('', $tf);
                if ($tf) {
                    echo $tf;
                } else { ?>
                    <input type="text"
                        name="tags"
                        id="actags"
                        class="form-control"
                        value="<?php echo $this->escape($tags); ?>"/>
                <?php } ?>
                <span class="hint"><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_TAGS_HINT'); ?></span>
            </label>
        </div>

        <div class="grid">
            <div class="col span6">
                <div class="form-group">
                    <label for="field-layout"
                        class="form-control<?php if ($this->task == 'save' && !$this->entry->get('layout')) {
                            echo ' fieldWithErrors';
                                           } ?>">
                        <?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_LAYOUT'); ?>
                        <select name="fields[layout]" id="field-layout">
                            <option value="grid"<?php echo $gridSelected; ?>><?php
                                echo $gridLabel;
                            ?></option>
                            <option value="list"<?php echo $listSelected; ?>><?php
                                echo $listLabel;
                            ?></option>
                        </select>
                    </label>
                </div>
            </div>
            <div class="col span6 omega">
                <div class="form-group">
                    <label for="field-sort"
                        class="form-control<?php if ($this->task == 'save' && !$this->entry->get('sort')) {
                            echo ' fieldWithErrors';
                                           } ?>">
                        <?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_SORT'); ?>
                        <select name="fields[sort]" id="field-sort">
                            <option value="created"<?php echo $createdSelected; ?>><?php
                                echo $createdLabel;
                            ?></option>
                            <option value="ordering"<?php echo $orderingSelected; ?>><?php
                                echo $orderingLabel;
                            ?></option>
                        </select>
                    </label>
                </div>
            </div>
        </div>
        <p class="hint"><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_SORT_DETAILS'); ?></p>
    </fieldset>

    <input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->entry->get('id')); ?>" />
    <input type="hidden"
        name="fields[object_id]"
        value="<?php echo $this->escape($this->group->get('gidNumber')); ?>"/>
    <input type="hidden" name="fields[object_type]" value="group" />
    <input type="hidden" name="fields[created]" value="<?php echo $this->escape($this->entry->get('created')); ?>" />
    <input type="hidden"
        name="fields[created_by]"
        value="<?php echo $this->escape($this->entry->get('created_by')); ?>"/>
    <input type="hidden" name="fields[state]" value="<?php echo $this->escape($this->entry->get('state')); ?>" />

    <input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="active" value="<?php echo $this->name; ?>" />

    <?php echo Html::input('token'); ?>
    <input type="hidden" name="action" value="savecollection" />

    <p class="submit">
        <input class="btn btn-success" type="submit" value="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SAVE'); ?>" />
    </p>
</form>
