<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$boosts = $this->boosts;
?>

<table class="adminlist">

	<?php
		$this->view('_boosts_list_header')
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
</table>
