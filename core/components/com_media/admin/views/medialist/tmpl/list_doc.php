<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Filesystem;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\User;
use Hubzero\Facades\Event;

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

// Querystring option
$t = '';
$tmpl = Request::getCmd('tmpl');
if ($tmpl) :
    $t .= '&tmpl=' . $tmpl;
endif;

// Get a shortened name
$name = Filesystem::name($this->currentDoc['name']);
if ($tmpl == 'component' && strlen($name) > 10) :
    $name = substr($name, 0, 10) . ' ... ';
endif;
$name .= '.' . $ext;

// Download link
$token = Session::getFormToken();
$href = Route::url(
    'index.php?option=' . $this->option
    . '&task=download&' . $token . '=1'
    . '&file=' . urlencode($this->currentDoc['path'])
);

// Last modified time
$this->currentDoc['modified'] = filemtime(
    COM_MEDIA_BASE . $this->currentDoc['path']
);
$modified = Date::of($this->currentDoc['modified']);

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

$escapedName = $this->escape($this->currentDoc['name']);
$docUrl = COM_MEDIA_BASEURL . $this->currentDoc['path'];
?>
    <tr class="media-item media-item-list">
        <td width="50%">
            <a class="doc-item"
                href="<?php echo $docUrl; ?>"
                title="<?php echo $escapedName; ?>">
                <span class="media-icon">
                    <img src="<?php echo $icon; ?>"
                        alt="<?php echo $imgAlt; ?>" />
                </span>
                <span class="media-name">
                    <?php echo $this->escape($name); ?>
                </span>
            </a>
        </td>
        <td>
            <span class="media-size"><?php echo $fileSize; ?></span>
        </td>
    <?php if ($tmpl != 'component') : ?>
        <td>
            <span class="media-type"><?php echo strtoupper($ext); ?></span>
        </td>
        <td>
            <time class="media-modified"
                datetime="<?php echo $modified->format('Y-m-d\TH:i:s\Z'); ?>">
                <?php echo $modified->toSql(); ?>
            </time>
        </td>
    <?php endif; ?>
    <?php if ($tmpl != 'component' || User::authorise('core.delete', 'com_media')) : ?>
        <td>
            <div class="media-preview-inner">
                <span class="media-options-btn"></span>
                <div class="media-options">
                    <ul>
                        <?php if ($tmpl != 'component') : ?>
                            <li>
                                <?php
                                $infoUrl = Route::url(
                                    'index.php?option=' . $this->option
                                    . '&controller=medialist&task=info' . $t
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
                                    . '&controller=medialist&task=path' . $t
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
                                    . '&task=delete' . $t
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
            </div>
        </td>
    <?php endif; ?>
    </tr>
<?php
Event::trigger(
    'onContentAfterDisplay',
    array('com_media.file', &$this->_tmp_doc, &$params)
);
