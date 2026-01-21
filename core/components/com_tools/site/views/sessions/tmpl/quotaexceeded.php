<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('tools.css');
?>
<header id="content-header">
    <h2><?php echo Lang::txt('COM_TOOLS_QUOTAEXCEEDED'); ?></h2>
</header><!-- / #content-header -->

<?php
$sectionClass = 'main section';
if (!$this->config->get('access-manage-session') && $this->active == 'all') {
    $sectionClass .= ' hide';
}
?>
<section class="<?php echo $sectionClass; ?>" id="mysessions-section">
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
                $resumeUrl = Route::url(
                    'index.php?option=' . $this->option
                    . '&controller=' . $this->controller
                    . '&task=session&app=' . $session->appname
                    . '&sess=' . $session->sessnum
                );
                $resumeTitle = Lang::txt('COM_TOOLS_RESUME_TITLE');
                ?>
                <td>
                    <a href="<?php echo $resumeUrl; ?>"
                        title="<?php echo $resumeTitle; ?>"
                    ><?php echo $session->sessname; ?></a>
                </td>
                <td><?php echo $session->start; ?></td>
                <td><?php echo $session->accesstime; ?></td>
                <?php if (User::get('username') == $session->username) {
                    $stopUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=' . $this->controller
                        . '&task=stop&app=' . $session->appname
                        . '&sess=' . $session->sessnum
                    );
                    $termTitle = Lang::txt('COM_TOOLS_TERMINATE_TITLE');
                    $termLabel = Lang::txt('COM_TOOLS_TERMINATE');
                    ?>
                <td>
                    <a class="closetool"
                        href="<?php echo $stopUrl; ?>"
                        title="<?php echo $termTitle; ?>"
                    ><?php echo $termLabel; ?></a>
                </td>
                <?php } else {
                    $unshareUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=' . $this->controller
                        . '&task=unshare&app=' . $session->appname
                        . '&sess=' . $session->sessnum
                    );
                    $disconnTitle = Lang::txt('COM_TOOLS_DISCONNECT_TITLE');
                    $disconnLabel = Lang::txt('COM_TOOLS_DISCONNECT');
                    $ownerLabel = Lang::txt('COM_TOOLS_MY_SESSIONS_OWNER')
                        . ': ' . $session->username;
                    ?>
                <td>
                    <a class="disconnect"
                        href="<?php echo $unshareUrl; ?>"
                        title="<?php echo $disconnTitle; ?>"
                    ><?php echo $disconnLabel; ?></a>
                    <span class="owner"><?php echo $ownerLabel; ?></span>
                </td>
                <?php } ?>
            </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
</section><!-- / .section -->
