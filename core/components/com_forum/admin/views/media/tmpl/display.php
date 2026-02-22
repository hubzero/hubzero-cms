<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('framework', true);

$this->css('media.css')
    ->js('media.js');

$attachments = $this->post->attachments;

$formAction = Route::url(
    'index.php?option=' . $this->option . '&controller='
    . $this->controller . '&id=' . $this->post->get('id')
);
?>

<div id="attachments">
    <form action="<?php echo $formAction; ?>" method="post" id="filelist">
        <?php if (count($attachments) == 0) { ?>
            <p><?php echo Lang::txt('COM_FORUM_NO_FILES_FOUND'); ?></p>
        <?php } else { ?>
            <table>
                <tbody>
                <?php foreach ($attachments as $k => $attachment) { ?>
                    <tr>
                        <td width="100%">
                            <?php
                            $downloadUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller='
                                . $this->controller . '&id=' . $this->post->get('id')
                                . '&task=download&attachment=' . $attachment->get('id')
                                . '&' . Session::getFormToken() . '=1'
                            );
                            $fileExt = Filesystem::extension($attachment->get('filename'));
                            ?>
                            <a download="download"
                                href="<?php echo $downloadUrl; ?>"
                                class="icon-file file <?php echo $fileExt; ?>">
                                <?php echo $this->escape(trim($attachment->get('filename'), DS)); ?>
                            </a>
                        </td>
                        <td>
                            <?php
                            $deleteUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller='
                                . $this->controller . '&task=delete&attachment='
                                . $attachment->get('id') . '&id=' . $this->post->get('id')
                                . '&tmpl=component&' . Session::getFormToken() . '=1'
                            );
                            $confirmMsg = Lang::txt(
                                'COM_FORUM_MEDIA_DELETE_FILE',
                                $attachment->get('filename')
                            );
                            ?>
                            <a class="icon-delete delete deletefile"
                                target="media"
                                href="<?php echo $deleteUrl; ?>"
                                data-file="<?php echo $attachment->get('filename'); ?>"
                                data-confirm="<?php echo $confirmMsg; ?>"
                                title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
                                <span><?php echo Lang::txt('JACTION_DELETE'); ?></span>
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
