<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{
 *     first_vote: mixed,
 *     last_vote: mixed,
 *     lists: mixed,
 *     params: mixed,
 *     poll: mixed,
 *     votes: mixed,
 * } $this
 */

$this->css();
?>

<header id="content-header">
    <h2><?php echo Lang::txt('COM_POLL'); ?></h2>

    <div id="content-header-extra">
        <p>
            <a class="icon-stats btn" href="<?php echo Route::url('index.php?option=com_poll&view=latest'); ?>">
                <?php echo Lang::txt('COM_POLL_TAKE_LATEST_POLL'); ?>
            </a>
        </p>
    </div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<form action="<?php echo Route::url('index.php?option=com_poll&view=poll'); ?>" method="get" name="poll" id="poll">
    <section class="main section">
        <label for="id">
            <?php echo Lang::txt('COM_POLL_SELECT'); ?>
            <?php echo $this->lists['polls']; ?>
        </label>
    </section>
    <section class="below section">
        <?php
        $this
            ->view('results_graph')
            ->set('first_vote', $this->first_vote)
            ->set('last_vote', $this->last_vote)
            ->set('lists', $this->lists)
            ->set('params', $this->params)
            ->set('poll', $this->poll)
            ->set('votes', $this->votes)
            ->display();
        ?>
    </section><!-- / .main section -->
</form>