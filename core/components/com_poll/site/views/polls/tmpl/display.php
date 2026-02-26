<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

defined('_HZEXEC_') or die();

$this->css();
?>

<header id="content-header">
    <h2><?php echo Lang::txt('COM_POLL'); ?></h2>

    <div id="content-header-extra">
        <p>
            <a
                class="icon-stats btn"
                href="<?php echo Route::url('index.php?option=com_poll&view=latest'); ?>"
            >
                <?php echo Lang::txt('COM_POLL_TAKE_LATEST_POLL'); ?>
            </a>
        </p>
    </div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section polls">
    <div class="grid">
    <?php
    $i = 0;
    foreach ($this->polls as $poll) { ?>
        <div class="col span4<?php if ($i == 2) {
            echo ' omega';
                             } ?>">
            <div class="poll">
                <div class="details">
                    <?php if ($poll->get('open')) {
                        $voteAction = Route::url(
                            'index.php?option=com_poll&task=vote'
                        );
                        $resultsUrl = Route::url(
                            'index.php?option=com_poll&view=poll&id='
                            . $this->escape($poll->get('id'))
                        );
                        ?>
                        <form
                            id="poll<?php echo $poll->get('id'); ?>"
                            method="post"
                            action="<?php echo $voteAction; ?>"
                        >
                            <fieldset>
                                <legend>
                                    <?php echo $this->escape($poll->get('title')); ?>
                                </legend>

                                <?php $pollOptions = $poll->options()->where('text', '!=', '')->ordered()->rows(); ?>
                                <ul class="poll-options">
                                    <?php foreach ($pollOptions as $option) : ?>
                                        <li>
                                            <input
                                                type="radio"
                                                name="voteid"
                                                id="voteid<?php echo $option->id; ?>"
                                                value="<?php echo $this->escape($option->id); ?>"
                                            />
                                            <label for="voteid<?php echo $option->id; ?>">
                                                <?php echo $this->escape(str_replace('&#039;', "'", $option->text)); ?>
                                            </label>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <p>
                                    <input
                                        type="submit"
                                        name="task_button"
                                        class="button"
                                        value="<?php echo Lang::txt('COM_POLL_VOTE'); ?>"
                                    />
                                     &nbsp;
                                    <a href="<?php echo $resultsUrl; ?>">
                                        <?php echo Lang::txt('COM_POLL_RESULTS'); ?>
                                    </a>
                                </p>

                                <input type="hidden" name="option" value="com_poll" />
                                <input type="hidden" name="task" value="vote" />
                                <input type="hidden" name="id" value="<?php echo $this->escape($poll->id); ?>" />
                                <?php echo Html::input('token'); ?>
                            </fieldset>
                        </form>
                    <?php } else { ?>
                        <h3><?php echo $this->escape($poll->get('title')); ?></h3>
                        <ul class="poll-results">
                            <?php $i = 1; ?>
                            <?php $closedOptions = $poll->options()->where('text', '!=', '')->ordered()->rows(); ?>
                            <?php foreach ($closedOptions as $option) : ?>
                                <?php
                                $option->percent = ($poll->voters
                                    ? round(100 * $option->hits / $poll->voters, 1)
                                    : 0);
                                $option->class   = 'polls_color_' . $i;
                                $i++;

                                $this->css('
									.' . $this->option . ' .option' . $option->id . ' {
										width: ' . $option->percent . '%;
									}
								');
                                ?>
                                <li>
                                    <span class="optn">
                                        <?php echo $this->escape(str_replace('&#039;', "'", $option->text)); ?>
                                    </span>
                                    <span class="hits">
                                        <?php echo $this->escape($option->percent); ?>%
                                    </span>
                                    <?php $barClass = $option->class . ' option' . $option->id; ?>
                                    <div class="graph">
                                        <strong class="bar <?php echo $barClass; ?>">
                                            <span><?php echo $this->escape($option->hits); ?>%</span>
                                        </strong>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php } ?>
                </div>

                <div class="meta">
                    <div class="grid">
                        <div class="col span6">
                            <?php $votesTotal = $poll->dates()->total(); ?>
                            <span class="opt icon-votes">
                                <?php echo Lang::txt('COM_POLL_VOTES', $votesTotal); ?>
                            </span>
                        </div>
                        <div class="col span6 omega">
                            <?php $openStatus = $poll->get('open')
                                ? Lang::txt('open')
                                : Lang::txt('closed'); ?>
                            <span class="status <?php echo $openStatus; ?>">
                                <?php echo $openStatus; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if ($i == 2) {
            echo '</div><div class="grid">';
        }
        $i++;
    }
    ?>
    </div>
</section><!-- / .main section -->
