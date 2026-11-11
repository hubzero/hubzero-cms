<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$this->css();
?>
<h3 class="section-header" id="questions">
    <?php echo Lang::txt('PLG_PUBLICATION_QUESTIONS_RECENT_QUESTIONS'); ?>
</h3>

<div class="container">
    <p class="section-options">
        <?php
        $newUrl = Route::url($this->publication->link('questions') . '&action=new#ask');
        ?>
        <a class="icon-add add btn"
            href="<?= $newUrl ?>"><?= Lang::txt('PLG_PUBLICATION_QUESTIONS_ASK_A_QUESTION') ?></a>
    </p>
    <table class="questions entries">
        <caption>
            <?php echo Lang::txt('PLG_PUBLICATION_QUESTIONS_RECENT_QUESTIONS'); ?>
            <span>(<?php echo ($this->rows) ? count($this->rows) : '0'; ?>)</span>
        </caption>
        <tbody>
    <?php if (count($this->rows)) { ?>
        <?php
        $i = 1;

        foreach ($this->rows as $row) {
            $i++;

            // author name
            $name = Lang::txt('JANONYMOUS');
            if (!$row->get('anonymous')) {
                $user = User::getInstance($row->get('created_by'));
                if (is_object($user)) {
                    $name = '<a href="' . Route::url('index.php?option=com_members&id=' . $user->get('id')) . '">'
                        . $this->escape(stripslashes($user->get('name'))) . '</a>';
                } else {
                    $name = Lang::txt('PLG_PUBLICATION_QUESTIONS_UNKNOWN');
                }
            }

            $cls  = ($row->get('state') == 1) ? 'answered' : '';
            $cls  = ($row->isReported()) ? 'flagged' : $cls;
            $cls .= ($row->get('created_by') == User::get('username')) ? ' mine' : '';
            ?>
            <tr<?php echo ($cls) ? ' class="' . $cls . '"' : ''; ?>>
                <th>
                    <span class="entry-id"><?php echo $row->get('id'); ?></span>
                </th>
                <td>
                    <?php if (!$row->isReported()) { ?>
                        <?php
                        $linkUrl = Route::url($row->link());
                        ?>
                        <a class="entry-title"
                            href="<?= $linkUrl ?>"><?= $this->escape(strip_tags($row->get('subject'))) ?></a><br />
                    <?php } else { ?>
                        <?php
                        $entryTitleText = Lang::txt('PLG_PUBLICATION_QUESTIONS_QUESTION_UNDER_REVIEW');
                        ?>
                        <span class="entry-title"><?= $entryTitleText ?></span><br />
                    <?php } ?>
                    <span class="entry-details">
                        <?= Lang::txt('PLG_PUBLICATION_QUESTIONS_ASKED_BY', $name) ?> <span
                            class="entry-date-at">@</span>
                        <span class="entry-time"><time
                            datetime="<?= $row->created() ?>"><?= $row->created('time') ?></time></span> <span
                            class="entry-date-on">on</span>
                        <span class="entry-date"><time
                            datetime="<?= $row->created() ?>"><?= $row->created('date') ?></time></span>
                        <span class="entry-details-divider">&bull;</span>
                        <span class="entry-state">
                            <?php echo ($row->get('state') == 1) ? Lang::txt('Closed') : Lang::txt('Open'); ?>
                        </span>
                        <span class="entry-details-divider">&bull;</span>
                        <span class="entry-comments">
                            <?php
                            $linkUrl = Route::url($row->link() . '#answers');
                            $titleText = Lang::txt('There are %s responses to this question.', $row->get('rcount'));
                            ?>
                            <a href="<?= $linkUrl ?>" title="<?= $titleText ?>">
                                <?php echo $row->responses->count(); ?>
                            </a>
                        </span>
                    </span>
                </td>
                <?php if ($this->banking) { ?>
                    <td class="reward">
                        <?php if ($row->get('reward') == 1 && $this->banking) { ?>
                            <?php
                            $titleText = Lang::txt(
                                'COM_ANSWERS_THERE_IS_A_REWARD_FOR_ANSWERING',
                                $row->get('points', 0),
                            );
                            ?>
                            <span class="entry-reward"><?= $row->get('points') ?> <a href="<?= $this->infolink ?>"
                                title="<?= $titleText ?>">Points</a></span>
                        <?php } ?>
                    </td>
                <?php } ?>
                <td class="voting">
                    <span class="vote-like">
                        <?php if (User::isGuest()) { ?>
                            <span class="vote-button <?= ($row->get('helpful', 0) > 0) ? 'like' : 'neutral' ?> tooltips"
                                title="Vote this up :: Please login to vote.">
                                <?php echo $row->get('helpful', 0); ?><span> Like</span>
                            </span>
                        <?php } else { ?>
                            <?php
                            $voteUrl = Route::url(
                                'index.php?option=com_answers&task=vote&id=' . $row->get('id')
                                    . '&category=question&vote=yes',
                            );
                            ?>
                            <button type="submit" form="hz-post-action" formaction="<?= $voteUrl ?>"
                                class="vote-button <?= ($row->get('helpful', 0) > 0) ? 'like' : 'neutral' ?> tooltips"
                                title="Vote this up :: <?= $row->get('helpful', 0) ?> people liked this">
                                <?php echo $row->get('helpful', 0); ?><span> Like</span>
                            </button>
                        <?php } ?>
                    </span>
                </td>
            </tr>
        <?php } ?>
    <?php } else { ?>
            <tr class="noresults">
                <td>
                    <?php echo Lang::txt('PLG_PUBLICATION_QUESTIONS_NO_QUESTIONS_FOUND'); ?>
                </td>
            </tr>
    <?php } ?>
        </tbody>
    </table>
</div><!-- / .container -->
