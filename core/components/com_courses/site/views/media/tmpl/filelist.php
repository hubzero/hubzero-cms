<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Config;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{config: mixed, docs: mixed, folders: mixed, images: mixed, listdir: mixed} $this
 */

// The hub's own address, for the file paths shown to copy.
$siteUrl = rtrim(trim((string) Config::get('live_site', '')) ?: Request::root(), '/');

Html::behavior('framework', true);

$this->css('media.css');
$this->js('media.js');

$base = rtrim(Request::base(true), '/');

$course = \Components\Courses\Models\Course::getInstance($this->listdir);
?>

<div id="file_list">
    <form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="filelist">
        <?php if (count($this->images) == 0 && count($this->folders) == 0 && count($this->docs) == 0) { ?>
            <p><?php echo Lang::txt('COM_COURSES_NO_FILES_FOUND'); ?></p>
        <?php } else { ?>
            <table>
                <tbody>
                <?php
                $folders = $this->folders;
                for ($i = 0; $i < count($folders); $i++) {
                    $folder_name = key($folders);

                    $num_files = 0;
                    if (is_dir(PATH_APP . DS . $folders[$folder_name])) {
                        $d = @dir(PATH_APP . DS . $folders[$folder_name]);

                        while (false !== ($entry = $d->read())) {
                            if (substr($entry, 0, 1) != '.') {
                                $num_files++;
                            }
                        }
                        $d->close();
                    }

                    if ($this->listdir == '/') {
                        $this->set('listdir', '');
                    }
                    ?>
                    <tr>
                        <td width="100%" colspan="2">
                            <span class="icon-folder"><?php echo $folder_name; ?></span>
                        </td>
                        <td>
                            <?php
                            $linkUrl = $base . '/index.php?option=' . $this->option
                                . '&amp;controller=media&amp;task=deletefolder&amp;folder=' . DS
                                . $folders[$folder_name] . '&amp;listdir=' . $this->listdir . '&amp;no_html=1&amp;'
                                . Session::getFormToken() . '=1';
                            $confirmText = Lang::txt('Are you sure you want to delete the folder "%s"?', $folder_name);
                            $dataNotempty = Lang::txt(
                                'There are %s files/folders in this folder. Please delete all files/folder first.',
                                $num_files,
                            );
                            ?>
                            <a class="icon-delete delete delete-folder" href="<?= $linkUrl ?>" target="filer"
                                data-confirm="<?= $confirmText ?>" data-files="<?= $num_files ?>"
                                data-notempty="<?= $dataNotempty ?>" title="<?= Lang::txt('JACTION_DELETE') ?>">
                                <?php echo Lang::txt('JACTION_DELETE'); ?>
                            </a>
                        </td>
                    </tr>
                    <?php
                    next($folders);
                }

                $docs = $this->docs;
                for ($i = 0; $i < count($docs); $i++) {
                    $doc_name = key($docs);
                    $iconfile = $this->config->get('iconpath') . DS . substr($doc_name, -3) . '.png';

                    if (file_exists(PATH_APP . $iconfile)) {
                        $icon = $iconfile;
                    } else {
                        $icon = $this->config->get('iconpath') . DS . 'unknown.png';
                    }
                    ?>
                    <tr>
                        <td width="100%">
                            <span class="icon-file"><?php echo $docs[$doc_name]; ?></span>
                        </td>
                        <td>
                            <?php if (is_object($course)) : ?>
                                <?php
                                $dataPath = $siteUrl . DS . 'courses' . DS
                                    . $course->get('cn') . DS . 'File:' . $docs[$doc_name];
                                ?>
                                <a href="#" class="icon-path filepath" data-path="<?= $dataPath ?>"
                                    title="<?= Lang::txt('COM_COURSES_SHOW_FILE_PATH') ?>">
                                    <?php echo Lang::txt('COM_COURSES_SHOW_FILE_PATH'); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $linkUrl = $base . '/index.php?option=' . $this->option
                                . '&amp;controller=media&amp;task=deletefile&amp;file=' . $docs[$doc_name]
                                . '&amp;listdir=' . $this->listdir . '&amp;no_html=1&amp;' . Session::getFormToken()
                                . '=1';
                            $confirmText = Lang::txt(
                                'Are you sure you want to delete the file "%s"?',
                                $docs[$doc_name],
                            );
                            ?>
                            <a class="icon-delete delete delete-file" href="<?= $linkUrl ?>" target="filer"
                                data-confirm="<?= $confirmText ?>" title="<?= Lang::txt('JACTION_DELETE') ?>">
                                <?php echo Lang::txt('JACTION_DELETE'); ?>
                            </a>
                        </td>
                    </tr>
                    <?php
                    next($docs);
                }

                $images = $this->images;
                for ($i = 0; $i < count($images); $i++) {
                    $image_name = key($images);
                    ?>
                    <tr>
                        <td width="100%">
                            <span class="icon-image"><?php echo $images[$image_name]; ?></span>
                        </td>
                        <td>
                            <?php if (is_object($course)) : ?>
                                <?php
                                $dataPath = $siteUrl . DS . 'courses' . DS
                                    . $course->get('cn') . DS . 'Image:' . $images[$image_name];
                                ?>
                                <a href="#" class="icon-path filepath" data-path="<?= $dataPath ?>"
                                    title="<?= Lang::txt('COM_COURSES_SHOW_FILE_PATH') ?>">
                                    <?php echo Lang::txt('COM_COURSES_SHOW_FILE_PATH'); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $linkUrl = $base . '/index.php?option=' . $this->option
                                . '&amp;controller=media&amp;task=deletefile&amp;file=' . $images[$image_name]
                                . '&amp;listdir=' . $this->listdir . '&amp;no_html=1&amp;' . Session::getFormToken()
                                . '=1';
                            $confirmText = Lang::txt(
                                'Are you sure you want to delete the folder "%s"?',
                                $images[$image_name],
                            );
                            ?>
                            <a class="icon-delete delete delete-file" href="<?= $linkUrl ?>" target="filer"
                                data-confirm="<?= $confirmText ?>" title="<?= Lang::txt('JACTION_DELETE') ?>">
                                <?php echo Lang::txt('JACTION_DELETE'); ?>
                            </a>
                        </td>
                    </tr>
                    <?php
                    next($images);
                }
                ?>
                </tbody>
            </table>
        <?php } ?>
    </form>
</div>
