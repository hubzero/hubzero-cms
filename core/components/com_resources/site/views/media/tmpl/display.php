<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Html::behavior('framework', true);

$this->css('component.css')
    ->js('media.js');
?>
<?php $actionUrl = Route::url('index.php?option=' . $this->option); ?>
<form action="<?php echo $actionUrl; ?>" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">
    <fieldset>
        <label for="upload">
            <input type="file" class="option" name="upload" id="upload" />
            <input type="submit" class="option" value="<?php echo strtolower(Lang::txt('COM_CONTRIBUTE_UPLOAD')); ?>" />
        </label>

        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
        <input type="hidden" name="resource" value="<?php echo $this->resource; ?>" />
        <input type="hidden" name="task" value="upload" />
    </fieldset>

    <?php if ($this->getError()) { ?>
        <p class="error">
            <?php echo implode('<br />', $this->getErrors()); ?>
        </p>
    <?php } ?>

    <?php if (count($this->folders) > 0 || count($this->docs) > 0) { ?>
        <table>
            <tbody>
            <?php
            $docs = $this->docs;
            for ($i = 0; $i < count($docs); $i++) {
                $docName = key($docs);

                $subdird = ($this->subdir && $this->subdir != DS) ? $this->subdir . DS : DS;
                ?>
                <tr>
                    <td width="100%">
                        <?php
                        $resId = $this->row->alias
                            ? $this->row->alias
                            : $this->resource;
                        echo Route::url(
                            'index.php?option=com_resources&id='
                            . $resId . '&task=download&file='
                            . $docs[$docName]
                        );
                        ?>
                    </td>
                    <td>
                        <?php
                        $data_confirmTxt = Lang::txt(
                            'Are you sure you want to delete the file "%s"?',
                            $docs[$docName]
                        );
                        ?>
                        <a class="icon-delete delete delete-file"
                           <?php
                            $deleteHref = Request::base(true)
                               . '/index.php?option=' . $this->option
                               . '&amp;controller=' . $this->controller
                               . '&amp;task=delete&amp;file='
                               . $docs[$docName]
                               . '&amp;resource=' . $this->resource
                               . '&amp;tmpl=component&amp;subdir='
                               . $this->subdir . '&amp;'
                               . Session::getFormToken() . '=1';
                            ?>
                           href="<?php echo $deleteHref; ?>"
                           target="filer"
                           data-confirm="<?php echo $data_confirmTxt; ?>"
                           title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
                            <span><?php echo Lang::txt('JACTION_DELETE'); ?></span>
                        </a>
                    </td>
                </tr>
                <?php
                next($docs);
            }
            ?>
            </tbody>
        </table>
    <?php } ?>

    <?php echo Html::input('token'); ?>
</form>
