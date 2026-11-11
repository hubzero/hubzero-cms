<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;
use Hubzero\Facades\Session;

// No direct access.
defined('_HZEXEC_') or die();

$this->css('tools.css');
?>
<header id="content-header">
    <h2><?php echo Lang::txt('COM_TOOLS_QUOTAEXCEEDED'); ?></h2>
</header><!-- / #content-header -->

<section class="main section<?php if (!$this->config->get('access-manage-session') && $this->active == 'all') {
    echo ' hide';
                            } else {
                                echo '';
                            } ?>" id="mysessions-section">
    <p class="warning"><?php echo Lang::txt('COM_TOOLS_ERROR_QUOTAEXCEEDED'); ?></p>
    <table class="sessions">
        <thead>
            <tr>
                <th><?php echo Lang::txt('COM_TOOLS_MYSESSIONS_COL_SESSION'); ?></th>
                <th><?php echo Lang::txt('COM_TOOLS_MYSESSIONS_COL_STARTED'); ?></th>
                <th><?php echo Lang::txt('COM_TOOLS_MYSESSIONS_COL_LAST_ACCESSED'); ?></th>
                <th><?php echo Lang::txt('COM_TOOLS_MYSESSIONS_COL_OPTION'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($this->sessions) {
            $cls = 'even';
            foreach ($this->sessions as $session) {
                $cls = ($cls == 'odd') ? 'even' : 'odd';
                ?>
            <tr class="<?php echo $cls; ?>">
                <?php
                $sessionUrl = Route::url(
                    'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=session&app='
                        . $session->appname . '&sess=' . $session->sessnum,
                );
                ?>
                <td><a href="<?= $sessionUrl ?>"
                    title="<?= Lang::txt('COM_TOOLS_RESUME_TITLE') ?>"><?= $this->escape($session->sessname) ?></a></td>
                <td><?php echo $session->start; ?></td>
                <td><?php echo $session->accesstime; ?></td>
                <?php if (User::get('username') == $session->username) {
                    $stopUrl = Route::url(
                        'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=stop&app='
                            . $session->appname . '&sess=' . $session->sessnum . '&' . Session::getFormToken() . '=1',
                    );
                    ?>
                    <?php
                    $titleText = Lang::txt('COM_TOOLS_TERMINATE_TITLE');
                    ?>
                <td><a class="closetool" href="<?= $stopUrl ?>"
                    title="<?= $titleText ?>"><?= Lang::txt('COM_TOOLS_TERMINATE') ?></a></td>
                <?php } else {
                    $unshareUrl = Route::url(
                        'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=unshare&app='
                            . $session->appname . '&sess=' . $session->sessnum . '&' . Session::getFormToken() . '=1',
                    );
                    ?>
                    <?php
                    $linkText = Lang::txt('COM_TOOLS_DISCONNECT');
                    $ownerText = Lang::txt('COM_TOOLS_MY_SESSIONS_OWNER') . ': ' . $session->username;
                    ?>
                <td><a class="disconnect" href="<?= $unshareUrl ?>"
                    title="<?= Lang::txt('COM_TOOLS_DISCONNECT_TITLE') ?>"><?= $linkText ?></a> <span
                    class="owner"><?= $ownerText ?></span></td>
                <?php } ?>
            </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
</section><!-- / .section -->
