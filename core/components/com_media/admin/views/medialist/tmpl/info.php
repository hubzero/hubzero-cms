<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

if ($this->data['type'] != 'folder') :
    $ext = Filesystem::extension($this->data['name']);

    $icon = Html::asset(
        'image',
        'assets/filetypes/' . $ext . '.svg',
        '',
        null,
        true,
        true
    );
    if (!$icon) :
        $icon = Html::asset(
            'image',
            'assets/filetypes/file.svg',
            '',
            null,
            true,
            true
        );
    endif;
else :
    $icon = Html::asset(
        'image',
        'assets/filetypes/folder.svg',
        '',
        null,
        true,
        true
    );
endif;

$fileSize = \Components\Media\Admin\Helpers\MediaHelper::parseSize(
    $this->data['size']
);
$imgAlt = $this->escape(
    Lang::txt('COM_MEDIA_IMAGE_TITLE', $this->data['name'], $fileSize)
);

$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=medialist&file=' . urlencode($this->data['path'])
);
?>

<form action="<?php echo $formAction; ?>"
    id="component-form" method="post"
    name="adminForm" autocomplete="off">
    <fieldset>
        <h2 class="modal-title">
            <?php echo Lang::txt('COM_MEDIA_FILE_INFO'); ?>
        </h2>
    </fieldset>
    <div class="grid">
        <div class="col span5">
            <div class="media-preview">
                <div class="media-preview-inner">
                    <?php if ($this->data['type'] == 'img') : ?>
                        <?php
                        $fileExt = Filesystem::extension($this->data['name']);
                        $escapedName = $this->escape($this->data['name']);
                        $imgWidth = ($this->data['width'] < 260)
                            ? $this->data['width'] : '260';
                        ?>
                        <div class="media-thumb img-preview <?php echo $fileExt; ?>"
                            title="<?php echo $escapedName; ?>">
                            <span class="media-preview-shim"></span><!--
                            --><img
                                src="<?php echo COM_MEDIA_BASEURL . $this->data['path']; ?>"
                                alt="<?php echo $imgAlt; ?>"
                                width="<?php echo $imgWidth; ?>" />
                        </div>
                    <?php else : ?>
                        <?php
                        $fileExt = Filesystem::extension($this->data['name']);
                        $escapedName = $this->escape($this->data['name']);
                        ?>
                        <div class="media-thumb doc-item <?php echo $fileExt; ?>"
                            title="<?php echo $escapedName; ?>">
                            <span class="media-preview-shim"></span><!--
                            --><img src="<?php echo $icon; ?>"
                                alt="<?php echo $imgAlt; ?>"
                                width="80" />
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col span7">
            <div class="input-wrap">
                <?php $nameLabel = Lang::txt('COM_MEDIA_LIST_HEADER_NAME'); ?>
                <span class="media-info-label"><?php echo $nameLabel; ?>:</span>
                <span class="media-info-value">
                    <?php echo $this->escape($this->data['name']); ?>
                </span>
            </div>

            <div class="input-wrap">
                <?php $pathLabel = Lang::txt('COM_MEDIA_LIST_HEADER_PATH'); ?>
                <span class="media-info-label"><?php echo $pathLabel; ?>:</span>
                <span class="media-info-value">
                    <?php echo $this->escape($this->data['path']); ?>
                </span>
            </div>

            <?php if ($this->data['type'] != 'folder') : ?>
                <?php if ($this->data['type'] == 'img') : ?>
                    <div class="grid">
                        <div class="col span4">
                <?php endif; ?>
                <div class="input-wrap">
                    <?php $sizeLabel = Lang::txt('COM_MEDIA_LIST_HEADER_SIZE'); ?>
                    <span class="media-info-label"><?php echo $sizeLabel; ?>:</span>
                    <span class="media-info-value">
                        <?php echo Hubzero\Utility\Number::formatBytes($this->data['size']); ?>
                    </span>
                </div>
                <?php if ($this->data['type'] == 'img') : ?>
                        </div>
                        <div class="col span4">
                            <div class="input-wrap">
                                <?php $widthLabel = Lang::txt('COM_MEDIA_LIST_HEADER_WIDTH'); ?>
                                <span class="media-info-label">
                                    <?php echo $widthLabel; ?>:
                                </span>
                                <span class="media-info-value">
                                    <?php echo $this->data['width']; ?>px
                                </span>
                            </div>
                        </div>
                        <div class="col span4">
                            <div class="input-wrap">
                                <?php $heightLabel = Lang::txt('COM_MEDIA_LIST_HEADER_HEIGHT'); ?>
                                <span class="media-info-label">
                                    <?php echo $heightLabel; ?>:
                                </span>
                                <span class="media-info-value">
                                    <?php echo $this->data['height']; ?>px
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="input-wrap">
                <?php $modLabel = Lang::txt('COM_MEDIA_LIST_HEADER_MODIFIED'); ?>
                <span class="media-info-label"><?php echo $modLabel; ?>:</span>
                <span class="media-info-value">
                    <?php echo Date::of($this->data['modified'])->toSql(); ?>
                </span>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="option"
        value="<?php echo $this->option; ?>" />
    <?php echo Html::input('token'); ?>
</form>
