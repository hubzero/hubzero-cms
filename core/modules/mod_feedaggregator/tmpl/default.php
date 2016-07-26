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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;

if ($posts != false)
{

	$actualItems = count($posts);
	$setItems    = $params->get('itemcount', 5);

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
			foreach ($posts as $currItem)
			{
				// item title
				?>
				<li class="newsfeed-item">
					<a href="<?php echo $currItem->url; ?>" target="_blank">
						<h5><?php echo $currItem->title; ?></h5>
					</a>

					<?php
					// item description
					if ($params->get('showdescription', 1))
					{
						// item description
						$text = $currItem->description;
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
