<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
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
 */

// No direct access
defined('_HZEXEC_') or die();

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

$minHeight = $cols > 3 ? round($this->minHeight/$cols) : $this->minHeight;
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
			$style = $genStyle . 'background:url(\'' . Route::url('index.php?option=' . $this->option . '&alias='
				. $this->model->get('alias') . '&controller=media&media=' . $selected[$i]['image']) . '\') no-repeat;';
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