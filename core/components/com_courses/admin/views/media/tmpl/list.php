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
use Hubzero\Facades\Session;

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('framework', true);

$this->js('media.js');

$base = Request::base(true);
$imgBase = $base . '/core/components/'
    . $this->option . '/admin/assets/img';
?>
    <div id="attachments">
        <?php
            $routeUrl = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
            );
            ?>
        <form action="<?php echo $routeUrl; ?>" method="post" id="filelist" name="filelist">
            <table>
                <tbody>
<?php if (count($this->folders) == 0 && count($this->docs) == 0) { ?>
                    <tr>
                        <td>
                            <?php echo Lang::txt('COM_COURSES_NO_FILE_FOUNDS'); ?>
                        </td>
                    </tr>
<?php } else { ?>
            <?php
            $folders = $this->folders;
            for ($i = 0; $i < count($folders); $i++) {
                $folderName = key($folders);

                $numFiles = 0;
                if (is_dir(PATH_APP . DS . $folders[$folderName])) {
                    $d = @dir(PATH_APP . DS . $folders[$folderName]);

                    while (false !== ($entry = $d->read())) {
                        if (substr($entry, 0, 1) != '.') {
                            $numFiles++;
                        }
                    }
                    $d->close();
                }

                if ($this->listdir == '/') {
                    $this->listdir = '';
                }
                $subdird = ($this->subdir && $this->subdir != DS)
                    ? $this->subdir . DS : DS;
                ?>
                    <tr>
                        <td>
                            <img
                                src="<?php echo $imgBase; ?>/folder.png"
                                alt="<?php echo $folderName; ?>"
                                width="16"
                                height="16" />
                        </td>
                        <td width="100%">
                            <?php echo $folderName; ?>
                        </td>
                        <td>
                            <?php
                                $routeUrl = Route::url(
                                    'index.php?option=' . $this->option
                                    . '&controller=' . $this->controller
                                    . '&task=deletefolder&delFolder='
                                    . DS . $folders[$folderName]
                                    . '&listdir=' . $this->listdir
                                    . '&tmpl=component&subdir='
                                    . $this->subdir . '&course='
                                    . $this->course_id . '&'
                                    . Session::getFormToken() . '=1'
                                );
                                $confirmMsg = Lang::txt(
                                    'Are you sure you want to delete'
                                    . ' the folder "%s"?',
                                    $folderName
                                );
                                $txt = Lang::txt('COM_COURSES_FILES');
                                $clearMsg = Lang::txt(
                                    'COM_COURSES_CLEAR_FOLDER'
                                ) . ' ' . $txt;
                            ?>
                            <a
                                class="delete-folder"
                                href="<?php echo $routeUrl; ?>"
                                data-files="<?php echo $numFiles; ?>"
                                data-confirm="<?php echo $confirmMsg; ?>"
                                data-notempty="<?php echo $clearMsg; ?>"
                                title="<?php echo Lang::txt('COM_COURSES_DELETE'); ?>">
                                <img
                                    src="<?php echo $imgBase; ?>/trash.png"
                                    width="15"
                                    height="15"
                                    alt="<?php echo Lang::txt('COM_COURSES_DELETE'); ?>" />
                            </a>
                        </td>
                    </tr>
                <?php
                next($folders);
            }
            $docs = $this->docs;
            for ($i = 0; $i < count($docs); $i++) {
                $docName = key($docs);

                $subdird = ($this->subdir && $this->subdir != DS)
                    ? $this->subdir . DS : DS;
                ?>
                    <tr>
                        <td>
                            <img
                                src="<?php echo $imgBase; ?>/file.png"
                                alt="<?php echo $docName; ?>"
                                width="16"
                                height="16" />
                        </td>
                        <td width="100%">
                            <?php echo $docs[$docName]; ?>
                        </td>
                        <td>
                            <?php
                                $routeUrl = Route::url(
                                    'index.php?option=' . $this->option
                                    . '&controller=' . $this->controller
                                    . '&task=deletefile&delFile='
                                    . $docs[$docName] . '&listdir='
                                    . $this->listdir
                                    . '&tmpl=component&subdir='
                                    . $this->subdir . '&course='
                                    . $this->course_id . '&'
                                    . Session::getFormToken() . '=1'
                                );
                                $confirmMsg = Lang::txt(
                                    'Are you sure you want to delete'
                                    . ' the file "%s"?',
                                    $docs[$docName]
                                );
                            ?>
                            <a
                                class="delete-file"
                                href="<?php echo $routeUrl; ?>"
                                data-confirm="<?php echo $confirmMsg; ?>"
                                title="<?php echo Lang::txt('COM_COURSES_DELETE'); ?>">
                                <img
                                    src="<?php echo $imgBase; ?>/trash.png"
                                    width="15"
                                    height="15"
                                    alt="<?php echo Lang::txt('COM_COURSES_DELETE'); ?>" />
                            </a>
                        </td>
                    </tr>
                <?php
                next($docs);
            }
            ?>
<?php } ?>
                </tbody>
            </table>
        </form>
    <?php if ($this->getError()) { ?>
        <p class="error"><?php echo $this->getError(); ?></p>
    <?php } ?>
    </div>
