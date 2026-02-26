<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$route = 'index.php?option=com_publications&task=submit';
$url   = Route::url($route . '&pid=' . $this->pub->id);

$this->css()
    ->js()
    ->css('provisioned')
    ->js('setup');

?>
<div id="project-wrap">
    <section class="main section">
        <header id="content-header">
            <h2><?php echo $this->title; ?></h2>
        </header>

        <?php $mySubmissions = ucfirst(Lang::txt('COM_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS')); ?>
        <?php $truncTitle = \Hubzero\Utility\Str::truncate($this->pub->title, 65); ?>
        <h3 class="prov-header">
            <a href="<?php echo $route; ?>"><?php echo $mySubmissions; ?></a>
            &raquo;
            <a href="<?php echo $url; ?>">
                "<?php echo $truncTitle; ?>"</a>
            &raquo;
            <?php echo Lang::txt('COM_PROJECTS_PROVISIONED_PROJECT'); ?>
        </h3>

        <?php
            // Display status message
            $this->view('_statusmsg', 'projects')
                 ->set('error', $this->getError())
                 ->set('msg', $this->msg)
                 ->display();
        ?>

        <div id="activate-intro">
            <div class="grid">
                <div class="col span6 first">
                    <h3><?php echo Lang::txt('COM_PROJECTS_ACTIVATE_WHAT_YOU_GET'); ?></h3>
                    <ul id="activate-features">
                        <li id="feature-files">
                            <span class="ima">&nbsp;</span>
                            <span class="desc"><?php echo Lang::txt('COM_PROJECTS_ACTIVATE_GET_REPOSITORY'); ?></span>
                        </li>
                        <li id="feature-todo">
                            <span class="ima">&nbsp;</span>
                            <span class="desc"><?php echo Lang::txt('COM_PROJECTS_ACTIVATE_GET_TODO'); ?></span>
                        </li>
                        <li id="feature-wiki">
                            <span class="ima">&nbsp;</span>
                            <span class="desc"><?php echo Lang::txt('COM_PROJECTS_ACTIVATE_GET_WIKI'); ?></span>
                        </li>
                        <li id="andmore">
                            <?php
                            $routeUrl3 = Route::url('index.php?option=' . $this->option . '&task=features');
                            $langTxt4 = Lang::txt('COM_PROJECTS_ACTIVATE_AND_MORE');
                            ?>
                            <a href="<?php echo $routeUrl3; ?>"><?php echo $langTxt4; ?></a>
                        </li>
                    </ul>
                </div>
                <div class="col span6 omega">
                    <div id="activate-body">
                        <h3><?php echo Lang::txt('COM_PROJECTS_ACTIVATE_YOUR_NEW_PROJECT'); ?></h3>
                        <?php
                        $activateUrl = Route::url(
                            'index.php?option=com_projects&alias='
                            . $this->model->get('alias')
                            . '&task=activate'
                        );
                        ?>
                        <form
                            action="<?php echo $activateUrl; ?>"
                            method="post"
                            id="activate-form"
                            enctype="multipart/form-data"
                        >
                            <fieldset>
                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?php echo $this->model->get('id'); ?>"
                                    id="projectid"
                                />
                                <input type="hidden" name="task" value="activate" />
                                <input type="hidden" name="confirm" value="1" />
                                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                                <input
                                    type="hidden"
                                    name="verified"
                                    id="verified"
                                    value="<?php echo $this->verified; ?>"
                                />
                                <input type="hidden" name="pubid" value="<?php echo $this->pub->id; ?>" />
                            </fieldset>
                            <div id="activate-summary">
                                <p>
                                    <span class="activate-label">Publication:</span>
                                    <span class="prominent"><?php echo $this->pub->title; ?></span>
                                </p>
                                <p>
                                    <span class="activate-label">
                                        <?php echo Lang::txt('COM_PROJECTS_TEAM'); ?>:</span> <?php echo $this->team; ?>
                                </p>
                            </div>
                            <fieldset>
                                <label for="field-title">
                                    <?php
                                    $langTxt7 = Lang::txt('COM_PROJECTS_PROJECT_TITLE');
                                    $langTxt8 = Lang::txt('COM_PROJECTS_HINTS_TITLE');
                                    ?>
                                    <?php
                                    $tooltipTitle1 = $langTxt7 . ' :: ' . $langTxt8;
                                    ?>
                                    <span
                                        class="pub-info-pop tooltips"
                                        title="<?php echo $tooltipTitle1; ?>"
                                    >&nbsp;</span>
                                    <?php echo Lang::txt('COM_PROJECTS_PROJECT_TITLE'); ?>
                                    <?php $pubTitle = $this->pub->title; ?>
                                    <input
                                        name="title"
                                        id="field-title"
                                        maxlength="250"
                                        type="text"
                                        value="<?php echo $pubTitle; ?>"
                                        class="verifyme long"
                                    />
                                </label>

                                <label for="field-alias">
                                    <?php
                                    $langTxt9 = Lang::txt('COM_PROJECTS_CHOOSE_ALIAS');
                                    $langTxt10 = Lang::txt('COM_PROJECTS_HINTS_NAME');
                                    ?>
                                    <?php
                                    $tooltipTitle2 = $langTxt9 . '::' . $langTxt10;
                                    ?>
                                    <span
                                        class="pub-info-pop tooltips"
                                        title="<?php echo $tooltipTitle2; ?>"
                                    >&nbsp;</span>
                                    <?php echo Lang::txt('COM_PROJECTS_ALIAS_NAME'); ?>
                                    <span class="verification"></span>
                                    <?php $suggestedAlias = $this->suggested; ?>
                                    <input
                                        name="new-alias"
                                        id="field-alias"
                                        maxlength="30"
                                        type="text"
                                        value="<?php echo $suggestedAlias; ?>"
                                        class="verifyme long"
                                    />
                                </label>
                                <p class="submitarea">
                                    <?php
                                    $activateTxt = Lang::txt('COM_PROJECTS_ACTIVATE_CREATE_A_PROJECT');
                                    ?>
                                    <input
                                        type="submit"
                                        id="b-continue"
                                        class="btn btn-primary active"
                                        value="<?php echo $activateTxt; ?>"
                                    />
                                    <span class="btn btncancel">
                                        <a href="<?php echo $url; ?>"><?php
                                            echo Lang::txt(JCANCEL);
                                        ?></a>
                                    </span>
                                </p>
                            </fieldset>
                        </form>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div><!-- / #introduction.section -->
        <div class="clear"></div>
    </section><!-- / .main section -->
</div>