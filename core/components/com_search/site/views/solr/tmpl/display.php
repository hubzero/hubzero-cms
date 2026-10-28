<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('search-enhanced')
    ->css('jquery.datetimepicker.css', 'system');
$this->js('solr')
    ->js('jquery.datetimepicker', 'system');

$terms = isset($this->terms) ? $this->terms : '';
$noResult = ($this->results && count($this->results)) > 0 ? false : true;
$tagSearchEnabled = $this->tagSearchEnabled;

if ($this->section == 'map') :
    $this->css('../js/OpenLayers-2.13.1/theme/default/style.css');
    $this->js('OpenLayers-2.13.1/OpenLayers.js');
    $this->js('searchmap.js');
endif;
?>

<!-- start component output -->
<header id="content-header">
    <h2><?php echo Lang::txt('COM_SEARCH_SEARCH'); ?></h2>
</header><!-- / #content-header -->

<?php $formUrl = Route::url('index.php?option=com_search'); ?>
<form action="<?php echo $formUrl; ?>" method="get" class="container" id="solr">
    <section class="options section">
        <div class="section-inner data-entry">
        <input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('Search'); ?>" />
        <fieldset class="entry-search">
            <legend><?php echo Lang::txt('COM_SEARCH_SITE'); ?></legend>
            <label for="terms"><?php echo Lang::txt('COM_SEARCH_TERMS'); ?></label>
            <?php $searchPrompt = Lang::txt('COM_SEARCH_ENTER_PROMPT'); ?>
            <input type="text"
                name="terms"
                id="terms"
                value="<?php echo htmlspecialchars($terms); ?>"
                placeholder="<?php echo $searchPrompt; ?>"
            />
            <input type="hidden"
                name="section"
                value="<?php echo $this->escape($this->section); ?>"
            />
            <?php $typeVal = !empty($this->type) ? $this->type : ''; ?>
            <input type="hidden"
                name="type"
                value="<?php echo $typeVal; ?>"
            />
        </fieldset>
        <?php
        if ($tagSearchEnabled) {
            $tags_list = Event::trigger(
                'hubzero.onGetMultiEntry',
                array(
                    array('tags', 'tags', 'actags', '', $this->tags)
                )
            );

            if (count($tags_list) > 0) {
                echo $tags_list[0];
            } else {
                echo '<input type="text" name="tags" value="' . $this->escape($this->tags) . '" />';
            }
        }
        ?>
        <?php if ($this->section == 'map' && 0) { ?>
            <fieldset class="map-search">
                <input type="hidden" name="minlat" id="minlat" value="<?php if (isset($this->minlat)) {
                    echo $this->minlat;
                                                                      } ?>" />
                <input type="hidden" name="minlon" id="minlon" value="<?php if (isset($this->minlon)) {
                    echo $this->minlon;
                                                                      } ?>" />
                <input type="hidden" name="maxlat" id="maxlat" value="<?php if (isset($this->maxlat)) {
                    echo $this->maxlat;
                                                                      } ?>" />
                <input type="hidden" name="maxlon" id="maxlon" value="<?php if (isset($this->maxlon)) {
                    echo $this->maxlon;
                                                                      } ?>" />
            </fieldset>
        <?php } // end if ?>
        </div>
    </section>

    <section class="main section">
        <?php if ($noResult) {
            if (!empty($terms)) { ?>
                <div class="info">
                    <?php if (isset($this->spellSuggestions)) { ?>
                        <h3><?php echo Lang::txt('COM_SEARCH_DIDYOUMEAN'); ?></h3>
                        <?php foreach ($this->spellSuggestions as $suggestion) { ?>
                            <?php foreach ($suggestion->getWords() as $word) { ?>
                                <?php
                                    $wordUrl = Route::url(
                                        'search?terms=' . $word['word']
                                        . '&section=content'
                                    );
                                ?>
                                <a href="<?php echo $wordUrl; ?>">
                                    <?php echo $word['word']; ?>
                                </a>
                            <?php } ?>
                        <?php } ?>
                    <?php } else { ?>
                        <p><?php echo Lang::txt('COM_SEARCH_NO_RESULTS'); ?></p>
                    <?php } ?>
                </div>
            <?php }
        } else { ?>
            <div class="section-inner hz-layout-with-aside">
                <nav class="aside">
                    <div class="container facet">
                        <h3>Category</h3>
                        <?php
                            $allUrl = Route::url(
                                'index.php?option=com_search&terms='
                                . $this->terms
                            );
                            $activeClass = ($this->type == '')
                                ? 'class="active"' : '';
                        ?>
                        <?php if ($this->type == '') : ?>
                            <ul>
                                <li>
                                    <a <?php echo $activeClass; ?>
                                        href="<?php echo $allUrl; ?>"
                                    >
                                        <?php echo Lang::txt('COM_SEARCH_FILTER_ALL'); ?>
                                        <span class="item-count">
                                            <?php echo $this->total; ?>
                                        </span>
                                    </a>
                                </li>
                                <?php foreach ($this->facets as $facet) : ?>
                                    <?php echo $facet->formatWithCounts(
                                        $this->facetCounts,
                                        $this->type,
                                        $this->terms,
                                        $this->childTermsString
                                    ); ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php else : ?>
                            <ul>
                                <li>
                                    <a <?php echo $activeClass; ?>
                                        href="<?php echo $allUrl; ?>"
                                    >
                                        <?php echo Lang::txt('COM_SEARCH_ALL_COMPONENTS'); ?>
                                    </a>
                                </li>
                                <?php echo $this->searchComponent->formatWithCounts(
                                    $this->facetCounts,
                                    $this->type,
                                    $this->terms,
                                    $this->childTermsString,
                                    $this->filters
                                );?>
                            </ul>
                        <?php endif; ?>
                    </div><!-- / .container -->
                </nav><!-- / .aside -->
                <div class="subject">
                    <div class="container">
                        <div class="results list"><!-- add "tiled" to class for tiled view -->
                            <?php foreach ($this->results as $result) : ?>
                                <?php
                                if (is_array($result)) {
                                    $hubType = isset($result['hubtype'])
                                        ? strtolower($result['hubtype'])
                                        : '';
                                } else {
                                    $hubType = isset($result->result['hubtype'])
                                        ? strtolower($result->result['hubtype'])
                                        : '';
                                }

                                $templateOverride = '';
                                if (!empty($this->viewOverrides[$hubType])) {
                                    $overrideView = new \Hubzero\View\View(
                                        $this->viewOverrides[$hubType]
                                    );
                                    $overrideView->set('result', $result)
                                        ->set('terms', $this->terms)
                                        ->set('tagSearch', $tagSearchEnabled)
                                        ->display();
                                } else {
                                    $resultData = is_array($result)
                                        ? $result
                                        : $result->result;
                                    $this->setLayout('_result')
                                        ->setName('solr')
                                        ->set('result', $resultData)
                                        ->set('terms', $terms)
                                        ->set('tagSearch', $tagSearchEnabled)
                                        ->display();
                                }
                                ?>
                            <?php endforeach; ?>
                        </div><!-- / .results list -->
                        <nav class="pagination">
                            <?php echo $this->pagination->render(); ?>
                        </nav>
                        <div class="clearfix"></div>
                    </div><!-- / .container -->
                </div><!-- / .subject -->
            </div><!-- / .section-inner -->
        <?php } // end if ?>
    </section><!-- / .main section -->
</form>
<!-- end component output -->
