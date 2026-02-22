<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->currentDoc['path'] = ltrim($this->currentDoc['path'], '/');

// File type icon
$ext  = Filesystem::extension($this->currentDoc['name']);

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

// Get a shortened name
$name = Filesystem::name($this->currentDoc['name']);
if (strlen($name) > 10) :
    $name = substr($name, 0, 10) . ' ... ';
endif;
$name .= '.' . $ext;

// Querystring option
$t = '';
$tmpl = Request::getCmd('tmpl');
if ($tmpl) :
    $t .= '&tmpl=' . $tmpl;
endif;

// Download link
$token = Session::getFormToken();
$href = Route::url(
    'index.php?option=' . $this->option
    . '&task=download&' . $token . '=1'
    . '&file=' . urlencode($this->currentDoc['path'])
);

// Image alt text
$fileSize = \Components\Media\Admin\Helpers\MediaHelper::parseSize(
    $this->currentDoc['size']
);
$imgAlt = $this->escape(
    Lang::txt(
        'COM_MEDIA_IMAGE_TITLE',
        $this->currentDoc['name'],
        $fileSize
    )
);

// Before display event
$params = new Hubzero\Config\Registry();
Event::trigger(
    'onContentBeforeDisplay',
    array('com_media.file', &$this->_tmp_doc, &$params)
);

$docUrl = COM_MEDIA_BASEURL . $this->currentDoc['path'];
$docExt = Filesystem::extension($this->currentDoc['name']);
$escapedName = $this->escape($this->currentDoc['name']);
?>
        <div class="media-item media-item-thumb">
            <div class="media-preview">
                <div class="media-preview-inner">
                    <a href="<?php echo $docUrl; ?>"
                        class="media-thumb doc-item <?php echo $docExt; ?>"
                        title="<?php echo $escapedName; ?>">
                        <span class="media-preview-shim"></span><!--
                        --><img src="<?php echo $icon; ?>"
                            alt="<?php echo $imgAlt; ?>"
                            width="80" />
                    </a>
                    <span class="media-options-btn"></span>
                </div>
            </div>
            <div class="media-info">
                <div class="media-name">
                    <?php echo $this->escape($name); ?>
                </div>
                <?php if ($tmpl != 'component' || User::authorise('core.delete', 'com_media')) : ?>
                    <div class="media-options">
                        <ul>
                            <?php if ($tmpl != 'component') : ?>
                                <li>
                                    <?php
                                    $infoUrl = Route::url(
                                        'index.php?option=' . $this->option
                                        . '&controller=medialist&task=info'
                                        . '&tmpl=' . $tmpl
                                        . '&' . $token . '=1'
                                        . '&file=' . urlencode($this->currentDoc['path'])
                                    );
                                    $infoLabel = Lang::txt('COM_MEDIA_FILE_INFO');
                                    ?>
                                    <a class="icon-info media-opt-info"
                                        href="<?php echo $infoUrl; ?>">
                                        <?php echo $infoLabel; ?>
                                    </a>
                                </li>
                                <li>
                                    <span class="separator"></span>
                                </li>
                                <li>
                                    <?php $dlLabel = Lang::txt('COM_MEDIA_DOWNLOAD'); ?>
                                    <a download
                                        class="icon-download media-opt-download"
                                        href="<?php echo $href; ?>">
                                        <?php echo $dlLabel; ?>
                                    </a>
                                </li>
                                <li>
                                    <?php
                                    $pathUrl = Route::url(
                                        'index.php?option=' . $this->option
                                        . '&controller=medialist&task=path'
                                        . '&tmpl=' . $tmpl
                                        . '&' . $token . '=1'
                                        . '&file=' . urlencode($this->currentDoc['path'])
                                    );
                                    $linkLabel = Lang::txt('COM_MEDIA_FILE_LINK');
                                    ?>
                                    <a class="icon-link media-opt-path"
                                        href="<?php echo $pathUrl; ?>">
                                        <?php echo $linkLabel; ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (User::authorise('core.delete', 'com_media')) : ?>
                                <li>
                                    <span class="separator"></span>
                                </li>
                                <li>
                                    <?php
                                    $deleteUrl = Route::url(
                                        'index.php?option=' . $this->option
                                        . '&task=delete&tmpl=' . $tmpl
                                        . '&' . $token . '=1'
                                        . '&rm=' . urlencode($this->currentDoc['path'])
                                    );
                                    $deleteLabel = Lang::txt('JACTION_DELETE');
                                    ?>
                                    <a class="icon-trash media-opt-delete"
                                        href="<?php echo $deleteUrl; ?>">
                                        <?php echo $deleteLabel; ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
<?php
Event::trigger(
    'onContentAfterDisplay',
    array('com_media.file', &$this->_tmp_doc, &$params)
);
