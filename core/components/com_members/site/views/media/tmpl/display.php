<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<div id="member-picture">
    <?php $formAction = Route::url('index.php?option=' . $this->option . '&controller=media'); ?>
    <form action="<?php echo $formAction; ?>" method="post" enctype="multipart/form-data" name="filelist" id="filelist">
        <fieldset>
            <legend><?php echo Lang::txt('UPLOAD'); ?> <?php echo Lang::txt('WILL_REPLACE_EXISTING_IMAGE'); ?></legend>

            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="controller" value="media" />
            <input type="hidden" name="no_html" value="1" />
            <input type="hidden" name="task" value="upload" />
            <input type="hidden" name="id" value="<?php echo $this->id; ?>" />
            <?php echo Html::input('token'); ?>

            <input type="file" name="upload" id="upload" size="17" />
            <input type="submit" value="<?php echo Lang::txt('UPLOAD'); ?>" />
        </fieldset>

        <?php if ($this->getError()) { ?>
            <p class="error"><?php echo $this->getError(); ?></p>
        <?php } ?>

        <table>
            <caption><label for="image"><?php echo Lang::txt('MEMBER_PICTURE'); ?></label></caption>
            <tbody>
            <?php
            $k = 0;

            if ($this->file && file_exists($this->file_path . DS . $this->file)) {
                $this_size = filesize($this->file_path . DS . $this->file);
                list($width, $height, $type, $attr) = getimagesize($this->file_path . DS . $this->file);
                ?>
                <tr>
                    <td rowspan="6"><img
                        src="<?php echo $this->webpath . DS . $this->path . DS . $this->file; ?>"
                        alt="<?php echo Lang::txt('MEMBER_PICTURE'); ?>"
                        id="conimage"/></td>
                    <td><?php echo Lang::txt('FILE'); ?>:</td>
                    <td><?php echo $this->file; ?></td>
                </tr>
                <tr>
                    <td><?php echo Lang::txt('SIZE'); ?>:</td>
                    <td><?php echo \Hubzero\Utility\Number::formatBytes($this_size); ?></td>
                </tr>
                <tr>
                    <td><?php echo Lang::txt('WIDTH'); ?>:</td>
                    <td><?php echo $width; ?> px</td>
                </tr>
                <tr>
                    <td><?php echo Lang::txt('HEIGHT'); ?>:</td>
                    <td><?php echo $height; ?> px</td>
                </tr>
                <tr>
                    <td><input type="hidden" name="currentfile" value="<?php echo $this->file; ?>" /></td>
                    <?php $deleteTxt = Lang::txt('JACTION_DELETE'); ?>
                    <?php
                    $deleteHref = 'index.php?option=' . $this->option
                        . '&amp;controller=media&amp;task=deleteimg&amp;file='
                        . $this->file . '&amp;id=' . $this->id
                        . '&amp;no_html=1';
                    ?>
                    <td><a href="<?php echo $deleteHref; ?>">[ <?php echo $deleteTxt; ?> ]</a></td>
                </tr>
            <?php } else { ?>
                <tr>
                    <td colspan="4">
                        <img
                            src="<?php echo $this->default_picture; ?>"
                            alt="<?php echo Lang::txt('NO_MEMBER_PICTURE'); ?>"/>
                        <input type="hidden" name="currentfile" value="" />
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </form>
</div>
