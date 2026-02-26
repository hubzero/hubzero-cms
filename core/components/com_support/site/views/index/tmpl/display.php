<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Component;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

$this->css('introduction.css', 'system')
    ->css();
?>

<header id="content-header">
    <h2><?php echo $this->title; ?></h2>
</header>

<section id="introduction" class="section">
    <div class="grid">
        <div class="col span9">
            <div class="grid">
                <div class="col span-half">
                    <h3>Getting Help</h3>
                    <p>We offer several ways of finding content and encourage exploring our knowledge base
                    and engaging the community for support.</p>
                </div><!-- / .col span-half -->
                <div class="col span-half omega">
                    <h3>When All Else Fails</h3>
                    <p>Report problems to us directly and track their progress. We will try our best to
                    answer your questions and work with you to resolve any issues you may have.</p>
                </div><!-- / .col span-half -->
            </div>
        </div>
        <div class="col span3 omega">
            <h3>Quick Links</h3>
            <ul>
                <?php
                $faqUrl    = Route::url('index.php?option=com_help&component=support&page=faqs');
                $reportUrl = Route::url('index.php?option=com_support&task=new');
                $trackUrl  = Route::url('index.php?option=com_support&task=tickets');
                ?>
                <li><a class="ticket-help popup" href="<?php echo $faqUrl; ?>">Support FAQ's</a></li>
                <?php if (Component::isEnabled('com_kb')) { ?>
                    <?php $kbUrl = Route::url('index.php?option=com_kb'); ?>
                    <li><a class="com-kb" href="<?php echo $kbUrl; ?>">Knowledge Base</a></li>
                <?php } ?>
                <li><a class="ticket-report" href="<?php echo $reportUrl; ?>">Report Problems</a></li>
                <li><a class="ticket-track" href="<?php echo $trackUrl; ?>">Track Tickets</a></li>
            </ul>
        </div>
    </div>
</section><!-- / #introduction.section -->

<section class="section">
    <?php
    $hasResources = Component::isEnabled('com_resources');
    $hasTags      = Component::isEnabled('com_tags');
    $hasSearch    = Component::isEnabled('com_search');
    if ($hasResources || $hasTags || $hasSearch) { ?>
        <div class="grid">
            <div class="col span3">
                <h2>Finding Content</h2>
            </div><!-- / .col span3 -->
            <div class="col span3">
                <?php if ($hasResources) { ?>
                    <?php $resourcesUrl = Route::url('index.php?option=com_resources'); ?>
                    <div class="content-presentation card-link">
                        <h3><a class="com-resources" href="<?php echo $resourcesUrl; ?>">Resources</a></h3>
                        <p>Find the latest cutting-edge research in our resources.</p>
                    </div><!-- / .presentations -->
                <?php } ?>
            </div><!-- / .col span3 -->
            <div class="col span3">
                <?php if ($hasTags) { ?>
                    <?php $tagsUrl = Route::url('index.php?option=com_tags'); ?>
                    <div class="content-tag card-link">
                        <h3><a class="com-tags" href="<?php echo $tagsUrl; ?>">Tags</a></h3>
                        <p>Explore all our content through tags or even tag content yourself.</p>
                    </div><!-- / .tag -->
                <?php } ?>
            </div><!-- / .col span3 -->
            <div class="col span3 omega">
                <?php if ($hasSearch) { ?>
                    <?php $searchUrl = Route::url('index.php?option=com_search'); ?>
                    <div class="content-search card-link">
                        <h3><a class="com-search" href="<?php echo $searchUrl; ?>">Search</a></h3>
                        <p>Try searching for a title, author, tag, phrase, or keywords.</p>
                    </div><!-- / .search -->
                <?php } ?>
            </div><!-- / .col span3 -->
        </div><!-- / .grid -->
    <?php } ?>

    <?php
    $hasAnswers  = Component::isEnabled('com_answers');
    $hasWishlist = Component::isEnabled('com_wishlist');
    $hasWiki     = Component::isEnabled('com_wiki');
    if ($hasAnswers || $hasWishlist || $hasWiki) { ?>
        <div class="grid">
            <div class="col span3">
                <h2>Community Help</h2>
            </div><!-- / .col span3 -->
            <?php
            $i = 0;
            ?>
            <?php if ($hasAnswers) {
                $i++;
                $answersUrl = Route::url('index.php?option=com_answers');
                ?>
                <div class="col span3">
                    <div class="feedback card-link">
                        <h3>
                            <a class="com-answers" href="<?php echo $answersUrl; ?>">
                                Questions &amp; Answers
                            </a>
                        </h3>
                        <p>Get your questions answered and help others find the clue.</p>
                    </div><!-- / .feedback -->
                </div><!-- / .col span3 -->
            <?php } ?>
            <?php if ($hasWishlist) {
                $i++;
                $wishlistUrl = Route::url('index.php?option=com_wishlist');
                ?>
                <div class="col span3">
                    <div class="idea card-link">
                        <h3><a href="<?php echo $wishlistUrl; ?>">Wish List</a></h3>
                        <p>Tell everyone your ideas or features you would like to see.</p>
                    </div><!-- / .idea -->
                </div><!-- / .col span3 -->
            <?php } ?>
            <?php if ($hasWiki) {
                $i++;
                $wikiUrl = Route::url('index.php?option=com_wiki');
                ?>
                <div class="col span3<?php echo $i == 3 ? ' omega' : ''; ?>">
                    <div class="wiki card-link">
                        <h3><a class="com-wiki" href="<?php echo $wikiUrl; ?>">Wiki</a></h3>
                        <p>Take a look at our user-generated wiki pages or write your own.</p>
                    </div><!-- / .wiki -->
                </div><!-- / .col span3 -->
            <?php } ?>
        </div><!-- / .grid -->
    <?php } ?>

    <div class="grid">
        <div class="col span3">
            <h2>Getting Support</h2>
        </div><!-- / .col span3 -->
        <?php
        $i = 0;
        ?>
        <?php if (Component::isEnabled('com_kb')) {
            $i++;
            $kbUrl = Route::url('index.php?option=com_kb');
            ?>
            <div class="col span3">
                <div class="series card-link">
                    <h3><a class="com-kb" href="<?php echo $kbUrl; ?>">Knowledge Base</a></h3>
                    <p>Find answers to frequently asked questions, helpful tips, and any other
                    information we thought might be useful.</p>
                </div><!-- / .series -->
            </div><!-- / .col span3 -->
        <?php } ?>
        <div class="col span3">
            <?php $reportUrl = Route::url('index.php?option=com_support&task=new'); ?>
            <div class="note card-link">
                <h3><a class="ticket-report" href="<?php echo $reportUrl; ?>">Report Problems</a></h3>
                <p>Report problems with our form and have your problem entered into our ticket tracking
                system. We guarantee a response!</p>
            </div><!-- / .note -->
        </div><!-- / .col span3 -->
        <div class="col span3 omega">
            <?php $trackUrl = Route::url('index.php?option=com_support&task=tickets'); ?>
            <div class="ticket card-link">
                <h3><a class="ticket-track" href="<?php echo $trackUrl; ?>">Track Tickets</a></h3>
                <p>Have a problem entered into our ticket tracking system? Track its progress,
                add comments and notes, or close resolved issues.</p>
            </div><!-- / .ticket -->
        </div><!-- / .col span3 -->
    </div><!-- / .grid -->
</section><!-- / .section -->
