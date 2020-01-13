<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$field = $this->field;
$sortingCriteria = $this->sortingCriteria;
$sortedColumn = $sortingCriteria['field'];
$sortDirection = $sortingCriteria['direction'];
$title = $this->title;

if ($sortedColumn == $field && $sortDirection == 'desc')
{
	$caret = '&#x2303;';
}
else
{
	$caret = '&#x2304;';
	$sortDirection = 'asc';
}
?>

<td class="sortable"
	data-sort-direction="<?php echo $sortDirection; ?>"
	data-sort-field="<?php echo $field; ?>">
	<?php echo $title; ?>

	<span class="fontcon">
		<?php echo $caret; ?>
	</span>
</td>
