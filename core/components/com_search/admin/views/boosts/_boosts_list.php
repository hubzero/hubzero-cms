<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$boosts = $this->boosts;
$sortField = $this->sortField;
$sortDirection = $this->sortDirection;
?>

<table class="adminlist">

	<?php
		$this->view('_boosts_list_header')
			->set('sortField', $sortField)
			->set('sortDirection', $sortDirection)
			->display();
	?>

	<tbody>
		<?php
			$i = 0;
			foreach ($boosts as $boost):
				$this->view('_boost_item')
					->set('boost', $boost)
					->set('order', $i)
					->display();
				$i++;
			endforeach;
		?>
	</tbody>

	<tfoot>
		<tr>
			<td colspan="9">
				<?php	echo $boosts->pagination ?>
			</td>
		</tr>
	</tfoot>
</table>
