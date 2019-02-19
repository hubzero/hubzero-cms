<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	<div id="container1" class="<?php echo $this->option; ?>-chart" data-datasets="<?php echo $this->option; ?>-data"></div>
	<script type="application/json" id="<?php echo $this->option; ?>-data">
		<?php
		$c = '';
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
	<div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
