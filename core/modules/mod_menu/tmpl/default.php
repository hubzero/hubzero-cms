<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die;

// Note. It is important to remove spaces between elements.
?>

<ul class="menu<?php echo $class_sfx; ?>"<?php
	$tag = '';
	if ($params->get('tag_id')!=NULL)
	{
		$tag = $params->get('tag_id').'';
		echo ' id="' . $tag . '"';
	}
?>>
	<?php
	foreach ($list as $i => &$item) :
		$class = 'item-' . $item->id;
		if ($item->id == $active_id)
		{
			$class .= ' current';
		}

		if (in_array($item->id, $path))
		{
			$class .= ' active';
		}
		elseif ($item->type == 'alias')
		{
			$aliasToId = $item->params->get('aliasoptions');
			if (count($path) > 0 && $aliasToId == $path[count($path)-1])
			{
				$class .= ' active';
			}
			elseif (in_array($aliasToId, $path))
			{
				$class .= ' alias-parent-active';
			}
		}

		if ($item->deeper)
		{
			$class .= ' deeper';
		}

		if ($item->parent)
		{
			$class .= ' parent';
		}

		if (!empty($class))
		{
			$class = ' class="' . trim($class) . '"';
		}

		echo '<li' . $class . '>';

		// Render the menu item.
		switch ($item->type) :
			case 'separator':
			case 'url':
			case 'component':
				require $this->getLayoutPath('default_' . $item->type);
			break;

			default:
				require $this->getLayoutPath('default_url');
			break;
		endswitch;

		// The next item is deeper.
		if ($item->deeper)
		{
			echo '<ul>';
		}
		// The next item is shallower.
		elseif ($item->shallower)
		{
			echo '</li>';
			echo str_repeat('</ul></li>', $item->level_diff);
		}
		// The next item is on the same level.
		else
		{
			echo '</li>';
		}
	endforeach;
	?>
</ul>
