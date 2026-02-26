<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

$this->css('import.css')
    ->js('import.js');

$base = $this->member->link() . '&active=citations';
?>
<section id="import" class="section">
    <div class="section-inner">
        <?php foreach ($this->messages as $message) { ?>
            <p class="<?php echo $message['type']; ?>"><?php echo $message['message']; ?></p>
        <?php } ?>

        <?php
        $importUrl = Route::url($base . '&task=import');
        $step1 = Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP1');
        $step1Name = Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP1_NAME');
        $step2 = Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP2');
        $step2Name = Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP2_NAME');
        $step3 = Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP3');
        $step3Name = Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP3_NAME');
        ?>
        <ul id="steps">
            <li>
                <a href="<?php echo $importUrl; ?>" class="active">
                    <?php echo $step1; ?>
                    <span><?php echo $step1Name; ?></span>
                </a>
            </li>
            <li>
                <a>
                    <?php echo $step2; ?>
                    <span><?php echo $step2Name; ?></span>
                </a>
            </li>
            <li>
                <a>
                    <?php echo $step3; ?>
                    <span><?php echo $step3Name; ?></span>
                </a>
            </li>
        </ul><!-- / #steps -->

        <form id="hubForm"
            class="full"
            enctype="multipart/form-data"
            method="post"
            action="<?php echo Route::url($base . '&task=upload'); ?>">
            <fieldset>
                <legend><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_UPLOAD'); ?>:</legend>

                <div class="grid">
                    <div class="col span6">
                        <label for="citations_file">
                            <?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_UPLOAD_FILE'); ?>:
                            <span class="required">
                                <?php echo Lang::txt('JREQUIRED'); ?>
                            </span>
                            <input
                                type="file"
                                name="citations_file"
                                id="citations_file"
                            />
                            <span class="hint">
                                <?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_UPLOAD_MAX'); ?>
                            </span>
                        </label>
                    </div>
                    <div class="col span6 omega">
                        <p>
                            <strong><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_ACCEPTABLE'); ?></strong><br />
                            <?php echo implode('<br />', $this->accepted_files); ?>
                        </p>
                    </div>
                </div>
            </fieldset>

            <p class="submit">
                <input type="submit"
                    class="btn btn-success"
                    name="submit"
                    value="<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_UPLOAD'); ?>"/>

                <a class="btn btn-secondary" href="<?php echo Route::url($base); ?>">
                    <?php echo Lang::txt('JCANCEL'); ?>
                </a>
            </p>

            <?php echo Html::input('token'); ?>
            <input type="hidden" name="option" value="com_members" />
            <input type="hidden" name="id" value="<?php echo $this->member->get('id'); ?>" />
            <input type="hidden" name="active" value="citations" />
            <input type="hidden" name="action" value="upload" />
        </form>
    </div>
</section><!-- / .section -->
