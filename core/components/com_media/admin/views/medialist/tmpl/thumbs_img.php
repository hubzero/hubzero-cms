<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Filesystem;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\User;
use Hubzero\Facades\Event;

// No direct access.
defined('_HZEXEC_') or die();

$this->currentImg['path'] = ltrim($this->currentImg['path'], '/');

// Get a shortened name
$ext  = Filesystem::extension($this->currentImg['name']);
$name = Filesystem::name($this->currentImg['name']);
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
    . '&file=' . urlencode($this->currentImg['path'])
);

// Image alt text
$fileSize = \Components\Media\Admin\Helpers\MediaHelper::parseSize(
    $this->currentImg['size']
);
$imgAlt = Lang::txt(
    'COM_MEDIA_IMAGE_TITLE',
    $this->currentImg['name'],
    $fileSize
);

// Before display event
$params = new Hubzero\Config\Registry();
Event::trigger(
    'onContentBeforeDisplay',
    array('com_media.file', &$this->_tmp_img, &$params)
);

$imgUrl = COM_MEDIA_BASEURL . $this->currentImg['path'];
$imgExt = Filesystem::extension($this->currentImg['name']);
$escapedName = $this->escape($this->currentImg['name']);
?>
        <div class="media-item media-item-thumb">
            <div class="media-preview">
                <div class="media-preview-inner">
                    <a href="<?php echo $imgUrl; ?>"
                        class="media-thumb doc-item img-preview <?php echo $imgExt; ?>"
                        title="<?php echo $escapedName; ?>">
                        <span class="media-preview-shim"></span><!--
                        --><img src="<?php echo $imgUrl; ?>"
                            alt="<?php echo $imgAlt; ?>"
                            width="160" />
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
                                        . '&controller=medialist&task=info' . $t
                                        . '&' . $token . '=1'
                                        . '&file=' . urlencode($this->currentImg['path'])
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
                                        . '&controller=medialist&task=path' . $t
                                        . '&' . $token . '=1'
                                        . '&file=' . urlencode($this->currentImg['path'])
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
                                        . '&task=delete' . $t
                                        . '&' . $token . '=1'
                                        . '&rm=' . urlencode($this->currentImg['path'])
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
    array('com_media.file', &$this->_tmp_img, &$params)
);
