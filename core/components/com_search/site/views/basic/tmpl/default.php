<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Event;

// No direct access.
defined('_HZEXEC_') or die();

// Push some resources to the tmeplate
$this->css()
     ->js();

$show_weight = array_key_exists('show_weight', $_GET);
?>
<header id="content-header">
    <h2><?php echo Lang::txt('COM_SEARCH'); ?></h2>
</header><!-- / #content-header -->

<section class="main section">
    <div class="section-inner hz-layout-with-aside">
        <div class="subject">
            <?php $searchUrl = Route::url('index.php?option=com_search'); ?>
            <form action="<?php echo $searchUrl; ?>" method="get" class="container data-entry">
                <?php $submitLabel = Lang::txt('COM_SEARCH_SEARCH'); ?>
                <input class="entry-search-submit" type="submit" value="<?php echo $submitLabel; ?>" />
                <fieldset class="entry-search">
                    <legend><?php echo Lang::txt('COM_SEARCH_SITE'); ?></legend>
                    <label for="terms"><?php echo Lang::txt('COM_SEARCH_TERMS'); ?></label>
                    <?php $termsPlaceholder = Lang::txt('COM_SEARCH_TERMS_PLACEHOLDER'); ?>
                    <input type="text"
                        name="terms"
                        id="terms"
                        value="<?php echo $this->escape($this->terms); ?>"
                        placeholder="<?php echo $termsPlaceholder; ?>"
                    />
                </fieldset>
            </form>
    <?php if ($this->results->valid()) : ?>
        <?php if (($ct = $this->results->get_custom_title())) : ?>
            <?php
                $genericUrl = Route::url(
                    'index.php?option=com_search&terms='
                    . urlencode($this->terms) . '&force-generic=1'
                );
            ?>
            <p class="information">
                You are viewing <strong><?php echo $ct; ?></strong>
                matching your query.
                <a href="<?php echo $genericUrl; ?>">View all results.</a>
            </p>
        <?php endif; ?>

            <div class="container">
                <?php
                $total  = $this->results->get_plugin_list_count();
                $offset = $this->results->get_offset();
                $limit  = $this->results->get_limit();
                $limit  = ($limit == 0) ? ($limit + 1) : $limit;
                $current_page = $offset / $limit + 1;
                $total_pages  = ceil($total / $limit);
                ?>
                <h3>
                    <?php echo Lang::txt('COM_SEARCH_RESULTS'); ?>
                    <span>(<?php echo Lang::txt('COM_SEARCH_RESULTS_PAGE_OF', $current_page, $total_pages); ?>)</span>
                </h3>

                <?php if (($tags = $this->results->get_tags())) : ?>
                    <ol class="tags">
                        <?php foreach ($tags as $tag) : ?>
                            <?php $tagUrl = Route::url($tag->get_link()); ?>
                            <li>
                                <a class="tag" href="<?php echo $tagUrl; ?>">
                                    <?php echo $tag->get_title(); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>

                <?php foreach ($this->results->get_widgets() as $widget) : ?>
                    <?php echo $widget; ?>
                <?php endforeach; ?>

                <ol class="results">
                <?php foreach ($this->results as $res) : ?>
                    <li>
                        <?php
                            $eventName = 'search.onBeforeSearchRender'
                                . ucfirst($res->get_plugin());
                            $before = Event::trigger($eventName, array($res));
                        ?>
                        <p class="title">
                            <a href="<?php echo $res->get_link(); ?>">
                                <?php echo $res->get_highlighted_title(); ?>
                            </a>
                        </p>
                        <div class="summary">
                            <?php if ($res->has_metadata()) : ?>
                                <p class="details">
                                    <?php if (($section = $res->get_section())) {
                                        ?><span class="section"><?php echo $section; ?></span><?php
                                    }?>
                                    <?php if (($date = $res->get_date())) {
                                        ?><span class="date"><?php echo Date::of($date)->format('j M Y'); ?></span><?php
                                    } ?>
                                    <?php if (($contributors = $res->get_contributors())) : ?>
                                    <span class="contributors">
                                            <?php
                                                $contrib_ids = $res->get_contributor_ids();
                                                $contrib_len = count($contributors);
                                            ?>
                                            Contributor(s):
                                            <?php foreach ($contributors as $idx => $contrib) : ?>
                                                <?php if (isset($contrib_ids[$idx])) : ?>
                                                    <?php
                                                    $memberUrl = Route::url(
                                                        'index.php?option=com_members&id='
                                                        . $contrib_ids[$idx]
                                                    );
                                                    ?>
                                                <a href="<?php echo $memberUrl; ?>">
                                                    <?php echo $contrib; ?>
                                                </a><?php if ($idx != $contrib_len - 1) {
                                                    echo ', ';
                                                    } ?>
                                                <?php else : ?>
                                                    <?php echo $contrib; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                    </span>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($before) : ?>
                                <div class="result-pre">
                                    <?php foreach ($before as $html) : ?>
                                        <?php echo $html; ?>
                                    <?php endforeach; ?>
                                </div><!-- / .result-pre -->
                            <?php endif; ?>
                            <?php if ($show_weight) : ?>
                                <ul class="weight-log">
                                    <?php foreach ($res->get_weight_log() as $entry) : ?>
                                        <li><?php echo $entry; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            <div class="result-description<?php echo $before ? ' shifted' : ''; ?>">
                                <p><?php echo $res->get_highlighted_excerpt(); ?></p>
                            </div><!-- / .result-description<?php echo $before ? ' shifted' : ''; ?> -->
                            <p class="clear"></p>
                        </div><!-- / .summary -->
                        <?php
                            $last_type = null;
                        if (($children = $res->get_children())) :
                            $ctypec = array();
                            foreach ($children as $child) {
                                if (($section = $child->get_section())) {
                                    if (!array_key_exists($section, $ctypec)) {
                                        $ctypec[$section] = 1;
                                    } else {
                                        ++$ctypec[$section];
                                    }
                                }
                            }
                            if ($ctypec) :
                                ?>
                            <ul class="child-types">
                                <?php foreach ($children as $idx => $child) : ?>
                                    <?php
                                    if (!($current_type = $child->get_section())) {
                                        continue;
                                    }
                                    if (!$last_type) :
                                        ?>
                                        <li>
                                            <?php
                                                $typeLabel = $current_type == 'Questions'
                                                    ? 'Answers' : $current_type;
                                                $sectionCount = $ctypec[$child->get_section()];
                                            ?>
                                            <h4>
                                                <span class="expand"></span>
                                                <?php echo $typeLabel; ?>
                                                <small>(<?php echo $sectionCount; ?>)</small>
                                            </h4>
                                            <ul class="child-result">
                                    <?php elseif ($last_type != $current_type) : ?>
                                            </ul>
                                        </li>
                                        <li>
                                            <?php $sectionCount = $ctypec[$child->get_section()]; ?>
                                            <h4>
                                                <span class="expand"></span>
                                                <?php echo $current_type; ?>
                                                <small>(<?php echo $sectionCount; ?>)</small>
                                            </h4>
                                            <ul class="child-result">
                                            <?php
                                    endif;
                                        $last_type = $current_type;
                                    ?>
                                        <li class="<?php echo $idx & 1 ? 'odd' : 'even'; ?>">
                                            <a href="<?php echo $child->get_link(); ?>">
                                                <?php echo $child->get_highlighted_title(); ?>
                                            </a>
                                            <p>
                                                <?php echo $child->get_highlighted_excerpt(); ?>
                                            </p>
                                        </li>
                                <?php endforeach; ?>
                                    </ul>
                                </li>
                            </ul>
                            <?php endif; ?>
                        <?php endif; ?>
                        <p class="url">
                            <a href="<?php echo $res->get_link(); ?>">
                                <?php echo $res->get_link(); ?>
                            </a>
                        </p>
                    </li>
                <?php endforeach; ?>
                </ol>
                <form action="<?php echo Route::url('index.php?option=com_search'); ?>" method="get">
                    <?php
                    $pagination = $this->pagination(
                        $this->total,
                        $this->results->get_offset(),
                        $this->results->get_limit()
                    );
                    $pagination->setAdditionalUrlParam('terms', $this->terms);
                    echo $pagination->render();
                    ?>
                    <input type="hidden"
                        name="terms"
                        value="<?php echo $this->escape($this->terms); ?>"
                    />
                    <div class="clearfix"></div>
                </form>
            </div><!-- / .container -->
    <?php elseif (($raw = $this->terms->get_raw())) : ?>
        <?php $noResultsMsg = Lang::txt('COM_SEARCH_RESULTS_NONE', $this->escape($raw)); ?>
        <p><?php echo $noResultsMsg; ?></p>
        <?php
            // raw terms were specified but no chunks were parsed out,
            // meaning they were all stop words, so we can give a
            // quasi-helpful explanation of why nothing turned up
        if (!$this->terms->any() || strlen($raw) <= 3) :
            ?>
            <p class="warning"><?php echo Lang::txt('COM_SEARCH_WARNING_SHORT_WORDS'); ?></p>
        <?php endif; ?>
    <?php endif; ?>
        </div><!-- / .subject -->
        <div class="aside">
        <div class="container">
            <h3>
                <?php echo Lang::txt('COM_SEARCH_FILTER_RESULTS'); ?>
            </h3>
        <?php if ($this->results->get_total_count()) : ?>
            <ul class="sub-nav">
                <li>
                    <?php
                        $filterAllUrl = Route::url(
                            'index.php?option=com_search&terms='
                            . $this->url_terms
                        );
                        $filterAllLabel = Lang::txt('COM_SEARCH_FILTER_ALL');
                        $totalCount = $this->results->get_total_count();
                    ?>
                    <?php if ($this->plugin) : ?>
                        <a href="<?php echo $filterAllUrl; ?>">
                            <?php echo $filterAllLabel; ?>
                            <span class="item-count">
                                <?php echo $totalCount; ?>
                            </span>
                        </a>
                    <?php else : ?>
                        <strong>
                            <?php echo $filterAllLabel; ?>
                            <span class="item-count">
                                <?php echo $totalCount; ?>
                            </span>
                        </strong>
                    <?php endif; ?>
                </li>
            <?php foreach ($this->results->get_result_counts() as $cat => $def) : ?>
                <?php if ($def['count']) : ?>
                    <li>
                        <?php if ($this->plugin == $cat && !$this->section) : ?>
                            <strong>
                                <?php echo $def['friendly_name']; ?>
                                <span class="item-count">
                                    <?php echo $def['count']; ?>
                                </span>
                            </strong>
                        <?php else : ?>
                            <?php
                                $catUrl = Route::url(
                                    'index.php?option=com_search&terms='
                                    . $cat . ':' . $this->url_terms
                                );
                            ?>
                            <a href="<?php echo $catUrl; ?>">
                                <?php echo $def['friendly_name']; ?>
                                <span class="item-count">
                                    <?php echo $def['count']; ?>
                                </span>
                            </a>
                        <?php endif; ?>
                        <?php
                        $searchPluginName = ucfirst($def['plugin_name']);
                        $fc_child_flag = 'Plugins\\Search\\'
                            . $searchPluginName . '\\'
                            . $searchPluginName
                            . '::FIRST_CLASS_CHILDREN';
                        $hasChildren = (!defined($fc_child_flag)
                            || constant($fc_child_flag))
                            && array_key_exists('sections', $def)
                            && count($def['sections']) > 1;
                        if ($hasChildren) :
                            ?>
                            <ul>
                            <?php foreach ($def['sections'] as $section_key => $sdef) : ?>
                                <?php
                                $isActiveSection = $this->plugin
                                    && $this->section
                                    && $cat == $this->plugin
                                    && $this->section == $section_key;
                                if (!$isActiveSection) :
                                    ?>
                                    <?php
                                        $sectionUrl = Route::url(
                                            'index.php?option=com_search&terms='
                                            . $cat . ':' . $section_key
                                            . ':' . $this->url_terms
                                        );
                                    ?>
                                    <li>
                                        <a href="<?php echo $sectionUrl; ?>">
                                            <?php echo $sdef['name']; ?>
                                            <span class="item-count">
                                                <?php echo $sdef['count']; ?>
                                            </span>
                                        </a>
                                    </li>
                                <?php else : ?>
                                    <li>
                                        <strong>
                                            <?php echo $sdef['name']; ?>
                                            <span class="item-count">
                                                <?php echo $sdef['count']; ?>
                                            </span>
                                        </strong>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        </div><!-- / .container -->
    </div><!-- / .aside -->
    </div>
</section><!-- / .main section -->
