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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		<div class="col span8">
			<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=view'); ?>" method="get" class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_TAGS_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<?php echo $this->autocompleter('tags', 'tag', '', 'actags'); ?>
				</fieldset>
			</form><!-- / .container -->
			<p><?php echo Lang::txt('COM_TAGS_ARE'); ?></p>
			<p><a class="popup" href="<?php echo Route::url('index.php?option=com_help&component=' . substr($this->option, 4) . '&page=index'); ?>"><?php echo Lang::txt('COM_TAGS_HOW_DO_TAGS_WORK'); ?></a></p>
		</div>
		<div class="col span3 offset1 omega">
			<a class="btn icon-browse" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>">
				<?php echo Lang::txt('COM_TAGS_BROWSE_LIST'); ?>
			</a>
		</div>
	</div>
</section><!-- / #introduction.section -->

<section class="section">

	<div class="grid">
		<div class="col span6 recent-tags">
			<h2><?php echo Lang::txt('COM_TAGS_RECENTLY_USED'); ?></h2>

			<?php
			$filters = array(
				'limit'    => 50,
				'admin'    => 0,
				'sort'     => 'taggedon',
				'sort_Dir' => 'DESC'
			);

			if ($this->config->get('cache', 1))
			{
				$ttl = intval($this->config->get('cache_time', 15));

				if (!($cloud = Cache::get('tags.recent')))
				{
					$cloud = $this->cloud->render('html', $filters, true);

					Cache::put('tags.recent', $cloud, $ttl);
				}
			}
			else
			{
				$cloud = $this->cloud->render('html', $filters, true);
			}

			echo ($cloud ? $cloud : '<p class="warning">' . Lang::txt('COM_TAGS_NO_TAGS') . '</p>' . "\n");
			?>
		</div><!-- / .col span6 -->
		<div class="col span6 omega top-tags">
			<h2><?php echo Lang::txt('COM_TAGS_TOP_USED'); ?></h2>

			<?php
			$filters = array(
				'limit'    => 50,
				'admin'    => 0,
				'sort'     => 'objects',
				'sort_Dir' => 'DESC'
			);

			if ($this->config->get('cache', 1))
			{
				if (!($cloud = Cache::get('tags.top')))
				{
					$cloud = $this->cloud->render('html', $filters, true);

					Cache::put('tags.top', $cloud, $ttl);
				}
			}
			else
			{
				$cloud = $this->cloud->render('html', $filters, true);
			}

			echo ($cloud ? $cloud : '<p class="warning">' . Lang::txt('COM_TAGS_NO_TAGS') . '</p>' . "\n");
			?>
		</div><!-- / .col span6 omega -->
	</div><!-- / .grid -->

</section><!-- / .section -->
