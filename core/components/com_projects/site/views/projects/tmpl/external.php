<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js()
     ->css('external')
     ->css('extended.css');

// Get project params
$params = $this->model->params;
$theme = $params->get('theme', $this->config->get('theme', 'light'));

// Include theme CSS
$this->css('theme' . $theme . '.css');

?>
<div id="project-wrap" class="theme publicview">
    <div id="content-header-extra">
        <ul id="useroptions">
            <?php
            $routeUrl = Route::url('index.php?option=' . $this->option . '&task=browse');
            $langTxt2 = Lang::txt('COM_PROJECTS_ALL_PROJECTS');
            ?>
            <li><a class="btn icon-browse" href="<?php echo $routeUrl; ?>"><?php echo $langTxt2; ?></a></li>
            <?php if (User::authorise('core.create', $this->option)) { ?>
                <?php
                $routeUrl3 = Route::url('index.php?option=' . $this->option . '&task=start');
                $langTxt4 = Lang::txt('COM_PROJECTS_START_NEW');
                ?>
                <li><a class="btn icon-add" href="<?php echo $routeUrl3; ?>"><?php echo $langTxt4; ?></a></li>
            <?php } ?>
        </ul>
    </div><!-- / #content-header-extra -->

    <?php if ($this->model->access('member') && !$this->reviewer) { // Public preview for authorized users ?>
        <div id="project-preview">
            <?php
            $returnUrl = Route::url(
                'index.php?option=' . $this->option
                . '&alias=' . $this->model->get('alias')
            );
            ?>
            <p><?php echo Lang::txt('COM_PROJECTS_THIS_IS_PROJECT_PREVIEW'); ?>
            <span><?php echo Lang::txt('COM_PROJECTS_RETURN_TO'); ?>
            <a href="<?php echo $returnUrl; ?>"><?php
                echo Lang::txt('COM_PROJECTS_PROJECT_PAGE');
            ?></a></span></p>
        </div>
    <?php } elseif ($this->reviewer) { ?>
        <div id="project-preview">
            <?php
            $browseUrl = Route::url(
                'index.php?option=' . $this->option
                . '&task=browse&reviewer=' . $this->reviewer
            );
            ?>
            <p><?php echo Lang::txt('COM_PROJECTS_REVIEWER_PROJECT_PREVIEW'); ?>
            <span><?php echo Lang::txt('COM_PROJECTS_RETURN_TO'); ?>
            <a href="<?php echo $browseUrl; ?>"><?php
                echo Lang::txt('COM_PROJECTS_PROJECT_LIST');
            ?></a></span></p>
        </div>
    <?php } ?>

    <?php
    // Draw top header
    $this->view('_topheader')
         ->set('model', $this->model)
         ->set('publicView', true)
         ->set('option', $this->option)
         ->display();

    // Draw top menu
    $this->view('_topmenu', 'projects')
         ->set('model', $this->model)
         ->set('active', $this->active)
         ->set('tabs', $this->tabs)
         ->set('option', $this->option)
         ->set('guest', User::isGuest())
         ->set('publicView', true)
         ->display();
    ?>

    <section class="main section">
        <div class="project-inner-wrap grid">
            <?php $member = $this->model->member(); ?>
            <?php
            $routeUrl13 = Route::url(
                'index
    . php?option=com_projects&task=requestaccess&alias='
                . $this->model->get('alias')
                . '&'
                . Session::getFormToken()
                . '=1'
            );
            ?>
            <?php $link = $routeUrl13; ?>
            <?php if ($this->model->allowMembershipRequest()) : ?>
                <?php if (!$member || $member->status == 2) : ?>
                    <div class="btn-container tooltips span4">
                        <?php
                        $langTxt14 = Lang::txt('COM_PROJECTS_REQUEST_MEMBERSHIP');
                        ?>
                        <a href="<?php echo $link;?>" class="tooltips btn btn-success"><?php echo $langTxt14;?></a>
                    </div>
                <?php elseif ($member->get('status') == 3) : ?>
                    <div class="btn-container tooltips span4" title="Membership Request Pending">
                        <a href="<?php echo $link; ?>" class="tooltips btn btn-success" disabled>
                            <?php echo Lang::txt('COM_PROJECTS_REQUEST_MEMBERSHIP');?></a>
                    </div>
                <?php elseif ($member->get('status') == 4) : ?>
                    <?php
                        $params = new Hubzero\Config\Registry($member->get('params'));
                        $denyMessage = 'Membership has been denied. <br/>';
                        $denyMessage .= 'Reason: <br/>';
                        $denyMessage .= $params->get('denyMessage');
                    ?>

                    <div class="btn-container tooltips span4" title="<?php echo $denyMessage;?>">
                        <?php
                        $langTxt16 = Lang::txt('COM_PROJECTS_REQUEST_MEMBERSHIP');
                        ?>
                        <a href="<?php echo $link; ?>" class="btn btn-success" disabled><?php echo $langTxt16;?></a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
                        <?php if ($this->model->about('parsed')) {
                                $val = $this->model->about('parsed');
                                $componentPath = Component::path('com_redirect');
                            if ($componentPath) {
                                    require_once($componentPath . DS . "helpers" . DS . "converter.php");
                                    $val = \Component\Redirect\Helpers\Converter::convert($val);
                            } else {
                                    $val = preg_replace(
                                        '#<a\s[^>]*href="([^"]*)"[^>]*?>(.*?)</a>#is',
                                        "<a href='$1' rel='nofollow'>$2</a>",
                                        $val
                                    );
                            }
                            ?>
                <div class="public-list-header">
                    <h3><?php echo Lang::txt('COM_PROJECTS_ABOUT'); ?></h3>
                </div>
                <div class="public-list-wrap">
                            <?php echo $val; ?>
                </div>
                        <?php } ?>
            <?php
            // Side blocks from plugins?
            $sections = Event::trigger('projects.onProjectPublicList', array($this->model));

            if (!empty($sections)) {
                foreach ($sections as $section) {
                    echo !empty($section) ? $section : null;
                }
            }
            ?>
        </div>
    </section><!-- / .main section -->
</div>
