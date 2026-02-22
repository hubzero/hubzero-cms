<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>
</header>

<section id="import" class="section">
    <div class="section-inner">
        <?php foreach ($this->messages as $message) { ?>
            <p class="<?php echo $message['type']; ?>"><?php echo $message['message']; ?></p>
        <?php } ?>

        <?php
        $baseUrl = Request::base(true);
        $step1 = Lang::txt('COM_CITATIONS_IMPORT_STEP1');
        $step1Name = Lang::txt('COM_CITATIONS_IMPORT_STEP1_NAME');
        $step2 = Lang::txt('COM_CITATIONS_IMPORT_STEP2');
        $step2Name = Lang::txt('COM_CITATIONS_IMPORT_STEP2_NAME');
        $step3 = Lang::txt('COM_CITATIONS_IMPORT_STEP3');
        $step3Name = Lang::txt('COM_CITATIONS_IMPORT_STEP3_NAME');
        $uploadUrl = Route::url(
            'index.php?option=' . $this->option . '&task=import_upload'
        );
        ?>
        <ul id="steps">
            <li>
                <a href="<?php echo $baseUrl; ?>/citations/import" class="active">
                    <?php echo $step1; ?><span><?php echo $step1Name; ?></span>
                </a>
            </li>
            <li>
                <a><?php echo $step2; ?><span><?php echo $step2Name; ?></span></a>
            </li>
            <li>
                <a><?php echo $step3; ?><span><?php echo $step3Name; ?></span></a>
            </li>
        </ul><!-- / #steps -->

        <form id="hubForm" enctype="multipart/form-data" method="post" action="<?php echo $uploadUrl; ?>">
            <p class="explaination">
                <strong><u><?php echo Lang::txt('COM_CITATIONS_IMPORT_ACCEPTABLE'); ?></u></strong><br />
                <?php echo implode("<br />", $this->accepted_files); ?>
            </p>
            <fieldset>
                <legend><?php echo Lang::txt('COM_CITATIONS_IMPORT_UPLOAD'); ?>:</legend>
                <?php $uploadLabel = Lang::txt('COM_CITATIONS_IMPORT_UPLOAD_FILE'); ?>
                <label><?php echo $uploadLabel; ?>: <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
                    <input type="file" name="citations_file" />
                    <span class="hint"><?php echo Lang::txt('COM_CITATIONS_IMPORT_UPLOAD_MAX'); ?></span>
                </label>
            </fieldset>

            <p class="submit">
                <input type="submit" name="submit" value="<?php echo Lang::txt('COM_CITATIONS_IMPORT_UPLOAD'); ?>" />
            </p>

            <?php echo Html::input('token'); ?>
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <?php if (isset($this->gid)) : ?>
                <input type="hidden" name="group" value="<?php echo $this->gid; ?>" />
            <?php endif; ?>
            <input type="hidden" name="task" value="import_upload" />
        </form>
    </div>
</section><!-- / .section -->
