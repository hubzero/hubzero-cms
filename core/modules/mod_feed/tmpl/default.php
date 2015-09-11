<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
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

// no direct access
defined('_HZEXEC_') or die;

if ($feed != false)
{
	//image handling
	$iUrl   = isset($feed->image->url)   ? $feed->image->url   : null;
	$iTitle = isset($feed->image->title) ? $feed->image->title : null;
	?>
	<div style="direction: <?php echo $rssrtl ? 'rtl' :'ltr'; ?>; text-align: <?php echo $rssrtl ? 'right' :'left'; ?> ! important"  class="feed<?php echo $moduleclass_sfx; ?>">
	<?php
	// feed description
	if (!is_null($feed->title) && $params->get('rsstitle', 1)) {
		?>

				<h4>
					<a href="<?php echo str_replace('&', '&amp', $feed->link); ?>" rel="external">
						<?php echo $feed->title; ?>
					</a>
				</h4>

		<?php
	}

	// feed description
	if ($params->get('rssdesc', 1)) {
	?>
		<?php echo $feed->description; ?>
		<?php
	}

	// feed image
	if ($params->get('rssimage', 1) && $iUrl) {
	?>
		<img src="<?php echo $iUrl; ?>" alt="<?php echo @$iTitle; ?>" />

	<?php
	}

	$actualItems = count($feed->items);
	$setItems    = $params->get('rssitems', 5);

	if ($setItems > $actualItems)
	{
		$totalItems = $actualItems;
	}
	else
	{
		$totalItems = $setItems;
	}
	?>

			<ul class="newsfeed<?php echo $params->get('moduleclass_sfx'); ?>">
			<?php
			$words = $params->def('word_count', 0);
			for ($j = 0; $j < $totalItems; $j ++)
			{
				$currItem = & $feed->items[$j];
				// item title
				?>
				<li class="newsfeed-item">
					<?php
					if (!is_null($currItem->get_link()))
					{
						if (!is_null($feed->title) && $params->get('rsstitle', 1))
						{
							echo '<h5 class="feed-link">';
						}
						else
						{
							echo '<h4 class="feed-link">';
						}
						?>
						<a href="<?php echo $currItem->get_link(); ?>" target="_blank">
							<?php echo $currItem->get_title(); ?>
						</a>
						<?php
						if (!is_null($feed->title) && $params->get('rsstitle', 1))
						{
							echo '</h5>';
						}
						else
						{
							echo '</h4>';
						}
					}

					// item description
					if ($params->get('rssitemdesc', 1))
					{
						// item description
						$text = $currItem->get_description();
						$text = str_replace('&apos;', "'", $text);
						$text = strip_tags($text);
						// word limit check
						if ($words)
						{
							$texts = explode(' ', $text);
							$count = count($texts);
							if ($count > $words)
							{
								$text = '';
								for ($i = 0; $i < $words; $i ++)
								{
									$text .= ' '.$texts[$i];
								}
								$text .= '...';
							}
						}
						?>

							<p><?php echo $text; ?></p>

						<?php
					}
					?>
				</li>
				<?php
			}
			?>
			</ul>

	</div>
	<?php
}
