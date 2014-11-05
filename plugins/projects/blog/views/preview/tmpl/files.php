<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css()
     ->js();

$body   	= '';
$selected 	= $this->selected;

if (!$selected || empty($selected))
{
	return false;
}
$rows 	= 1;
$cols 	= count($selected);
$limit  = 12; // Do not show more that 12 previews
$empty  = 0;

if (count($selected) % 2 == 0)
{
	$rows = count($selected)/2;
	$cols = count($selected) > 2 ? 4 : 2;
}
elseif (count($selected) % 3 == 0)
{
	$rows = count($selected)/3;
	$cols = 3;
}
elseif (count($selected) >= 5)
{
	$cols = 3;
	$rows = ceil(count($selected)/3);
	$empty = ($rows * $cols) - count($selected);
}

$minHeight = round($this->minHeight/$cols);
$genStyle = 'min-height:' . $minHeight . 'px;';

$colors = array('#909a9e', '#878795', '#a7a9a4', 'black', '#646d70', '#e2d8c5', '#d4cfd8');
if ($empty)
{
	for ($i = 0; $i < $empty; $i++)
	{
		shuffle($colors);
		$color = isset($colors[$i]) ? $colors[$i] : 'black';
		$selected[] = array('color' => $color);
	}

	// Randomize
	shuffle($selected);
}
?>
<section class="photos grid<?php echo $cols; ?>">
	<?php for ($i = 0; $i < count($selected); $i++) {
		if ($i >= $limit)
		{
			break;
		}
		if (isset($selected[$i]['image']))
		{
			$style = $genStyle . 'background:url(\'' . JRoute::_('index.php?option=' . $this->option . '&alias='
				. $this->project->alias . '&controller=media&media=' . $selected[$i]['image']) . '\') no-repeat;';
		}
		else
		{
			$style = $genStyle . 'background:' . $selected[$i]['color'] . ';';
		}
		if (count($selected) == 1)
		{
			// Single image - do not scale
			$style .= 'background-size:contain !important;background-position: center;';
		}
	?>
		<span style="<?php echo $style; ?>"></span>
	<?php } ?>
</section>