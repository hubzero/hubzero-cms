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


// no direct access
defined('_JEXEC') or die;

foreach ($list as $item) : ?>
	<li <?php if ($_SERVER['PHP_SELF'] == Route::url(ContentHelperRoute::getCategoryRoute($item->id))) echo ' class="active"';?>>
		<?php $levelup = $item->level-$startLevel -1; ?>
		<h<?php echo $params->get('item_heading')+ $levelup; ?>>
			<a href="<?php echo Route::url(ContentHelperRoute::getCategoryRoute($item->id)); ?>">
				<?php echo $item->title; ?>
			</a>
		</h<?php echo $params->get('item_heading')+ $levelup; ?>>

		<?php
		if ($params->get('show_description', 0))
		{
			echo JHtml::_('content.prepare', $item->description, $item->getParams(), 'mod_articles_categories.content');
		}
		if ($params->get('show_children', 0) && (($params->get('maxlevel', 0) == 0) || ($params->get('maxlevel') >= ($item->level - $startLevel))) && count($item->getChildren()))
		{
			echo '<ul>';
			$temp = $list;
			$list = $item->getChildren();
			require $this->getLayoutPath($params->get('layout', 'default') . '_items');
			$list = $temp;
			echo '</ul>';
		}
		?>
	</li>
<?php endforeach; ?>
