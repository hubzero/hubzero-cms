<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Component;
use Hubzero\Facades\Config;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();

Toolbar::title(Lang::txt('COM_INSTALLER_TITLE_PACKAGES'), 'install');
if ($canDo->get('core.admin')) {
    Toolbar::preferences('com_installer');
    Toolbar::divider();
}
Toolbar::help('warnings');

$example = Component::path('com_installer') . '/config/composer.json.dist';
?>
<div id="installer-warnings">
    <?php $formAction = Route::url('index.php?option=com_installer&controller=warnings'); ?>
    <form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="item-form">
        <div class="input-wrap">
            <?php
            $composerPath = PATH_APP . '/composer.json';
            $missingMsg = Lang::txt('COM_INSTALLER_MSG_WARNINGS_MISSING_COMPOSER', $composerPath);
            ?>
            <p class="error"><?php echo $missingMsg; ?></p>
            <?php
            if (file_exists($example)) :
                $contents = file_get_contents($example);
                if ($contents) :
                    ?>
                    <?php $sampleLabel = Lang::txt('COM_INSTALLER_MSG_WARNINGS_MISSING_COMPOSER_SAMPLE'); ?>
                    <label for="sample"><?php echo $sampleLabel; ?></label>
                    <textarea name="sample" id="sample" cols="100" rows="28"><?php
                    $site = preg_replace('/[^a-zA-Z0-9\-]/', '', strtolower(Config::get('sitename')));

                    $contents = json_decode($contents);
                    $contents->name = $site . '/' . $site . '-app';
                    $contents = json_encode($contents, JSON_PRETTY_PRINT);
                    $contents = str_replace('\/', '/', $contents);

                    echo $contents;
                    ?></textarea>
                    <?php
                endif;
            endif;
            ?>
        </div>
    </form>
</div>
