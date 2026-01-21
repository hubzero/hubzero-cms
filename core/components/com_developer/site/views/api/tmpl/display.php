<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('introduction', 'system')
     ->css('intro')
     ->css();

$docsUrl = Route::url('index.php?option=com_developer&controller=api&task=docs');
$appsUrl = Route::url('index.php?option=com_developer&controller=applications');
$authAppsUrl = Route::url('index.php?option=com_developer&controller=applications#authorized');
$newAppUrl = Route::url('index.php?option=com_developer&controller=applications&task=new');
?>

<header id="content-header">
    <h2><?php echo Lang::txt('COM_DEVELOPER_API_HOME'); ?></h2>

    <div id="content-header-extra">
        <p>
            <a
                class="btn icon-code"
                href="<?php echo Route::url('index.php?option=com_developer'); ?>"
            >
                <?php echo Lang::txt('COM_DEVELOPER'); ?>
            </a>
        </p>
    </div>
</header>

<section id="introduction" class="section api">
    <div class="section-inner">
        <div class="grid">
            <div class="col span8">
                <h3><?php echo Lang::txt('COM_DEVELOPER_API_GETSTARTED'); ?></h3>
                <p><?php echo Lang::txt('COM_DEVELOPER_API_GETSTARTED_DESC'); ?></p>
            </div>
            <div class="col span4 hasOnlyButton omega">
                <p>
                    <a class="btn icon-docs" href="<?php echo $docsUrl; ?>">
                        <?php echo Lang::txt('COM_DEVELOPER_API_LINK_DOCUMENTATION'); ?>
                    </a>
                </p>
            </div>
        </div>
    </div>
</section>

<section class="main section">
    <div class="section-inner">
        <div class="grid">
            <div class="col span3">
                <h2><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS'); ?></h2>
            </div>
            <div class="col span3">
                <h3>
                    <a href="<?php echo $appsUrl; ?>">
                        <?php echo Lang::txt('COM_DEVELOPER_API_MY_APPLICATIONS'); ?>
                    </a>
                </h3>
                <p><?php echo Lang::txt('COM_DEVELOPER_API_MY_APPLICATIONS_DESC'); ?></p>
                <p>
                    <a href="<?php echo $appsUrl; ?>">
                        <?php echo Lang::txt('COM_DEVELOPER_API_MANAGE'); ?>
                    </a>
                </p>
            </div>
            <div class="col span3">
                <h3>
                    <a href="<?php echo $authAppsUrl; ?>">
                        <?php echo Lang::txt('COM_DEVELOPER_API_AUTHORIZED_APPLICATIONS'); ?>
                    </a>
                </h3>
                <p><?php echo Lang::txt('COM_DEVELOPER_API_AUTHORIZED_APPLICATIONS_DESC'); ?></p>
                <p>
                    <a href="<?php echo $authAppsUrl; ?>">
                        <?php echo Lang::txt('COM_DEVELOPER_API_MANAGE'); ?>
                    </a>
                </p>
            </div>
            <div class="col span3 omega">
                <h3>
                    <a href="<?php echo $newAppUrl; ?>">
                        <?php echo Lang::txt('COM_DEVELOPER_API_NEW_APPLICATION'); ?>
                    </a>
                </h3>
                <p><?php echo Lang::txt('COM_DEVELOPER_API_NEW_APPLICATION_DESC'); ?></p>
                <p>
                    <a href="<?php echo $newAppUrl; ?>">
                        <?php echo Lang::txt('COM_DEVELOPER_API_CREATE'); ?>
                    </a>
                </p>
            </div>
        </div>
        <?php /*<div class="grid">
            <div class="col span3">
                <h2><?php echo Lang::txt('COM_DEVELOPER_API_LEARN'); ?></h2>
            </div>
            <div class="col span3">
                <h3><a href="<?php echo $docsUrl; ?>">
                    <?php echo Lang::txt('COM_DEVELOPER_API_DOCS'); ?>
                </a></h3>
                <p><?php echo Lang::txt('COM_DEVELOPER_API_DOCS_DESC'); ?></p>
                <p><a href="<?php echo $docsUrl; ?>">
                    <?php echo Lang::txt('COM_DEVELOPER_API_MANAGE'); ?>
                </a></p>
            </div>
            <div class="col span3">
                <?php
                $consoleUrl = Route::url(
                    'index.php?option=com_developer&controller=api&task=console'
                );
                ?>
                <h3><a href="<?php echo $consoleUrl; ?>">
                    <?php echo Lang::txt('COM_DEVELOPER_API_CONSOLE'); ?>
                </a></h3>
                <p><?php echo Lang::txt('COM_DEVELOPER_API_CONSOLE_DESC'); ?></p>
                <p><a href="<?php echo $consoleUrl; ?>">
                    <?php echo Lang::txt('COM_DEVELOPER_API_MANAGE'); ?>
                </a></p>
            </div>
            <div class="col span3 omega">
                <?php
                $statusUrl = Route::url(
                    'index.php?option=com_developer&controller=api&task=status'
                );
                ?>
                <h3><a href="<?php echo $statusUrl; ?>">
                    <?php echo Lang::txt('COM_DEVELOPER_API_STATUS'); ?>
                </a></h3>
                <p><?php echo Lang::txt('COM_DEVELOPER_API_STATUS_DESC'); ?></p>
                <p><a href="<?php echo $statusUrl; ?>">
                    <?php echo Lang::txt('COM_DEVELOPER_API_CREATE'); ?>
                </a></p>
            </div>
        </div>*/ ?>
    </div>
</section>
