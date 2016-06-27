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
$this->css('search-enhanced');
$this->js('handlebars','system');
$this->js('moment.min', 'system');
$this->js('search1');

$terms = isset($this->terms) ? $this->terms : '';
$noResult = count($this->results) > 0 ? false : true;

?>

<!-- start component output -->
<header id="content-header">
	<h2>Search</h2>
</header><!-- / #content-header -->

<section class="options section">
	<form action="/search/" method="get" class="container data-entry">
		<input class="entry-search-submit" type="submit" value="Search" />
		<fieldset class="entry-search">
			<legend>Search site</legend>
			<label for="terms">Search terms</label>
			<input type="text" name="terms" id="terms" value="<?php echo $terms; ?>" placeholder="Enter keyword or phrase" />
		</fieldset>
	</form>

	<nav>
		<ul class="result-views">
			<li class="active"><a href="#">Content</a></li>
			<li><a href="#">Files</a></li>
			<li><a href="#">Map</a></li>
		</ul>
	</nav>
</section>
<section class="main section">
	<div class="section-inner">
		<nav class="aside">
			<?php if (!$noResult): ?>
			<div class="container facet">
				<h3>Category</h3>
				<ul>
					<li>
						<a <?php echo ($this->type == '') ? 'class="active"' : ''; ?> href="/search/?terms=<?php echo $this->terms; ?>">All Categories <span class="item-count"><?php echo $this->catTotal; ?></span></a>
					</li>
					<?php foreach ($this->categories as $category): ?>
					<?php if ($category['count'] > 0): ?>
					<li>
						<a <?php echo ($this->type == $category['type']) ? 'class="active"' : ''; ?> href="/search/?terms=<?php echo $this->terms; ?>&type=<?php echo $category['type']; ?>"><?php echo $category['name'] ?> <span class="item-count"><?php echo $category['count']; ?></span></a>
					</li>
					<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div><!-- / .container -->
			<?php endif; ?>
		</nav><!-- / .aside -->
		<div class="subject">
			<div class="container">
				<?php if (!$noResult): ?>
				<div class="results list"><!-- add "tiled" to class for tiled view -->
					<?php foreach ($this->results as $result): ?>
					<div class="result">
						<div class="result-body">

							<!-- Title : mandatory -->
							<h3 class="result-title"><a href="<?php echo $result['url']; ?>"><b><!-- highlight portion --></b><?php echo $result['title']; ?></a></h3>

							<div class="result-extras">
								<!-- Cateogory : mandatory -->
								<span class="result-category"><?php echo ucfirst($result['hubtype']); ?></span>

								<! -- Date  : mandatory -->
								<?php $date = new \Hubzero\Utility\Date($result['date']); ?>
								<span class="result-timestamp"><time datetime="<?php echo $result['date'] ?>"><?php echo $date->toLocal('Y-m-d h:mA'); ?></time></span>

								<!-- Authors -->
								<?php if (isset($result['author'])): ?>
								<span class="result-authors">
									<span class="result-author"><?php echo $result['authorString']; ?></span>
								</span>
								<?php endif; ?>

							</div>
							<!-- Snippet : mandatory -->
							<div class="result-snippet">
							<?php echo $result['snippet']; ?>
							</div><!-- end result snippet -->

							<?php if (isset($result['tags'])): ?>
							<!-- Tags -->
							<div class="result-tags">
								<ul class="tags">
									<?php foreach ($result['tags'] as $tag): ?>
									<li><a class="tag" href="#"><?php echo $tag; ?></a></li>
									<?php endforeach; ?>
								</ul>
							</div>
							<?php endif; ?>

							<!-- Result URL -->
							<div class="result-url"><a href="<?php echo $result['url'] ?>"><?php echo $result['url']; ?></a></div>

						</div> <!-- End Result Body -->
					</div> <!-- End Result -->
					<?php endforeach; ?>
				</div>
				<form action="/search/" method="get">
					<nav class="pagination">
						<?php echo $this->pagination->render(); ?>
					</nav>
					<?php endif; ?>
					<div class="clearfix"></div>
					<input type="hidden" name="terms" value="<?php echo $terms; ?>" />
				</form>
			</div><!-- / .container -->
		</div><!-- / .subject -->
	</div>
</section><!-- / .main section -->
<!-- end component output -->
