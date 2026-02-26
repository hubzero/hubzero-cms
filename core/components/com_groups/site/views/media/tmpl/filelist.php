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

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('modal');

// push scripts and styles
$this->css()
     ->css('jquery.fancybox.css', 'system')
     ->css('media.css')
     ->js()
     ->js('groups.medialist')
     ->js('jquery.fileuploader', 'system')
     ->js('jquery.contextMenu', 'system')
     ->css('jquery.contextMenu.css', 'system');

// define base URI
$baseURI = Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn'));

//get request vars
$type          = Request::getWord('type', '', 'get');
$ckeditor      = Request::getString('CKEditor', '', 'get');
$ckeditorFunc  = Request::getInt('CKEditorFuncNum', 0, 'get');
$ckeditorQuery = '&type=' . $type . '&CKEditor=' . $ckeditor . '&CKEditorFuncNum=' . $ckeditorFunc;
?>

<script type="text/javascript">
    function ckeditorInsertFile( file )
    {
        var opener = window.parent;
        HUB.GroupsMediaList.ckeditorInsert( file, opener );
    }
</script>

<div class="upload-filelist-toolbar">
    <div class="toolbar cf">
        <?php
            $folder   = '';
            $segments = explode('/', ltrim($this->relpath, DS));
        ?>
        <ul class="path">
            <?php if ($this->group->get('type') == 3) : ?>
                <?php $txt = Lang::txt('COM_GROUPS_MEDIA_PATH_SLASH_ROOT'); ?>
                <li><a data-folder="/" href="javascript:(void);"><?php echo $txt; ?></a></li>
            <?php endif; ?>
            <?php foreach ($segments as $segment) : ?>
                <?php $folder .= DS . $segment; ?>
                <li class="divider"><?php echo Lang::txt('COM_GROUPS_MEDIA_PATH_SLASH'); ?></li>
                <?php $v1 = $this->escape($folder); ?>
                <?php $v0 = $this->escape($segment); ?>
                <li><a data-folder="<?php echo $v1; ?>" href="javascript:(void);"><?php echo $v0; ?></a></li>
            <?php endforeach; ?>
        </ul>
        <div class="buttons"></div>
    </div>
    <div class="filelist-headers">
        <ul>
            <li>
                <div class="name"><?php echo Lang::txt('COM_GROUPS_MEDIA_NAME'); ?></div>
                <div class="modified"><?php echo Lang::txt('COM_GROUPS_MEDIA_MODIFIED'); ?></div>
            </li>
        </ul>
    </div>
</div>

<div class="upload-filelist">
    <ul>
        <?php foreach ($this->folders as $folder) : ?>
            <li class="folder">
                <div class="name">
                    <?php
                        $folder = ltrim($folder, '/');

                        $dataFolder = $this->relpath . $folder;
                    if ($this->relpath != '/') {
                        $dataFolder = $this->relpath . '/' . $folder;
                    }

                        $moveFolderPath     = Route::url('index.php?option=com_groups&cn=' .
                            $this->group->get('cn') .
                            '&controller=media&task=movefolder&folder=' .
                            $dataFolder .
                            '&tmpl=component&' .
                            Session::getFormToken() .
                            '=1');
                        $renameFolderPath   = Route::url('index.php?option=com_groups&cn=' .
                            $this->group->get('cn') .
                            '&controller=media&task=renamefolder&folder=' .
                            $dataFolder .
                            '&tmpl=component&' .
                            Session::getFormToken() .
                            '=1');
                        $deleteFolderPath   = Route::url('index.php?option=com_groups&cn=' .
                            $this->group->get('cn') .
                            '&controller=media&task=deletefolder&folder=' .
                            $dataFolder .
                            '&tmpl=component&' .
                            Session::getFormToken() .
                            '=1');
                    ?>
                    <a
                        href="javascript:void(0);"
                        data-action-delete="<?php echo $deleteFolderPath; ?>"
                        data-action-rename="<?php echo $renameFolderPath; ?>"
                        data-action-move="<?php echo $moveFolderPath; ?>"
                        data-folder="<?php echo $this->escape($dataFolder); ?>"><?php echo $this->
                            escape($folder); ?>
                        </a>
                </div>
                <div class="modified">
                    --
                </div>
            </li>
        <?php endforeach; ?>
        <?php $mimeTypes = \Hubzero\Filesystem\Util\MimeType::getExtensionToMimeTypeMap(); ?>
        <?php foreach ($this->files as $file) : ?>
            <?php
                $file = ltrim($file, DS);

                // build file path
                $filePath    = $this->path . DS . $file;
                $relFilePath = $this->relpath . DS . $file;

                // get file info & stats
                $fileInfo   = @pathInfo($filePath);
                $filesize   = @filesize($filePath);
                $dimensions = @getimagesize($filePath);
                $modified   = @filemtime($filePath);

                // formatted results
            if (isset($fileinfo['extension'])) {
                $extension = $fileInfo['extension'];
            } else {
                $extension = Filesystem::extension($file);

                if (!$extension) {
                    $fileContent = file_get_contents($filePath);
                    $mimeType = \Hubzero\Filesystem\Util\MimeType::detectByContent($fileContent);
                    $extension = array_search($mimeType, $mimeTypes);
                }
            }

                $formattedFilesize   = \Hubzero\Utility\Number::formatBytes($filesize);
                $formattedDimensions = $dimensions ? $dimensions[0] . 'px &times; ' . $dimensions[1] . 'px' : '';
                $formattedModified   = Date::of($modified)->toLocal('m/d/Y g:ia');

                // is this file an image
            $isImage   = in_array($extension, array('jpg','jpeg','jpe','png','gif','bmp','tiff','tif')) ? true : false;
                $isArchive = (in_array($extension, array('zip', 'tar', 'gz'))) ? true : false;

                // build paths
                $downloadPath = $baseURI . DS . 'File:' . $relFilePath;
                $movePath     = Route::url('index.php?option=com_groups&cn=' .
                    $this->group->get('cn') .
                    '&controller=media&task=movefile&file=' .
                    $relFilePath .
                    '&format=raw&' .
                    Session::getFormToken() .
                    '=1'); //tmpl=component');
                $renamePath   = Route::url('index.php?option=com_groups&cn=' .
                    $this->group->get('cn') .
                    '&controller=media&task=renamefile&file=' .
                    $relFilePath .
                    '&format=raw&' .
                    Session::getFormToken() .
                    '=1'); //tmpl=component');
                $extractPath  = Route::url('index.php?option=com_groups&cn=' .
                    $this->group->get('cn') .
                    '&controller=media&task=extractfile&file=' .
                    $relFilePath .
                    '&format=raw&' .
                    Session::getFormToken() .
                    '=1'); //tmpl=component');
                $deletePath   = Route::url('index.php?option=com_groups&cn=' .
                    $this->group->get('cn') .
                    '&controller=media&task=deletefile&file=' .
                    $relFilePath .
                    '&format=raw&' .
                    Session::getFormToken() .
                    '=1'); //tmpl=component');
                //$rawPath  = Route::url('index.php?option=com_groups&cn='.$this->group
                //    ->get('cn').'&controller=media&task=rawfile&file=' . $relFilePath . '&tmpl=component');
            ?>
            <li class="file file-<?php echo strtolower($extension); ?>">
                <div class="name">
                    <a href="javascript:void(0);"><?php echo $file; ?></a>
                </div>
                <div class="modified">
                    <?php echo ($modified) ? $formattedModified : '--'; ?>
                </div>
            </li>
            <li class="file-details cf">
                <div class="right">
                    <div class="title"><?php echo Lang::txt('COM_GROUPS_MEDIA_FILE_PREVIEW'); ?></div>
                    <div class="preview">
                        <?php if ($isImage) : ?>
                            <?php $v1 = rtrim(Request::base(true), '/'); ?>
                            <?php $v0 = $downloadPath; ?>
                            <img
                                src="<?php echo $v1; ?>/core/components/com_groups/site/assets/img/loading.gif"
                                data-src="<?php echo $v0; ?>" />
                        <?php else : ?>
                            <?php $txt = Lang::txt('COM_GROUPS_MEDIA_FILE_PREVIEW_NOT_AVAILABLE'); ?>
                            <p><strong><?php echo $txt; ?></strong></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="left">
                    <div class="title"><?php echo Lang::txt('COM_GROUPS_MEDIA_FILE_DETAILS'); ?></div>
                    <ul>
                        <li>
                            <?php $txt = Lang::txt('COM_GROUPS_MEDIA_FILE_NAME'); ?>
                            <strong><?php echo $txt; ?>: </strong> <?php echo $file; ?>
                        </li>
                        <li>
                            <?php $txt = Lang::txt('COM_GROUPS_MEDIA_FILE_SIZE'); ?>
                            <strong><?php echo $txt; ?>: </strong> <?php echo $formattedFilesize; ?>
                        </li>
                        <?php if ($isImage) : ?>
                            <li>
                                <?php $txt = Lang::txt('COM_GROUPS_MEDIA_FILE_DIMENSIONS'); ?>
                                <strong><?php echo $txt; ?>: </strong> <?php echo $formattedDimensions; ?>
                            </li>
                        <?php endif; ?>
                        <li class="path">
                            <?php $txt = Lang::txt('COM_GROUPS_MEDIA_FILE_PATH'); ?>
                            <strong><?php echo $txt; ?>: </strong> <span><?php echo $downloadPath; ?></span>
                        </li>
                        <li>
                            <?php if ($this->authorized && isset($ckeditor) && $ckeditor != '') : ?>
                                <?php $v1 = $downloadPath; ?>
                                <?php $v0 = Lang::txt('COM_GROUPS_MEDIA_INSERT_FILE'); ?>
                                <a
                                    href="javascript:void(0);"
                                    class="btn btn-secondary icon-add"
                                    onclick="return ckeditorInsertFile('<?php echo $v1; ?>');"><?php echo $v0; ?></a>
                            <?php endif; ?>
                            <a
                                href="<?php echo $downloadPath; ?>"
                                class="btn btn-secondary icon-download action-download">
                                    <?php echo Lang::txt('COM_GROUPS_MEDIA_DOWNLOAD'); ?>
                                </a>
                            <?php if ($this->authorized) : ?>
                                <?php if ($this->group->published == 1) : ?>
                                    <a
                                        href="<?php echo $renamePath; ?>"
                                        class="btn btn-secondary icon-edit action-rename">
                                            <?php echo Lang::txt('COM_GROUPS_MEDIA_RENAME'); ?>
                                        </a>
                                    <a
                                        href="<?php echo $movePath; ?>"
                                        class="btn btn-secondary icon-move action-move">
                                            <?php echo Lang::txt('COM_GROUPS_MEDIA_MOVE'); ?>
                                        </a>
                                <?php endif; ?>
                                <?php if ($isArchive) : ?>
                                    <a
                                        href="<?php echo $extractPath; ?>"
                                        class="btn btn-secondary icon-extract action-extract">
                                            <?php echo Lang::txt('COM_GROUPS_MEDIA_EXTRACT'); ?>
                                        </a>
                                <?php endif; ?>
                                <?php if ($this->group->published == 1) : ?>
                                    <a
                                        data-file="<?php echo $relFilePath; ?>"
                                        href="<?php echo $deletePath; ?>"
                                        class="btn btn-secondary icon-delete action-delete">
                                            <?php echo Lang::txt('COM_GROUPS_MEDIA_DELETE'); ?>
                                        </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>
            </li>
        <?php endforeach; ?>
        <?php if (count($this->folders) == 0 && count($this->files) == 0) : ?>
            <li><em><?php echo Lang::txt('COM_GROUPS_MEDIA_NO_FILES'); ?></em></li>
        <?php endif;?>
    </ul>
</div>