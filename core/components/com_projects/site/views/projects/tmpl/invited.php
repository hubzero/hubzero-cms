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

$html  = '';

$rtrn = Request::getString('REQUEST_URI', Route::url('index.php?option='
    . $this->option
    . '&task='
    . $this->task), 'server');

?>
<div id="project-wrap">
    <section class="main section">
        <?php
            $this->view('_header')
                 ->set('model', $this->model)
                 ->set('showPic', 1)
                 ->set('showPrivacy', 0)
                 ->set('goBack', 0)
                 ->set('showUnderline', 1)
                 ->set('option', $this->option)
                 ->display();
        ?>
        <h3><?php echo Lang::txt('COM_PROJECTS_INVITED_CONFIRM'); ?></h3>
        <div id="confirm-invite" class="invitation">
            <div class="grid">
                <div class="col span6">
                    <p>
                        <?php
                        $langTxt1 = Lang::txt('COM_PROJECTS_INVITED_NEED_ACCOUNT_TO_JOIN');
                        $langTxt2 = Lang::txt('COM_PROJECTS_INVITED_CONFIRM_SCREEN');
                        ?>
                        <?php echo $langTxt2 . ' "' . $this->model->get('title') . '". ' . $langTxt1; ?>
                    </p>
                </div>
                <div class="col span6 omega">
                    <p>
                        <?php
                        $routeUrl3 = Route::url(
                            'index
    . php?option=com_users&view=login&return='
                            . base64_encode($rtrn)
                        );
                        $langTxt4 = Lang::txt('COM_PROJECTS_INVITED_HAVE_ACCOUNT');
                        $langTxt5 = Lang::txt('COM_PROJECTS_INVITED_PLEASE_LOGIN');
                        ?>
                        <?php echo $langTxt4 . ' <a href="' . $routeUrl3 .  '">' . $langTxt5 . '</a>'; ?>
                    </p>
                    <p>
                        <?php
                        $routeUrl6 = Route::url(
                            'index
    . php?option=com_members&controller=register&return='
                            . base64_encode($rtrn)
                        );
                        $langTxt7 = Lang::txt('COM_PROJECTS_INVITED_DO_NOT_HAVE_ACCOUNT');
                        $langTxt8 = Lang::txt('COM_PROJECTS_INVITED_PLEASE_REGISTER');
                        ?>
                        <?php echo $langTxt7 . ' <a href="' . $routeUrl6 .  '">' . $langTxt8 . '</a>'; ?>
                    </p>
                </div>
            </div>
        </div>
    </section><!-- / .main section -->
</div>