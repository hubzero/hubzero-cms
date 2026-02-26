<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$pdf    = $this->pdf;
$resp   = $this->resp;
$dep    = $this->dep;
$record = $resp->getAnswers();
?>

<header id="content-header">
    <h2>Results: <?php echo $this->title ?></h2>

    <div id="content-header-extra">
        <p>
            <a class="icon-prev back btn" href="<?php echo Route::url($this->base); ?>">
                <?php echo Lang::txt('Back to course'); ?>
            </a>
        </p>
    </div>
</header>

<section class="main section">
    <p>Completed <?php echo Date::of($resp->getEndTime())->toLocal('r'); ?></p>
    <p>Score <strong><?php echo $record['summary']['score'] ?>%</strong></p>
    <?php if ($this->dep->getResultsClosed() == 'details') : ?>
        <p>
            More detailed results will be available
            <?php
            if ($dep->getEndTime()) {
                $endLocal = Date::of($dep->getEndTime())->toLocal('r');
                $diff = \Components\Courses\Helpers\Form::timeDiff(
                    strtotime($this->dep->getEndTime())
                    - strtotime(Date::of('now'))
                );
                echo $endLocal . " (about " . $diff . " from now)";
            } else {
                echo 'soon';
            }
            ?>.
            Check the course progress page for more details.
        </p>
    <?php endif; ?>
    <?php if ($this->dep->getAllowedAttempts() > 1) : ?>
        <?php $attempt = $resp->getAttemptNumber(); ?>
        <p>
            You are allowed <strong><?php echo $this->dep->getAllowedAttempts() ?></strong> attempts.
            <?php $val = \Components\Courses\Helpers\Form::toOrdinal((int)$attempt); ?>
            This was your <strong><?php echo $val; ?></strong> attempt.
        </p>
        <form action="<?php echo Route::url($this->base . '&task=form.complete') ?>">
            <input type="hidden" name="crumb" value="<?php echo $this->dep->getCrumb() ?>" />
            <?php $completedAttempts = $resp->getCompletedAttempts(); ?>
            <?php if ($completedAttempts && count($completedAttempts) > 0) : ?>
                <p>
                    View another completed attempt:
                    <select name="attempt">
                        <?php foreach ($completedAttempts as $completedAttempt) : ?>
                            <?php
                                $sel = ($completedAttempt == $attempt)
                                    ? ' selected="selected"' : '';
                                $ord = \Components\Courses\Helpers\Form::toOrdinal(
                                    $completedAttempt
                                );
                            ?>
                            <option value="<?php echo $completedAttempt; ?>"<?php echo $sel; ?>>
                                <?php echo $ord; ?> attempt
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input class="btn btn-secondary" type="submit" value="GO" />
                </p>

                <?php
                    $nextAttempt = (count($completedAttempts) < $dep->getAllowedAttempts())
                        ? (count($completedAttempts) + 1) : null;
                ?>
            <?php endif; ?>

            <?php if ($dep->getState() == 'active' && isset($nextAttempt)) : ?>
                <p>
                    <?php
                        $routeUrl = Route::url(
                            $this->base . '&task=form.complete&crumb=' . $this->dep->getCrumb() . '&attempt='
                            . $nextAttempt
                        );
                    ?>
                    <a class="btn btn-warning" href="<?php echo $routeUrl; ?>">
                        Take your next attempt!
                    </a>
                </p>
            <?php endif; ?>
        </form>
    <?php endif; ?>
</section>