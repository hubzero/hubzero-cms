<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Component;
use Hubzero\Facades\Document;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Pathway;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

if (Pathway::count() <= 0) {
    Pathway::append(
        Lang::txt('COM_KB'),
        'index.php?option=' . $this->option
    );
}

Document::setTitle(Lang::txt('COM_KB'));

$formUrl = Route::url(
    'index.php?option=' . $this->option . '&section=all'
);
?>
<header id="content-header">
    <h2><?php echo Lang::txt('COM_KB'); ?></h2>
</header>

<section class="main section">
    <div class="section-inner hz-layout-with-aside">
        <div class="subject">
            <form action="<?php echo $formUrl; ?>" method="get">
                <div class="container data-entry">
                    <?php $searchTxt = Lang::txt('COM_KB_SEARCH'); ?>
                    <input class="entry-search-submit" type="submit"
                        value="<?php echo $searchTxt; ?>" />
                    <fieldset class="entry-search">
                        <?php $legendTxt = Lang::txt('COM_KB_SEARCH_LEGEND'); ?>
                        <legend><?php echo $legendTxt; ?></legend>
                        <?php $labelTxt = Lang::txt('COM_KB_SEARCH_LABEL'); ?>
                        <label for="entry-search-field">
                            <?php echo $labelTxt; ?>
                        </label>
                        <?php
                        $placeholderTxt = Lang::txt(
                            'COM_KB_SEARCH_PLACEHOLDER'
                        );
                        ?>
                        <input type="text" name="search"
                            id="entry-search-field" value=""
                            placeholder="<?php echo $placeholderTxt; ?>" />
                    </fieldset>
                </div><!-- / .container -->

                <div class="container">
                    <div class="container-block">
                        <h3><?php echo Lang::txt('COM_KB_ARTICLES'); ?></h3>
                        <div class="grid">
                            <div class="col span-half">
                                <?php
                                $popUrl = Route::url(
                                    'index.php?option=' . $this->option
                                    . '&task=article&section=all'
                                    . '&order=popularity'
                                );
                                $readTitle = Lang::txt('COM_KB_READ_ARTICLE');
                                ?>
                                <h4>
                                    <a href="<?php echo $popUrl; ?>">
                                        <?php echo Lang::txt('COM_KB_POPULAR_ARTICLES'); ?>
                                        <span class="more">&raquo;</span>
                                    </a>
                                </h4>
                                <?php
                                $popular = $this->archive->articles()
                                            ->whereIn('access', User::getAuthorisedViewLevels())
                                            ->whereEquals('state', 1)
                                            ->order('helpful', 'desc') // (a.helpful - a.nothelpful)
                                            ->limit(5)
                                            ->rows();
                                if (count($popular) > 0) { ?>
                                    <ul class="articles">
                                    <?php foreach ($popular as $row) {
                                        $rowUrl = Route::url($row->link());
                                        ?>
                                        <li class="icon-file">
                                            <a href="<?php echo $rowUrl; ?>"
                                                title="<?php echo $readTitle; ?>">
                                                <?php echo $this->escape(stripslashes($row->get('title'))); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    </ul>
                                <?php } else { ?>
                                    <p><?php echo Lang::txt('COM_KB_NO_ARTICLES'); ?></p>
                                <?php } ?>
                            </div><!-- / .col span-half -->
                            <div class="col span-half omega">
                                <?php
                                $recUrl = Route::url(
                                    'index.php?option=' . $this->option
                                    . '&task=article&section=all'
                                    . '&order=recent'
                                );
                                ?>
                                <h4>
                                    <a href="<?php echo $recUrl; ?>">
                                        <?php echo Lang::txt('COM_KB_RECENT_ARTICLES'); ?>
                                        <span class="more">&raquo;</span>
                                    </a>
                                </h4>
                                <?php
                                $recent = $this->archive->articles()
                                            ->whereIn('access', User::getAuthorisedViewLevels())
                                            ->whereEquals('state', 1)
                                            ->order('modified', 'desc')
                                            ->order('created', 'desc')
                                            ->limit(5)
                                            ->rows();
                                if (count($recent) > 0) { ?>
                                    <ul class="articles">
                                    <?php foreach ($recent as $row) {
                                        $rowUrl = Route::url($row->link());
                                        ?>
                                        <li class="icon-file">
                                            <a href="<?php echo $rowUrl; ?>"
                                                title="<?php echo $readTitle; ?>">
                                                <?php echo $this->escape(stripslashes($row->get('title'))); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    </ul>
                                <?php } else { ?>
                                    <p><?php echo Lang::txt('COM_KB_NO_ARTICLES'); ?></p>
                                <?php } ?>
                            </div><!-- / .col span-half -->
                        </div><!-- / .grid -->

                        <h3><?php echo Lang::txt('COM_KB_CATEGORIES'); ?></h3>
                        <div class="grid">
                        <?php
                        $i = 0;

                        $categories = $this->archive->categories(array(
                            'state' => 1,
                            'access' => User::getAuthorisedViewLevels()
                        ));

                        foreach ($categories as $row) {
                            $articles = $row->articles()
                                ->whereEquals('state', 1)
                                ->whereIn('access', User::getAuthorisedViewLevels())
                                ->order('modified', 'desc')
                                ->order('created', 'desc')
                                ->limit(3)
                                ->rows();

                            if ($articles->count() <= 0) {
                                continue;
                            }

                            $i++;
                            switch ($i) {
                                case 1:
                                    $cls = '';
                                    break;
                                case 2:
                                    $cls = ' omega';
                                    break;
                            }

                            $catUrl = Route::url($row->link());
                            $catTitle = $this->escape(
                                stripslashes($row->get('title'))
                            );
                            $catCount = $row->get('articles', 0);
                            ?>
                            <div class="col span-half<?php echo $cls; ?>">
                                <h4>
                                    <a href="<?php echo $catUrl; ?>">
                                        <?php echo $catTitle; ?>
                                        <span>(<?php echo $catCount; ?>)</span>
                                        <span class="more">&raquo;</span>
                                    </a>
                                </h4>
                                <?php if ($articles->count() > 0) { ?>
                                    <ul class="articles">
                                    <?php foreach ($articles as $article) {
                                        $article->set('calias', $row->get('path'));
                                        $artUrl = Route::url($article->link());
                                        ?>
                                        <li class="icon-file">
                                            <a href="<?php echo $artUrl; ?>">
                                                <?php echo $this->escape(stripslashes($article->get('title'))); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    </ul>
                                <?php } else { ?>
                                    <p><?php echo Lang::txt('COM_KB_NO_ARTICLES'); ?></p>
                                <?php } ?>
                            </div><!-- / .col span-half <?php echo $cls; ?> -->
                            <?php
                            //echo ($i >= 2) ? '<div class="clearfix"></div>' : '';

                            if ($i >= 2) {
                                $i = 0;
                            }
                        }
                        ?>
                        </div><!-- / .grid -->
                    </div><!-- / .container-block -->
                </div><!-- / .container -->
            </form>
        </div><!-- / .subject -->

        <aside class="aside">
            <?php if (Component::isEnabled('com_answers')) { ?>
                <div class="container">
                    <h3><?php echo Lang::txt('COM_KB_COMMUNITY'); ?></h3>
                    <p>
                        <?php echo Lang::txt('COM_KB_COMMUNITY_CANT_FIND'); ?>
                        <?php
                        $answersUrl = Route::url(
                            'index.php?option=com_answers'
                        );
                        $answersLink = '<a href="' . $answersUrl . '">'
                            . Lang::txt('COM_ANSWERS') . '</a>';
                        echo Lang::txt(
                            'COM_KB_COMMUNITY_TRY_ANSWERS',
                            $answersLink
                        );
                        ?>
                    </p>
                </div><!-- / .container -->
            <?php } ?>
            <?php if (Component::isEnabled('com_wishlist')) { ?>
                <div class="container">
                    <?php $featureTxt = Lang::txt('COM_KB_FEATURE_REQUEST'); ?>
                    <h3><?php echo $featureTxt; ?></h3>
                    <p>
                        <?php echo Lang::txt('COM_KB_HAVE_A_FEATURE_REQUEST'); ?>
                        <?php
                        $wishUrl = Route::url(
                            'index.php?option=com_wishlist'
                        );
                        ?>
                        <a href="<?php echo $wishUrl; ?>">
                            <?php echo Lang::txt('COM_KB_FEATURE_TELL_US'); ?>
                        </a>
                    </p>
                </div><!-- / .container -->
            <?php } ?>
            <?php if (Component::isEnabled('com_support')) { ?>
                <div class="container">
                    <?php $troubleTxt = Lang::txt('COM_KB_TROUBLE_REPORT'); ?>
                    <h3><?php echo $troubleTxt; ?></h3>
                    <p>
                        <?php echo Lang::txt('COM_KB_TROUBLE_FOUND_BUG'); ?>
                        <?php
                        $supportUrl = Route::url(
                            'index.php?option=com_support'
                            . '&controller=tickets&task=new'
                        );
                        ?>
                        <a href="<?php echo $supportUrl; ?>">
                            <?php echo Lang::txt('COM_KB_TROUBLE_TELL_US'); ?>
                        </a>
                    </p>
                </div><!-- / .container -->
            <?php } ?>
        </aside><!-- / .aside -->
    </div><!-- / .section-inner -->
</section><!-- / .main section -->
