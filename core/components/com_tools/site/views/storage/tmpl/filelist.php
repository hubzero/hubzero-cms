<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Filesystem;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;

// No direct access.
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{dirtree: mixed, docs: mixed, folders: mixed, listdir: mixed} $this
 */

Html::behavior('framework', true);

$this
    ->css('storage.css')
    ->js('media.js');
?>
    <div id="small-page">
        <div class="databrowser">
            <div id="filelist">
                <table>
                    <caption>
                        <span class="icon-home home">
                            <?php if (count($this->dirtree) > 0) { ?>
                                <?php
                                $filelistUrl = Route::url(
                                    'index.php?option=' . $this->option . '&controller=' . $this->controller
                                        . '&task=filelist&tmpl=component',
                                );
                                ?>
                                <a href="<?= $filelistUrl ?>"><?= Lang::txt('COM_TOOLS_HOME') ?></a>
                            <?php } else { ?>
                                <span><?php echo Lang::txt('COM_TOOLS_HOME'); ?></span>
                            <?php } ?>
                        </span>
                        <?php
                        if (count($this->dirtree) > 0) {
                            $path = '';
                            $i = 0;
                            foreach ($this->dirtree as $branch) {
                                if ($branch != '') {
                                    $path .= $branch . DS;
                                    $i++;
                                    ?>
                                    <span class="arrow">&raquo;</span>
                                    <span class="icon-folder folder">
                                        <?php if ($i != count($this->dirtree)) { ?>
                                            <?php
                                            $filelistUrl = Route::url(
                                                'index.php?option=' . $this->option . '&controller=' . $this->controller
                                                    . '&task=filelist&tmpl=component&listdir=' . $path,
                                            );
                                            ?>
                                            <a href="<?= $filelistUrl ?>"><?= ucfirst($branch) ?></a>
                                        <?php } else { ?>
                                            <span><?php echo ucfirst($branch); ?></span>
                                        <?php } ?>
                                    </span>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </caption>
                    <tbody>
                    <?php
                    foreach ($this->folders as $fullpath => $name) {
                        $dir = DS . $name;
                        $numFiles = count(Filesystem::files($fullpath, '.', false, true, []));

                        if ($this->listdir == DS) {
                            $this->set('listdir', '');
                        }
                        $d = ($this->listdir) ? $this->listdir . DS . $name : DS . $name;
                        ?>
                        <tr>
                            <td width="100%">
                                <?php
                                $filelistUrl = Route::url(
                                    'index.php?option=' . $this->option . '&controller=' . $this->controller
                                        . '&task=filelist&tmpl=component&listdir=' . urlencode($d),
                                );
                                ?>
                                <a class="icon-folder" href="<?= $filelistUrl ?>">
                                    <?php echo $dir; ?>
                                </a>
                            </td>
                            <td class="file-size">
                            </td>
                            <td>
                                <?php if ($dir != '/data' && $dir != '/sessions') { ?>
                                    <?php
                                    $linkUrl = 'index.php?option=' . $this->option . '&amp;controller='
                                        . $this->controller . '&amp;task=deletefolder&amp;delFolder=' . urlencode($dir)
                                        . '&amp;listdir=' . urlencode($this->listdir) . '&amp;tmpl=component&amp;'
                                        . Session::getFormToken() . '=1';
                                    $confirmText = Lang::txt('Are you sure you want to delete the folder "%s"?', $dir);
                                    $dataNotempty = Lang::txt('Sorry unable to delete folder because it is not empty');
                                    ?>
                                    <a class="delete icon-delete delete-folder" href="<?= $linkUrl ?>"
                                        data-confirm="<?= $confirmText ?>" data-files="<?= $numFiles ?>"
                                        data-notempty="<?= $dataNotempty ?>" target="filer"
                                        title="<?= Lang::txt('JACTION_DELETE') ?>">

                                        <?php echo Lang::txt('JACTION_DELETE'); ?>
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                    }

                    foreach ($this->docs as $fullpath => $name) {
                        ?>
                        <tr>
                            <td width="100%">
                                <span class="icon-file"><?php echo $name; ?></span>
                            </td>
                            <td class="file-size">
                                <?php echo \Hubzero\Utility\Number::formatBytes(filesize($fullpath)); ?>
                            </td>
                            <td>
                                <?php
                                $linkUrl = 'index.php?option=' . $this->option . '&amp;controller=' . $this->controller
                                    . '&amp;task=deletefile&amp;file=' . urlencode($name) . '&amp;listdir='
                                    . urlencode($this->listdir) . '&amp;tmpl=component&amp;' . Session::getFormToken()
                                    . '=1';
                                $confirmText = Lang::txt('Are you sure you want to delete the file "%s"?', $name);
                                ?>
                                <a class="delete icon-delete delete-file" href="<?= $linkUrl ?>" target="filer"
                                    data-confirm="<?= $confirmText ?>" title="<?= Lang::txt('JACTION_DELETE') ?>">

                                    <?php echo Lang::txt('JACTION_DELETE'); ?>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        <?php if ($this->getError()) { ?>
            <p class="error"><?php echo $this->getError(); ?></p>
        <?php } ?>
        </div>
    </div>
