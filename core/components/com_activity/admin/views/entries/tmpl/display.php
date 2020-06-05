<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_ACTIVITY_TITLE'), 'activity');

if (User::authorise('core.admin', $this->option))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}
Toolbar::spacer();
Toolbar::help('entries');

Html::behavior('chart');

$this->css()
	->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<div id="container1" class="<?php echo $this->option; ?>-chart chart" data-datasets="<?php echo $this->option; ?>-data"></div>
	<script type="application/json" id="<?php echo $this->option; ?>-data">
		<?php
		$c = '';
		$top = 0;
		if ($this->data)
		{
			$c = array();

			foreach ($this->data as $k => $v)
			{
				$top = $v > $top ? $v : $top;
				$c[] = '[' . Date::of($k)->toUnix() . ',' . $v . ']';
			}

			$c = implode(',', $c);
		}
		?>
		{
			"datasets": [
				{
					"color": "orange",
					"label": "<?php echo Lang::txt('COM_ACTIVITY_RECENT'); ?>",
					"data": [<?php echo $c; ?>]
				}
			]
		}
	</script>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
