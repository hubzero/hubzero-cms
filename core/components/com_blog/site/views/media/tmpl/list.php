<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
    ->js('media.js');
?>

<div id="attachments">
    <?php
    $formAction = Route::url(
        'index.php?option=' . $this->option
        . '&controller=' . $this->controller
    );
    ?>
    <form action="<?php echo $formAction; ?>"
        method="post"
        id="filelist"
    >
        <?php if (count($this->folders) == 0 && count($this->docs) == 0) { ?>
            <p><?php echo Lang::txt('COM_BLOG_NO_FILES_FOUND'); ?></p>
        <?php } else { ?>
            <table>
                <tbody>
                <?php
                $base = rtrim(Request::base(true), '/');
                foreach ($this->folders as $k => $folder) {
                    $num_files = count(Filesystem::files(PATH_APP . DS . $folder));
                    ?>
                    <tr>
                        <td width="100%">
                            <span class="icon-folder folder">
                                <?php echo $k; ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $deleteFolderUrl = Route::url(
                                'index.php?option=' . $this->option
                                . '&controller=' . $this->controller
                                . '&task=deletefolder'
                                . '&folder=' . basename($folder)
                                . '&scope=' . urlencode($this->archive->get('scope'))
                                . '&id=' . $this->archive->get('scope_id')
                                . '&tmpl=component&'
                                . Session::getFormToken() . '=1'
                            );
                            $confirmMsg = Lang::txt(
                                'Are you sure you want to delete folder "%s"?',
                                basename($folder)
                            );
                            $notEmptyMsg = Lang::txt(
                                'There are %s files/folders in the folder.'
                                . ' Please delete all files/folder first.',
                                $num_files
                            );
                            ?>
                            <a class="icon-delete delete delete-folder"
                                href="<?php echo $deleteFolderUrl; ?>"
                                target="filer"
                                data-confirm="<?php echo $confirmMsg; ?>"
                                data-files="<?php echo $num_files; ?>"
                                data-notempty="<?php echo $notEmptyMsg; ?>"
                                title="<?php echo Lang::txt('COM_BLOG_DELETE'); ?>"
                            >
                                <span><?php echo Lang::txt('COM_BLOG_DELETE'); ?></span>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                <?php foreach ($this->docs as $doc) { ?>
                    <tr>
                        <td width="100%">
                            <span class="icon-file file <?php echo Filesystem::extension($doc); ?>">
                                <?php echo $this->escape(basename($doc)); ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $deleteFileUrl = Route::url(
                                'index.php?option=' . $this->option
                                . '&controller=' . $this->controller
                                . '&task=deletefile'
                                . '&file=' . basename($doc)
                                . '&scope=' . urlencode($this->archive->get('scope'))
                                . '&id=' . $this->archive->get('scope_id')
                                . '&tmpl=component&'
                                . Session::getFormToken() . '=1'
                            );
                            $confirmFileMsg = Lang::txt(
                                'Are you sure you want to delete file "%s"?',
                                basename($doc)
                            );
                            ?>
                            <a class="icon-delete delete delete-file"
                                href="<?php echo $deleteFileUrl; ?>"
                                target="filer"
                                data-confirm="<?php echo $confirmFileMsg; ?>"
                                title="<?php echo Lang::txt('COM_BLOG_DELETE'); ?>"
                            >
                                <span><?php echo Lang::txt('COM_BLOG_DELETE'); ?></span>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php } ?>

        <?php echo Html::input('token'); ?>
    </form>

    <?php if ($this->getError()) { ?>
        <p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
    <?php } ?>
</div>