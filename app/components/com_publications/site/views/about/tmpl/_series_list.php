<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$series = $this->series;
?>

<h4><?php echo Lang::txt('COM_PUBLICATIONS_SERIES'); ?></h4>
<div class="pub-content">
	<p><?php echo Lang::txt('COM_PUBLICATIONS_IS_PART_OF_SERIES'); ?></p>
	<ul class="element-list">
	<?php
		foreach ($series as $seriesData):
			$this->view('_series_item')
				->set('series', $seriesData)
				->display();
		endforeach;
	?>
	</ul>
</div>
