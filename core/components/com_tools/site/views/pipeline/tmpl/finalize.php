<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{status: mixed, title: mixed} $this
 */

// get tool access text
$toolaccess = \Components\Tools\Helpers\Html::getToolAccess($this->status['exec'], $this->status['membergroups']);
// get source code access text
$codeaccess = \Components\Tools\Helpers\Html::getCodeAccess($this->status['code']);
// get wiki access text
$wikiaccess = \Components\Tools\Helpers\Html::getWikiAccess($this->status['wiki']);

$this
     ->css('pipeline.css')
     ->js('pipeline.js');
?>
<header id="content-header">
    <h2><?php echo $this->escape($this->title); ?></h2>

    <div id="content-header-extra">
        <ul id="useroptions">
            <?php
            $statusUrl = Route::url(
                'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app='
                    . $this->status['toolname'],
            );
            ?>
            <li>
                <a class="icon-status status btn" href="<?= $statusUrl ?>"><?= Lang::txt('COM_TOOLS_TOOL_STATUS') ?></a>
            </li>
            <?php
            $createUrl = Route::url(
                'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=create',
            );
            ?>
            <li><a class="icon-add add btn"
                href="<?= $createUrl ?>"><?= Lang::txt('COM_TOOLS_CONTRIBTOOL_NEW_TOOL') ?></a></li>
        </ul>
    </div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
    <?php \Components\Tools\Helpers\Html::writeApproval('Approve'); ?>

    <?php if ($this->getError()) { ?>
        <p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
    <?php } ?>

    <h4><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_FINAL_REVIEW'); ?>:</h4>
    <?php
    $finalizeversionUrl = Route::url(
        'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=finalizeversion&app='
            . $this->status['toolname'],
    );
    ?>
    <form action="<?= $finalizeversionUrl ?>" method="post" id="versionForm" name="versionForm">
        <fieldset class="versionfield">
            <div class="grid">
                <div class="col span-half">
                    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
                    <input type="hidden" name="task" value="finalizeversion" />
                    <input type="hidden" name="newstate"
                        value="<?php echo \Components\Tools\Helpers\Html::getStatusNum('Approved'); ?>" />
                    <input type="hidden" name="id" value="<?php echo $this->status['toolid']; ?>" />
                    <input type="hidden" name="app" value="<?php echo $this->status['toolname']; ?>" />
                    <?php echo Html::input('token'); ?>
                    <div>
                        <?php
                        $editUrl = Route::url(
                            'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&app='
                                . $this->status['toolname'],
                        );
                        ?>
                        <h4>Tool Information <a class="edit button" href="<?= $editUrl ?>"
                            title="Edit this version information">Edit</a></h4>
                        <p><span class="heading"><?= Lang::txt('COM_TOOLS_TITLE') ?>: </span><span
                            class="desc"><?= $this->escape(stripslashes($this->status['title'])) ?></span></p>
                        <p><span class="heading"><?= Lang::txt('COM_TOOLS_VERSION') ?>: </span><span
                            class="desc"><?= $this->escape(stripslashes($this->status['version'])) ?></span>
                            <?php
                            $versionsUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller=' . $this->controller
                                    . '&task=versions&action=confirm&app=' . $this->status['toolname'],
                            );
                            ?>
                            <span class="actionlink">[<a href="<?= $versionsUrl ?>">edit</a>]</span></p>
                        <p><span class="heading"><?= Lang::txt('COM_TOOLS_DESCRIPTION') ?>: </span><span
                            class="desc"><?= $this->escape(stripslashes($this->status['description'])) ?></span></p>
                        <p><span class="heading"><?= Lang::txt('COM_TOOLS_TOOL_ACCESS') ?>: </span><span
                            class="desc"> <?= $toolaccess ?></span></p>
                        <p><span class="heading"><?= Lang::txt('COM_TOOLS_SOURCE_CODE') ?>: </span><span
                            class="desc"> <?= $codeaccess ?></span></p>
                        <p><span class="heading"><?= Lang::txt('COM_TOOLS_WIKI_ACCESS') ?>: </span><span
                            class="desc"> <?= $wikiaccess ?></span></p>
                        <p><span class="heading"><?= Lang::txt('COM_TOOLS_SCREEN_SIZE') ?>: </span><span
                            class="desc"> <?= $this->escape($this->status['vncGeometry']) ?></span></p>
                        <?php
                        $descText = \Components\Tools\Helpers\Html::getDevTeam($this->status['developers']);
                        ?>
                        <p><span class="heading"><?= Lang::txt('COM_TOOLS_DEVELOPERS') ?>: </span><span
                            class="desc"> <?= $descText ?></span></p>
                        <?php
                        $descText = \Components\Tools\Helpers\Html::getDevTeam($this->status['authors']);
                        ?>
                        <p><span class="heading"><?= Lang::txt('COM_TOOLS_AUTHORS') ?>: </span><span
                            class="desc"> <?= $descText ?></span></p>
                        <?php
                        $linkUrl = Route::url(
                            'index.php?option=com_resources&alias=' . $this->status['toolname'] . '&rev=dev',
                        );
                        ?>
                        <p><a href="<?= $linkUrl ?>"><?= Lang::txt('COM_TOOLS_PREVIEW_RES_PAGE') ?></a></p>
                    </div>
                </div>
                <?php if ($this->status['license']) { ?>
                <div class="col span-half omega">
                    <h4>
                        <?php echo Lang::txt('COM_TOOLS_TOOL_LICENSE'); ?>
                        <span class="actionlink">
                            <?php
                            $licenseUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller=' . $this->controller
                                    . '&task=license&app=' . $this->status['toolname'] . '&action=confirm',
                            );
                            ?>
                            [<a href="<?= $licenseUrl ?>"><?= Lang::txt('COM_TOOLS_EDIT') ?></a>]
                        </span>
                    </h4>
                    <pre class="licensetxt"><?php echo $this->escape(stripslashes($this->status['license'])); ?></pre>
                </div>
                <?php } ?>
            </div><!-- / .grid -->
            <div class="moveon">
                <input type="submit" value="<?php echo Lang::txt('COM_TOOLS_APPROVE_THIS_TOOL'); ?>" />
            </div>
        </fieldset>
    </form>
</section>