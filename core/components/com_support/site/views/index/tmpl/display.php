<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

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
                    <p>We offer several ways of finding content and encourage exploring our knowledge base and engaging the community for support.</p>
                </div><!-- / .col span-half -->
                <div class="col span-half omega">
                    <h3>When All Else Fails</h3>
                    <p>Report problems to us directly and track their progress. We will try our best to answer your questions and work with you to resolve any issues you may have.</p>
                </div><!-- / .col span-half -->
            </div>
        </div>
        <div class="col span3 omega">
            <h3>Quick Links</h3>
            <ul>
                <li><a class="ticket-help popup" href="<?php echo Route::url('index.php?option=com_help&component=support&page=faqs'); ?>">Support FAQ's</a></li>
                <?php if (Component::isEnabled('com_kb')) { ?>
                    <li><a class="com-kb" href="<?php echo Route::url('index.php?option=com_kb'); ?>">Knowledge Base</a></li>
                <?php } ?>
                <li><a class="ticket-report" href="<?php echo Route::url('index.php?option=com_support&task=new'); ?>">Report Problems</a></li>
                <li><a class="ticket-track" href="<?php echo Route::url('index.php?option=com_support&task=tickets'); ?>">Track Tickets</a></li>
            </ul>
        </div>
    </div>
</section><!-- / #introduction.section -->

<section class="section">
    <?php if (Component::isEnabled('com_resources') || Component::isEnabled('com_tags') || Component::isEnabled('com_search')) { ?>
        <div class="grid">
            <div class="col span3">
                <h2>Finding Content</h2>
            </div><!-- / .col span3 -->
            <div class="col span3">
                <?php if (Component::isEnabled('com_resources')) { ?>
                    <div class="content-presentation card-link">
                        <h3><a class="com-resources" href="<?php echo Route::url('index.php?option=com_resources'); ?>">Resources</a></h3>
                        <p>Find the latest cutting-edge research in our resources.</p>
                    </div><!-- / .presentations -->
                <?php } ?>
            </div><!-- / .col span3 -->
            <div class="col span3">
                <?php if (Component::isEnabled('com_tags')) { ?>
                    <div class="content-tag card-link">
                        <h3><a class="com-tags" href="<?php echo Route::url('index.php?option=com_tags'); ?>">Tags</a></h3>
                        <p>Explore all our content through tags or even tag content yourself.</p>
                    </div><!-- / .tag -->
                <?php } ?>
            </div><!-- / .col span3 -->
            <div class="col span3 omega">
                <?php if (Component::isEnabled('com_search')) { ?>
                    <div class="content-search card-link">
                        <h3><a class="com-search" href="<?php echo Route::url('index.php?option=com_search'); ?>">Search</a></h3>
                        <p>Try searching for a title, author, tag, phrase, or keywords.</p>
                    </div><!-- / .search -->
                <?php } ?>
            </div><!-- / .col span3 -->
        </div><!-- / .grid -->
    <?php } ?>

    <?php if (Component::isEnabled('com_answers') || Component::isEnabled('com_wishlist') || Component::isEnabled('com_wiki')) { ?>
        <div class="grid">
            <div class="col span3">
                <h2>Community Help</h2>
            </div><!-- / .col span3 -->
            <?php
            $i = 0;
            ?>
            <?php if (Component::isEnabled('com_answers')) {
                $i++; ?>
                <div class="col span3">
                    <div class="feedback card-link">
                        <h3><a class="com-answers" href="<?php echo Route::url('index.php?option=com_answers'); ?>">Questions &amp; Answers</a></h3>
                        <p>Get your questions answered and help others find the clue.</p>
                    </div><!-- / .feedback -->
                </div><!-- / .col span3 -->
            <?php } ?>
            <?php if (Component::isEnabled('com_wishlist')) {
                $i++; ?>
                <div class="col span3">
                    <div class="idea card-link">
                        <h3><a href="<?php echo Route::url('index.php?option=com_wishlist'); ?>">Wish List</a></h3>
                        <p>Tell everyone your ideas or features you would like to see.</p>
                    </div><!-- / .idea -->
                </div><!-- / .col span3 -->
            <?php } ?>
            <?php if (Component::isEnabled('com_wiki')) {
                $i++; ?>
                <div class="col span3<?php echo $i == 3 ? ' omega' : ''; ?>">
                    <div class="wiki card-link">
                        <h3><a class="com-wiki" href="<?php echo Route::url('index.php?option=com_wiki'); ?>">Wiki</a></h3>
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
            $i++; ?>
            <div class="col span3">
                <div class="series card-link">
                    <h3><a class="com-kb" href="<?php echo Route::url('index.php?option=com_kb'); ?>">Knowledge Base</a></h3>
                    <p>Find answers to frequently asked questions, helpful tips, and any other information we thought might be useful.</p>
                </div><!-- / .series -->
            </div><!-- / .col span3 -->
        <?php } ?>
        <div class="col span3">
            <div class="note card-link">
                <h3><a class="ticket-report" href="<?php echo Route::url('index.php?option=com_support&task=new'); ?>">Report Problems</a></h3>
                <p>Report problems with our form and have your problem entered into our ticket tracking system. We guarantee a response!</p>
            </div><!-- / .note -->
        </div><!-- / .col span3 -->
        <div class="col span3 omega">
            <div class="ticket card-link">
                <h3><a class="ticket-track" href="<?php echo Route::url('index.php?option=com_support&task=tickets'); ?>">Track Tickets</a></h3>
                <p>Have a problem entered into our ticket tracking system? Track its progress, add comments and notes, or close resolved issues.</p>
            </div><!-- / .ticket -->
        </div><!-- / .col span3 -->
    </div><!-- / .grid -->
</section><!-- / .section -->