<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

$body     = '';
$selected = $this->selected;

if (!$selected || empty($selected))
{
	return false;
}
$rows   = 1;
$cols   = count($selected);
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
	<?php
	for ($i = 0; $i < count($selected); $i++)
	{
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
		<?php
	}
	?>
</section>