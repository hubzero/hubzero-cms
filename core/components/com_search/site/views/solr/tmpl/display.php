<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('search-enhanced')
	->css('jquery.datetimepicker.css', 'system');
$this->js('suggest')
	->js('solr')
	->js('jquery.datetimepicker', 'system');

$terms = isset($this->terms) ? $this->terms : '';
$noResult = count($this->results) > 0 ? false : true;
$searchParams = Component::params('com_search');
$tagSearch = ($searchParams->get('solr_tagsearch', 0) == 1) ? true : false;

?>

<?php if ($this->section == 'map') { ?>
	<link rel="stylesheet" type="text/css" href="/core/components/com_search/site/assets/js/OpenLayers-2.13.1/theme/default/style.css"/>
	<script type="text/javascript" src="/core/components/com_search/site/assets/js/OpenLayers-2.13.1/OpenLayers.js"></script> <!-- (2.0) -->
	<script type="text/javascript" src="/core/components/com_search/site/assets/js/searchmap.js"></script> <!-- (2.0) -->
<?php } ?>

<!-- start component output -->
<header id="content-header">
	<h2>Search</h2>
</header><!-- / #content-header -->

<form action="<?php echo Route::url('index.php?option=com_search'); ?>" method="get" class="container data-entry">
	<section class="options section">
		<input class="entry-search-submit" type="submit" value="Search" />
		<fieldset class="entry-search">
			<legend>Search site</legend>
			<label for="terms">Search terms</label>
			<input type="text" name="terms" id="terms" value="<?php echo htmlspecialchars($terms); ?>" placeholder="Enter keyword or phrase" />
			<input type="hidden" name="section" value="<?php echo $this->escape($this->section); ?>" />
			<input type="hidden" name="type" value="<?php echo !empty($this->type) ? $this->type : ''; ?>" />
		</fieldset>
		<?php
		if ($tagSearch)
		{
			$tags_list = Event::trigger('hubzero.onGetMultiEntry', 
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
				<input type="hidden" name="minlat" id="minlat" value="<?php if (isset($this->minlat)) { echo $this->minlat; } ?>" />
				<input type="hidden" name="minlon" id="minlon" value="<?php if (isset($this->minlon)) { echo $this->minlon; } ?>" />
				<input type="hidden" name="maxlat" id="maxlat" value="<?php if (isset($this->maxlat)) { echo $this->maxlat; } ?>" />
				<input type="hidden" name="maxlon" id="maxlon" value="<?php if (isset($this->maxlon)) { echo $this->maxlon; } ?>" />
			</fieldset>
		<?php } // end if ?>
	</section>

	<section class="main section">
		<?php if ($noResult) {
			if (!empty($terms))
			{ ?>
				<div class="info">
					<?php if (isset($this->spellSuggestions)) { ?>
						<h3><?php echo Lang::txt('COM_SEARCH_DIDYOUMEAN'); ?></h3>
						<?php foreach ($this->spellSuggestions as $suggestion) { ?>
							<?php foreach ($suggestion->getWords() as $word) { ?>
								<a href="<?php echo Route::url('search?terms=' . $word['word'] . '&section=content'); ?>"><?php echo $word['word']; ?></a>
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
						<?php if ($this->type == ''): ?>
							<ul>
								<li>
									<a <?php echo ($this->type == '') ? 'class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=com_search&terms=' . $this->terms); ?>">All Categories <span class="item-count">
										<?php echo $this->total; ?></span>
									</a>
								</li>
								<?php foreach ($this->facets as $facet): ?>
									<?php echo $facet->formatWithCounts($this->facetCounts, $this->type, $this->terms, $this->childTermsString); ?>
								<?php endforeach; ?>
							</ul>
						<?php else: ?>
							<ul>
								<li>
									<a <?php echo ($this->type == '') ? 'class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=com_search&terms=' . $this->terms); ?>"> &lt; Back</a>
								</li>
								<?php echo $this->searchComponent->formatWithCounts($this->facetCounts, $this->type, $this->terms, $this->childTermsString, $this->filters);?>
							</ul>
						<?php endif; ?>
					</div><!-- / .container -->
				</nav><!-- / .aside -->
				<div class="subject">
					<div class="container">
						<div class="results list"><!-- add "tiled" to class for tiled view -->
							<?php foreach ($this->results as $result): ?>
								<?php 
									$hubType = isset($result['hubtype']) ? strtolower($result['hubtype']) : '';
									$templateOverride = '';
									if (!empty($this->viewOverrides[$hubType]))
									{
										$overrideView = new \Hubzero\View\View($this->viewOverrides[$hubType]);
										$overrideView->set('result', $result)
											->set('terms', $this->terms)
											->set('tagSearch', $tagSearch)
											->display();
									}
									else
									{
										$this->setLayout('_result')
											->setName('solr')
											->set('result', $result)
											->set('terms', $terms)
											->set('tagSearch', $tagSearch)
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
