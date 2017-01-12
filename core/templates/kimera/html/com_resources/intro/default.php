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

$this->css('introduction.css', 'system')
     ->css()
     ->js();
?>
<header id="content-header" class="intro-page">
	<h2><?php echo $this->title; ?></h2>

	<nav id="content-header-extra">
		<p>
			<a class="btn icon-add" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=new'); ?>">
				<?php echo Lang::txt('Submit a resource'); ?>
			</a>
		</p>
	</nav>
</header><!-- / #content-header -->

<section id="introduction" class="section">
	<div class="grid">
		<div class="col span8">
			<div class="container data-entry">
				<form action="<?php echo Route::url('index.php?option=com_search'); ?>" method="get">
					<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('Search'); ?>" />
					<fieldset class="entry-search">
						<input type="text" name="terms" value="" placeholder="<?php echo Lang::txt('What are you interested in?'); ?>" />
						<!-- <input type="hidden" name="option" value="<?php echo $this->option; ?>" /> -->
						<input type="hidden" name="domains[]" value="resources" />
						<input type="hidden" name="section" value="resources" />
					</fieldset>
				</form>
			</div><!-- / .container -->
			<p>
				<?php echo Lang::txt('Resources are <strong>user-submitted</strong> pieces of content that range from video presentations to publications to simulation tools.'); ?>
			</p>
			<p>
				<a class="popup" href="<?php echo Route::url('index.php?option=com_help&component=' . substr($this->option, 4) . '&page=index'); ?>">
					<?php echo Lang::txt('More on how resources work &raquo;'); ?>
				</a>
			</p>
		</div>
		<div class="col span3 offset1 omega">
			<a class="btn icon-browse" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>">
				<?php echo Lang::txt('Browse the catalog'); ?>
			</a>
		</div>
	</div>
</section><!-- / #introduction.section -->

<section class="section">
	<?php
	if ($this->categories) {
	?>
		<div class="grid">
			<div class="col span3">
				<h2><?php echo Lang::txt('Categories'); ?></h2>
			</div><!-- / .col span3 -->
			<div class="col span9 omega">
				<div class="grid">
				<?php
				$i = 0;
				$clm = '';

				foreach ($this->categories as $category)
				{
					if ($category->id == 7 && !Component::isEnabled('com_tools', true))
					{
						continue;
					}

					$i++;
					switch ($i)
					{
						case 3: $clm = 'omega'; break;
						case 2: $clm = ''; break;
						case 1:
						default: $clm = ''; break;
					}

					if (substr($category->alias, -3) == 'ies')
					{
						$cls = $category->alias;
					}
					else
					{
						$cls = rtrim($category->alias, 's');
					}
					?>
					<div class="col span-third <?php echo $clm; ?>">
						<div class="<?php echo $cls; ?>">
							<h3>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&type=' . $category->alias); ?>">
									<?php echo $this->escape(strip_tags(stripslashes($category->type))); ?>
								</a>
							</h3>
							<p>
								<?php echo $this->escape(strip_tags(stripslashes($category->description))); ?>
							</p>
							<p>
								<a class="read-more" href="<?php echo Route::url('index.php?option=' . $this->option . '&type=' . $category->alias); ?>" title="<?php echo Lang::txt('Browse %s', $this->escape(stripslashes($category->type))); ?>">
									<?php echo Lang::txt('Browse <span>%s </span>&rsaquo;', $this->escape(stripslashes($category->type))); ?>
								</a>
							</p>
						</div>
					</div><!-- / .col span-third <?php echo $clm; ?> -->
					<?php
					if ($clm == 'omega')
					{
						echo '</div><div class="grid">';
						$clm = '';
						$i = 0;
					}
				}
				if ($i == 1)
				{
					?>
					<div class="col span-third">
						<p> </p>
					</div><!-- / .col span-third -->
					<?php
				}
				if ($i == 1 || $i == 2)
				{
					?>
					<div class="col span-third omega">
						<p> </p>
					</div><!-- / .col span-third -->
					<?php
				}
				?>
				</div><!-- / .grid -->
			</div><!-- / .col span9 omega -->
		</div><!-- / .grid -->
	<?php
	}
	?>
</section><!-- / .section -->
