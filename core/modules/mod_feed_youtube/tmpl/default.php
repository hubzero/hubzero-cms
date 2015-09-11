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

// no direct access
defined('_HZEXEC_') or die();
?>
<div class="youtubefeed<?php echo $this->params->get('moduleclass_sfx'); ?>">
<?php
if ($this->feed)
{
	// Image handling
	$iTitle = isset($this->feed->image->title) ? $this->feed->image->title : null;

	// Get layout
	$layout = $this->params->get('layout') ? $this->params->get('layout') : 'vertical';

	// Push some CSS to the template
	$this->css();

	$youtube_ima  = DS . trim($this->params->get('imagepath'), DS);
	if (!is_file(PATH_APP . $youtube_ima))
	{
		$youtube_ima = '';
	}

	// Link to more videos
	$morelink =  $this->params->get('moreurl') ? str_replace('&', '&amp', $this->params->get('moreurl')) : str_replace('&', '&amp', $this->feed->link);

	// Feed image & title
	if ((!is_null($this->feed->title) or $this->params->get('feedtitle', '')) && $this->params->get('rsstitle', 1)) { ?>
		<h3 class="feed_title">
			<?php if ((!is_null($this->feed->title) or $this->params->get('feedtitle', '')) && $this->params->get('rsstitle', 1)) { ?>
				<a href="<?php echo $morelink; ?>" rel="external">
					<?php echo ($this->params->get('feedtitle') ? $this->params->get('feedtitle') : $this->feed->title); ?>
				</a>
			<?php } ?>
			<?php if ($this->params->get('rssimage', 1) && $youtube_ima) { ?>
				<a href="<?php echo str_replace('&', '&amp', $this->feed->link); ?>" rel="external">
					<img src="<?php echo $youtube_ima; ?>" alt="<?php echo @$iTitle; ?>"/>
				</a>
			<?php } ?>
		</h3>
	<?php } ?>

	<?php if ((!is_null($this->feed->description) or $this->params->get('feeddesc', '')) && $this->params->get('rssdesc', 0)) { ?>
		<p><?php echo ($this->params->get('feeddesc') ? $this->params->get('feeddesc') : $this->feed->description); ?></p>
	<?php } ?>

	<ul class="layout_<?php echo $layout; ?>">
		<?php
		$actualItems = count($this->feed->items);
		$setItems    = $this->params->get('rssitems', 5);
		$totalItems  = ($setItems > $actualItems ? $actualItems : $setItems);

		$path = DS . trim($this->params->get('webpath', '/site/youtube'), DS);
		if (!is_dir(PATH_APP . $path))
		{
			Filesystem::makeDirectory(PATH_APP . $path);
		}
		$isDir = is_dir(PATH_APP . $path);

		$words = $this->params->def('word_count', 0);
		for ($j = 0; $j < $totalItems; $j ++)
		{
			$currItem = & $this->feed->items[$j];
		?>
		<li>
			<?php
			if (!is_null($currItem->get_link()))
			{
				// Get video id
				$match = array();
				$vid = 0;
				preg_match("/youtube\.com\/watch\?v=(.*)/", $currItem->get_link() , $match);
				if (count($match) > 1 && strlen($match[1]) > 11)
				{
					$vid = substr($match[1], 0, 11);
				}

				// Copy thumbnail to server
				if ($vid && $isDir)
				{
					$img_src = 'http://img.youtube.com/vi/' . $vid . '/default.jpg';
					$thumb   = $path . DS . $vid . '.jpg';

					if (!is_file(PATH_APP . $thumb))
					{
						copy($img_src, PATH_APP . $thumb);
					}
					if (!is_file(PATH_APP . $thumb))
					{
						$vid = 0;
					}
				}

				// Display with thumbnails
				if ($vid) { ?>
					<a href="<?php echo $currItem->get_link(); ?>" rel="external">
						<img src="<?php echo $thumb; ?>" alt="" />
					</a>
					<a href="<?php echo $currItem->get_link(); ?>" rel="external">
						<span><?php echo $currItem->get_title(); ?></span>
					</a>
				<?php } else { ?>
					<a href="<?php echo $currItem->get_link(); ?>" rel="external">
						<span><?php echo $currItem->get_title(); ?></span>
					</a>
				<?php
				}
			}
			?>
		</li>
		<?php
		}
		?>
	</ul>

	<?php if ($layout == 'horizontal') { ?>
		<div class="clear"></div>
	<?php } ?>

	<?php if (!is_null($this->params->get('moreurl'))  && $this->params->get('showmorelink', 0)) { ?>
		<p class="more">
			<a href="<?php echo $morelink; ?>" rel="external"><?php echo Lang::txt('MOD_FEED_YOUTUBE_MORE'); ?></a>
		</p>
	<?php } ?>
<?php } ?>
</div>
