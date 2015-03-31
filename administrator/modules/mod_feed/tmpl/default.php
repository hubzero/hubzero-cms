<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_JEXEC') or die;
?>
<div style="direction: <?php echo $rssrtl ? 'rtl' :'ltr'; ?>; text-align: <?php echo $rssrtl ? 'right' :'left'; ?>">
	<?php
	if ($rssDoc != false)
	{
		// channel header and link
		$channel['title'] = $filter->clean($rssDoc->get_title());
		$channel['link']  = $filter->clean($rssDoc->get_link());
		$channel['description'] = $filter->clean($rssDoc->get_description());

		// channel image if exists
		$image['url']   = $rssDoc->get_image_url();
		$image['title'] = $rssDoc->get_image_title();

		//image handling
		$iUrl   = isset($image['url']) ? $image['url'] : null;
		$iTitle = isset($image['title']) ? $image['title'] : null;

		// items
		$items = $rssDoc->get_items();

		// feed elements
		$items = array_slice($items, 0, $rssitems);
		?>
		<table class="moduletable<?php echo $this->escape($params->get('moduleclass_sfx')); ?>">
			<tbody>
			<?php
			// feed description
			if (!is_null($channel['title']) && $rsstitle) {
			?>
				<tr>
					<td>
						<strong>
							<a href="<?php echo $this->escape(str_replace('&', '&amp;', $channel['link'])); ?>" target="_blank">
							<?php echo $this->escape($channel['title']); ?></a>
						</strong>
					</td>
				</tr>
			<?php
			}

			// feed description
			if ($rssdesc) {
			?>
				<tr>
					<td>
						<?php echo $channel['description']; ?>
					</td>
				</tr>
			<?php
			}

			// feed image
			if ($rssimage && $iUrl) {
			?>
				<tr>
					<td>
						<img src="<?php echo $this->escape($iUrl); ?>" alt="<?php echo $this->escape(@$iTitle); ?>" />
					</td>
				</tr>
			<?php
			}

			$actualItems = count($items);
			$setItems = $rssitems;

			if ($setItems > $actualItems) {
				$totalItems = $actualItems;
			} else {
				$totalItems = $setItems;
			}
			?>
				<tr>
					<td>
						<ul class="newsfeed<?php echo $this->escape($moduleclass_sfx); ?>"  >
						<?php
						for ($j = 0; $j < $totalItems; $j ++)
						{
							$currItem = & $items[$j];
							// item title
							?>
							<li>
							<?php
							if (!is_null($currItem->get_link())) {
							?>
								<a href="<?php echo $this->escape($currItem->get_link()); ?>" target="_child">
								<?php echo $this->escape($currItem->get_title()); ?></a>
							<?php
							}

							// item description
							if ($rssitemdesc)
							{
								// item description
								$text = $filter->clean(html_entity_decode($currItem->get_description(), ENT_COMPAT, 'UTF-8'));
								$text = str_replace('&apos;', "'", $text);

								// word limit check
								if ($words) {
									$texts = explode(' ', $text);
									$count = count($texts);
									if ($count > $words) {
										$text = '';
										for ($i = 0; $i < $words; $i ++)
										{
											$text .= ' '.$texts[$i];
										}
										$text .= '...';
									}
								}
								?>
								<div style="text-align: <?php echo $rssrtl ? 'right': 'left'; ?> !important">
									<?php echo $text; ?>
								</div>
								<?php
							}
							?>
							</li>
							<?php
						}
						?>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}
	?>
</div>
