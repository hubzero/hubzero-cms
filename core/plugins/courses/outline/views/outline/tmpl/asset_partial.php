<?php

// @phpcs:disable PSR1.Files.SideEffects
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$href = Route::url($this->base . '&asset=' . $this->a->get('id'));

if ($this->a->get('type') == 'video') {
    $href = Route::url($this->base . '&active=outline&unit=' . $this->unit->get('alias') . '&b=' .
    $this->ag->get('alias'));
}

?>

<?php
$assetId = $this->a->get('id');
$assetType = $this->a->get('type');
$assetSubtype = $this->a->get('subtype');
$stateClass = ($this->a->get('state') == 0)
    ? ' notpublished'
    : ' published';
$assetTitle = $this->escape(
    stripslashes($this->a->get('title'))
);
?>
<li id="asset_<?php echo $assetId ?>"
    class="asset-item asset <?php echo $assetType ?> <?php echo $assetSubtype ?><?php echo $stateClass ?>">
    <div class="sortable-assets-handle"></div>
    <div class="asset-item-title title toggle-editable">
        <?php echo $assetTitle ?>
    </div>
    <div class="title-edit">
        <form action="<?php echo Request::base(true); ?>/api/courses/asset/save" class="asset-title-form">
            <input class="title-text" name="title" type="text" value="<?php echo $this->a->get('title') ?>" />
            <input class="asset-title-save" type="submit" value="Save" />
            <input class="asset-title-reset" type="reset" value="Cancel" />
            <input type="hidden" name="course_id" value="<?php echo $this->course->get('id'); ?>" />
            <input type="hidden" name="offering" value="<?php echo $this->course->offering()->alias(); ?>" />
            <input type="hidden" name="id" value="<?php echo $this->a->get('id'); ?>" />
        </form>
    </div>
    <a class="asset-preview" href="<?php echo $href ?>" title="preview"></a>
    <a class="asset-edit" href="#" title="edit"></a>
    <?php if ($this->a->get('type') == 'form') : ?>
        <a class="asset-edit-deployment <?php echo ($this->a->get('state') != 1) ? 'hide' : ''; ?>"
            href="#"
            title="edit deployment"></a>
        <a class="asset-edit-layout" href="#" title="edit layout"></a>
    <?php endif; ?>
    <a class="asset-delete" href="#" title="delete"></a>
    <form action="<?php echo Request::base(true); ?>/api/courses/asset/togglepublished" class="next-step-publish">
        <span class="next-step-publish">
            <label class="published-label" for="published">
                <?php
                $pubLabelTxt = ($this->a->get('state') == 0)
                    ? 'Mark as reviewed and publish?'
                    : 'Published';
                ?>
                <span class="published-label-text">
                    <?php echo $pubLabelTxt ?>
                </span>
                <input
                    class="published-checkbox"
                    name="published"
                    type="checkbox"
                    <?php echo ($this->a->get('state') == 0) ? '' : 'checked="checked"' ?> />
                <input type="hidden" class="asset_id" name="id" value="<?php echo $this->a->get('id'); ?>" />
                <input type="hidden" name="course_id" value="<?php echo $this->course->get('id'); ?>" />
                <input type="hidden" name="scope_id" value="<?php echo $this->ag->get('id'); ?>" />
                <input type="hidden" name="scope" value="asset_group" />
                <input type="hidden" name="offering" value="<?php echo $this->course->offering()->alias(); ?>" />
            </label>
        </span>
    </form>
    <div class="restore">
        <button>Restore</button>
    </div>
</li>
